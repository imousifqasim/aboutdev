const { getSetting, setSetting } = require('../../helpers');

exports.index = async (req, res) => {
  const settings = {
    site_name: await getSetting('site_name', 'DropLaunch'),
    site_description: await getSetting('site_description', 'Create your personal portfolio page'),
    site_keywords: await getSetting('site_keywords', 'portfolio, links, bio'),
    site_og_title: await getSetting('site_og_title', ''),
    site_og_description: await getSetting('site_og_description', ''),
    custom_domain_target: await getSetting('custom_domain_target', ''),
    site_twitter_handle: await getSetting('site_twitter_handle', ''),
    site_canonical_url: await getSetting('site_canonical_url', ''),
    premium_price_usd: await getSetting('premium_price_usd', '9.99'),
    premium_price_pkr: await getSetting('premium_price_pkr', '2999'),
    contact_email: await getSetting('contact_email', 'admin@droplaunch.dev'),
    footer_text: await getSetting('footer_text', ''),
  };

  res.render('admin/settings/index', { title: 'Site Settings', settings });
};

exports.update = async (req, res) => {
  const keys = ['site_name', 'site_description', 'site_keywords', 'site_og_title', 'site_og_description', 'site_og_image', 'custom_domain_target', 'site_twitter_handle', 'site_canonical_url', 'premium_price_usd', 'premium_price_pkr', 'contact_email', 'footer_text'];
  for (const key of keys) {
    if (req.body[key] !== undefined) {
      await setSetting(key, req.body[key]);
    }
  }
  req.flash('success', 'Settings updated successfully!');
  res.redirect('/admin/settings');
};
