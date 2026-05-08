const express = require('express');
const multer = require('multer');
const path = require('path');
const { v4: uuidv4 } = require('uuid');
const { isAuthenticated, isGuest, isAdmin, isNotBanned } = require('../middleware/auth');
const { csrfValidateMultipart } = require('../middleware/csrf');

const homeController = require('../controllers/homeController');
const loginController = require('../controllers/auth/loginController');
const registerController = require('../controllers/auth/registerController');
const dashboardController = require('../controllers/user/dashboardController');
const profileController = require('../controllers/user/profileController');
const linkController = require('../controllers/user/linkController');
const paymentController = require('../controllers/user/paymentController');
const adminDashboardController = require('../controllers/admin/dashboardController');
const adminUserController = require('../controllers/admin/userController');
const adminPaymentController = require('../controllers/admin/paymentController');
const adminSubscriptionController = require('../controllers/admin/subscriptionController');
const adminSettingsController = require('../controllers/admin/settingsController');
const publicProfileController = require('../controllers/publicProfileController');

const router = express.Router();

const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    let folder = 'profiles';
    if (req.uploadType === 'gallery') folder = 'gallery';
    else if (req.uploadType === 'background') folder = 'backgrounds';
    else if (req.uploadType === 'screenshot') folder = 'payment-screenshots';
    cb(null, path.join(__dirname, '../../public/uploads', folder));
  },
  filename: (req, file, cb) => {
    const ext = path.extname(file.originalname);
    cb(null, uuidv4() + ext);
  },
});

const upload = multer({
  storage,
  limits: { fileSize: 5 * 1024 * 1024 },
  fileFilter: (req, file, cb) => {
    const allowedTypes = /jpeg|jpg|png|gif|webp/;
    const ext = allowedTypes.test(path.extname(file.originalname).toLowerCase());
    const mime = allowedTypes.test(file.mimetype);
    if (ext && mime) return cb(null, true);
    cb(new Error('Only image files are allowed.'));
  },
});

function setUploadType(type) {
  return (req, res, next) => { req.uploadType = type; next(); };
}

// Home
router.get('/', homeController.index);

// Auth Routes (Guest only)
router.get('/login', isGuest, loginController.showLoginForm);
router.post('/login', isGuest, loginController.login);
router.get('/register', isGuest, registerController.showRegistrationForm);
router.post('/register', isGuest, registerController.register);
router.get('/forgot-password', isGuest, loginController.showForgotPasswordForm);
router.post('/forgot-password', isGuest, loginController.forgotPassword);
router.get('/reset-password/:token', isGuest, loginController.showResetPasswordForm);
router.post('/reset-password/:token', isGuest, loginController.resetPassword);
router.post('/logout', loginController.logout);

// User Dashboard Routes
const dashboardRouter = express.Router();
dashboardRouter.use(isAuthenticated, isNotBanned);

dashboardRouter.get('/', dashboardController.index);

// Profile
dashboardRouter.get('/profile', profileController.edit);
dashboardRouter.post('/profile', setUploadType('profiles'), upload.single('image'), csrfValidateMultipart, profileController.update);
dashboardRouter.post('/profile/password', profileController.updatePassword);
dashboardRouter.post('/profile/theme', profileController.updateTheme);
dashboardRouter.post('/profile/gallery', setUploadType('gallery'), upload.array('gallery_images', 10), csrfValidateMultipart, profileController.updateGallery);
dashboardRouter.post('/profile/gallery/:index/delete', profileController.removeGalleryImage);
dashboardRouter.post('/profile/videos', profileController.updateVideos);
dashboardRouter.post('/profile/videos/:index/delete', profileController.removeVideo);
dashboardRouter.post('/profile/spotlight', profileController.updateSpotlight);
dashboardRouter.post('/profile/testimonials', profileController.updateTestimonials);
dashboardRouter.post('/profile/testimonials/:index/delete', profileController.removeTestimonial);
dashboardRouter.post('/profile/resume', profileController.updateResume);
dashboardRouter.post('/profile/resume/:type/:index/delete', profileController.removeResumeItem);
dashboardRouter.post('/profile/contact-form', profileController.toggleContactForm);
dashboardRouter.post('/profile/background', setUploadType('background'), upload.single('background_image'), csrfValidateMultipart, profileController.updateBackground);
dashboardRouter.post('/profile/background/delete', profileController.removeBackground);
dashboardRouter.post('/profile/pages', profileController.updatePages);
dashboardRouter.post('/profile/pages/:id/delete', profileController.removePage);
dashboardRouter.post('/profile/integrations', profileController.updateIntegrations);
dashboardRouter.post('/profile/footer', profileController.updateFooter);
dashboardRouter.post('/profile/default-theme', profileController.updateDefaultTheme);

// Email Signature
dashboardRouter.get('/email-signature', profileController.emailSignature);

// Messages
dashboardRouter.get('/messages', profileController.messages);
dashboardRouter.post('/messages/:id/read', profileController.markMessageRead);
dashboardRouter.post('/messages/:id/reply', profileController.replyMessage);
dashboardRouter.post('/messages/:id/delete', profileController.deleteMessage);

// Links
dashboardRouter.get('/links', linkController.index);
dashboardRouter.post('/links', linkController.store);
dashboardRouter.post('/links/:id/update', linkController.update);
dashboardRouter.post('/links/:id/delete', linkController.destroy);
dashboardRouter.post('/links/:id/toggle', linkController.toggleActive);
dashboardRouter.post('/links/reorder', linkController.reorder);

// Payments
dashboardRouter.get('/payments', paymentController.index);
dashboardRouter.post('/payments', setUploadType('screenshot'), upload.single('screenshot'), csrfValidateMultipart, paymentController.store);

router.use('/dashboard', dashboardRouter);

// Admin Routes
const adminRouter = express.Router();
adminRouter.use(isAuthenticated, isAdmin);

adminRouter.get('/', adminDashboardController.index);
adminRouter.get('/users', adminUserController.index);
adminRouter.get('/users/:id', adminUserController.show);
adminRouter.post('/users/:id/ban', adminUserController.toggleBan);
adminRouter.post('/users/:id/delete', adminUserController.destroy);
adminRouter.get('/payments', adminPaymentController.index);
adminRouter.get('/payments/:id', adminPaymentController.show);
adminRouter.post('/payments/:id/approve', adminPaymentController.approve);
adminRouter.post('/payments/:id/reject', adminPaymentController.reject);
adminRouter.get('/subscriptions', adminSubscriptionController.index);
adminRouter.post('/users/:id/premium', adminSubscriptionController.togglePremium);
adminRouter.get('/settings', adminSettingsController.index);
adminRouter.post('/settings', adminSettingsController.update);

router.use('/admin', adminRouter);

// Link Click Tracking
router.get('/click/:id', publicProfileController.trackClick);

// Contact Form
router.post('/:username/contact', publicProfileController.sendMessage);

// Custom Pages
router.get('/:username/:pageSlug', publicProfileController.showPage);

// Public Profile (must be last)
router.get('/:username', publicProfileController.show);

module.exports = router;
