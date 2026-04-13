const publisherEditEventConfig = window.publisherEditEventConfig || {};

document.addEventListener('DOMContentLoaded', function () {
    const coverFileInput = document.getElementById('coverFileInput');
    if (coverFileInput) {
        coverFileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (loadEvent) {
                const newImagePreview = document.getElementById('newImagePreview');
                const newImageContainer = document.getElementById('newImagePreviewContainer');
                if (newImagePreview) newImagePreview.src = loadEvent.target.result;
                if (newImageContainer) newImageContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    const editEventForm = document.getElementById('edit-event');
    if (!editEventForm) return;

    editEventForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const submitBtn = document.querySelector('.publish-btn');
        const originalText = submitBtn ? submitBtn.innerHTML : '';

        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Event...';
            submitBtn.disabled = true;
        }

        const formData = new FormData(this);
        formData.set('ajax', '1');

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(response => {
                if (!response.ok && response.status !== 200) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new Error('Server returned non-JSON response: ' + text.substring(0, 200));
                    });
                }

                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showSuccessMessage('Event updated successfully! Redirecting to event view...');
                    setTimeout(function () {
                        window.location.href = publisherEditEventConfig.redirectUrl || '/unipulse/public/publisher/eventview';
                    }, 1500);
                } else {
                    let errorMessage = 'Please fix the following errors:\n';
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([, message]) => {
                            errorMessage += `- ${message}\n`;
                        });
                    } else {
                        errorMessage += data.message || 'Unknown error occurred';
                    }
                    alert(errorMessage);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Network or server error: ' + error.message + '. Please check the console for more details.');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
    });
});

function clearNewImage() {
    const coverFileInput = document.getElementById('coverFileInput');
    const newImageContainer = document.getElementById('newImagePreviewContainer');

    if (coverFileInput) coverFileInput.value = '';
    if (newImageContainer) newImageContainer.style.display = 'none';
}

function showSuccessMessage(message) {
    const existingMessage = document.querySelector('.success-message');
    if (existingMessage) {
        existingMessage.remove();
    }

    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.style.cssText = [
        'background: #4CAF50',
        'color: white',
        'padding: 15px 20px',
        'border-radius: 5px',
        'margin: 20px 0',
        'font-size: 16px',
        'text-align: center',
        'box-shadow: 0 2px 5px rgba(0,0,0,0.2)',
        'animation: slideDown 0.3s ease-out'
    ].join('; ');

    successDiv.innerHTML = `
        <strong>${message}</strong>
        <button onclick="this.parentElement.remove()" style="
            background: none;
            border: none;
            color: white;
            float: right;
            cursor: pointer;
            font-size: 18px;
            margin-top: -2px;
        ">x</button>
    `;

    const form = document.getElementById('edit-event');
    if (form) {
        form.insertBefore(successDiv, form.firstChild);
    }

    setTimeout(() => {
        if (successDiv.parentElement) {
            successDiv.remove();
        }
    }, 5000);
}

window.clearNewImage = clearNewImage;
window.showSuccessMessage = showSuccessMessage;
