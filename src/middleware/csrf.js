const crypto = require('crypto');

const MAX_TOKENS = 5;

function generateToken() {
  return crypto.randomBytes(32).toString('hex');
}

function isMultipart(req) {
  const ct = req.headers['content-type'] || '';
  return ct.startsWith('multipart/form-data');
}

function validateToken(req, res) {
  const token = (req.body && req.body._csrf) || req.headers['x-csrf-token'];
  const tokenIndex = token ? req.session.csrfTokens.indexOf(token) : -1;
  if (!token || tokenIndex === -1) {
    req.flash('error', 'Invalid or missing CSRF token. Please try again.');
    res.redirect('/');
    return false;
  }

  req.session.csrfTokens.splice(tokenIndex, 1);
  const newToken = generateToken();
  req.session.csrfTokens.push(newToken);
  if (req.session.csrfTokens.length > MAX_TOKENS) {
    req.session.csrfTokens = req.session.csrfTokens.slice(-MAX_TOKENS);
  }
  return true;
}

function csrfProtection(req, res, next) {
  if (!req.session.csrfTokens || req.session.csrfTokens.length === 0) {
    req.session.csrfTokens = [generateToken()];
  }

  res.locals.csrfToken = req.session.csrfTokens[req.session.csrfTokens.length - 1];

  if (['GET', 'HEAD', 'OPTIONS'].includes(req.method)) {
    return next();
  }

  if (isMultipart(req)) {
    return next();
  }

  if (validateToken(req, res)) {
    next();
  }
}

function csrfValidateMultipart(req, res, next) {
  if (validateToken(req, res)) {
    next();
  }
}

module.exports = csrfProtection;
module.exports.csrfValidateMultipart = csrfValidateMultipart;
