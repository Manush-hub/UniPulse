document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.tab-btn');

    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
            const tabName = this.dataset.tab;
            switchTab(tabName);
        });
    });
});

function switchTab(tabName) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));

    const activeButton = document.querySelector(`.tab-btn[data-tab="${tabName}"]`);
    const activePane = document.getElementById(`${tabName}-tab`);

    if (activeButton) {
        activeButton.classList.add('active');
    }

    if (activePane) {
        activePane.classList.add('active');
    }
}

function acceptDonation(donationId) {
    if (!confirm('Mark this donation as accepted?')) {
        return;
    }

    const formData = new FormData();
    formData.append('donation_id', donationId);

    fetch('/unipulse/public/publisher/donations/accept', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Donation moved to Accepted.');
                location.reload();
            } else {
                alert(data.message || 'Failed to update donation.');
            }
        })
        .catch(error => {
            console.error('Error accepting donation:', error);
            alert('An error occurred. Please try again.');
        });
}

function rejectDonation(donationId) {
    if (!confirm('Mark this donation as rejected?')) {
        return;
    }

    const formData = new FormData();
    formData.append('donation_id', donationId);

    fetch('/unipulse/public/publisher/donations/reject', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Donation moved to Rejected.');
                location.reload();
            } else {
                alert(data.message || 'Failed to update donation.');
            }
        })
        .catch(error => {
            console.error('Error rejecting donation:', error);
            alert('An error occurred. Please try again.');
        });
}

window.acceptDonation = acceptDonation;
window.rejectDonation = rejectDonation;
