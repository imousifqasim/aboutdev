const { getSetting } = require('../helpers');

async function loadSiteSettings(req, res, next) {
  try {
    res.locals.siteSettings = {
      siteName: await getSetting('site_name', 'DropLaunch'),
      siteDescription: await getSetting('site_description', 'Create your personal portfolio page'),
      siteKeywords: await getSetting('site_keywords', 'portfolio, links, bio'),
      siteOgTitle: await getSetting('site_og_title', ''),
      siteOgDescription: await getSetting('site_og_description', ''),
      siteOgImage: await getSetting('site_og_image', ''),
      customDomainTarget: await getSetting('custom_domain_target', ''),
      siteTwitterHandle: await getSetting('site_twitter_handle', ''),
      siteCanonicalUrl: await getSetting('site_canonical_url', ''),
      contactEmail: await getSetting('contact_email', 'admin@droplaunch.dev'),
      footerText: await getSetting('footer_text', ''),
    };
  } catch (error) {
    return next(error);
  }
  next();
}

module.exports = loadSiteSettings;
