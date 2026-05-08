# DropLaunch

A complete full-stack SaaS portfolio platform similar to About.me / Linktree with manual payment system. Built with **Node.js**, **Express**, **EJS**, **Prisma ORM**, and **MySQL**.

## Tech Stack

- **Backend:** Node.js + Express.js
- **Template Engine:** EJS
- **ORM:** Prisma
- **Database:** MySQL
- **Styling:** Tailwind CSS (CDN) + Alpine.js
- **Auth:** Session-based with bcrypt
- **File Uploads:** Multer

## Features

### User Features
- Custom profile URL (`/username`)
- Profile editor (bio, social links, image, SEO)
- 8 themes (Default, Dark, Minimal, Gradient, Bold, Ocean, Sunset, Forest)
- Link management with icons and drag-drop reordering
- Email signature generator
- QR code for profile sharing
- Spotlight/CTA button
- Contact form & message inbox (Premium)
- Testimonials section (Premium)
- Resume/CV section (Premium)
- Gallery & Videos (Premium)
- Full-page hero background (Premium)
- Analytics dashboard with charts

### Admin Features
- Dashboard with stats and user growth chart
- User management (view, ban, delete)
- Payment approval/rejection system
- Subscription control
- Site settings (branding, pricing, SEO)

### Manual Payment System
- Binance (USDT), JazzCash, Raast ID, Meezan Bank
- User submits transaction ID + screenshot
- Admin reviews and approves/rejects
- Auto premium activation on approval

## Quick Start

```bash
# Install dependencies
npm install

# Copy environment file
cp .env.example .env
# Edit .env with your database credentials

# Run migrations
npx prisma migrate dev

# Seed database
node prisma/seed.js

# Start the server
npm start
```

Server runs at `http://localhost:8000`

## Default Accounts

| Role  | Email               | Password |
|-------|---------------------|----------|
| Admin | admin@droplaunch.dev | password |
| User  | demo@droplaunch.dev  | password |

## Project Structure

```
droplaunch/
├── app.js                 # Express app entry point
├── prisma/
│   ├── schema.prisma      # Database schema
│   └── seed.js            # Database seeder
├── src/
│   ├── prisma.js          # Prisma client instance
│   ├── helpers.js          # Utility functions
│   ├── middleware/         # Auth, user loading
│   ├── controllers/       # Route handlers
│   │   ├── auth/          # Login, register
│   │   ├── user/          # Dashboard, profile, links, payments
│   │   └── admin/         # Admin dashboard, users, payments, settings
│   └── routes/            # Express routes
├── views/                 # EJS templates
│   ├── layouts/           # App & dashboard layouts
│   ├── auth/              # Login, register
│   ├── user/              # User dashboard views
│   ├── admin/             # Admin panel views
│   └── public/            # Public profile
└── public/                # Static files & uploads
```

## Deployment

This app can be deployed to any Node.js hosting:
- **Railway** - Easiest, supports MySQL
- **Render** - Docker-based
- **Fly.io** - Containerized
- **DigitalOcean** - VPS
- **Vercel** - With serverless adapter

## License

ISC
