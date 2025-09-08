// Initialize sponsor dashboard on page load
document.addEventListener('DOMContentLoaded', function () {
    initializeSponsorDashboard();
    loadSponsorData();
    loadSponsorshipRequests();
    loadActiveSponsorships();
    loadNotifications();
    setupEventListeners();
});

// Sample data for sponsor dashboard
const sponsorData = {
    companyName: 'TechCorp Ltd',
    contactPerson: 'John Smith',
    email: 'john@techcorp.com',
    totalSponsorships: 8,
    pendingRequests: 5,
    totalInvestment: 4200,
    avatar: '/unipulse/public/assets/images/default-avatar.png'
};

const sponsorshipRequests = [
    {
        id: 1,
        event: 'Annual Tech Symposium',
        organizer: 'Computer Science Society',
        date: '2025-09-20',
        amount: 1000,
        status: 'pending'
    },
    {
        id: 2,
        event: 'Cultural Festival',
        organizer: 'Arts Club',
        date: '2025-10-05',
        amount: 750,
        status: 'pending'
    },
    {
        id: 3,
        event: 'Sports Tournament',
        organizer: 'Sports Department',
        date: '2025-09-15',
        amount: 1500,
        status: 'pending'
    }
];

const activeSponsorships = [
    {
        id: 1,
        event: 'Hackathon 2025',
        organizer: 'Engineering Faculty',
        startDate: '2025-08-10',
        endDate: '2025-08-12',
        investment: 2000,
        status: 'active',
        audienceReach: 12500,
        engagementRate: 5.2,
        roi: 3.5
    },
    {
        id: 2,
        event: 'Business Summit',
        organizer: 'Business School',
        startDate: '2025-07-15',
        endDate: '2025-07-17',
        investment: 1500,
        status: 'active',
        audienceReach: 8600,
        engagementRate: 4.1,
        roi: 2.8
    }
];

const notifications = [
    {
        id: 1,
        title: 'New Sponsorship Request',
        message: 'Arts Club requested sponsorship for Cultural Festival',
        time: '2 hours ago',
        unread: true,
        type: 'request'
    },
    {
        id: 2,
        event: 'Event Update',
        message: 'Hackathon 2025 date has been changed to August 15-17',
        time: '1 day ago',
        unread: true,
        type: 'update'
    },
    {
        id: 3,
        title: 'Payment Received',
        message: 'Your sponsorship payment for Business Summit has been processed',
        time: '3 days ago',
        unread: false,
        type: 'payment'
    }
];

// Initialize sponsor dashboard
function initializeSponsorDashboard() {
    updateDateTime();
    setInterval(updateDateTime, 60000); // Update every minute

    setupScrollAnimations();
}

// Load sponsor data
function loadSponsorData() {
    document.getElementById('username').textContent = sponsorData.companyName;
    document.getElementById('welcomeUsername').textContent = sponsorData.companyName;
    document.getElementById('userRole').textContent = 'Sponsor';
    document.getElementById('totalSponsorships').textContent = sponsorData.totalSponsorships;
    document.getElementById('pendingRequests').textContent = sponsorData.pendingRequests;
    document.getElementById('totalInvestment').textContent = `LKR ${sponsorData.totalInvestment}`;
}

// Load sponsorship requests
function loadSponsorshipRequests() {
    const table = document.getElementById('requestsTable');
    table.innerHTML = '';

    // Add header row
    const headerRow = document.createElement('div');
    headerRow.className = 'request-row header';
    headerRow.innerHTML = `
        <div>Event & Organizer</div>
        <div>Date</div>
        <div>Amount</div>
        <div>Actions</div>
    `;
    table.appendChild(headerRow);

    // Add request rows
    sponsorshipRequests.forEach(request => {
        const requestRow = createRequestRow(request);
        table.appendChild(requestRow);
    });
}

// Create request row
function createRequestRow(request) {
    const row = document.createElement('div');
    row.className = 'request-row';
    row.onclick = () => viewRequestDetails(request.id);

    row.innerHTML = `
        <div>
            <div class="request-event">${request.event}</div>
            <div class="request-organizer">${request.organizer}</div>
        </div>
        <div class="request-date">${formatDate(request.date)}</div>
    <div class="request-amount">LKR ${request.amount}</div>
        <div class="request-actions">
            <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); acceptRequest(${request.id})">Accept</button>
            <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); rejectRequest(${request.id})">Reject</button>
        </div>
    `;

    return row;
}

// Load active sponsorships
function loadActiveSponsorships() {
    const grid = document.getElementById('sponsorshipsGrid');
    grid.innerHTML = '';

    activeSponsorships.forEach(sponsorship => {
        const card = createSponsorshipCard(sponsorship);
        grid.appendChild(card);
    });
}

// Create sponsorship card
function createSponsorshipCard(sponsorship) {
    const card = document.createElement('div');
    card.className = 'sponsorship-card';
    card.onclick = () => viewSponsorshipDetails(sponsorship.id);

    const statusClass = `status-${sponsorship.status}`;

    card.innerHTML = `
        <div class="sponsorship-header">
            <h3 class="sponsorship-title">${sponsorship.event}</h3>
            <div class="sponsorship-organizer">${sponsorship.organizer}</div>
            <div class="sponsorship-badge ${statusClass}">${sponsorship.status}</div>
        </div>
        <div class="sponsorship-content">
            <div class="sponsorship-details">
                <div class="sponsorship-detail">
                    <span class="detail-label">Investment</span>
                    <span class="detail-value">LKR ${sponsorship.investment}</span>
                </div>
                <div class="sponsorship-detail">
                    <span class="detail-label">Duration</span>
                    <span class="detail-value">${formatDate(sponsorship.startDate)} - ${formatDate(sponsorship.endDate)}</span>
                </div>
                <div class="sponsorship-detail">
                    <span class="detail-label">Audience Reach</span>
                    <span class="detail-value">${sponsorship.audienceReach.toLocaleString()}</span>
                </div>
                <div class="sponsorship-detail">
                    <span class="detail-label">ROI</span>
                    <span class="detail-value">${sponsorship.roi}x</span>
                </div>
            </div>
            <div class="sponsorship-actions">
                <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); viewPerformance(${sponsorship.id})">View Performance</button>
                <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); contactOrganizer(${sponsorship.id})">Contact Organizer</button>
            </div>
        </div>
    `;

    return card;
}

// Load notifications
function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');

    notificationList.innerHTML = '';

    const unreadCount = notifications.filter(n => n.unread).length;
    notificationBadge.textContent = unreadCount;
    notificationBadge.style.display = unreadCount > 0 ? 'flex' : 'none';

    notifications.forEach(notification => {
        const notificationItem = createNotificationItem(notification);
        notificationList.appendChild(notificationItem);
    });
}

// Create notification item
function createNotificationItem(notification) {
    const item = document.createElement('div');
    item.className = `notification-item ${notification.unread ? 'unread' : ''}`;
    item.onclick = () => markNotificationAsRead(notification.id);

    item.innerHTML = `
        <div class="notification-content">
            <h4>${notification.title}</h4>
            <p>${notification.message}</p>
            <div class="notification-time">${notification.time}</div>
        </div>
    `;

    return item;
}

// Setup event listeners
function setupEventListeners() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.notifications')) {
            document.getElementById('notificationDropdown').classList.remove('show');
        }
        if (!e.target.closest('.user-menu')) {
            document.getElementById('userDropdown').classList.remove('show');
        }
    });
}

// Toggle notifications dropdown
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('show');
}

// Toggle user menu dropdown
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Mark notification as read
function markNotificationAsRead(notificationId) {
    const notification = notifications.find(n => n.id === notificationId);
    if (notification) {
        notification.unread = false;
        loadNotifications();
    }
}

// Mark all notifications as read
function markAllAsRead() {
    notifications.forEach(n => n.unread = false);
    loadNotifications();
}

// Sponsor-specific functions
function acceptRequest(requestId) {
    console.log(`Accepting request ${requestId}`);
    // API call to accept sponsorship request
    alert(`Sponsorship request ${requestId} accepted!`);
    // Refresh the requests list
    loadSponsorshipRequests();
}

function rejectRequest(requestId) {
    console.log(`Rejecting request ${requestId}`);
    // API call to reject sponsorship request
    if (confirm('Are you sure you want to reject this sponsorship request?')) {
        alert(`Sponsorship request ${requestId} rejected.`);
        // Refresh the requests list
        loadSponsorshipRequests();
    }
}

function viewRequestDetails(requestId) {
    window.location.href = `sponsorship-details.html?id=${requestId}`;
}

function viewSponsorshipDetails(sponsorshipId) {
    window.location.href = `sponsorship-management.html?id=${sponsorshipId}`;
}

function viewPerformance(sponsorshipId) {
    window.location.href = `analytics.html?id=${sponsorshipId}`;
}

function contactOrganizer(sponsorshipId) {
    window.location.href = `messages.html?sponsorship=${sponsorshipId}`;
}

// Update date and time
function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };

    // You can add a date/time display element if needed
    console.log('Current time:', now.toLocaleDateString('en-US', options));
}

// Format date
function formatDate(dateString) {
    const options = {
        month: 'short',
        day: 'numeric'
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Setup scroll animations
function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe sections for animation
    document.querySelectorAll('.action-card, .sponsorship-card, .performance-card').forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });
}

// Utility functions for API integration
async function fetchSponsorData() {
    try {
        // Replace with actual API call
        const response = await fetch('/api/sponsor/dashboard');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching sponsor data:', error);
        return sponsorData; // Fallback to sample data
    }
}

async function fetchSponsorshipRequests() {
    try {
        // Replace with actual API call
        const response = await fetch('/api/sponsor/requests');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching sponsorship requests:', error);
        return sponsorshipRequests;
    }
}

async function fetchActiveSponsorships() {
    try {
        // Replace with actual API call
        const response = await fetch('/api/sponsor/active-sponsorships');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching active sponsorships:', error);
        return activeSponsorships;
    }
}