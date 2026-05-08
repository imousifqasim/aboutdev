const { getSetting } = require('../helpers');

exports.index = async (req, res) => {
  const premiumPrice = await getSetting('premium_price', '9.99');
  const premiumCurrency = await getSetting('premium_currency', 'USD');
  res.render('home', { title: 'DropLaunch - Create Your Personal Portfolio Page', premiumPrice, premiumCurrency });
};
