/**
 * ACEBEAM ENGINEERING LIMITED - Official Website Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Menu Toggle
  const mobileToggle = document.querySelector('.mobile-toggle');
  const navMenu = document.querySelector('.nav-menu');
  
  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener('click', () => {
      navMenu.classList.toggle('active');
      const icon = mobileToggle.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
      }
    });

    // Close menu when clicking links
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        navMenu.classList.remove('active');
        const icon = mobileToggle.querySelector('i');
        if (icon) {
          icon.classList.add('fa-bars');
          icon.classList.remove('fa-times');
        }
      });
    });
  }

  // 2. Header Scroll Effect
  const header = document.querySelector('.main-header');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      header?.classList.add('scrolled');
    } else {
      header?.classList.remove('scrolled');
    }
  });

  // 3. RFQ Form Submission Handler
  const rfqForm = document.getElementById('rfqForm');
  const formStatus = document.getElementById('formStatus');

  if (rfqForm) {
    rfqForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const submitBtn = rfqForm.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending Request...';

      if (formStatus) {
        formStatus.style.display = 'none';
        formStatus.className = 'form-status';
      }

      const formData = new FormData(rfqForm);
      const clientEmail = formData.get('email') || '';
      const clientName = formData.get('name') || 'Client';

      let sentSuccessfully = false;

      // Check if testing locally via file protocol or running on a live web server
      const isFileProtocol = window.location.protocol === 'file:';

      if (!isFileProtocol) {
        // Direct cPanel mail.php dispatch (fastest server-side execution)
        try {
          const controller = new AbortController();
          const timeoutId = setTimeout(() => controller.abort(), 4000);

          const response = await fetch('mail.php', {
            method: 'POST',
            body: formData,
            signal: controller.signal
          });
          clearTimeout(timeoutId);

          if (response.ok) {
            sentSuccessfully = true;
          }
        } catch (phpErr) {
          console.warn('Direct mail.php unavailable, switching to cloud dispatcher:', phpErr);
        }
      }

      // Cloud dispatcher fallback if mail.php is not active or when testing locally
      if (!sentSuccessfully) {
        try {
          formData.append('_captcha', 'false');
          formData.append('_template', 'table');
          formData.append('_subject', `New RFQ: ${formData.get('service') || 'General Inquiry'} - ${clientName}`);

          const fallbackResp = await fetch('https://formsubmit.co/ajax/info@acebeamel.com', {
            method: 'POST',
            body: formData,
            headers: {
              'Accept': 'application/json'
            }
          });

          if (fallbackResp.ok) {
            sentSuccessfully = true;
          }
        } catch (fbErr) {
          console.error('Cloud dispatcher error:', fbErr);
        }
      }

      if (sentSuccessfully) {
        if (formStatus) {
          formStatus.innerHTML = `
            <div style="background: #e6f9f8; color: #0b111e; border: 1.5px solid #62C6C2; padding: 18px 24px; border-radius: 10px; margin-top: 20px; box-shadow: 0 4px 14px rgba(98, 198, 194, 0.2);">
              <h4 style="color: #0b111e; margin: 0 0 8px; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle-check" style="color: #20BA5A; font-size: 1.3rem;"></i>
                Inquiry Dispatched to info@acebeamel.com
              </h4>
              <p style="margin: 0 0 6px; font-size: 0.92rem; color: #334155; line-height: 1.5;">
                Thank you, <strong>${clientName}</strong>! Your technical project specifications have been sent directly to our official engineering desk at <strong>info@acebeamel.com</strong>.
              </p>
              <p style="margin: 0; font-size: 0.85rem; color: #64748b;">
                For immediate technical dispatch, you can also reach our engineering lines directly at <strong>+234 803 764 6371</strong>.
              </p>
            </div>
          `;
          formStatus.style.display = 'block';
        }
        rfqForm.reset();
      } else {
        if (formStatus) {
          formStatus.innerHTML = `
            <div style="background: #fef2f2; color: #991b1b; border: 1.5px solid #f87171; padding: 16px 20px; border-radius: 10px; margin-top: 20px;">
              <strong>Notice:</strong> We could not connect to the mail server. Please call our engineering desk directly at <strong>+234 803 764 6371</strong> or email us at <strong>info@acebeamel.com</strong>.
            </div>
          `;
          formStatus.style.display = 'block';
        }
      }

      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    });
  }

  // 4. Scrollspy: Highlight Active Nav Link When Scrolling
  const trackedSections = [
    { id: 'home', el: document.getElementById('home') },
    { id: 'about', el: document.getElementById('about') },
    { id: 'services', el: document.getElementById('services') },
    { id: 'projects', el: document.getElementById('projects') },
    { id: 'why-us', el: document.getElementById('why-us') },
    { id: 'contact', el: document.getElementById('rfq') || document.getElementById('contact') }
  ].filter(item => item.el !== null);

  const navLinks = document.querySelectorAll('.nav-link');

  function updateActiveNavLink() {
    const scrollPos = window.scrollY + 160; // offset for sticky header
    const windowBottom = window.scrollY + window.innerHeight;
    const docBottom = document.documentElement.scrollHeight;

    // If reached bottom of page, highlight Contact
    if (windowBottom >= docBottom - 60) {
      setActive('contact');
      return;
    }

    let currentSection = 'home';
    for (let i = 0; i < trackedSections.length; i++) {
      const sec = trackedSections[i];
      const top = sec.el.offsetTop;
      const height = sec.el.offsetHeight;
      if (scrollPos >= top && scrollPos < top + height) {
        currentSection = sec.id;
        break;
      }
    }

    setActive(currentSection);
  }

  function setActive(sectionId) {
    navLinks.forEach(link => {
      const target = link.getAttribute('href');
      if (target === `#${sectionId}`) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  }

  window.addEventListener('scroll', updateActiveNavLink, { passive: true });
  window.addEventListener('resize', updateActiveNavLink, { passive: true });
  updateActiveNavLink();

  // 5. Draggable & Moveable WhatsApp Widget (Prevents covering page content)
  const waWidget = document.getElementById('whatsappWidget');
  const waLink = document.getElementById('whatsappLink');

  if (waWidget && waLink) {
    let isDragging = false;
    let hasMoved = false;
    let startX = 0, startY = 0;
    let initialLeft = 0, initialTop = 0;

    const onPointerDown = (e) => {
      // Allow primary mouse click (button 0) or touch
      if (e.button && e.button !== 0) return;

      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;

      const rect = waWidget.getBoundingClientRect();
      startX = clientX;
      startY = clientY;
      initialLeft = rect.left;
      initialTop = rect.top;
      hasMoved = false;

      document.addEventListener('mousemove', onPointerMove, { passive: false });
      document.addEventListener('mouseup', onPointerUp);
      document.addEventListener('touchmove', onPointerMove, { passive: false });
      document.addEventListener('touchend', onPointerUp);
    };

    const onPointerMove = (e) => {
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;

      const dx = clientX - startX;
      const dy = clientY - startY;

      if (!hasMoved && Math.hypot(dx, dy) > 6) {
        hasMoved = true;
        isDragging = true;
        waWidget.classList.add('is-dragging');
      }

      if (isDragging) {
        if (e.cancelable) e.preventDefault();

        let newX = initialLeft + dx;
        let newY = initialTop + dy;

        // Viewport boundaries
        const padding = 12;
        const maxX = window.innerWidth - waWidget.offsetWidth - padding;
        const maxY = window.innerHeight - waWidget.offsetHeight - padding;

        newX = Math.max(padding, Math.min(newX, maxX));
        newY = Math.max(padding, Math.min(newY, maxY));

        waWidget.style.left = `${newX}px`;
        waWidget.style.top = `${newY}px`;
        waWidget.style.right = 'auto';
        waWidget.style.bottom = 'auto';
      }
    };

    const onPointerUp = () => {
      document.removeEventListener('mousemove', onPointerMove);
      document.removeEventListener('mouseup', onPointerUp);
      document.removeEventListener('touchmove', onPointerMove);
      document.removeEventListener('touchend', onPointerUp);

      if (isDragging) {
        setTimeout(() => {
          isDragging = false;
          waWidget.classList.remove('is-dragging');
        }, 100);
      }
    };

    waWidget.addEventListener('mousedown', onPointerDown);
    waWidget.addEventListener('touchstart', onPointerDown, { passive: true });

    // Cancel opening WhatsApp if user was dragging
    waLink.addEventListener('click', (e) => {
      if (hasMoved) {
        e.preventDefault();
        e.stopPropagation();
      }
    });
  }
});
