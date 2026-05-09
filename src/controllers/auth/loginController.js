const bcrypt = require('bcryptjs');
const crypto = require('crypto');
const prisma = require('../../prisma');
const { sendPasswordResetEmail } = require('../../mail');
const speakeasy = require('speakeasy');

exports.showLoginForm = (req, res) => {
  res.render('auth/login', { title: 'Login' });
};

exports.login = async (req, res) => {
  const { email, password, remember } = req.body;
  if (!email || !password) {
    req.flash('error', 'Email and password are required.');
    return res.redirect('/login');
  }

  const user = await prisma.user.findUnique({ where: { email } });
  if (!user || !user.password || !(await bcrypt.compare(password, user.password))) {
    // Log failed login attempt
    if (user) {
      await prisma.loginHistory.create({
        data: {
          userId: user.id,
          ipAddress: req.ip,
          userAgent: req.get('User-Agent'),
          success: false
        }
      });
    }

    req.flash('error', 'The provided credentials do not match our records.');
    return res.redirect('/login');
  }

  if (user.isBanned) {
    req.flash('error', 'Your account has been banned.');
    return res.redirect('/login');
  }

  // Check if 2FA is enabled
  if (user.twoFactorEnabled) {
    req.session.pending2FAUserId = user.id;
    return res.redirect('/login?2fa=1');
  }

  // Complete login
  req.session.regenerate((err) => {
    if (err) {
      req.flash('error', 'Login failed. Please try again.');
      return res.redirect('/login');
    }
    req.session.userId = user.id;
    req.session.save(() => {
      // Update login info
      prisma.user.update({
        where: { id: user.id },
        data: {
          lastLoginAt: new Date(),
          loginCount: { increment: 1 }
        }
      }).catch(console.error);

      // Log successful login
      prisma.loginHistory.create({
        data: {
          userId: user.id,
          ipAddress: req.ip,
          userAgent: req.get('User-Agent'),
          success: true
        }
      }).catch(console.error);

      if (user.role === 'admin') {
        return res.redirect('/admin');
      }
      res.redirect('/dashboard');
    });
  });
};

exports.logout = (req, res) => {
  req.session.destroy(() => {
    res.redirect('/');
  });
};

exports.showForgotPasswordForm = (req, res) => {
  res.render('auth/forgot-password', { title: 'Forgot Password' });
};

exports.forgotPassword = async (req, res) => {
  const { email } = req.body;
  if (!email) {
    req.flash('error', 'Email is required.');
    return res.redirect('/forgot-password');
  }

  const user = await prisma.user.findUnique({ where: { email } });
  if (!user) {
    req.flash('success', 'If an account with that email exists, we have sent a password reset link.');
    return res.redirect('/forgot-password');
  }

  const resetToken = crypto.randomBytes(32).toString('hex');
  const resetTokenExpires = new Date(Date.now() + 3600000); // 1 hour

  await prisma.user.update({
    where: { id: user.id },
    data: { resetToken, resetTokenExpires },
  });

  try {
    await sendPasswordResetEmail(user.email, resetToken);
    req.flash('success', 'Password reset link sent to your email.');
  } catch (error) {
    console.error('Password reset email error:', error);
    req.flash('error', 'Failed to send reset email. Please try again.');
  }

  res.redirect('/forgot-password');
};

exports.showResetPasswordForm = async (req, res) => {
  const { token } = req.params;
  const user = await prisma.user.findFirst({
    where: {
      resetToken: token,
      resetTokenExpires: { gt: new Date() },
    },
  });

  if (!user) {
    req.flash('error', 'Invalid or expired reset token.');
    return res.redirect('/login');
  }

  res.render('auth/reset-password', { title: 'Reset Password', token });
};

exports.resetPassword = async (req, res) => {
  const { token } = req.params;
  const { password, password_confirmation } = req.body;

  if (!password || password.length < 6) {
    req.flash('error', 'Password must be at least 6 characters.');
    return res.redirect(`/reset-password/${token}`);
  }

  if (password !== password_confirmation) {
    req.flash('error', 'Passwords do not match.');
    return res.redirect(`/reset-password/${token}`);
  }

  const user = await prisma.user.findFirst({
    where: {
      resetToken: token,
      resetTokenExpires: { gt: new Date() },
    },
  });

  if (!user) {
    req.flash('error', 'Invalid or expired reset token.');
    return res.redirect('/login');
  }

  const hashedPassword = await bcrypt.hash(password, 10);
  await prisma.user.update({
    where: { id: user.id },
    data: {
      password: hashedPassword,
      resetToken: null,
      resetTokenExpires: null,
    },
  });

  req.flash('success', 'Password reset successfully. You can now log in.');
  res.redirect('/login');
};
