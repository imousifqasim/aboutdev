const prisma = require('../../prisma');

exports.index = async (req, res) => {
  const { search, status } = req.query;
  const where = { role: 'user' };

  if (search) {
    where.OR = [
      { name: { contains: search } },
      { email: { contains: search } },
    ];
  }

  if (status === 'banned') where.isBanned = true;
  if (status === 'premium') {
    where.profile = { isPremium: true };
  }
  if (status === 'free') {
    where.profile = { isPremium: false };
  }

  const users = await prisma.user.findMany({
    where,
    include: { profile: true },
    orderBy: { createdAt: 'desc' },
  });

  res.render('admin/users/index', { title: 'User Management', users, search, status });
};

exports.show = async (req, res) => {
  const user = await prisma.user.findUnique({
    where: { id: parseInt(req.params.id) },
    include: {
      profile: true,
      links: true,
      payments: { orderBy: { createdAt: 'desc' } },
      subscriptions: { orderBy: { createdAt: 'desc' } },
      analytics: { orderBy: { date: 'desc' }, take: 30 },
    },
  });

  if (!user) {
    req.flash('error', 'User not found.');
    return res.redirect('/admin/users');
  }

  res.render('admin/users/show', { title: `User: ${user.name}`, user: req.user, viewUser: user });
};

exports.toggleBan = async (req, res) => {
  const user = await prisma.user.findUnique({ where: { id: parseInt(req.params.id) } });
  if (!user || user.role === 'admin') {
    req.flash('error', 'Cannot ban an admin user.');
    return res.redirect('/admin/users');
  }

  await prisma.user.update({ where: { id: user.id }, data: { isBanned: !user.isBanned } });
  const status = !user.isBanned ? 'banned' : 'unbanned';
  req.flash('success', `User has been ${status}.`);
  res.redirect('/admin/users');
};

exports.destroy = async (req, res) => {
  const user = await prisma.user.findUnique({ where: { id: parseInt(req.params.id) } });
  if (!user || user.role === 'admin') {
    req.flash('error', 'Cannot delete an admin user.');
    return res.redirect('/admin/users');
  }

  await prisma.user.delete({ where: { id: user.id } });
  req.flash('success', 'User deleted successfully.');
  res.redirect('/admin/users');
};
