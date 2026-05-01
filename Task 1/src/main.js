import './style.css';

console.log('Steganography App Initialized');

// Test scroll progress bar on load
window.addEventListener('DOMContentLoaded', () => {
  const progressBar = document.getElementById('scroll-progress');
  if (progressBar) {
    console.log('✅ Scroll progress bar found!', progressBar);
    console.log('Current styles:', {
      width: progressBar.style.width,
      display: window.getComputedStyle(progressBar).display,
      position: window.getComputedStyle(progressBar).position,
      top: window.getComputedStyle(progressBar).top,
      zIndex: window.getComputedStyle(progressBar).zIndex
    });
  } else {
    console.error('❌ Scroll progress bar NOT found!');
  }
});

// Login Form Handling
const loginForm = document.getElementById('loginForm');
if (loginForm) {
  loginForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const btn = loginForm.querySelector('button');
    const originalText = btn.innerText;

    // Get values
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;

    btn.innerText = 'Verifying Identity...';
    btn.style.opacity = '0.7';

    setTimeout(() => {
      btn.innerText = `Welcome, ${name}`;
      btn.style.backgroundColor = 'var(--primary-color)';

      setTimeout(() => {
        alert(`Access granted for ${email}. (Demo only)`);
        btn.innerText = originalText;
        btn.style.opacity = '1';
        loginForm.reset();
      }, 1000);
    }, 1500);
  });
}

// Reveal Interaction
const revealBtn = document.getElementById('reveal-btn');
const hiddenMsg = document.getElementById('hidden-msg');
const demoOverlay = document.getElementById('demo-overlay');

if (revealBtn && hiddenMsg && demoOverlay) {
  revealBtn.addEventListener('click', () => {
    if (hiddenMsg.style.opacity === '0' || hiddenMsg.style.opacity === '') {
      // Reveal
      hiddenMsg.style.opacity = '1';
      hiddenMsg.style.transform = 'translateY(0)';
      demoOverlay.style.background = 'rgba(0,0,0,0.7)'; // Darken background to make text pop
      revealBtn.innerText = 'Hide Data';
    } else {
      // Hide
      hiddenMsg.style.opacity = '0';
      hiddenMsg.style.transform = 'translateY(20px)';
      demoOverlay.style.background = 'rgba(0,0,0,0.1)'; // Restore light overlay
      revealBtn.innerText = 'Reveal Hidden Data';
    }
  });
}


// Smooth scrolling for anchor links
// Smooth scrolling for anchor links with offset for fixed navbar
function getNavbarHeight() {
  const nav = document.querySelector('.navbar');
  return nav ? nav.offsetHeight : 90;
}

function scrollToElementWithOffset(el) {
  const y = el.getBoundingClientRect().top + window.scrollY - getNavbarHeight() - 8;
  window.scrollTo({ top: Math.max(y, 0), behavior: 'smooth' });
}

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const href = this.getAttribute('href');
    const target = document.querySelector(href);
    if (target) {
      e.preventDefault();
      // update the URL hash without jumping
      history.pushState(null, '', href);
      scrollToElementWithOffset(target);
    }
  });
});

// On initial load, if there's a hash, scroll to it with offset
window.addEventListener('load', () => {
  if (location.hash) {
    const target = document.querySelector(location.hash);
    if (target) setTimeout(() => scrollToElementWithOffset(target), 50);
  }
});

// Handle back/forward or programmatic hash changes
window.addEventListener('hashchange', () => {
  if (location.hash) {
    const target = document.querySelector(location.hash);
    if (target) scrollToElementWithOffset(target);
  }
});

// Animation Observer
const observerOptions = {
  root: null,
  rootMargin: '0px',
  threshold: 0.1
};

const observer = new IntersectionObserver((entries, observer) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

document.querySelectorAll('.card, .content-split, .section-title, .hero-content').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(20px)';
  el.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
  observer.observe(el);
});

// Scroll Progress Bar Logic
function updateScrollProgress() {
  const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
  const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
  const scrolled = (winScroll / height) * 100;
  const progressBar = document.getElementById('scroll-progress');

  if (progressBar) {
    progressBar.style.width = scrolled + "%";
    console.log('Scroll progress:', scrolled.toFixed(2) + '%', 'Width set to:', progressBar.style.width);
  } else {
    console.log('Progress bar element not found!');
  }

  // Show/Hide Scroll to Top Button
  const scrollToTopBtn = document.getElementById('scrollToTop');
  if (scrollToTopBtn) {
    if (winScroll > 300) {
      scrollToTopBtn.classList.add('visible');
    } else {
      scrollToTopBtn.classList.remove('visible');
    }
  }
}

// Add scroll listener
window.addEventListener('scroll', updateScrollProgress);

// Update immediately on load
window.addEventListener('load', updateScrollProgress);
document.addEventListener('DOMContentLoaded', updateScrollProgress);

// Scroll to Top Button Click
const scrollToTopBtn = document.getElementById('scrollToTop');
if (scrollToTopBtn) {
  scrollToTopBtn.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
}
