const prisma = require('../../prisma');

exports.index = async (req, res) => {
  const user = req.user;
  const links = await prisma.link.findMany({ where: { userId: user.id }, orderBy: { position: 'asc' } });
  const totalViews = await prisma.analytic.aggregate({ where: { userId: user.id }, _sum: { profileViews: true } });
  const totalClicks = links.reduce((sum, l) => sum + l.clicks, 0);
  const totalLinks = links.length;

  const thirtyDaysAgo = new Date();
  thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

  const recentAnalytics = await prisma.analytic.findMany({
    where: { userId: user.id, date: { gte: thirtyDaysAgo } },
    orderBy: { date: 'asc' },
  });

  const chartData = [];
  for (let i = 29; i >= 0; i--) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    const dateStr = d.toISOString().split('T')[0];
    const dayData = recentAnalytics.find(a => a.date.toISOString().split('T')[0] === dateStr);
    chartData.push({
      date: d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
      views: dayData ? dayData.profileViews : 0,
      clicks: dayData ? dayData.linkClicks : 0,
    });
  }

  res.render('user/dashboard', {
    title: 'Dashboard',
    user,
    totalViews: totalViews._sum.profileViews || 0,
    totalClicks,
    totalLinks,
    chartData,
  });
};
