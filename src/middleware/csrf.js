const crypto = require('crypto');

function generateToken() {
  return crypto.randomBytes(32).toString('hex');
}

function csrfProtection(req, res, next) {
  if (!req.session.csrfToken) {
    req.session.csrfToken = generateToken();
  }

  res.locals.csrfToken = req.session.csrfToken;

  if (['GET', 'HEAD', 'OPTIONS'].includes(req.method)) {
    return next();
  }

  const token = req.body._csrf || req.headers['x-csrf-token'];
  if (!token || token !== req.session.csrfToken) {
    req.flash('error', 'Invalid or missing CSRF token. Please try again.');
    return res.redirect(req.get('Referer') || '/');
  }

  req.session.csrfToken = generateToken();
  next();
}

module.exports = csrfProtection;
