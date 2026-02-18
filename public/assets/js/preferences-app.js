document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('preferencesForm');
    const nextBtn = document.getElementById('nextBtn');
    const skipBtn = document.getElementById('skipBtn');

    // Highlight selected tags
    document.querySelectorAll('.tags label').forEach(label => {
        label.addEventListener('click', function(e) {
            // Toggle checked state
            const input = label.querySelector('input');
            input.checked = !input.checked;
            label.classList.toggle('selected', input.checked);
        });
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = {};
        ['music', 'sport', 'business', 'exhibition'].forEach(category => {
            data[category] = Array.from(form.querySelectorAll(`input[name='${category}']:checked`)).map(i => i.value);
        });
        data.location = form.location.value;
        // You can send 'data' to your backend here
        alert('Preferences saved!\n' + JSON.stringify(data, null, 2));
        // window.location.href = '/next-onboarding-step'; // Uncomment to redirect
    });

    // Handle skip
    skipBtn.addEventListener('click', function() {
        // window.location.href = '/next-onboarding-step'; // Uncomment to redirect
        alert('Skipped preferences!');
    });
});
