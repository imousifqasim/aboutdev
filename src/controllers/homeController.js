const prisma = require('../prisma');
const { getSetting, isPremium } = require('../helpers');

async function renderPublicProfileFromCustomDomain(req, res, profile) {
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
}

exports.index = async (req, res) => {
  const appUrl = process.env.APP_URL || `http://${req.headers.host}`;
  let mainHost;
  try {
    mainHost = new URL(appUrl).hostname;
  } catch (err) {
    mainHost = req.hostname;
  }

  if (req.hostname && req.hostname !== mainHost) {
    const profile = await prisma.profile.findUnique({
      where: { customDomain: req.hostname },
      include: { user: true },
    });
    if (profile) {
      return renderPublicProfileFromCustomDomain(req, res, profile);
    }
  }

  const premiumPriceUSD = await getSetting('premium_price_usd', '9.99');
  const premiumPricePKR = await getSetting('premium_price_pkr', '2999');

  res.render('home', {
    title: 'DropLaunch - Create Your Personal Portfolio Page',
    premiumPriceUSD,
    premiumPricePKR,
  });
};
