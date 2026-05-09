const prisma = require('../../prisma');
const { isPremium } = require('../../helpers');
const path = require('path');
const fs = require('fs');

// Portfolio Builder Controller
exports.builder = async (req, res) => {
  const user = await prisma.user.findUnique({
    where: { id: req.user.id },
    include: { profile: true }
  });

  user.isPremiumUser = await isPremium(user.id);
  const unreadCount = await prisma.contactMessage.count({
    where: { userId: user.id, isRead: false }
  });

  // Get portfolio data or initialize empty
  const portfolioData = user.profile.portfolioData || {
    sections: [],
    settings: {
      theme: 'default',
      layout: 'boxed',
      colors: {
        primary: '#4f46e5',
        background: '#ffffff',
        text: '#1f2937'
      },
      fonts: {
        heading: 'Inter',
        body: 'Inter'
      }
    }
  };

  // Check premium restrictions
  const premiumFeatures = {
    customDomain: user.isPremiumUser,
    advancedSeo: user.isPremiumUser,
    analytics: user.isPremiumUser,
    aiSuggestions: user.isPremiumUser,
    multipleThemes: user.isPremiumUser,
    customFonts: user.isPremiumUser,
    animations: user.isPremiumUser,
    videoSupport: user.isPremiumUser,
    audioSupport: user.isPremiumUser,
    contactForm: user.isPremiumUser,
    testimonials: user.isPremiumUser,
    services: user.isPremiumUser,
    gallery: user.isPremiumUser,
    resume: user.isPremiumUser
  };

  res.render('user/portfolio/builder', {
    title: 'Portfolio Builder',
    user,
    unreadCount,
    portfolioData: JSON.stringify(portfolioData),
    premiumFeatures: JSON.stringify(premiumFeatures)
  });
};

exports.savePortfolio = async (req, res) => {
  try {
    const { portfolio_data } = req.body;
    const portfolioData = JSON.parse(portfolio_data);

    await prisma.profile.update({
      where: { id: req.user.profile.id },
      data: {
        portfolioData: portfolioData
      }
    });

    res.json({ success: true, message: 'Portfolio saved successfully!' });
  } catch (error) {
    console.error('Portfolio save error:', error);
    res.status(500).json({ success: false, message: 'Failed to save portfolio' });
  }
};

exports.uploadMedia = async (req, res) => {
  try {
    if (!req.file) {
      return res.status(400).json({ success: false, message: 'No file uploaded' });
    }

    const fileUrl = `/uploads/portfolio/${req.file.filename}`;
    res.json({
      success: true,
      url: fileUrl,
      filename: req.file.filename
    });
  } catch (error) {
    console.error('Media upload error:', error);
    res.status(500).json({ success: false, message: 'Upload failed' });
  }
};

exports.preview = async (req, res) => {
  const profile = await prisma.profile.findUnique({
    where: { username: req.params.username },
    include: { user: true }
  });

  if (!profile) {
    return res.status(404).render('error', { title: 'Profile Not Found', error: 'Profile not found' });
  }

  // Get portfolio data
  const portfolioData = profile.portfolioData || { sections: [] };

  // Track portfolio view analytics
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  try {
    await prisma.analytic.upsert({
      where: {
        userId_date: {
          userId: profile.user.id,
          date: today
        }
      },
      update: {
        portfolioViews: { increment: 1 }
      },
      create: {
        userId: profile.user.id,
        date: today,
        portfolioViews: 1,
        referrer: req.get('Referer') || null,
        country: req.get('CF-IPCountry') || null, // Cloudflare header
        device: req.useragent?.isMobile ? 'mobile' : req.useragent?.isTablet ? 'tablet' : 'desktop'
      }
    });
  } catch (error) {
    console.error('Analytics tracking error:', error);
  }

  res.render('public/portfolio-preview', {
    title: profile.metaTitle || `${profile.user.name} - Portfolio`,
    profile,
    user: profile.user,
    portfolioData,
    isPreview: true
  });
};

exports.templates = async (req, res) => {
  const user = await prisma.user.findUnique({
    where: { id: req.user.id },
    include: { profile: true }
  });

  user.isPremiumUser = await isPremium(user.id);
  const unreadCount = await prisma.contactMessage.count({
    where: { userId: user.id, isRead: false }
  });

  // Predefined templates organized by category
  const templates = {
    developer: {
      id: 'developer',
      name: 'Developer Portfolio',
      category: 'Technology',
      description: 'Showcase your coding projects and technical expertise',
      sections: [
        { type: 'hero' }, { type: 'about' }, { type: 'projects' }, { type: 'experience' }, { type: 'skills' }, { type: 'contact' }
      ]
    },
    designer: {
      id: 'designer',
      name: 'Designer Portfolio',
      category: 'Creative',
      description: 'Display your design work and creative process',
      sections: [
        { type: 'hero' }, { type: 'gallery' }, { type: 'about' }, { type: 'services' }, { type: 'process' }, { type: 'contact' }
      ]
    },
    freelancer: {
      id: 'freelancer',
      name: 'Freelancer Portfolio',
      category: 'Business',
      description: 'Showcase your freelance work and client success stories',
      sections: [
        { type: 'hero' }, { type: 'services' }, { type: 'testimonials' }, { type: 'about' }, { type: 'stats' }, { type: 'contact' }
      ]
    },
    photographer: {
      id: 'photographer',
      name: 'Photographer Portfolio',
      category: 'Creative',
      description: 'Display your photography work and capture moments',
      sections: [
        { type: 'hero' }, { type: 'gallery' }, { type: 'about' }, { type: 'services' }, { type: 'testimonials' }, { type: 'contact' }
      ]
    },
    student: {
      id: 'student',
      name: 'Student Portfolio',
      category: 'Education',
      description: 'Showcase your academic achievements and projects',
      sections: [
        { type: 'hero' }, { type: 'about' }, { type: 'education' }, { type: 'projects' }, { type: 'experience' }, { type: 'skills' }, { type: 'contact' }
      ]
    },
    agency: {
      id: 'agency',
      name: 'Agency Portfolio',
      category: 'Business',
      description: 'Showcase your agency\'s work and team expertise',
      sections: [
        { type: 'hero' }, { type: 'about' }, { type: 'services' }, { type: 'projects' }, { type: 'testimonials' }, { type: 'stats' }, { type: 'contact' }
      ]
    }
  };

  res.render('user/portfolio/templates', {
    title: 'Portfolio Templates',
    user,
    unreadCount,
    templates: Object.values(templates)
  });
};

exports.applyTemplate = async (req, res) => {
  try {
    const { template_id } = req.body;

    // Get templates from the same source as templates view
    const templates = {
      developer: {
        id: 'developer',
        name: 'Developer Portfolio',
        category: 'Technology',
        description: 'Showcase your coding projects and technical expertise',
        sections: [
          { type: 'hero', content: { title: 'Hi, I\'m a Full-Stack Developer', subtitle: 'Building scalable web applications with modern technologies', backgroundImage: '', ctaText: 'View My Work', ctaLink: '#projects' } },
          { type: 'about', content: { bio: 'Passionate full-stack developer with 5+ years of experience building web applications. I specialize in React, Node.js, and cloud technologies. I love solving complex problems and creating intuitive user experiences.', skills: ['JavaScript', 'React', 'Node.js', 'Python', 'AWS', 'Docker'], image: '' } },
          { type: 'projects', content: { title: 'Featured Projects', items: [
            { title: 'E-commerce Platform', description: 'Full-stack e-commerce solution with React frontend and Node.js backend', image: '', link: '', technologies: ['React', 'Node.js', 'MongoDB'] },
            { title: 'Task Management App', description: 'Collaborative project management tool with real-time updates', image: '', link: '', technologies: ['Vue.js', 'Express', 'PostgreSQL'] },
            { title: 'API Gateway', description: 'Microservices API gateway with authentication and rate limiting', image: '', link: '', technologies: ['Node.js', 'Redis', 'JWT'] }
          ]} },
          { type: 'experience', content: { title: 'Work Experience', items: [
            { company: 'Tech Corp', position: 'Senior Developer', duration: '2022 - Present', description: 'Led development of customer-facing applications serving 100k+ users' },
            { company: 'StartupXYZ', position: 'Full-Stack Developer', duration: '2020 - 2022', description: 'Built MVP from scratch and scaled to 10k users' }
          ]} },
          { type: 'skills', content: { title: 'Technical Skills', categories: [
            { name: 'Frontend', skills: ['React', 'Vue.js', 'Angular', 'TypeScript', 'HTML5', 'CSS3', 'Sass'] },
            { name: 'Backend', skills: ['Node.js', 'Python', 'PHP', 'Express', 'Django', 'REST APIs'] },
            { name: 'Database', skills: ['MongoDB', 'PostgreSQL', 'MySQL', 'Redis'] },
            { name: 'DevOps', skills: ['AWS', 'Docker', 'Kubernetes', 'CI/CD', 'Git'] }
          ]} },
          { type: 'contact', content: { title: 'Let\'s Work Together', subtitle: 'I\'m always interested in new opportunities and exciting projects', email: '', phone: '', linkedin: '', github: '' } }
        ],
        settings: {
          theme: 'developer',
          layout: 'boxed',
          colors: { primary: '#4f46e5', background: '#ffffff', text: '#1f2937' },
          fonts: { heading: 'Inter', body: 'Inter' }
        }
      },
      designer: {
        id: 'designer',
        name: 'Designer Portfolio',
        category: 'Creative',
        description: 'Display your design work and creative process',
        sections: [
          { type: 'hero', content: { title: 'Creative UI/UX Designer', subtitle: 'Designing experiences that matter', backgroundImage: '', ctaText: 'Explore My Work', ctaLink: '#gallery' } },
          { type: 'gallery', content: { title: 'Design Portfolio', images: [
            { url: '', alt: 'Mobile App Design', caption: 'E-commerce mobile app redesign' },
            { url: '', alt: 'Website Design', caption: 'Brand identity and website for startup' },
            { url: '', alt: 'Logo Design', caption: 'Logo collection for various clients' }
          ]} },
          { type: 'about', content: { bio: 'Creative designer with a passion for user-centered design. I specialize in creating beautiful, functional interfaces that solve real problems. My approach combines strategic thinking with pixel-perfect execution.', skills: ['UI/UX Design', 'Figma', 'Adobe Creative Suite', 'Prototyping', 'User Research'], image: '' } },
          { type: 'services', content: { title: 'Design Services', services: [
            { name: 'UI/UX Design', description: 'Complete user interface and experience design for web and mobile', price: '$50/hour' },
            { name: 'Brand Identity', description: 'Logo design, brand guidelines, and visual identity systems', price: '$1000/project' },
            { name: 'Design Systems', description: 'Scalable design systems and component libraries', price: '$2000/project' }
          ]} },
          { type: 'process', content: { title: 'My Design Process', steps: [
            { title: 'Research', description: 'User interviews, competitive analysis, and requirement gathering' },
            { title: 'Ideation', description: 'Wireframing, user flows, and concept development' },
            { title: 'Design', description: 'High-fidelity mockups and interactive prototypes' },
            { title: 'Testing', description: 'User testing, iteration, and final delivery' }
          ]} },
          { type: 'contact', content: { title: 'Start Your Project', subtitle: 'Let\'s create something amazing together', email: '', phone: '', linkedin: '', behance: '' } }
        ],
        settings: {
          theme: 'designer',
          layout: 'fullwidth',
          colors: { primary: '#ec4899', background: '#ffffff', text: '#1f2937' },
          fonts: { heading: 'Poppins', body: 'Inter' }
        }
      },
      freelancer: {
        id: 'freelancer',
        name: 'Freelancer Portfolio',
        category: 'Business',
        description: 'Showcase your freelance work and client success stories',
        sections: [
          { type: 'hero', content: { title: 'Professional Freelancer', subtitle: 'Delivering exceptional results for clients worldwide', backgroundImage: '', ctaText: 'View Services', ctaLink: '#services' } },
          { type: 'services', content: { title: 'My Services', services: [
            { name: 'Web Development', description: 'Custom websites and web applications', price: 'Starting at $1000' },
            { name: 'Content Writing', description: 'SEO-optimized articles and blog posts', price: '$50/article' },
            { name: 'Digital Marketing', description: 'Social media management and campaign strategy', price: '$500/month' }
          ]} },
          { type: 'testimonials', content: { title: 'Client Testimonials', items: [
            { name: 'Sarah Johnson', company: 'Tech Startup', text: 'Exceptional work and great communication. Highly recommended!', rating: 5 },
            { name: 'Mike Chen', company: 'E-commerce Store', text: 'Delivered the project on time and exceeded expectations.', rating: 5 },
            { name: 'Lisa Rodriguez', company: 'Marketing Agency', text: 'Professional freelancer with outstanding results.', rating: 5 }
          ]} },
          { type: 'about', content: { bio: 'Experienced freelancer with 8+ years in digital services. I help businesses grow online through strategic web development, content creation, and digital marketing. My goal is to deliver measurable results that drive your success.', skills: ['Project Management', 'Client Communication', 'SEO', 'Analytics'], image: '' } },
          { type: 'stats', content: { title: 'My Achievements', stats: [
            { number: '150+', label: 'Projects Completed' },
            { number: '98%', label: 'Client Satisfaction' },
            { number: '50+', label: 'Happy Clients' },
            { number: '3+', label: 'Years Experience' }
          ]} },
          { type: 'contact', content: { title: 'Ready to Get Started?', subtitle: 'Let\'s discuss your project and how I can help you succeed', email: '', phone: '', linkedin: '', upwork: '' } }
        ],
        settings: {
          theme: 'freelancer',
          layout: 'boxed',
          colors: { primary: '#059669', background: '#ffffff', text: '#1f2937' },
          fonts: { heading: 'Inter', body: 'Inter' }
        }
      },
      photographer: {
        id: 'photographer',
        name: 'Photographer Portfolio',
        category: 'Creative',
        description: 'Display your photography work and capture moments',
        sections: [
          { type: 'hero', content: { title: 'Professional Photographer', subtitle: 'Capturing life\'s precious moments', backgroundImage: '', ctaText: 'View Gallery', ctaLink: '#gallery' } },
          { type: 'gallery', content: { title: 'Photography Portfolio', images: [
            { url: '', alt: 'Wedding Photography', caption: 'Romantic wedding ceremony in golden hour' },
            { url: '', alt: 'Portrait Session', caption: 'Natural light portrait photography' },
            { url: '', alt: 'Event Photography', caption: 'Corporate event coverage' }
          ]} },
          { type: 'about', content: { bio: 'Award-winning photographer specializing in weddings, portraits, and events. I believe every moment deserves to be captured beautifully. My passion is telling stories through imagery and creating lasting memories for my clients.', skills: ['Wedding Photography', 'Portrait Photography', 'Event Photography', 'Photo Editing'], image: '' } },
          { type: 'services', content: { title: 'Photography Services', services: [
            { name: 'Wedding Photography', description: 'Complete wedding day coverage with engagement session', price: '$2500' },
            { name: 'Portrait Session', description: 'Professional headshots and family portraits', price: '$300' },
            { name: 'Event Photography', description: 'Corporate events, parties, and celebrations', price: '$800' }
          ]} },
          { type: 'testimonials', content: { title: 'Client Reviews', items: [
            { name: 'Emma & James', text: 'Our wedding photos are absolutely stunning. Couldn\'t be happier!', rating: 5 },
            { name: 'Corporate Client', text: 'Professional and timely. Great work for our company event.', rating: 5 },
            { name: 'Family Session', text: 'Made our family feel comfortable and captured genuine moments.', rating: 5 }
          ]} },
          { type: 'contact', content: { title: 'Book Your Session', subtitle: 'Let\'s create beautiful memories together', email: '', phone: '', instagram: '', website: '' } }
        ],
        settings: {
          theme: 'photographer',
          layout: 'fullwidth',
          colors: { primary: '#7c3aed', background: '#ffffff', text: '#1f2937' },
          fonts: { heading: 'Playfair Display', body: 'Inter' }
        }
      },
      student: {
        id: 'student',
        name: 'Student Portfolio',
        category: 'Education',
        description: 'Showcase your academic achievements and projects',
        sections: [
          { type: 'hero', content: { title: 'Aspiring Professional', subtitle: 'Building my future through education and experience', backgroundImage: '', ctaText: 'View My Work', ctaLink: '#projects' } },
          { type: 'about', content: { bio: 'Motivated student pursuing a degree in Computer Science. Passionate about technology and innovation. Eager to apply classroom knowledge to real-world projects and gain valuable experience in the field.', skills: ['Programming', 'Data Analysis', 'Project Management', 'Communication'], image: '' } },
          { type: 'education', content: { title: 'Education', institutions: [
            { name: 'University Name', degree: 'Bachelor of Science in Computer Science', year: 'Expected 2025', gpa: '3.8' },
            { name: 'High School', degree: 'High School Diploma', year: '2021', achievements: 'Valedictorian' }
          ]} },
          { type: 'projects', content: { title: 'Academic Projects', items: [
            { title: 'Machine Learning Research', description: 'Developed ML model for predictive analytics', technologies: ['Python', 'TensorFlow', 'Pandas'] },
            { title: 'Web Application', description: 'Full-stack web app for student management', technologies: ['React', 'Node.js', 'MongoDB'] },
            { title: 'Mobile App', description: 'Cross-platform mobile application', technologies: ['React Native', 'Firebase'] }
          ]} },
          { type: 'experience', content: { title: 'Experience', items: [
            { company: 'Tech Company', position: 'Intern', duration: 'Summer 2023', description: 'Assisted in software development and testing' },
            { company: 'Research Lab', position: 'Research Assistant', duration: '2022 - 2023', description: 'Conducted data analysis and literature review' }
          ]} },
          { type: 'skills', content: { title: 'Skills & Technologies', categories: [
            { name: 'Programming', skills: ['Java', 'Python', 'JavaScript', 'C++'] },
            { name: 'Tools', skills: ['Git', 'Docker', 'AWS', 'VS Code'] },
            { name: 'Soft Skills', skills: ['Teamwork', 'Problem Solving', 'Communication'] }
          ]} },
          { type: 'contact', content: { title: 'Get In Touch', subtitle: 'Open to opportunities and collaborations', email: '', phone: '', linkedin: '', github: '' } }
        ],
        settings: {
          theme: 'student',
          layout: 'boxed',
          colors: { primary: '#0891b2', background: '#ffffff', text: '#1f2937' },
          fonts: { heading: 'Inter', body: 'Inter' }
        }
      },
      agency: {
        id: 'agency',
        name: 'Agency Portfolio',
        category: 'Business',
        description: 'Showcase your agency\'s work and team expertise',
        sections: [
          { type: 'hero', content: { title: 'Digital Agency', subtitle: 'Transforming businesses through innovative digital solutions', backgroundImage: '', ctaText: 'See Our Work', ctaLink: '#projects' } },
          { type: 'about', content: { bio: 'Leading digital agency specializing in web development, design, and digital marketing. We partner with businesses to create exceptional digital experiences that drive growth and engagement. Our team of experts delivers results that matter.', team: [
            { name: 'John Smith', role: 'CEO & Founder', image: '' },
            { name: 'Sarah Johnson', role: 'Creative Director', image: '' },
            { name: 'Mike Chen', role: 'Technical Lead', image: '' }
          ], image: '' } },
          { type: 'services', content: { title: 'Our Services', services: [
            { name: 'Web Development', description: 'Custom websites and web applications', price: 'Starting at $5000' },
            { name: 'Brand Design', description: 'Complete brand identity and design systems', price: 'Starting at $3000' },
            { name: 'Digital Marketing', description: 'SEO, PPC, and social media campaigns', price: 'Starting at $2000/month' },
            { name: 'E-commerce', description: 'Online stores and payment integration', price: 'Starting at $8000' }
          ]} },
          { type: 'projects', content: { title: 'Featured Work', items: [
            { title: 'E-commerce Platform', description: 'Complete online store with 300% sales increase', image: '', link: '', technologies: ['Shopify', 'React', 'Node.js'] },
            { title: 'Brand Redesign', description: 'Full brand identity for Fortune 500 company', image: '', link: '', technologies: ['Figma', 'Adobe Suite'] },
            { title: 'Mobile App', description: 'Cross-platform app with 100k+ downloads', image: '', link: '', technologies: ['React Native', 'Firebase'] }
          ]} },
          { type: 'testimonials', content: { title: 'Client Success Stories', items: [
            { name: 'TechCorp Inc.', text: 'Outstanding results and professional team. Highly recommended!', rating: 5 },
            { name: 'Fashion Brand', text: 'Transformed our online presence completely. Amazing work!', rating: 5 },
            { name: 'StartupXYZ', text: 'From MVP to scale-up success. Thank you for everything!', rating: 5 }
          ]} },
          { type: 'stats', content: { title: 'Our Impact', stats: [
            { number: '500+', label: 'Projects Delivered' },
            { number: '98%', label: 'Client Retention' },
            { number: '50M+', label: 'Revenue Generated' },
            { number: '15+', label: 'Team Members' }
          ]} },
          { type: 'contact', content: { title: 'Start Your Project', subtitle: 'Let\'s discuss how we can help grow your business', email: '', phone: '', linkedin: '', website: '' } }
        ],
        settings: {
          theme: 'agency',
          layout: 'fullwidth',
          colors: { primary: '#374151', background: '#ffffff', text: '#1f2937' },
          fonts: { heading: 'Inter', body: 'Inter' }
        }
      }
    };

    const template = templates[template_id];
    if (!template) {
      return res.status(400).json({ success: false, message: 'Template not found' });
    }

    await prisma.profile.update({
      where: { id: req.user.profile.id },
      data: {
        portfolioData: template
      }
    });

    res.json({ success: true, message: 'Template applied successfully!' });
  } catch (error) {
    console.error('Template apply error:', error);
    res.status(500).json({ success: false, message: 'Failed to apply template' });
  }
};

exports.aiSuggestions = async (req, res) => {
  try {
    const { type, context } = req.body;
    const user = await prisma.user.findUnique({
      where: { id: req.user.id },
      include: { profile: true }
    });

    user.isPremiumUser = await isPremium(user.id);

    if (!user.isPremiumUser) {
      return res.status(403).json({ success: false, message: 'AI suggestions require premium subscription' });
    }

    let suggestions = [];

    switch (type) {
      case 'bio':
        suggestions = [
          `Passionate ${context || 'professional'} with expertise in delivering exceptional results. I specialize in creating innovative solutions that drive success and exceed expectations.`,
          `Creative and dedicated ${context || 'professional'} committed to excellence. With years of experience, I bring fresh perspectives and cutting-edge skills to every project.`,
          `Results-driven ${context || 'professional'} with a proven track record of success. I combine technical expertise with creative thinking to deliver outstanding outcomes.`
        ];
        break;

      case 'project_description':
        suggestions = [
          `A comprehensive ${context || 'solution'} built with modern technologies. This project showcases advanced development techniques and user-centric design principles.`,
          `An innovative ${context || 'application'} that solves real-world problems. Features include intuitive user interface, robust functionality, and scalable architecture.`,
          `A cutting-edge ${context || 'platform'} designed for optimal performance. Built with the latest technologies to ensure reliability, security, and exceptional user experience.`
        ];
        break;

      case 'services':
        suggestions = [
          {
            title: 'Web Development',
            description: 'Custom websites and web applications built with modern technologies and best practices.'
          },
          {
            title: 'UI/UX Design',
            description: 'User-centered design solutions that create intuitive and engaging digital experiences.'
          },
          {
            title: 'Consulting',
            description: 'Expert guidance and strategic advice to help your business grow and succeed.'
          }
        ];
        break;

      case 'hero_title':
        suggestions = [
          `Hi, I'm ${user.name}`,
          `Welcome to My Portfolio`,
          `${user.name} - ${context || 'Professional'}`
        ];
        break;

      case 'hero_subtitle':
        suggestions = [
          `Creating amazing digital experiences`,
          `Bringing ideas to life through technology`,
          `Delivering quality solutions with passion`
        ];
        break;

      default:
        suggestions = ['AI-powered content suggestion'];
    }

    res.json({ success: true, suggestions });
  } catch (error) {
    console.error('AI suggestions error:', error);
    res.status(500).json({ success: false, message: 'Failed to generate suggestions' });
  }
};

exports.analytics = async (req, res) => {
  try {
    const user = await prisma.user.findUnique({
      where: { id: req.user.id },
      include: { profile: true }
    });

    user.isPremiumUser = await isPremium(user.id);
    const unreadCount = await prisma.contactMessage.count({
      where: { userId: user.id, isRead: false }
    });

    // Get portfolio analytics data
    const analytics = {
      totalViews: user.profile.portfolioViews || 0,
      totalClicks: user.profile.portfolioClicks || 0,
      uniqueVisitors: user.profile.portfolioUniqueVisitors || 0,
      topPages: [
        { page: 'Home', views: 45, clicks: 12 },
        { page: 'Projects', views: 32, clicks: 8 },
        { page: 'About', views: 28, clicks: 5 },
        { page: 'Contact', views: 18, clicks: 3 }
      ],
      visitorStats: {
        countries: [
          { country: 'United States', visitors: 25, percentage: 35 },
          { country: 'United Kingdom', visitors: 18, percentage: 25 },
          { country: 'Canada', visitors: 12, percentage: 17 },
          { country: 'Germany', visitors: 8, percentage: 11 },
          { country: 'Others', visitors: 9, percentage: 12 }
        ],
        devices: [
          { device: 'Desktop', visitors: 42, percentage: 58 },
          { device: 'Mobile', visitors: 25, percentage: 35 },
          { device: 'Tablet', visitors: 5, percentage: 7 }
        ]
      },
      recentActivity: [
        { action: 'Portfolio viewed', location: 'New York, US', time: '2 hours ago' },
        { action: 'Project clicked', location: 'London, UK', time: '4 hours ago' },
        { action: 'Contact form submitted', location: 'Toronto, CA', time: '1 day ago' },
        { action: 'Portfolio viewed', location: 'Berlin, DE', time: '2 days ago' }
      ]
    };

    res.render('user/portfolio/analytics', {
      title: 'Portfolio Analytics',
      user,
      unreadCount,
      analytics
    });
  } catch (error) {
    console.error('Analytics error:', error);
    res.status(500).send('Internal server error');
  }
};