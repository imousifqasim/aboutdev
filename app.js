require('dotenv').config();
const express = require('express');
const session = require('express-session');
const flash = require('connect-flash');
const path = require('path');
const cookieParser = require('cookie-parser');
const loadUser = require('./src/middleware/loadUser');
const csrfProtection = require('./src/middleware/csrf');
const routes = require('./src/routes');
const { formatDate, timeAgo, formatCurrency } = require('./src/helpers');

const app = express();

// View engine
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Middleware
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

// Session
app.use(session({
  secret: process.env.SESSION_SECRET || 'droplaunch-secret',
  resave: false,
  saveUninitialized: false,
  cookie: {
    maxAge: 7 * 24 * 60 * 60 * 1000,
    httpOnly: true,
  },
}));

// Flash messages
app.use(flash());

// Load user from session
app.use(loadUser);

// CSRF protection
app.use(csrfProtection);

// Make flash messages and helpers available to all views
app.use((req, res, next) => {
  res.locals.success = req.flash('success');
  res.locals.error = req.flash('error');
  res.locals.formatDate = formatDate;
  res.locals.timeAgo = timeAgo;
  res.locals.formatCurrency = formatCurrency;
  res.locals.appName = process.env.APP_NAME || 'DropLaunch';
  res.locals.appUrl = process.env.APP_URL || 'http://localhost:8000';
  next();
});

// Routes
app.use('/', routes);

// 404 handler
app.use((req, res) => {
  res.status(404).render('layouts/error', { title: '404', message: 'Page not found.' });
});

// Error handler
app.use((err, req, res, next) => {
  console.error('Error:', err.message);
  console.error(err.stack);
  res.status(500).render('layouts/error', { title: '500', message: process.env.NODE_ENV === 'production' ? 'Something went wrong.' : (err.message || 'Something went wrong.') });
});

const PORT = process.env.PORT || 8000;
app.listen(PORT, '0.0.0.0', () => {
  console.log(`DropLaunch running at http://localhost:${PORT}`);
});
