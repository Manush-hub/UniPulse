// Content Moderation JavaScript
console.log('Content Moderation app loaded');

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeContentModeration();
    loadPendingEvents();
    setupContentModerationListeners();
});

function initializeContentModeration() {
    console.log('Initializing content moderation...');
    updateStats();
}

function loadPendingEvents() {
    const eventsList = document.getElementById('eventsList');
    if (!eventsList) {
        console.log('Events list element not found');
        return;
    }
    
    // Show loading state
    eventsList.innerHTML = '<div class="loading">Loading pending events...</div>';
    
    // Fetch pending events from backend
    fetch('/unipulse/public/moderator/contentmoderation/getPendingEvents')
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch pending events');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.events) {
                displayPendingEvents(data.events);
                updateStats(data.events.length);
            } else {
                eventsList.innerHTML = '<div class="no-data">No pending events found</div>';
                updateStats(0);
            }
        })
        .catch(error => {
            console.error('Error loading pending events:', error);
            eventsList.innerHTML = '<div class="no-data">Failed to load pending events</div>';
            updateStats(0);
        });
}

function displayPendingEvents(events) {
    const eventsList = document.getElementById('eventsList');
    if (!eventsList) return;
    
    // Clear existing content
    eventsList.innerHTML = '';
    
    if (events.length === 0) {
        eventsList.innerHTML = '<div class="no-data">No pending events</div>';
        return;
    }
    
    // Load events
    events.forEach(event => {
        const eventElement = createEventElement(event);
        eventsList.appendChild(eventElement);
    });
    
    console.log('Loaded', events.length, 'pending events');
}

function createEventElement(event) {
    const eventDiv = document.createElement('div');
    eventDiv.className = 'review-item';
    eventDiv.setAttribute('data-event-id', event.id);
    
    eventDiv.innerHTML = `
        <div class="review-info">
            <div class="review-title">${event.title}</div>
            <div class="review-meta">
                <span class="review-organizer">
                    <i class="fas fa-user"></i>
                    ${event.organizer}
                </span>
                <span class="review-category">
                    <i class="fas fa-tag"></i>
                    ${event.category}
                </span>
                <span class="review-date">
                    <i class="fas fa-calendar"></i>
                    Submitted: ${event.submitted}
                </span>
            </div>
            <div class="event-description">
                ${event.description}
            </div>
        </div>
        <div class="review-actions">
            <button class="review-btn view" onclick="viewEventDetails(${event.id})">
                <i class="fas fa-eye"></i>
                View
            </button>
            <button class="review-btn approve" onclick="approveEvent(${event.id})">
                <i class="fas fa-check"></i>
                Approve
            </button>
            <button class="review-btn reject" onclick="rejectEvent(${event.id})">
                <i class="fas fa-times"></i>
                Reject
            </button>
        </div>
    `;
    
    return eventDiv;
}

function setupContentModerationListeners() {
    // Filter buttons
    const filterButtons = document.querySelectorAll('.btn[onclick*="filter"]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('onclick').match(/filterEvents\('(.+)'\)/)[1];
            filterEvents(filter);
        });
    });
}

function updateStats(pendingCount = 0) {
    // Update various stat elements
    const pendingElements = document.querySelectorAll('#pendingEvents, #pendingCount');
    pendingElements.forEach(el => {
        if (el.id === 'pendingCount') {
            el.textContent = `${pendingCount} events`;
        } else {
            el.textContent = pendingCount;
        }
    });
}

function filterEvents(filter) {
    console.log('Filtering events by:', filter);
    const eventItems = document.querySelectorAll('.review-item');
    
    eventItems.forEach(item => {
        if (filter === 'all') {
            item.style.display = 'flex';
        } else if (filter === 'pending') {
            // Show only pending items (in this case, all are pending)
            item.style.display = 'flex';
        }
    });
}

function viewEventDetails(eventId) {
    console.log('Viewing event details for ID:', eventId);
    
    // Fetch event details from backend
    fetch(`/unipulse/public/moderator/contentmoderation/getEventDetails/${eventId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch event details');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.event) {
                showEventModal(data.event);
            } else {
                showNotification('Failed to load event details', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading event details:', error);
            showNotification('Failed to load event details', 'error');
        });
}

function approveEvent(eventId) {
    console.log('Approving event ID:', eventId);
    
    if (confirm('Are you sure you want to approve this event?')) {
        // Make API call to approve event
        fetch(`/unipulse/public/moderator/contentmoderation/approve/${eventId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Event approved successfully!', 'success');
                removeEventFromList(eventId);
                loadPendingEvents(); // Reload events to update count
            } else {
                showNotification(data.message || 'Failed to approve event', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while approving the event', 'error');
        });
    }
}

function rejectEvent(eventId) {
    console.log('Rejecting event ID:', eventId);
    
    const reason = prompt('Please provide a reason for rejection (optional):');
    if (reason === null) return; // User cancelled
    
    // Make API call to reject event
    const formData = new FormData();
    if (reason) {
        formData.append('reason', reason);
    }
    
    fetch(`/unipulse/public/moderator/contentmoderation/reject/${eventId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Event rejected successfully!', 'success');
            removeEventFromList(eventId);
            loadPendingEvents(); // Reload events to update count
        } else {
            showNotification(data.message || 'Failed to reject event', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while rejecting the event', 'error');
    });
}

function removeEventFromList(eventId) {
    const eventElement = document.querySelector(`[data-event-id="${eventId}"]`);
    if (eventElement) {
        eventElement.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        eventElement.style.opacity = '0';
        eventElement.style.transform = 'scale(0.95)';
        setTimeout(() => {
            eventElement.remove();
        }, 300);
    }
}

function showEventModal(event) {
    const modal = document.getElementById('eventModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    if (modal && modalTitle && modalBody) {
        modalTitle.textContent = event.title;
        modalBody.innerHTML = `
            <div class="event-details">
                <h4>Event Information</h4>
                <p><strong>Organizer:</strong> ${event.organizer}</p>
                <p><strong>Category:</strong> ${event.category}</p>
                <p><strong>Submitted:</strong> ${event.submitted}</p>
                <p><strong>Description:</strong></p>
                <p>${event.description}</p>
                <div class="modal-actions">
                    <button class="btn btn-success" onclick="approveEvent(${event.id}); closeModal('eventModal');">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button class="btn btn-danger" onclick="rejectEvent(${event.id}); closeModal('eventModal');">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
            </div>
        `;
        modal.classList.add('show');
        modal.style.display = 'flex';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 4px;
        color: white;
        font-weight: 500;
        z-index: 1001;
        min-width: 300px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
        background-color: ${type === 'success' ? '#28a745' : '#dc3545'};
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Bulk actions
function selectAllEvents() {
    console.log('Selecting all events');
    // Implementation for bulk selection
}

function approveSelected() {
    console.log('Approving selected events');
    // Implementation for bulk approval
}

function rejectSelected() {
    console.log('Rejecting selected events');
    // Implementation for bulk rejection
}