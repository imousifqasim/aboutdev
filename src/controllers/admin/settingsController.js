const { getSetting, setSetting } = require('../../helpers');

exports.index = async (req, res) => {
  const settings = {
    site_name: await getSetting('site_name', 'DropLaunch'),
    site_description: await getSetting('site_description', 'Create your personal portfolio page'),
    site_keywords: await getSetting('site_keywords', 'portfolio, links, bio'),
    premium_price: await getSetting('premium_price', '9.99'),
    premium_currency: await getSetting('premium_currency', 'USD'),
    contact_email: await getSetting('contact_email', 'admin@droplaunch.dev'),
    footer_text: await getSetting('footer_text', ''),
  };

  res.render('admin/settings/index', { title: 'Site Settings', settings });
};

exports.update = async (req, res) => {
  const keys = ['site_name', 'site_description', 'site_keywords', 'premium_price', 'premium_currency', 'contact_email', 'footer_text'];
  for (const key of keys) {
    if (req.body[key] !== undefined) {
      await setSetting(key, req.body[key]);
    }
  }
  req.flash('success', 'Settings updated successfully!');
  res.redirect('/admin/settings');
};
