const prisma = require('../../prisma');

exports.index = async (req, res) => {
  const { status } = req.query;
  const where = {};
  if (status) where.status = status;

  const payments = await prisma.payment.findMany({
    where,
    include: { user: true },
    orderBy: { createdAt: 'desc' },
  });

  res.render('admin/payments/index', { title: 'Payment Management', payments, status });
};

exports.show = async (req, res) => {
  const payment = await prisma.payment.findUnique({
    where: { id: parseInt(req.params.id) },
    include: {
      user: { include: { profile: true } },
      reviewer: true,
    },
  });

  if (!payment) {
    req.flash('error', 'Payment not found.');
    return res.redirect('/admin/payments');
  }

  res.render('admin/payments/show', { title: 'Payment Details', payment });
};

exports.approve = async (req, res) => {
  const paymentId = parseInt(req.params.id);
  const payment = await prisma.payment.findUnique({
    where: { id: paymentId },
    include: { user: { include: { profile: true } } },
  });

  if (!payment || payment.status !== 'pending') {
    req.flash('error', 'This payment has already been reviewed.');
    return res.redirect(`/admin/payments/${paymentId}`);
  }

  await prisma.$transaction(async (tx) => {
    await tx.payment.update({
      where: { id: paymentId },
      data: {
        status: 'approved',
        adminNote: req.body.admin_note || null,
        reviewedBy: req.user.id,
        reviewedAt: new Date(),
      },
    });

    await tx.profile.update({
      where: { id: payment.user.profile.id },
      data: { isPremium: true, showBranding: false },
    });

    await tx.subscription.updateMany({
      where: { userId: payment.userId, isActive: true },
      data: { isActive: false },
    });

    const endDate = new Date();
    endDate.setFullYear(endDate.getFullYear() + 1);

    await tx.subscription.create({
      data: {
        userId: payment.userId,
        plan: 'premium',
        startDate: new Date(),
        endDate,
        isActive: true,
      },
    });
  });

  req.flash('success', 'Payment approved. User upgraded to Premium!');
  res.redirect(`/admin/payments/${paymentId}`);
};

exports.reject = async (req, res) => {
  const paymentId = parseInt(req.params.id);
  const { admin_note } = req.body;

  if (!admin_note) {
    req.flash('error', 'Please provide a reason for rejection.');
    return res.redirect(`/admin/payments/${paymentId}`);
  }

  const payment = await prisma.payment.findUnique({ where: { id: paymentId } });
  if (!payment || payment.status !== 'pending') {
    req.flash('error', 'This payment has already been reviewed.');
    return res.redirect(`/admin/payments/${paymentId}`);
  }

  await prisma.payment.update({
    where: { id: paymentId },
    data: {
      status: 'rejected',
      adminNote: admin_note,
      reviewedBy: req.user.id,
      reviewedAt: new Date(),
    },
  });

  req.flash('success', 'Payment rejected.');
  res.redirect(`/admin/payments/${paymentId}`);
};
