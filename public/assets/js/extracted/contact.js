const contactForm = document.getElementById('contactForm');

if (contactForm) {
  contactForm.addEventListener('submit', function(event) {
    if (!contactForm.checkValidity()) {
      event.preventDefault();
      contactForm.reportValidity();
      return;
    }

    const submitBtn = contactForm.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Sending...';
    }
  });
}