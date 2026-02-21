// Publisher Sponsorships App
let currentSponsorshipId = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            switchTab(tabName);
        });
    });
});

// Switch between tabs
function switchTab(tabName) {
    // Remove active class from all tabs and panes
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
    
    // Add active class to selected tab and pane
    document.querySelector(`.tab-btn[data-tab="${tabName}"]`).classList.add('active');
    document.getElementById(`${tabName}-tab`).classList.add('active');
}

// Approve sponsorship
function approveSponsorshipButton(sponsorshipId) {
    if (!confirm('Are you sure you want to approve this sponsorship request?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('sponsorship_id', sponsorshipId);
    
    fetch('/unipulse/public/publisher/sponsorships/approve', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sponsorship approved successfully!');
            location.reload();
        } else {
            alert(data.message || 'Failed to approve sponsorship');
        }
    })
    .catch(error => {
        console.error('Error approving sponsorship:', error);
        alert('An error occurred. Please try again.');
    });
}

// Open reject modal
function openRejectModal(sponsorshipId) {
    currentSponsorshipId = sponsorshipId;
    document.getElementById('rejectModal').style.display = 'flex';
    document.getElementById('rejectReason').value = '';
}

// Close reject modal
function closeRejectModal() {
    currentSponsorshipId = null;
    document.getElementById('rejectModal').style.display = 'none';
}

// Confirm rejection
function confirmReject() {
    const reason = document.getElementById('rejectReason').value.trim();
    
    if (!reason) {
        alert('Please provide a reason for rejection');
        return;
    }
    
    const formData = new FormData();
    formData.append('sponsorship_id', currentSponsorshipId);
    formData.append('reason', reason);
    
    fetch('/unipulse/public/publisher/sponsorships/reject', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sponsorship rejected');
            closeRejectModal();
            location.reload();
        } else {
            alert(data.message || 'Failed to reject sponsorship');
        }
    })
    .catch(error => {
        console.error('Error rejecting sponsorship:', error);
        alert('An error occurred. Please try again.');
    });
}

// Mark sponsorship as completed
function completeSponsorshipButton(sponsorshipId) {
    if (!confirm('Mark this sponsorship as completed? This indicates you have received the payment.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('sponsorship_id', sponsorshipId);
    
    fetch('/unipulse/public/publisher/sponsorships/complete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sponsorship marked as completed!');
            location.reload();
        } else {
            alert(data.message || 'Failed to complete sponsorship');
        }
    })
    .catch(error => {
        console.error('Error completing sponsorship:', error);
        alert('An error occurred. Please try again.');
    });
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const rejectModal = document.getElementById('rejectModal');
    if (event.target === rejectModal) {
        closeRejectModal();
    }
});
