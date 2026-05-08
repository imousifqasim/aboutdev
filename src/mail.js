const nodemailer = require('nodemailer');

const smtpHost = process.env.SMTP_HOST;
const smtpPort = parseInt(process.env.SMTP_PORT || '587', 10);
const smtpSecure = process.env.SMTP_SECURE === 'true';
const smtpUser = process.env.SMTP_USER;
const smtpPass = process.env.SMTP_PASS;
const fromAddress = process.env.EMAIL_FROM || `DropLaunch <no-reply@${process.env.APP_URL ? new URL(process.env.APP_URL).hostname : 'localhost'}>`;

const transporter = smtpHost && smtpUser && smtpPass ? nodemailer.createTransport({
  host: smtpHost,
  port: smtpPort,
  secure: smtpSecure,
  auth: {
    user: smtpUser,
    pass: smtpPass,
  },
}) : null;

async function sendMail({ to, subject, html, text }) {
  if (!transporter) {
    console.warn('SMTP is not fully configured. Skipping email send.');
    return;
  }

  const mailOptions = {
    from: fromAddress,
    to,
    subject,
    html,
    text,
  };

  return transporter.sendMail(mailOptions);
}

module.exports = { sendMail };