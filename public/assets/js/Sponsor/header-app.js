document.addEventListener('DOMContentLoaded', function() {
    loadSponsorData();
    loadNotifications();
    setupEventListeners();
});

// Sample data for sponsor dashboard
const sponsorData = {
    companyName: 'Company',
    contactPerson: 'John Smith',
    email: 'john@techcorp.com',
    totalSponsorships: 8,
    pendingRequests: 5,
    totalInvestment: 4200,
    avatar: '/unipulse/public/assets/images/default-avatar.png'
};

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

// Load sponsor data
function loadSponsorData() {
    document.getElementById('username').textContent = sponsorData.companyName;
    //document.getElementById('welcomeUsername').textContent = sponsorData.companyName;
    document.getElementById('userRole').textContent = 'Sponsor';
    //document.getElementById('totalSponsorships').textContent = sponsorData.totalSponsorships;
    //document.getElementById('pendingRequests').textContent = sponsorData.pendingRequests;
    //document.getElementById('totalInvestment').textContent = `LKR ${sponsorData.totalInvestment}`;
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