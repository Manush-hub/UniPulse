/**
 * Volunteer Involvement Module
 * Displays customizable volunteer involvement information and success messages
 */

/**
 * Display volunteer involvement section with customizable message
 */
function displayVolunteerInvolvement(event) {
    // Create volunteer involvement section if it doesn't exist
    let volunteerInvolvementCard = document.getElementById('volunteerInvolvementCard');
    if (!volunteerInvolvementCard) {
        const volunteerCard = document.getElementById('volunteerCard');
        if (volunteerCard && volunteerCard.parentNode) {
            volunteerInvolvementCard = document.createElement('div');
            volunteerInvolvementCard.id = 'volunteerInvolvementCard';
            volunteerInvolvementCard.className = 'content-card';
            volunteerInvolvementCard.style.display = 'block';
            volunteerInvolvementCard.innerHTML = `
                <h3><i class="fas fa-heart"></i> Your Volunteer Involvement</h3>
                <div id="volunteerInvolvementInfo"></div>
            `;
            volunteerCard.parentNode.insertBefore(volunteerInvolvementCard, volunteerCard.nextSibling);
        }
    }

    const volunteerInvolvementInfo = document.getElementById('volunteerInvolvementInfo');
    if (!volunteerInvolvementInfo) return;

    let involvementHTML = '<div class="volunteer-involvement-detail">';
    
    const eventTitle = event.title || 'Event';
    const eventDate = event.event_date || 'TBA';
    const eventTime = event.event_time || 'TBA';
    
    // Customizable volunteer thank you message
    involvementHTML += `
        <div class="volunteer-involvement-message">
            <div class="message-box success-message">
                <i class="fas fa-check-circle"></i>
                <div class="message-content">
                    <h4>Welcome to Our Volunteer Team!</h4>
                    <p>Thank you for applying as a volunteer for <strong>${eventTitle}</strong>. We appreciate your commitment to making this event successful.</p>
                </div>
            </div>
        </div>

        <div class="volunteer-involvement-info">
            <div class="involvement-item">
                <i class="fas fa-calendar-alt"></i>
                <div>
                    <strong>Event Date & Time</strong>
                    <p>${eventDate} at ${eventTime}</p>
                </div>
            </div>
            
            <div class="involvement-item">
                <i class="fas fa-progress-circle"></i>
                <div>
                    <strong>Your Application Status</strong>
                    <p><span class="status-badge pending">Pending Approval</span></p>
                </div>
            </div>
            
            <div class="involvement-item">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>What Happens Next?</strong>
                    <p>The event organizer will review your application and send you a confirmation email within 1-2 days.</p>
                </div>
            </div>
        </div>

        <div class="volunteer-involvement-actions">
            <p class="involvement-note">
                <i class="fas fa-hand-holding-heart"></i> 
                You can view all your volunteer opportunities and track your volunteer journey in your dashboard.
            </p>
        </div>
    `;

    involvementHTML += '</div>';
    volunteerInvolvementInfo.innerHTML = involvementHTML;
}

/**
 * Show toast notification with customizable message
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    const iconMap = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'info': 'info-circle',
        'warning': 'exclamation-triangle'
    };
    
    const iconClass = iconMap[type] || 'info-circle';
    
    toast.innerHTML = `
        <i class="fas fa-${iconClass}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 10);
    
    // Remove after 4 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 4000);
}

/**
 * Customize volunteer involvement message
 * Usage: customizeVolunteerMessage({ thanksTitle: 'Custom Title', thankMessage: 'Custom Message', ... })
 */
function customizeVolunteerMessage(customConfig = {}) {
    const defaultConfig = {
        thanksTitle: 'Welcome to Our Volunteer Team!',
        thankMessage: 'Thank you for applying as a volunteer. We appreciate your commitment to making this event successful.',
        statusText: 'Pending Approval',
        nextStepsText: 'The event organizer will review your application and send you a confirmation email within 1-2 days.',
        dashboardText: 'You can view all your volunteer opportunities and track your volunteer journey in your dashboard.',
        successToastMessage: 'Thank you for applying! Your volunteer application has been submitted successfully.'
    };
    
    return { ...defaultConfig, ...customConfig };
}

// Export functions for global use
window.displayVolunteerInvolvement = displayVolunteerInvolvement;
window.showToast = showToast;
window.customizeVolunteerMessage = customizeVolunteerMessage;
