# Testing DropLaunch (Laravel SaaS Platform)

## Overview
DropLaunch is a Laravel 10 SaaS platform (About.me/Linktree clone) with Blade + Tailwind CSS (CDN) + Alpine.js. No build step required.

## Local Development Setup

### Prerequisites
- PHP 8.1+
- MySQL
- Composer

### Start Dev Server
```bash
sudo service mysql start
cd /home/ubuntu/linkfolio  # or wherever the repo is cloned
composer install
cp .env.example .env
php artisan key:generate
# Configure DB in .env: DB_DATABASE=linkfolio, DB_USERNAME=linkfolio, DB_PASSWORD=linkfolio_secret
php artisan migrate --seed
php artisan storage:link
php artisan serve --host=0.0.0.0 --port=8000
```

The app runs at `http://localhost:8000`.

### Database
- MySQL database name: `linkfolio`
- The DB name was not renamed when the app was rebranded from LinkFolio to DropLaunch
- Seeder creates admin and demo users with old email domain (@linkfolio.com) — use those emails for login even though the app branding says DropLaunch

## Test Accounts

| Role | Email | Password | Notes |
|------|-------|----------|-------|
| Admin | admin@linkfolio.com | password | Has admin panel access at /admin |
| Demo User | demo@linkfolio.com | password | Regular user with profile at /tousif |

**Important**: The seeded emails use `@linkfolio.com` (old domain), NOT `@droplaunch.dev`. The app was renamed but the database seeder emails were kept as-is since the DB was already seeded.

## Key Testing Flows

### 1. User Registration → Dashboard
- Navigate to `/register`
- Fill in name, username, email, password
- After registration, user lands on `/dashboard`
- Username prefix on register form should show `droplaunch.dev/`

### 2. Payment Submission (User Side)
- Login as regular user → `/dashboard/payments`
- If user is on Free plan, "Upgrade to Premium" form is visible
- Select payment method (Binance/JazzCash/Raast/Meezan Bank)
- Fill amount ($9.99), transaction ID, upload screenshot
- Submit → status shows as "Pending"

### 3. Payment Approval (Admin Side)
- Login as admin → `/admin/payments`
- View pending payment → click "View"
- Verify payment details (user, method, amount, transaction ID, screenshot)
- Click "Approve Payment"
- Expected: Success flash "Payment approved. User upgraded to Premium!"
- Status changes to "Approved", user's profile is_premium becomes true

### 4. Premium Features Verification
- After approval, login as the upgraded user
- Dashboard should show Plan: "Premium" with gold crown
- `/dashboard/payments` shows Current Plan: Premium with Active badge
- `/dashboard/profile` shows Gallery upload and Videos sections (hidden for free users)
- Public profile (`/{username}`) should NOT show "Made with DropLaunch" branding

### 5. Public Profile
- Navigate to `/{username}` (e.g., `/tousif`)
- Free users: "Made with DropLaunch" branding visible at bottom
- Premium users: branding hidden
- Profile shows name, bio, social links, custom links

### 6. Dark Mode
- Moon/sun icon in the nav bar toggles dark mode
- Uses localStorage for persistence
- Dark mode applies site-wide background change

## Common Gotchas

- **Email mismatch**: DB seeder uses old `@linkfolio.com` emails. Don't try to login with `@droplaunch.dev`.
- **No build step**: Tailwind CSS is loaded via CDN. No npm install or build needed.
- **Storage link**: Must run `php artisan storage:link` for uploaded screenshots to display.
- **Payment screenshot**: The payment detail page loads screenshots from `/storage/payment-screenshots/`. If images don't show, check storage link.
- **Verified middleware**: Dashboard routes require email verification. Seeded users have email_verified_at set, but newly registered users might get blocked if MAIL is not configured.
- **Admin protection**: Admin users cannot be banned or deleted by other admins (security fix).
- **Social link XSS**: Social link URLs are validated to only allow http/https schemes (javascript: URLs are rejected).

## Branding
- App name: DropLaunch (renamed from LinkFolio)
- Domain references: droplaunch.dev (changed from linkfolio.com)
- All views, controllers, seeder settings, and README use DropLaunch branding
- Database name remains `linkfolio` (not renamed)

## No CI
This repo has no CI configured. Testing is done manually via the local dev server.

## Devin Secrets Needed
No secrets are required for local testing. All test accounts are seeded with known passwords.
