// Simple test to verify JavaScript is working
console.log('Dashboard app.js loaded successfully');

// Test data
const testActivity = [
    {
        id: 1,
        type: 'approval',
        title: 'Event Approved',
        description: 'Tech Conference 2025 approved',
        time: '10 minutes ago',
        icon: 'check-circle'
    },
    {
        id: 2,
        type: 'rejection',
        title: 'Event Rejected',
        description: 'Inappropriate content in "Summer Party"',
        time: '45 minutes ago',
        icon: 'times-circle'
    }
];

const testReports = [
    {
        id: 1,
        content: 'Tech Workshop 2025',
        type: 'inappropriate',
        submitted: '2 hours ago',
        status: 'pending'
    },
    {
        id: 2,
        content: 'User comment on Cultural Festival',
        type: 'spam',
        submitted: '5 hours ago',
        status: 'in-progress'
    }
];

// Simple load functions
function loadTestActivity() {
    console.log('Loading test activity...');
    const activityList = document.getElementById('activityList');
    if (!activityList) {
        console.error('Activity list not found');
        return;
    }
    
    activityList.innerHTML = '';
    
    testActivity.forEach(activity => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><i class="fas fa-${activity.icon}"></i> ${activity.title}</td>
            <td>${activity.type}</td>
            <td>${activity.description}</td>
            <td>${activity.time}</td>
            <td>Completed</td>
        `;
        activityList.appendChild(row);
    });
    
    console.log('Test activity loaded');
}

function loadTestReports() {
    console.log('Loading test reports...');
    const reportsTable = document.getElementById('reportsTableBody');
    if (!reportsTable) {
        console.error('Reports table not found');
        return;
    }
    
    reportsTable.innerHTML = '';
    
    testReports.forEach(report => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${report.content}</td>
            <td>${report.type}</td>
            <td>${report.submitted}</td>
            <td>${report.status}</td>
            <td>
                <button onclick="alert('View report ${report.id}')">View</button>
                <button onclick="alert('Resolve report ${report.id}')">Resolve</button>
            </td>
        `;
        reportsTable.appendChild(row);
    });
    
    console.log('Test reports loaded');
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing test...');
    
    setTimeout(() => {
        loadTestActivity();
        loadTestReports();
    }, 500);
});