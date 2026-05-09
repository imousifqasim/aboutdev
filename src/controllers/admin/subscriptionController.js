const prisma = require('../../prisma');

exports.index = async (req, res) => {
  const users = await prisma.user.findMany({
    where: { role: 'user' },
    include: {
      profile: true,
      subscriptions: { orderBy: { createdAt: 'desc' }, take: 1 },
    },
    orderBy: { createdAt: 'desc' },
  });

  res.render('admin/subscriptions/index', { title: 'Subscriptions', users });
};

exports.togglePremium = async (req, res) => {
  const userId = parseInt(req.params.id);
  const user = await prisma.user.findUnique({
    where: { id: userId },
    include: { profile: true },
  });

  if (!user || !user.profile) {
    req.flash('error', 'User not found.');
    return res.redirect('/admin/subscriptions');
  }

  if (user.profile.isPremium) {
    await prisma.profile.update({ where: { id: user.profile.id }, data: { isPremium: false, showBranding: true } });
    await prisma.subscription.updateMany({ where: { userId, isActive: true }, data: { isActive: false } });
    req.flash('success', 'Premium removed from user.');
  } else {
    await prisma.profile.update({ where: { id: user.profile.id }, data: { isPremium: true, showBranding: false } });
    await prisma.subscription.updateMany({ where: { userId, isActive: true }, data: { isActive: false } });

    const endDate = new Date();
    endDate.setFullYear(endDate.getFullYear() + 1);

    await prisma.subscription.create({
      data: { userId, plan: 'premium', startDate: new Date(), endDate, isActive: true },
    });
    req.flash('success', 'Premium activated for user.');
  }

  res.redirect('/admin/subscriptions');
};
