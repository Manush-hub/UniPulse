function submitAppeal() {
    const config = window.signinAppealConfig;
    if (!config) {
        return;
    }

    const messageInput = document.getElementById('appealMessage');
    const resultDiv = document.getElementById('appealResult');
    if (!messageInput || !resultDiv) {
        return;
    }

    const message = messageInput.value.trim();
    if (!message) {
        alert('Please enter your appeal message');
        return;
    }

    fetch(config.submitUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            user_id: config.userId,
            user_type: config.userType,
            appeal_message: message
        })
    })
        .then(response => response.json())
        .then(data => {
            resultDiv.style.display = 'block';

            if (data.success) {
                resultDiv.innerHTML = '<div style="padding: 15px; background: #d4edda; color: #155724; border-radius: 4px;"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
                messageInput.value = '';
                setTimeout(() => {
                    window.location.href = config.redirectUrl;
                }, 3000);
            } else {
                resultDiv.innerHTML = '<div style="padding: 15px; background: #f8d7da; color: #721c24; border-radius: 4px;"><i class="fas fa-exclamation-circle"></i> ' + data.message + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting your appeal');
        });
}

window.submitAppeal = submitAppeal;
