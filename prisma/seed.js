const { PrismaClient } = require('@prisma/client');
const bcrypt = require('bcryptjs');
const prisma = new PrismaClient();

async function main() {
  console.log('Seeding database...');

  // Create Admin User
  const adminPassword = await bcrypt.hash('password', 10);
  const admin = await prisma.user.create({
    data: {
      name: 'Admin',
      email: 'admin@droplaunch.dev',
      password: adminPassword,
      role: 'admin',
      emailVerifiedAt: new Date(),
    },
  });

  await prisma.profile.create({
    data: { userId: admin.id, username: 'admin' },
  });

  // Create Demo User
  const demoPassword = await bcrypt.hash('password', 10);
  const demo = await prisma.user.create({
    data: {
      name: 'Tousif Ahmad',
      email: 'demo@droplaunch.dev',
      password: demoPassword,
      emailVerifiedAt: new Date(),
    },
  });

  await prisma.profile.create({
    data: {
      userId: demo.id,
      username: 'tousif',
      bio: 'Full-stack developer & entrepreneur. Building amazing things on the web.',
      theme: 'default',
      location: 'Pakistan',
      socialLinks: {
        github: 'https://github.com/imousifqasim',
        twitter: 'https://twitter.com/tousif',
        linkedin: 'https://linkedin.com/in/tousif',
      },
    },
  });

  await prisma.subscription.create({
    data: { userId: demo.id, plan: 'free', startDate: new Date() },
  });

  await prisma.link.createMany({
    data: [
      { userId: demo.id, title: 'My Website', url: 'https://example.com', position: 1 },
      { userId: demo.id, title: 'GitHub', url: 'https://github.com/imousifqasim', icon: 'github', position: 2 },
      { userId: demo.id, title: 'LinkedIn', url: 'https://linkedin.com/in/tousif', icon: 'linkedin', position: 3 },
    ],
  });

  // Default Site Settings
  const settings = {
    site_name: 'DropLaunch',
    site_description: 'Create your personal portfolio page and share your links with the world.',
    site_keywords: 'portfolio, links, bio, personal page, linktree alternative',
    premium_price: '9.99',
    premium_currency: 'USD',
    contact_email: 'admin@droplaunch.dev',
    footer_text: 'Made with DropLaunch',
  };

  for (const [key, value] of Object.entries(settings)) {
    await prisma.siteSetting.upsert({
      where: { key },
      update: { value },
      create: { key, value },
    });
  }

  console.log('Seeding complete!');
}

main()
  .catch(console.error)
  .finally(() => prisma.$disconnect());
