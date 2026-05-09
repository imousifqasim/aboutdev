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

async function sendPasswordResetEmail(email, token) {
  const resetUrl = `${process.env.APP_URL}/reset-password/${token}`;
  const subject = 'Reset Your Password - DropLaunch';
  const html = `
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
      <h2>Reset Your Password</h2>
      <p>You requested a password reset for your DropLaunch account.</p>
      <p>Click the link below to reset your password:</p>
      <a href="${resetUrl}" style="background-color: #4F46E5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password</a>
      <p>If you didn't request this, please ignore this email.</p>
      <p>This link will expire in 1 hour.</p>
      <p>Best,<br>DropLaunch Team</p>
    </div>
  `;
  const text = `
    Reset Your Password

    You requested a password reset for your DropLaunch account.

    Click the link below to reset your password:
    ${resetUrl}

    If you didn't request this, please ignore this email.

    This link will expire in 1 hour.

    Best,
    DropLaunch Team
  `;

  return sendMail({ to: email, subject, html, text });
}

module.exports = { sendMail, sendPasswordResetEmail };