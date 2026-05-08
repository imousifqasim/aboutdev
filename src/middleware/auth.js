function isAuthenticated(req, res, next) {
  if (req.session && req.session.userId) {
    return next();
  }
  req.flash('error', 'Please log in to continue.');
  res.redirect('/login');
}

function isGuest(req, res, next) {
  if (req.session && req.session.userId) {
    return res.redirect('/dashboard');
  }
  next();
}

function isAdmin(req, res, next) {
  if (req.user && req.user.role === 'admin') {
    return next();
  }
  res.status(403).send('Forbidden');
}

function isNotBanned(req, res, next) {
  if (req.user && req.user.isBanned) {
    req.session.destroy();
    return res.redirect('/login');
  }
  next();
}

module.exports = { isAuthenticated, isGuest, isAdmin, isNotBanned };
