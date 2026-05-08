const prisma = require('./prisma');

function timeAgo(date) {
  const seconds = Math.floor((new Date() - new Date(date)) / 1000);
  if (seconds < 60) return 'just now';
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
  const hours = Math.floor(minutes / 60);
  if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
  const days = Math.floor(hours / 24);
  if (days < 30) return `${days} day${days > 1 ? 's' : ''} ago`;
  const months = Math.floor(days / 30);
  return `${months} month${months > 1 ? 's' : ''} ago`;
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function formatCurrency(amount) {
  return parseFloat(amount).toFixed(2);
}

async function isPremium(userId) {
  const sub = await prisma.subscription.findFirst({
    where: {
      userId,
      isActive: true,
      plan: 'premium',
      OR: [
        { endDate: null },
        { endDate: { gte: new Date() } },
      ],
    },
    orderBy: { createdAt: 'desc' },
  });
  return sub !== null;
}

async function getSetting(key, defaultValue = null) {
  const setting = await prisma.siteSetting.findUnique({ where: { key } });
  return setting ? setting.value : defaultValue;
}

async function setSetting(key, value) {
  await prisma.siteSetting.upsert({
    where: { key },
    update: { value },
    create: { key, value },
  });
}

const RESERVED_USERNAMES = [
  'admin', 'dashboard', 'login', 'register', 'logout',
  'forgot-password', 'reset-password', 'email', 'click',
  'home', 'api', 'css', 'js', 'images', 'storage', 'uploads',
];

module.exports = { timeAgo, formatDate, formatCurrency, isPremium, getSetting, setSetting, RESERVED_USERNAMES };
