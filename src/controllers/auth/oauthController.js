const passport = require('passport');
const GoogleStrategy = require('passport-google-oauth20').Strategy;
const GitHubStrategy = require('passport-github2').Strategy;
const prisma = require('../../prisma');

// Passport configuration
passport.use(new GoogleStrategy({
  clientID: process.env.GOOGLE_CLIENT_ID,
  clientSecret: process.env.GOOGLE_CLIENT_SECRET,
  callbackURL: `${process.env.APP_URL}/auth/google/callback`
}, async (accessToken, refreshToken, profile, done) => {
  try {
    let user = await prisma.user.findUnique({
      where: { googleId: profile.id }
    });

    if (!user) {
      // Check if user exists with same email
      const existingUser = await prisma.user.findUnique({
        where: { email: profile.emails[0].value }
      });

      if (existingUser) {
        // Link Google account to existing user
        user = await prisma.user.update({
          where: { id: existingUser.id },
          data: {
            googleId: profile.id,
            provider: 'google'
          }
        });
      } else {
        // Create new user
        user = await prisma.user.create({
          data: {
            name: profile.displayName,
            email: profile.emails[0].value,
            googleId: profile.id,
            provider: 'google',
            emailVerifiedAt: new Date()
          }
        });

        // Create profile
        await prisma.profile.create({
          data: {
            userId: user.id,
            username: profile.emails[0].value.split('@')[0] + Math.random().toString(36).substring(2, 8)
          }
        });
      }
    }

    return done(null, user);
  } catch (error) {
    return done(error, null);
  }
}));

passport.use(new GitHubStrategy({
  clientID: process.env.GITHUB_CLIENT_ID,
  clientSecret: process.env.GITHUB_CLIENT_SECRET,
  callbackURL: `${process.env.APP_URL}/auth/github/callback`
}, async (accessToken, refreshToken, profile, done) => {
  try {
    let user = await prisma.user.findUnique({
      where: { githubId: profile.id }
    });

    if (!user) {
      // Check if user exists with same email
      const existingUser = await prisma.user.findUnique({
        where: { email: profile.emails[0].value }
      });

      if (existingUser) {
        // Link GitHub account to existing user
        user = await prisma.user.update({
          where: { id: existingUser.id },
          data: {
            githubId: profile.id,
            provider: 'github'
          }
        });
      } else {
        // Create new user
        user = await prisma.user.create({
          data: {
            name: profile.displayName || profile.username,
            email: profile.emails[0].value,
            githubId: profile.id,
            provider: 'github',
            emailVerifiedAt: new Date()
          }
        });

        // Create profile
        await prisma.profile.create({
          data: {
            userId: user.id,
            username: profile.username + Math.random().toString(36).substring(2, 8)
          }
        });
      }
    }

    return done(null, user);
  } catch (error) {
    return done(error, null);
  }
}));

passport.serializeUser((user, done) => {
  done(null, user.id);
});

passport.deserializeUser(async (id, done) => {
  try {
    const user = await prisma.user.findUnique({
      where: { id },
      include: { profile: true }
    });
    done(null, user);
  } catch (error) {
    done(error, null);
  }
});

// Controller methods
exports.googleAuth = passport.authenticate('google', {
  scope: ['profile', 'email']
});

exports.googleCallback = passport.authenticate('google', {
  failureRedirect: '/login'
});

exports.githubAuth = passport.authenticate('github', {
  scope: ['user:email']
});

exports.githubCallback = passport.authenticate('github', {
  failureRedirect: '/login'
});

exports.oauthCallback = async (req, res) => {
  // Update login info
  await prisma.user.update({
    where: { id: req.user.id },
    data: {
      lastLoginAt: new Date(),
      loginCount: { increment: 1 }
    }
  });

  // Log successful login
  await prisma.loginHistory.create({
    data: {
      userId: req.user.id,
      ipAddress: req.ip,
      userAgent: req.get('User-Agent'),
      success: true
    }
  });

  if (req.user.role === 'admin') {
    return res.redirect('/admin');
  }
  res.redirect('/dashboard');
};