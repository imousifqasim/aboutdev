const bcrypt = require('bcryptjs');
const prisma = require('../../prisma');
const { RESERVED_USERNAMES } = require('../../helpers');

exports.showRegistrationForm = (req, res) => {
  res.render('auth/register', { title: 'Register' });
};

exports.register = async (req, res) => {
  const { name, username, email, password, password_confirmation } = req.body;
  const errors = [];

  if (!name || !username || !email || !password) {
    errors.push('All fields are required.');
  }
  if (password !== password_confirmation) {
    errors.push('Passwords do not match.');
  }
  if (password && password.length < 8) {
    errors.push('Password must be at least 8 characters.');
  }
  if (username && !/^[a-zA-Z0-9_-]+$/.test(username)) {
    errors.push('Username can only contain letters, numbers, dashes and underscores.');
  }
  if (RESERVED_USERNAMES.includes(username?.toLowerCase())) {
    errors.push('This username is reserved.');
  }

  if (errors.length === 0) {
    const existingUser = await prisma.user.findUnique({ where: { email } });
    if (existingUser) errors.push('Email is already taken.');

    const existingProfile = await prisma.profile.findUnique({ where: { username: username?.toLowerCase() } });
    if (existingProfile) errors.push('Username is already taken.');
  }

  if (errors.length > 0) {
    req.flash('error', errors.join(' '));
    return res.redirect('/register');
  }

  const hashedPassword = await bcrypt.hash(password, 10);
  const user = await prisma.user.create({
    data: {
      name,
      email,
      password: hashedPassword,
      emailVerifiedAt: new Date(),
      provider: 'local'
    },
  });

  // Create profile
  await prisma.profile.create({
    data: {
      userId: user.id,
      username: username.toLowerCase()
    }
  });

  // Log registration
  await prisma.loginHistory.create({
    data: {
      userId: user.id,
      ipAddress: req.ip,
      userAgent: req.get('User-Agent'),
      success: true
    }
  });

  req.flash('success', 'Account created successfully! Please log in.');
  res.redirect('/login');
};
