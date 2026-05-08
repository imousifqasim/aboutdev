const prisma = require('../prisma');
const { isPremium } = require('../helpers');

exports.show = async (req, res) => {
  const { username } = req.params;
  const profile = await prisma.profile.findUnique({
    where: { username },
    include: { user: true },
  });

  if (!profile || profile.user.isBanned) {
    return res.status(404).render('layouts/error', { title: '404', message: 'Profile not found.' });
  }

  const user = profile.user;
  user.isPremiumUser = await isPremium(user.id);
  const links = await prisma.link.findMany({
    where: { userId: user.id, isActive: true },
    orderBy: { position: 'asc' },
  });

  const today = new Date().toISOString().split('T')[0];
  await prisma.analytic.upsert({
    where: { userId_date: { userId: user.id, date: new Date(today) } },
    update: { profileViews: { increment: 1 } },
    create: { userId: user.id, date: new Date(today), profileViews: 1 },
  });

  res.render('public/profile', { title: `${user.name} | DropLaunch`, profile, user, links, layout: false });
};

exports.trackClick = async (req, res) => {
  const link = await prisma.link.findUnique({
    where: { id: parseInt(req.params.id) },
    include: { user: true },
  });

  if (!link || !link.isActive || link.user.isBanned) {
    return res.status(404).send('Not found');
  }

  await prisma.link.update({ where: { id: link.id }, data: { clicks: { increment: 1 } } });

  const today = new Date().toISOString().split('T')[0];
  await prisma.analytic.upsert({
    where: { userId_date: { userId: link.userId, date: new Date(today) } },
    update: { linkClicks: { increment: 1 } },
    create: { userId: link.userId, date: new Date(today), linkClicks: 1 },
  });

  res.redirect(link.url);
};

exports.sendMessage = async (req, res) => {
  const { username } = req.params;
  const profile = await prisma.profile.findUnique({
    where: { username },
    include: { user: true },
  });

  if (!profile || profile.user.isBanned || !profile.contactFormEnabled) {
    return res.status(404).send('Not found');
  }

  const userIsPremium = await isPremium(profile.user.id);
  if (!userIsPremium) {
    return res.status(404).send('Not found');
  }

  const { sender_name, sender_email, message } = req.body;
  if (!sender_name || !sender_email || !message) {
    req.flash('error', 'All fields are required.');
    return res.redirect(`/${username}`);
  }

  await prisma.contactMessage.create({
    data: {
      userId: profile.user.id,
      senderName: sender_name,
      senderEmail: sender_email,
      message,
    },
  });

  req.flash('success', 'Message sent successfully!');
  res.redirect(`/${username}`);
};
