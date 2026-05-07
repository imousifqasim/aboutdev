const fs = require('fs');
const prisma = require('../../prisma');
const { isPremium } = require('../../helpers');

function cleanupFile(req) {
  if (req.file && req.file.path) {
    fs.unlink(req.file.path, () => {});
  }
}

const PAYMENT_METHODS = [
  { name: 'Binance (USDT)', value: 'binance', details: 'UID: 827969859' },
  { name: 'JazzCash', value: 'jazzcash', details: 'Number: 03286477314' },
  { name: 'Raast ID', value: 'raast', details: 'ID: 03286477314' },
  { name: 'Meezan Bank', value: 'meezan_bank', details: 'Account: 26720109424781 | Title: Tousif Ahmad' },
];

exports.index = async (req, res) => {
  const user = req.user;
  user.isPremiumUser = await isPremium(user.id);
  const payments = await prisma.payment.findMany({ where: { userId: user.id }, orderBy: { createdAt: 'desc' } });
  const activeSubscription = await prisma.subscription.findFirst({
    where: {
      userId: user.id, isActive: true, plan: 'premium',
      OR: [{ endDate: null }, { endDate: { gte: new Date() } }],
    },
    orderBy: { createdAt: 'desc' },
  });
  const unreadCount = await prisma.contactMessage.count({ where: { userId: user.id, isRead: false } });

  res.render('user/payments/index', {
    title: 'Subscription & Payments',
    user,
    payments,
    paymentMethods: PAYMENT_METHODS,
    activeSubscription,
    unreadCount,
  });
};

exports.store = async (req, res) => {
  const { method, amount, transaction_id } = req.body;
  const validMethods = PAYMENT_METHODS.map(m => m.value);

  if (!method || !validMethods.includes(method) || !amount || !transaction_id) {
    cleanupFile(req);
    req.flash('error', 'All fields are required.');
    return res.redirect('/dashboard/payments');
  }

  if (!req.file) {
    req.flash('error', 'Screenshot is required.');
    return res.redirect('/dashboard/payments');
  }

  const existingTx = await prisma.payment.findUnique({ where: { transactionId: transaction_id } });
  if (existingTx) {
    cleanupFile(req);
    req.flash('error', 'This transaction ID has already been used.');
    return res.redirect('/dashboard/payments');
  }

  try {
    await prisma.$transaction(async (tx) => {
      const pendingPayment = await tx.payment.findFirst({
        where: { userId: req.user.id, status: 'pending' },
      });
      if (pendingPayment) {
        throw new Error('pending-exists');
      }

      await tx.payment.create({
        data: {
          userId: req.user.id,
          method,
          amount: parseFloat(amount),
          transactionId: transaction_id,
          screenshotPath: 'payment-screenshots/' + req.file.filename,
          status: 'pending',
        },
      });
    });

    req.flash('success', 'Payment submitted successfully! It will be reviewed shortly.');
  } catch (err) {
    if (err.message === 'pending-exists') {
      cleanupFile(req);
      req.flash('error', 'You already have a pending payment. Please wait for it to be reviewed.');
    } else {
      cleanupFile(req);
      throw err;
    }
  }

  res.redirect('/dashboard/payments');
};
