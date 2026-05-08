const prisma = require('../../prisma');
const { isPremium, RESERVED_USERNAMES } = require('../../helpers');
const path = require('path');
const fs = require('fs');

function cleanupFile(req) {
  if (req.file && req.file.path) {
    fs.unlink(req.file.path, () => {});
  }
}

function cleanupFiles(req) {
  if (req.files && req.files.length > 0) {
    req.files.forEach(f => fs.unlink(f.path, () => {}));
  }
}

exports.edit = async (req, res) => {
  const user = await prisma.user.findUnique({ where: { id: req.user.id }, include: { profile: true } });
  user.isPremiumUser = await isPremium(user.id);
  const unreadCount = await prisma.contactMessage.count({ where: { userId: user.id, isRead: false } });
  res.render('user/profile/edit', { title: 'Edit Profile', user, unreadCount });
};

exports.update = async (req, res) => {
  const user = req.user;
  const profile = user.profile;
  const { name, username, bio, location, company, website, meta_title, meta_description } = req.body;

  if (!name || !username) {
    cleanupFile(req);
    req.flash('error', 'Name and username are required.');
    return res.redirect('/dashboard/profile');
  }

  if (RESERVED_USERNAMES.includes(username.toLowerCase())) {
    cleanupFile(req);
    req.flash('error', 'This username is reserved.');
    return res.redirect('/dashboard/profile');
  }

  const existing = await prisma.profile.findFirst({
    where: { username: username.toLowerCase(), NOT: { id: profile.id } },
  });
  if (existing) {
    cleanupFile(req);
    req.flash('error', 'Username is already taken.');
    return res.redirect('/dashboard/profile');
  }

  await prisma.user.update({ where: { id: user.id }, data: { name } });

  const data = {
    username: username.toLowerCase(),
    bio: bio || null,
    location: location || null,
    company: company || null,
    website: website || null,
    metaTitle: meta_title || null,
    metaDescription: meta_description || null,
  };

  if (req.file) {
    if (profile.image) {
      const oldPath = path.join(__dirname, '../../..', 'public/uploads', profile.image);
      if (fs.existsSync(oldPath)) fs.unlinkSync(oldPath);
    }
    data.image = 'profiles/' + req.file.filename;
  }

  const socialLinks = {};
  const platforms = ['twitter', 'instagram', 'facebook', 'linkedin', 'github', 'youtube', 'tiktok'];
  platforms.forEach(p => {
    const val = req.body[`social_${p}`];
    if (val && val.startsWith('http')) socialLinks[p] = val;
  });
  data.socialLinks = socialLinks;

  await prisma.profile.update({ where: { id: profile.id }, data });
  req.flash('success', 'Profile updated successfully!');
  res.redirect('/dashboard/profile');
};

exports.updateTheme = async (req, res) => {
  const { theme } = req.body;
  const validThemes = ['default', 'dark', 'gradient', 'minimal', 'bold', 'ocean', 'sunset', 'forest'];
  if (!validThemes.includes(theme)) {
    req.flash('error', 'Invalid theme.');
    return res.redirect('/dashboard/profile');
  }

  const premiumThemes = ['gradient', 'bold', 'ocean', 'sunset', 'forest'];
  if (premiumThemes.includes(theme) && !req.user.isPremiumUser) {
    req.flash('error', 'This theme requires a premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  await prisma.profile.update({ where: { id: req.user.profile.id }, data: { theme } });
  req.flash('success', 'Theme updated successfully!');
  res.redirect('/dashboard/profile');
};

exports.updateGallery = async (req, res) => {
  if (!req.user.isPremiumUser) {
    cleanupFiles(req);
    req.flash('error', 'Gallery feature requires premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  const profile = req.user.profile;
  const gallery = profile.gallery || [];

  if (req.files && req.files.length > 0) {
    req.files.forEach(f => gallery.push('gallery/' + f.filename));
  }

  await prisma.profile.update({ where: { id: profile.id }, data: { gallery } });
  req.flash('success', 'Gallery updated successfully!');
  res.redirect('/dashboard/profile');
};

exports.removeGalleryImage = async (req, res) => {
  const profile = req.user.profile;
  const gallery = profile.gallery || [];
  const index = parseInt(req.params.index);

  if (gallery[index]) {
    const filePath = path.join(__dirname, '../../..', 'public/uploads', gallery[index]);
    if (fs.existsSync(filePath)) fs.unlinkSync(filePath);
    gallery.splice(index, 1);
    await prisma.profile.update({ where: { id: profile.id }, data: { gallery } });
  }

  req.flash('success', 'Image removed.');
  res.redirect('/dashboard/profile');
};

exports.updateVideos = async (req, res) => {
  if (!req.user.isPremiumUser) {
    req.flash('error', 'Video feature requires premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  const { video_url, video_title } = req.body;
  if (!video_url) {
    req.flash('error', 'Video URL is required.');
    return res.redirect('/dashboard/profile');
  }

  const profile = req.user.profile;
  const videos = profile.videos || [];
  videos.push({ url: video_url, title: video_title || '' });

  await prisma.profile.update({ where: { id: profile.id }, data: { videos } });
  req.flash('success', 'Video added successfully!');
  res.redirect('/dashboard/profile');
};

exports.removeVideo = async (req, res) => {
  const profile = req.user.profile;
  const videos = profile.videos || [];
  const index = parseInt(req.params.index);

  if (videos[index]) {
    videos.splice(index, 1);
    await prisma.profile.update({ where: { id: profile.id }, data: { videos } });
  }

  req.flash('success', 'Video removed.');
  res.redirect('/dashboard/profile');
};

exports.updateSpotlight = async (req, res) => {
  const { spotlight_label, spotlight_url, spotlight_icon } = req.body;
  await prisma.profile.update({
    where: { id: req.user.profile.id },
    data: {
      spotlightLabel: spotlight_label || null,
      spotlightUrl: spotlight_url || null,
      spotlightIcon: spotlight_icon || null,
    },
  });
  req.flash('success', 'Spotlight button updated!');
  res.redirect('/dashboard/profile');
};

exports.updateTestimonials = async (req, res) => {
  if (!req.user.isPremiumUser) {
    req.flash('error', 'Testimonials require premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  const { testimonial_name, testimonial_role, testimonial_text } = req.body;
  if (!testimonial_name || !testimonial_text) {
    req.flash('error', 'Name and text are required.');
    return res.redirect('/dashboard/profile');
  }

  const profile = req.user.profile;
  const testimonials = profile.testimonials || [];
  testimonials.push({ name: testimonial_name, role: testimonial_role || '', text: testimonial_text });

  await prisma.profile.update({ where: { id: profile.id }, data: { testimonials } });
  req.flash('success', 'Testimonial added!');
  res.redirect('/dashboard/profile');
};

exports.removeTestimonial = async (req, res) => {
  const profile = req.user.profile;
  const testimonials = profile.testimonials || [];
  const index = parseInt(req.params.index);

  if (testimonials[index]) {
    testimonials.splice(index, 1);
    await prisma.profile.update({ where: { id: profile.id }, data: { testimonials } });
  }

  req.flash('success', 'Testimonial removed.');
  res.redirect('/dashboard/profile');
};

exports.updateResume = async (req, res) => {
  if (!req.user.isPremiumUser) {
    req.flash('error', 'Resume feature requires premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  const { resume_type, resume_title, resume_subtitle, resume_period, resume_description } = req.body;
  if (!resume_type || !resume_title) {
    req.flash('error', 'Type and title are required.');
    return res.redirect('/dashboard/profile');
  }

  const profile = req.user.profile;
  const resume = profile.resume || { education: [], experience: [], skills: [] };
  const key = resume_type === 'skill' ? 'skills' : resume_type;
  if (!resume[key]) resume[key] = [];

  const entry = { title: resume_title };
  if (resume_type !== 'skill') {
    entry.subtitle = resume_subtitle || '';
    entry.period = resume_period || '';
    entry.description = resume_description || '';
  }

  resume[key].push(entry);
  await prisma.profile.update({ where: { id: profile.id }, data: { resume } });
  req.flash('success', `${resume_type.charAt(0).toUpperCase() + resume_type.slice(1)} added!`);
  res.redirect('/dashboard/profile');
};

exports.removeResumeItem = async (req, res) => {
  const profile = req.user.profile;
  const resume = profile.resume || { education: [], experience: [], skills: [] };
  const { type, index: idx } = req.params;
  const key = type === 'skill' ? 'skills' : type;
  const index = parseInt(idx);

  if (resume[key] && resume[key][index]) {
    resume[key].splice(index, 1);
    await prisma.profile.update({ where: { id: profile.id }, data: { resume } });
  }

  req.flash('success', 'Item removed.');
  res.redirect('/dashboard/profile');
};

exports.toggleContactForm = async (req, res) => {
  if (!req.user.isPremiumUser) {
    req.flash('error', 'Contact form requires premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  const profile = req.user.profile;
  await prisma.profile.update({
    where: { id: profile.id },
    data: { contactFormEnabled: !profile.contactFormEnabled },
  });
  const status = !profile.contactFormEnabled ? 'enabled' : 'disabled';
  req.flash('success', `Contact form ${status}!`);
  res.redirect('/dashboard/profile');
};

exports.updateBackground = async (req, res) => {
  if (!req.user.isPremiumUser) {
    cleanupFile(req);
    req.flash('error', 'Background image requires premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  if (!req.file) {
    req.flash('error', 'Please select an image.');
    return res.redirect('/dashboard/profile');
  }

  const profile = req.user.profile;
  if (profile.backgroundImage) {
    const oldPath = path.join(__dirname, '../../..', 'public/uploads', profile.backgroundImage);
    if (fs.existsSync(oldPath)) fs.unlinkSync(oldPath);
  }

  await prisma.profile.update({
    where: { id: profile.id },
    data: { backgroundImage: 'backgrounds/' + req.file.filename },
  });
  req.flash('success', 'Background image updated!');
  res.redirect('/dashboard/profile');
};

exports.removeBackground = async (req, res) => {
  const profile = req.user.profile;
  if (profile.backgroundImage) {
    const oldPath = path.join(__dirname, '../../..', 'public/uploads', profile.backgroundImage);
    if (fs.existsSync(oldPath)) fs.unlinkSync(oldPath);
    await prisma.profile.update({ where: { id: profile.id }, data: { backgroundImage: null } });
  }
  req.flash('success', 'Background image removed.');
  res.redirect('/dashboard/profile');
};

exports.emailSignature = async (req, res) => {
  const user = await prisma.user.findUnique({ where: { id: req.user.id }, include: { profile: true } });
  user.isPremiumUser = await isPremium(user.id);
  const unreadCount = await prisma.contactMessage.count({ where: { userId: user.id, isRead: false } });
  res.render('user/email-signature', { title: 'Email Signature', user, unreadCount });
};

exports.messages = async (req, res) => {
  const user = req.user;
  user.isPremiumUser = await isPremium(user.id);
  const messages = await prisma.contactMessage.findMany({
    where: { userId: user.id },
    orderBy: { createdAt: 'desc' },
  });
  const unreadCount = messages.filter(m => !m.isRead).length;
  res.render('user/messages/index', { title: 'Messages', user, messages, unreadCount });
};

exports.markMessageRead = async (req, res) => {
  const message = await prisma.contactMessage.findUnique({ where: { id: parseInt(req.params.id) } });
  if (!message || message.userId !== req.user.id) {
    return res.status(403).send('Forbidden');
  }
  await prisma.contactMessage.update({ where: { id: message.id }, data: { isRead: true } });
  req.flash('success', 'Message marked as read.');
  res.redirect('/dashboard/messages');
};

exports.deleteMessage = async (req, res) => {
  const message = await prisma.contactMessage.findUnique({ where: { id: parseInt(req.params.id) } });
  if (!message || message.userId !== req.user.id) {
    return res.status(403).send('Forbidden');
  }
  await prisma.contactMessage.delete({ where: { id: message.id } });
  req.flash('success', 'Message deleted.');
  res.redirect('/dashboard/messages');
};
