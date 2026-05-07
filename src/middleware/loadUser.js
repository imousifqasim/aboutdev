const prisma = require('../prisma');
const { isPremium } = require('../helpers');

async function loadUser(req, res, next) {
  if (req.session && req.session.userId) {
    const user = await prisma.user.findUnique({
      where: { id: req.session.userId },
      include: { profile: true },
    });
    if (user) {
      user.isPremiumUser = await isPremium(user.id);
      req.user = user;
      res.locals.user = user;
    } else {
      req.session.userId = null;
    }
  }
  res.locals.user = res.locals.user || null;
  next();
}

module.exports = loadUser;
