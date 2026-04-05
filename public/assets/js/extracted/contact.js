const contactForm = document.getElementById('contactForm');
    const successMessage = document.getElementById('successMessage');

    if (contactForm) {
      contactForm.addEventListener('submit', function(event) {
        if (!contactForm.checkValidity()) {
          event.preventDefault();
          contactForm.reportValidity();
          return false;
        }

        // Show success message
        if (successMessage) {
          event.preventDefault();
          
          const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            category: document.getElementById('category').value,
            subject: document.getElementById('subject').value,
            message: document.getElementById('message').value
          };

          console.log('Form Data:', formData);

          successMessage.classList.add('show');
          
          contactForm.reset();

          // Scroll to success message
          successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });

          setTimeout(() => {
            successMessage.classList.remove('show');
            // Redirect to the same contact page
            window.location.href = window.location.pathname;
          }, 10000);
        }
      });
    }