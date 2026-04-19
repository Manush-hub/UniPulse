const publisherProfileConfig = window.publisherProfileConfig || {};
const publisherData = publisherProfileConfig.publisherData || {};
window.publisherData = publisherData;

function applyPreferenceButtonState(button, isActive) {
    if (isActive) {
        button.style.background = 'linear-gradient(135deg, #4A5BCC 0%, #23387f 100%)';
        button.style.borderColor = '#4A5BCC';
        button.style.color = 'white';
        button.style.boxShadow = '0 4px 15px rgba(74, 91, 204, 0.3)';
    } else {
        button.style.background = '#fafafa';
        button.style.borderColor = '#e0e0e0';
        button.style.color = '#666';
        button.style.boxShadow = 'none';
    }
}

function togglePreferenceBtn(button) {
    const wasActive = button.classList.contains('active');
    button.classList.toggle('active');
    const isNowActive = button.classList.contains('active');

    applyPreferenceButtonState(button, isNowActive);

    const activePreferences = [];
    document.querySelectorAll('.preference-btn-custom.active').forEach(btn => {
        const preference = btn.getAttribute('data-preference');
        if (preference) {
            activePreferences.push(preference);
        }
    });

    fetch(publisherProfileConfig.updatePreferencesUrl || '/unipulse/public/publisher/profile/updatePreferences', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'selected_preferences=' + encodeURIComponent(JSON.stringify(activePreferences))
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Failed to save preferences');
            }
        })
        .catch(error => {
            console.error('Preference update failed:', error);
            button.classList.toggle('active');
            applyPreferenceButtonState(button, wasActive);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.preference-btn-custom').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            togglePreferenceBtn(this);
        });
    });
});

window.togglePreferenceBtn = togglePreferenceBtn;
