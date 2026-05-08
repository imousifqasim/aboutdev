const prisma = require('../../prisma');
const { isPremium } = require('../../helpers');

exports.index = async (req, res) => {
  const user = req.user;
  user.isPremiumUser = await isPremium(user.id);
  const links = await prisma.link.findMany({ where: { userId: user.id }, orderBy: { position: 'asc' } });
  const unreadCount = await prisma.contactMessage.count({ where: { userId: user.id, isRead: false } });
  res.render('user/links/index', { title: 'My Links', user, links, unreadCount });
};

exports.store = async (req, res) => {
  const user = req.user;
  const linkCount = await prisma.link.count({ where: { userId: user.id } });

  if (!user.isPremiumUser && linkCount >= 5) {
    req.flash('error', 'Free plan allows up to 5 links. Upgrade to Premium for unlimited links.');
    return res.redirect('/dashboard/links');
  }

  const { title, url, icon } = req.body;
  if (!title || !url) {
    req.flash('error', 'Title and URL are required.');
    return res.redirect('/dashboard/links');
  }

  try {
    const parsedUrl = new URL(url);
    if (!['http:', 'https:'].includes(parsedUrl.protocol)) {
      req.flash('error', 'URL must start with http:// or https://');
      return res.redirect('/dashboard/links');
    }
  } catch {
    req.flash('error', 'Please enter a valid URL.');
    return res.redirect('/dashboard/links');
  }

  const maxPos = await prisma.link.aggregate({ where: { userId: user.id }, _max: { position: true } });
  await prisma.link.create({
    data: {
      userId: user.id,
      title,
      url,
      icon: icon || null,
      position: (maxPos._max.position || 0) + 1,
    },
  });

  req.flash('success', 'Link added successfully!');
  res.redirect('/dashboard/links');
};

exports.update = async (req, res) => {
  const link = await prisma.link.findUnique({ where: { id: parseInt(req.params.id) } });
  if (!link || link.userId !== req.user.id) return res.status(403).send('Forbidden');

  const { title, url, icon } = req.body;

  if (url) {
    try {
      const parsedUrl = new URL(url);
      if (!['http:', 'https:'].includes(parsedUrl.protocol)) {
        req.flash('error', 'URL must start with http:// or https://');
        return res.redirect('/dashboard/links');
      }
    } catch {
      req.flash('error', 'Please enter a valid URL.');
      return res.redirect('/dashboard/links');
    }
  }

  await prisma.link.update({
    where: { id: link.id },
    data: { title, url, icon: icon || null },
  });

  req.flash('success', 'Link updated successfully!');
  res.redirect('/dashboard/links');
};

exports.destroy = async (req, res) => {
  const link = await prisma.link.findUnique({ where: { id: parseInt(req.params.id) } });
  if (!link || link.userId !== req.user.id) return res.status(403).send('Forbidden');

  await prisma.link.delete({ where: { id: link.id } });
  req.flash('success', 'Link deleted successfully!');
  res.redirect('/dashboard/links');
};

exports.toggleActive = async (req, res) => {
  const link = await prisma.link.findUnique({ where: { id: parseInt(req.params.id) } });
  if (!link || link.userId !== req.user.id) return res.status(403).send('Forbidden');

  await prisma.link.update({ where: { id: link.id }, data: { isActive: !link.isActive } });
  req.flash('success', 'Link status updated!');
  res.redirect('/dashboard/links');
};

exports.reorder = async (req, res) => {
  const { order } = req.body;
  if (!order || !Array.isArray(order)) {
    return res.status(400).json({ error: 'Invalid order' });
  }

  for (let i = 0; i < order.length; i++) {
    await prisma.link.updateMany({
      where: { id: parseInt(order[i]), userId: req.user.id },
      data: { position: i },
    });
  }

  req.flash('success', 'Links reordered!');
  res.redirect('/dashboard/links');
};
