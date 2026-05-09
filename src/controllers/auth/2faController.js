const speakeasy = require('speakeasy');
const qrcode = require('qrcode');
const prisma = require('../../prisma');

exports.show2FASetup = async (req, res) => {
  const user = await prisma.user.findUnique({
    where: { id: req.user.id }
  });

  if (user.twoFactorEnabled) {
    return res.redirect('/dashboard/settings/security');
  }

  // Generate secret
  const secret = speakeasy.generateSecret({
    name: `DropLaunch (${user.email})`,
    issuer: 'DropLaunch'
  });

  // Store temp secret in session
  req.session.temp2FASecret = secret.base32;

  // Generate QR code
  const qrCodeUrl = await qrcode.toDataURL(secret.otpauth_url);

  res.render('user/settings/2fa-setup', {
    title: 'Setup Two-Factor Authentication',
    secret: secret.base32,
    qrCodeUrl,
    user
  });
};

exports.enable2FA = async (req, res) => {
  const { token } = req.body;

  if (!req.session.temp2FASecret) {
    req.flash('error', '2FA setup session expired. Please try again.');
    return res.redirect('/dashboard/settings/security');
  }

  // Verify token
  const verified = speakeasy.totp.verify({
    secret: req.session.temp2FASecret,
    encoding: 'base32',
    token: token,
    window: 2
  });

  if (!verified) {
    req.flash('error', 'Invalid 2FA token. Please try again.');
    return res.redirect('/dashboard/settings/2fa/setup');
  }

  // Save 2FA secret
  await prisma.user.update({
    where: { id: req.user.id },
    data: {
      twoFactorSecret: req.session.temp2FASecret,
      twoFactorEnabled: true
    }
  });

  // Clear temp secret
  delete req.session.temp2FASecret;

  // Log login history
  await prisma.loginHistory.create({
    data: {
      userId: req.user.id,
      ipAddress: req.ip,
      userAgent: req.get('User-Agent'),
      success: true
    }
  });

  req.flash('success', 'Two-factor authentication has been enabled.');
  res.redirect('/dashboard/settings/security');
};

exports.disable2FA = async (req, res) => {
  const { password } = req.body;
  const bcrypt = require('bcryptjs');

  const user = await prisma.user.findUnique({
    where: { id: req.user.id }
  });

  if (!await bcrypt.compare(password, user.password)) {
    req.flash('error', 'Incorrect password.');
    return res.redirect('/dashboard/settings/security');
  }

  await prisma.user.update({
    where: { id: req.user.id },
    data: {
      twoFactorSecret: null,
      twoFactorEnabled: false
    }
  });

  req.flash('success', 'Two-factor authentication has been disabled.');
  res.redirect('/dashboard/settings/security');
};

exports.verify2FA = async (req, res) => {
  const { token } = req.body;
  const userId = req.session.pending2FAUserId;

  if (!userId) {
    req.flash('error', '2FA verification session expired.');
    return res.redirect('/login');
  }

  const user = await prisma.user.findUnique({
    where: { id: userId }
  });

  if (!user || !user.twoFactorEnabled) {
    req.flash('error', '2FA not enabled for this account.');
    return res.redirect('/login');
  }

  const verified = speakeasy.totp.verify({
    secret: user.twoFactorSecret,
    encoding: 'base32',
    token: token,
    window: 2
  });

  if (!verified) {
    req.flash('error', 'Invalid 2FA token.');
    return res.redirect('/login?2fa=1');
  }

  // Complete login
  req.session.regenerate((err) => {
    if (err) {
      req.flash('error', 'Login failed. Please try again.');
      return res.redirect('/login');
    }

    req.session.userId = user.id;

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

    delete req.session.pending2FAUserId;

    req.session.save(() => {
      if (user.role === 'admin') {
        return res.redirect('/admin');
      }
      res.redirect('/dashboard');
    });
  });
};