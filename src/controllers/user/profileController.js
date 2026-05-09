const prisma = require('../../prisma');
const { isPremium, RESERVED_USERNAMES } = require('../../helpers');
const { sendMail } = require('../../mail');
const path = require('path');
const fs = require('fs');
const bcrypt = require('bcryptjs');

const industryThemes = [
  'developers', 'web-designers', 'graphic-designers', 'ui-ux-designers', 'video-editors',
  'content-creators', 'freelancers', 'digital-marketers', 'social-media-managers', 'photographers',
  'videographers', 'animators', 'bloggers', 'writers', 'copywriters', 'seo-experts',
  'app-developers', 'software-engineers', 'students', 'teachers', 'artists', 'musicians',
  'streamers', 'influencers', 'entrepreneurs', 'business-owners', 'agencies', 'consultants',
  'architects', 'fashion-designers', 'resume-builders', 'personal-brands', 'gamers', 'podcasters',
  'public-speakers', 'virtual-assistants', 'e-commerce-sellers', 'trainers-coaches', 'crypto-traders',
  'ai-creators', 'cyber-security-experts', 'wordpress-developers', 'shopify-experts', 'saas-founders', 'startup-teams'
];
const baseThemes = ['default', 'dark', 'minimal', 'gradient', 'bold', 'ocean', 'sunset', 'forest'];
const validThemes = [...baseThemes, ...industryThemes];
const premiumThemes = ['gradient', 'bold', 'ocean', 'sunset', 'forest', ...industryThemes];

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
  const { name, username, bio, location, company, website, custom_domain, meta_title, meta_description, og_title, og_description, focus_keyword } = req.body;

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

  const customDomain = custom_domain ? custom_domain.trim().toLowerCase() : null;
  if (customDomain && !req.user.isPremiumUser) {
    cleanupFile(req);
    req.flash('error', 'Custom domain requires a premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  if (customDomain && !/^[a-z0-9.-]+$/.test(customDomain)) {
    cleanupFile(req);
    req.flash('error', 'Custom domain contains invalid characters.');
    return res.redirect('/dashboard/profile');
  }

  if (customDomain) {
    const existingDomain = await prisma.profile.findFirst({
      where: { customDomain, NOT: { id: profile.id } },
    });
    if (existingDomain) {
      cleanupFile(req);
      req.flash('error', 'This custom domain is already in use.');
      return res.redirect('/dashboard/profile');
    }
  }

  const data = {
    username: username.toLowerCase(),
    bio: bio || null,
    location: location || null,
    company: company || null,
    website: website || null,
    customDomain: customDomain || null,
    metaTitle: meta_title || null,
    metaDescription: meta_description || null,
    ogTitle: og_title || null,
    ogDescription: og_description || null,
    focusKeyword: focus_keyword || null,
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

exports.updatePassword = async (req, res) => {
  const { current_password, new_password, confirm_password } = req.body;

  if (!current_password || !new_password || !confirm_password) {
    req.flash('error', 'All password fields are required.');
    return res.redirect('/dashboard/profile');
  }

  if (new_password.length < 6) {
    req.flash('error', 'New password must be at least 6 characters long.');
    return res.redirect('/dashboard/profile');
  }

  if (new_password !== confirm_password) {
    req.flash('error', 'New password and confirmation do not match.');
    return res.redirect('/dashboard/profile');
  }

  const user = await prisma.user.findUnique({ where: { id: req.user.id } });

  if (!(await bcrypt.compare(current_password, user.password))) {
    req.flash('error', 'Current password is incorrect.');
    return res.redirect('/dashboard/profile');
  }

  const hashedPassword = await bcrypt.hash(new_password, 10);
  await prisma.user.update({
    where: { id: req.user.id },
    data: { password: hashedPassword },
  });

  req.flash('success', 'Password updated successfully!');
  res.redirect('/dashboard/profile');
};

exports.updateTheme = async (req, res) => {
  const theme = req.body.industry_theme || req.body.theme;
  if (!validThemes.includes(theme)) {
    req.flash('error', 'Invalid theme.');
    return res.redirect('/dashboard/profile');
  }

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

exports.updatePages = async (req, res) => {
  if (!req.user.isPremiumUser) {
    req.flash('error', 'Pages feature requires premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  const { page_title, page_content, page_slug } = req.body;
  if (!page_title || !page_content) {
    req.flash('error', 'Page title and content are required.');
    return res.redirect('/dashboard/profile');
  }

  const profile = req.user.profile;
  const pages = profile.pages || [];
  const slug = page_slug || page_title.toLowerCase().replace(/[^a-z0-9]/g, '-').replace(/-+/g, '-');

  pages.push({
    id: Date.now().toString(),
    title: page_title,
    content: page_content,
    slug: slug,
    createdAt: new Date().toISOString()
  });

  await prisma.profile.update({ where: { id: profile.id }, data: { pages } });
  req.flash('success', 'Page added successfully!');
  res.redirect('/dashboard/profile');
};

exports.removePage = async (req, res) => {
  const profile = req.user.profile;
  const pages = profile.pages || [];
  const pageId = req.params.id;

  const updatedPages = pages.filter(p => p.id !== pageId);
  await prisma.profile.update({ where: { id: profile.id }, data: { pages: updatedPages } });

  req.flash('success', 'Page removed.');
  res.redirect('/dashboard/profile');
};

exports.updateIntegrations = async (req, res) => {
  if (!req.user.isPremiumUser) {
    req.flash('error', 'Integrations require premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  const integrations = {};
  const integrationTypes = ['google_analytics', 'facebook_pixel', 'twitter_pixel', 'custom_css', 'custom_js'];

  integrationTypes.forEach(type => {
    const value = req.body[`integration_${type}`];
    if (value && value.trim()) {
      integrations[type] = value.trim();
    }
  });

  await prisma.profile.update({
    where: { id: req.user.profile.id },
    data: { integrations },
  });

  req.flash('success', 'Integrations updated successfully!');
  res.redirect('/dashboard/profile');
};

exports.updateFooter = async (req, res) => {
  if (!req.user.isPremiumUser) {
    req.flash('error', 'Footer customization requires premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  const { footer_text, footer_links, footer_show_branding } = req.body;

  const footer = {
    text: footer_text || '',
    links: footer_links ? JSON.parse(footer_links) : [],
    showBranding: footer_show_branding === 'on'
  };

  await prisma.profile.update({
    where: { id: req.user.profile.id },
    data: { footer },
  });

  req.flash('success', 'Footer updated successfully!');
  res.redirect('/dashboard/profile');
};

exports.updateDefaultTheme = async (req, res) => {
  if (!req.user.isPremiumUser) {
    req.flash('error', 'Default theme settings require premium subscription.');
    return res.redirect('/dashboard/profile');
  }

  const { default_theme } = req.body;
  if (!validThemes.includes(default_theme)) {
    req.flash('error', 'Invalid theme selected.');
    return res.redirect('/dashboard/profile');
  }

  await prisma.profile.update({
    where: { id: req.user.profile.id },
    data: { defaultTheme: default_theme },
  });

  req.flash('success', 'Default theme updated successfully!');
  res.redirect('/dashboard/profile');
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

exports.replyMessage = async (req, res) => {
  const message = await prisma.contactMessage.findUnique({ where: { id: parseInt(req.params.id) } });
  if (!message || message.userId !== req.user.id) {
    return res.status(403).send('Forbidden');
  }

  const { reply_message } = req.body;
  if (!reply_message) {
    req.flash('error', 'Reply message cannot be blank.');
    return res.redirect('/dashboard/messages');
  }

  const emailSubject = `Reply from ${req.user.name}`;
  const emailHtml = `
    <p>${req.user.name} has replied to your message.</p>
    <p><strong>Reply:</strong></p>
    <p>${reply_message.replace(/\n/g, '<br>')}</p>
    <p>You can contact them at <strong>${req.user.email}</strong> if needed.</p>
  `;

  try {
    await sendMail({
      to: message.senderEmail,
      subject: emailSubject,
      html: emailHtml,
      text: `Reply from ${req.user.name}: ${reply_message}`,
    });
    await prisma.contactMessage.update({ where: { id: message.id }, data: { isRead: true } });
    req.flash('success', 'Reply sent successfully!');
  } catch (error) {
    console.error('Reply email error:', error.message || error);
    req.flash('error', 'Reply could not be sent. Please try again later.');
  }

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
