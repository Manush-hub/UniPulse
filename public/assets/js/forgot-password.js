document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const form = document.getElementById('forgotPasswordForm');
    const sendBtn = document.getElementById('sendResetBtn');
    const formStatus = document.getElementById('formStatus');

    if (emailInput) {
        emailInput.focus();
    }

    if (form && sendBtn && formStatus) {
        form.addEventListener('submit', function(e) {
            const emailValue = emailInput ? emailInput.value.trim() : '';

            if (!emailValue) {
                e.preventDefault();
                formStatus.style.display = 'block';
                formStatus.style.color = '#b91c1c';
                formStatus.textContent = 'Please enter your email address.';
                if (emailInput) {
                    emailInput.focus();
                }
                return;
            }

            sendBtn.disabled = true;
            sendBtn.style.opacity = '0.8';
            formStatus.style.display = 'none';
            formStatus.textContent = '';
        });
    }
});
