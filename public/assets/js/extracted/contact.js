const contactForm = document.getElementById('contactForm');
const successMessage = document.getElementById('successMessage');

if (successMessage && successMessage.dataset.show === '1') {
  successMessage.classList.add('show');

  setTimeout(() => {
    successMessage.classList.remove('show');
  }, 3500);
}

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