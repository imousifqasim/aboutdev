const bcrypt = require('bcryptjs');
const prisma = require('../../prisma');

exports.showLoginForm = (req, res) => {
  res.render('auth/login', { title: 'Login' });
};

exports.login = async (req, res) => {
  const { email, password, remember } = req.body;
  if (!email || !password) {
    req.flash('error', 'Email and password are required.');
    return res.redirect('/login');
  }

  const user = await prisma.user.findUnique({ where: { email } });
  if (!user || !(await bcrypt.compare(password, user.password))) {
    req.flash('error', 'The provided credentials do not match our records.');
    return res.redirect('/login');
  }

  if (user.isBanned) {
    req.flash('error', 'Your account has been banned.');
    return res.redirect('/login');
  }

  req.session.userId = user.id;
  req.session.save(() => {
    if (user.role === 'admin') {
      return res.redirect('/admin');
    }
    res.redirect('/dashboard');
  });
};

exports.logout = (req, res) => {
  req.session.destroy(() => {
    res.redirect('/');
  });
};
