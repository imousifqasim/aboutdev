const prisma = require('../../prisma');

exports.index = async (req, res) => {
  const totalUsers = await prisma.user.count({ where: { role: 'user' } });
  const premiumUsers = await prisma.profile.count({ where: { isPremium: true } });
  const pendingPayments = await prisma.payment.count({ where: { status: 'pending' } });
  const totalRevenue = await prisma.payment.aggregate({
    where: { status: 'approved' },
    _sum: { amount: true },
  });

  const recentUsers = await prisma.user.findMany({
    where: { role: 'user' },
    orderBy: { createdAt: 'desc' },
    take: 5,
  });

  const recentPayments = await prisma.payment.findMany({
    include: { user: true },
    orderBy: { createdAt: 'desc' },
    take: 5,
  });

  const monthlyUsers = [];
  for (let i = 5; i >= 0; i--) {
    const d = new Date();
    d.setMonth(d.getMonth() - i);
    const year = d.getFullYear();
    const month = d.getMonth();
    const startOfMonth = new Date(year, month, 1);
    const endOfMonth = new Date(year, month + 1, 0, 23, 59, 59);

    const count = await prisma.user.count({
      where: {
        role: 'user',
        createdAt: { gte: startOfMonth, lte: endOfMonth },
      },
    });

    monthlyUsers.push({
      month: d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' }),
      count,
    });
  }

  res.render('admin/dashboard', {
    title: 'Admin Dashboard',
    totalUsers,
    premiumUsers,
    pendingPayments,
    totalRevenue: totalRevenue._sum.amount || 0,
    recentUsers,
    recentPayments,
    monthlyUsers,
  });
};
