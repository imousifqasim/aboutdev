# LinkFolio - Personal Portfolio & Link-in-Bio SaaS Platform

A complete full-stack SaaS web application similar to About.me / Linktree with a manual payment system. Built with Laravel 10, Blade, Tailwind CSS, and MySQL.

## Features

### User Features
- **Custom Profile Pages** - `/username` URL with profile image, bio, social links, custom buttons
- **Link Management** - Add, edit, reorder, toggle visibility of links
- **Multiple Themes** - Default, Dark, Gradient, Minimal, Bold, Ocean, Sunset, Forest
- **Gallery & Videos** - Upload images and embed YouTube/Vimeo videos (Premium)
- **Analytics** - Track profile views and link clicks
- **Dark Mode** - System-wide light/dark mode toggle
- **Responsive Design** - Mobile-first, works on all devices
- **SEO Optimized** - Meta tags, Open Graph, canonical URLs

### Admin Features
- **Dashboard** - User stats, revenue tracking, growth charts
- **User Management** - View, ban/unban, delete users
- **Payment Approval** - Review payment screenshots, approve/reject with notes
- **Subscription Control** - Manage premium access, toggle premium for users
- **Site Settings** - SEO, pricing, branding, contact info

### Subscription System
- **Free Plan** - 5 links, basic theme, platform branding
- **Premium Plan** - Unlimited links, all themes, no branding, gallery, videos, analytics

### Manual Payment System
Supports: Binance (USDT), JazzCash, Raast ID, Meezan Bank
- User submits payment with transaction ID + screenshot
- Admin reviews and approves/rejects
- Auto-activates premium on approval

## Tech Stack
- **Backend:** Laravel 10 (PHP 8.1+)
- **Frontend:** Blade + Tailwind CSS (CDN) + Alpine.js
- **Database:** MySQL
- **Icons:** Font Awesome 6

## Installation

### Requirements
- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js (optional, for asset compilation)

### Setup

```bash
# Clone the repository
git clone <repo-url> linkfolio
cd linkfolio

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database in .env
DB_DATABASE=linkfolio
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed database (creates admin + demo user)
php artisan db:seed

# Create storage symlink
php artisan storage:link

# Start development server
php artisan serve
```

### Default Accounts

| Role  | Email              | Password |
|-------|--------------------|----------|
| Admin | admin@linkfolio.com | password |
| User  | demo@linkfolio.com  | password |

## Payment Methods Configuration

The following payment methods are pre-configured:
- **Binance (USDT):** UID 827969859
- **JazzCash:** 03286477314
- **Raast ID:** 03286477314
- **Meezan Bank:** Account 26720109424781, Title: Tousif Ahmad

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/          # Authentication controllers
│   │   ├── Admin/         # Admin panel controllers
│   │   ├── User/          # User dashboard controllers
│   │   ├── HomeController.php
│   │   └── PublicProfileController.php
│   └── Middleware/
│       ├── AdminMiddleware.php
│       └── CheckBanned.php
├── Models/
│   ├── User.php
│   ├── Profile.php
│   ├── Link.php
│   ├── Payment.php
│   ├── Subscription.php
│   ├── Analytic.php
│   └── SiteSetting.php
resources/views/
├── layouts/               # App layout, navigation, footer
├── auth/                  # Login, register, forgot password
├── user/                  # User dashboard views
├── admin/                 # Admin panel views
└── public/                # Public profile page
```

## Security

- Email verification on signup
- Password hashing (bcrypt)
- CSRF protection on all forms
- Input validation on all endpoints
- Role-based access control (user/admin)
- Banned user detection middleware
- Duplicate payment prevention

## License

MIT
