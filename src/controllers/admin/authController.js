const prisma = require('../../prisma');

exports.showAuthSettings = async (req, res) => {
  const settings = await prisma.siteSetting.findMany({
    where: {
      key: {
        in: [
          'google_login_enabled',
          'github_login_enabled',
          'email_verification_required',
          'registration_enabled',
          'password_min_length',
          'login_attempts_max',
          'session_timeout'
        ]
      }
    }
  });

  const settingsMap = {};
  settings.forEach(setting => {
    settingsMap[setting.key] = setting.value;
  });

  res.render('admin/settings/auth', {
    title: 'Authentication Settings',
    settings: settingsMap
  });
};

exports.updateAuthSettings = async (req, res) => {
  const {
    google_login_enabled,
    github_login_enabled,
    email_verification_required,
    registration_enabled,
    password_min_length,
    login_attempts_max,
    session_timeout
  } = req.body;

  const settings = [
    { key: 'google_login_enabled', value: google_login_enabled ? 'true' : 'false' },
    { key: 'github_login_enabled', value: github_login_enabled ? 'true' : 'false' },
    { key: 'email_verification_required', value: email_verification_required ? 'true' : 'false' },
    { key: 'registration_enabled', value: registration_enabled ? 'true' : 'false' },
    { key: 'password_min_length', value: password_min_length || '8' },
    { key: 'login_attempts_max', value: login_attempts_max || '5' },
    { key: 'session_timeout', value: session_timeout || '24' }
  ];

  for (const setting of settings) {
    await prisma.siteSetting.upsert({
      where: { key: setting.key },
      update: { value: setting.value },
      create: { key: setting.key, value: setting.value }
    });
  }

  req.flash('success', 'Authentication settings updated successfully.');
  res.redirect('/admin/settings/auth');
};

exports.showUsers = async (req, res) => {
  const page = parseInt(req.query.page) || 1;
  const limit = 20;
  const offset = (page - 1) * limit;

  const users = await prisma.user.findMany({
    include: {
      profile: true,
      subscriptions: {
        where: { isActive: true },
        orderBy: { createdAt: 'desc' },
        take: 1
      }
    },
    orderBy: { createdAt: 'desc' },
    skip: offset,
    take: limit
  });

  const totalUsers = await prisma.user.count();
  const totalPages = Math.ceil(totalUsers / limit);

  res.render('admin/users/index', {
    title: 'Users Management',
    users,
    currentPage: page,
    totalPages,
    totalUsers
  });
};

exports.showUser = async (req, res) => {
  const { id } = req.params;

  const user = await prisma.user.findUnique({
    where: { id: parseInt(id) },
    include: {
      profile: true,
      links: true,
      payments: true,
      subscriptions: true,
      loginHistory: {
        orderBy: { createdAt: 'desc' },
        take: 10
      },
      notifications: {
        orderBy: { createdAt: 'desc' },
        take: 10
      }
    }
  });

  if (!user) {
    req.flash('error', 'User not found.');
    return res.redirect('/admin/users');
  }

  res.render('admin/users/show', {
    title: `User: ${user.name}`,
    user
  });
};

exports.updateUser = async (req, res) => {
  const { id } = req.params;
  const { name, email, role, isBanned } = req.body;

  await prisma.user.update({
    where: { id: parseInt(id) },
    data: {
      name,
      email,
      role,
      isBanned: isBanned === 'on'
    }
  });

  req.flash('success', 'User updated successfully.');
  res.redirect(`/admin/users/${id}`);
};

exports.deleteUser = async (req, res) => {
  const { id } = req.params;

  await prisma.user.delete({
    where: { id: parseInt(id) }
  });

  req.flash('success', 'User deleted successfully.');
  res.redirect('/admin/users');
};

exports.toggleBan = async (req, res) => {
  const { id } = req.params;
  
  const user = await prisma.user.findUnique({
    where: { id: parseInt(id) }
  });

  if (!user) {
    req.flash('error', 'User not found.');
    return res.redirect('/admin/users');
  }

  if (user.role === 'admin') {
    req.flash('error', 'Cannot ban admin users.');
    return res.redirect(`/admin/users/${id}`);
  }

  await prisma.user.update({
    where: { id: parseInt(id) },
    data: { isBanned: !user.isBanned }
  });

  const status = !user.isBanned ? 'banned' : 'unbanned';
  req.flash('success', `User has been ${status} successfully.`);
  res.redirect(`/admin/users/${id}`);
};