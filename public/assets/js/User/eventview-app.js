// Sample events data (same as events-app.js for consistency)
const eventsData = [
    {
        id: 1,
        title: "Tech Conference 2025",
        description: "Annual technology conference featuring latest innovations in AI, blockchain, and cloud computing. Join industry experts and students for networking and learning opportunities. This conference will cover cutting-edge technologies and their practical applications in various industries.",
        category: "technology",
        university: "university-of-moratuwa",
        universityName: "University of Moratuwa",
        status: "upcoming",
        date: "2025-09-15",
        time: "09:00 AM",
        location: "Main Auditorium, UOM",
        organizer: "Computer Society",
        organizerEmail: "computersociety@uom.lk",
        participants: 250,
        maxParticipants: 300,
        image: null,
        schedule: [
            { time: "09:00 AM", activity: "Registration & Welcome Coffee" },
            { time: "10:00 AM", activity: "Keynote: Future of AI" },
            { time: "11:30 AM", activity: "Workshop: Blockchain Basics" },
            { time: "01:00 PM", activity: "Lunch Break" },
            { time: "02:00 PM", activity: "Panel: Cloud Computing Trends" },
            { time: "04:00 PM", activity: "Networking Session" },
            { time: "05:00 PM", activity: "Closing Remarks" }
        ],
        requirements: [
            "Laptop with internet connection",
            "Basic programming knowledge recommended",
            "Student ID for registration",
            "Notebook for taking notes"
        ]
    },
    {
        id: 2,
        title: "Inter-University Cricket Championship",
        description: "Annual cricket tournament between top universities in Sri Lanka. Support your university team in this exciting championship that brings together the best cricket talent from across the country.",
        category: "sports",
        university: "university-of-peradeniya",
        universityName: "University of Peradeniya",
        status: "ongoing",
        date: "2025-08-25",
        time: "08:00 AM",
        location: "University Cricket Ground",
        organizer: "Sports Club",
        organizerEmail: "sports@pdn.ac.lk",
        participants: 150,
        maxParticipants: 500,
        image: null,
        schedule: [
            { time: "08:00 AM", activity: "Team Registration" },
            { time: "09:00 AM", activity: "Opening Ceremony" },
            { time: "10:00 AM", activity: "First Match: UOM vs UOC" },
            { time: "02:00 PM", activity: "Second Match: UOP vs UOK" },
            { time: "06:00 PM", activity: "Day 1 Wrap-up" }
        ],
        requirements: [
            "University sports pass",
            "Comfortable sports attire recommended",
            "Sun protection (hat, sunscreen)",
            "Water bottle"
        ]
    },
    {
        id: 3,
        title: "Cultural Night 2025",
        description: "Celebrate diverse cultures with traditional dances, music performances, and cultural exhibitions from different communities. Experience the rich cultural heritage of Sri Lanka and other nations.",
        category: "cultural",
        university: "university-of-colombo",
        universityName: "University of Colombo",
        status: "upcoming",
        date: "2025-09-20",
        time: "06:00 PM",
        location: "Arts Theatre",
        organizer: "Cultural Society",
        organizerEmail: "cultural@cmb.ac.lk",
        participants: 180,
        maxParticipants: 400,
        image: null,
        schedule: [
            { time: "06:00 PM", activity: "Welcome & Cultural Exhibition" },
            { time: "06:30 PM", activity: "Traditional Dance Performances" },
            { time: "07:30 PM", activity: "Music & Song Presentations" },
            { time: "08:30 PM", activity: "Cultural Food Festival" },
            { time: "09:30 PM", activity: "Grand Finale & Awards" },
            { time: "10:00 PM", activity: "Closing Ceremony" }
        ],
        requirements: [
            "Formal or traditional attire preferred",
            "Camera for memorable moments",
            "Appetite for cultural foods",
            "Open mind for cultural exchange"
        ]
    }
];

let currentEvent = null;

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadEventDetails();
});

// Get event ID from URL parameters
function getEventIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

// Load event details
function loadEventDetails() {
    const eventId = getEventIdFromURL();
    
    if (!eventId) {
        showError();
        return;
    }

    // Show loading state
    showLoading();

    // Simulate API call delay
    setTimeout(() => {
        const event = eventsData.find(e => e.id == eventId);
        
        if (event) {
            currentEvent = event;
            displayEventDetails(event);
            loadSimilarEvents(event);
            hideLoading();
            showEventContainer();
        } else {
            hideLoading();
            showError();
        }
    }, 1000);
}

// Display event details
function displayEventDetails(event) {
    // Basic event info
    document.getElementById('eventCategory').textContent = capitalizeFirstLetter(event.category);
    document.getElementById('eventStatus').textContent = event.status;
    document.getElementById('eventTitle').textContent = event.title;
    document.getElementById('eventSummary').textContent = event.description.substring(0, 150) + '...';
    
    // Event details grid
    document.getElementById('eventDateTime').textContent = `${formatDate(event.date)} at ${event.time}`;
    document.getElementById('eventLocation').textContent = event.location;
    document.getElementById('eventUniversity').textContent = event.universityName;
    document.getElementById('eventParticipants').textContent = `${event.participants}/${event.maxParticipants}`;
    
    // Full description
    document.getElementById('eventDescription').textContent = event.description;
    
    // Schedule
    displaySchedule(event.schedule);
    
    // Requirements
    displayRequirements(event.requirements);
    
    // Organizer info
    document.getElementById('organizerName').textContent = event.organizer;
    
    // Statistics
    document.getElementById('totalParticipants').textContent = event.participants;
    document.getElementById('availableSpots').textContent = event.maxParticipants - event.participants;
    
    // Participation percentage
    const percentage = Math.round((event.participants / event.maxParticipants) * 100);
    document.getElementById('participationPercentage').textContent = `${percentage}%`;
    document.getElementById('participationFill').style.width = `${percentage}%`;
    
    // Set event link for sharing
    document.getElementById('eventLink').value = window.location.href;
    
    // Update status styling
    updateStatusStyling(event.status);
}

// Display event schedule
function displaySchedule(schedule) {
    const scheduleContainer = document.getElementById('eventSchedule');
    scheduleContainer.innerHTML = '';
    
    schedule.forEach(item => {
        const scheduleItem = document.createElement('div');
        scheduleItem.className = 'schedule-item';
        scheduleItem.innerHTML = `
            <span class="time">${item.time}</span>
            <span class="activity">${item.activity}</span>
        `;
        scheduleContainer.appendChild(scheduleItem);
    });
}

// Display event requirements
function displayRequirements(requirements) {
    const requirementsContainer = document.getElementById('eventRequirements');
    const requirementsList = document.createElement('ul');
    requirementsList.className = 'requirements-list';
    
    requirements.forEach(requirement => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `
            <i class="fas fa-check"></i>
            <span>${requirement}</span>
        `;
        requirementsList.appendChild(listItem);
    });
    
    requirementsContainer.innerHTML = '';
    requirementsContainer.appendChild(requirementsList);
}

// Load similar events
function loadSimilarEvents(currentEvent) {
    const similarEventsContainer = document.getElementById('similarEvents');
    const similarEvents = eventsData.filter(event => 
        event.id !== currentEvent.id && 
        (event.category === currentEvent.category || event.university === currentEvent.university)
    ).slice(0, 3);
    
    if (similarEvents.length === 0) {
        similarEventsContainer.innerHTML = '<p>No similar events found.</p>';
        return;
    }
    
    similarEventsContainer.innerHTML = '';
    similarEvents.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'similar-event-card';
        eventCard.onclick = () => viewEvent(event.id);
        eventCard.innerHTML = `
            <div class="similar-event-info">
                <h5>${event.title}</h5>
                <p class="similar-event-date">${formatDate(event.date)}</p>
                <span class="similar-event-category">${capitalizeFirstLetter(event.category)}</span>
            </div>
        `;
        similarEventsContainer.appendChild(eventCard);
    });
}

// Update status styling
function updateStatusStyling(status) {
    const statusElement = document.getElementById('eventStatus');
    statusElement.className = `event-status ${status}`;
    
    const joinBtn = document.getElementById('joinBtn');
    if (status === 'completed' || status === 'cancelled') {
        joinBtn.disabled = true;
        joinBtn.innerHTML = '<i class="fas fa-calendar-times"></i> Event Ended';
    } else if (currentEvent && currentEvent.participants >= currentEvent.maxParticipants) {
        joinBtn.disabled = true;
        joinBtn.innerHTML = '<i class="fas fa-users"></i> Event Full';
    }
}

// Navigation functions
function viewEvent(eventId) {
    window.location.href = `/unipulse/public/user/eventview?id=${eventId}`;
}

// Modal functions
function openJoinModal() {
    document.getElementById('joinModal').style.display = 'flex';
}

function closeJoinModal() {
    document.getElementById('joinModal').style.display = 'none';
}

function openShareModal() {
    document.getElementById('shareModal').style.display = 'flex';
}

function closeShareModal() {
    document.getElementById('shareModal').style.display = 'none';
}

// Event actions
function confirmJoinEvent() {
    const notes = document.getElementById('participantNotes').value;
    
    // Simulate joining event
    alert(`Successfully joined "${currentEvent.title}"!`);
    
    // Update UI
    currentEvent.participants++;
    document.getElementById('eventParticipants').textContent = `${currentEvent.participants}/${currentEvent.maxParticipants}`;
    document.getElementById('totalParticipants').textContent = currentEvent.participants;
    document.getElementById('availableSpots').textContent = currentEvent.maxParticipants - currentEvent.participants;
    
    // Update participation percentage
    const percentage = Math.round((currentEvent.participants / currentEvent.maxParticipants) * 100);
    document.getElementById('participationPercentage').textContent = `${percentage}%`;
    document.getElementById('participationFill').style.width = `${percentage}%`;
    
    closeJoinModal();
}

function contactOrganizer() {
    if (currentEvent && currentEvent.organizerEmail) {
        window.location.href = `mailto:${currentEvent.organizerEmail}?subject=Inquiry about ${currentEvent.title}`;
    } else {
        alert('Organizer contact information not available.');
    }
}

// Share functions
function shareViaFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
}

function shareViaTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Check out this event: ${currentEvent.title}`);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
}

function shareViaWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`Check out this event: ${currentEvent.title} - ${url}`);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}

function copyEventLink() {
    const eventLink = document.getElementById('eventLink');
    eventLink.select();
    document.execCommand('copy');
    alert('Event link copied to clipboard!');
}

// UI state management
function showLoading() {
    document.getElementById('loadingContainer').style.display = 'flex';
    document.getElementById('errorContainer').style.display = 'none';
    document.getElementById('eventContainer').style.display = 'none';
}

function hideLoading() {
    document.getElementById('loadingContainer').style.display = 'none';
}

function showError() {
    document.getElementById('errorContainer').style.display = 'flex';
    document.getElementById('eventContainer').style.display = 'none';
}

function showEventContainer() {
    document.getElementById('eventContainer').style.display = 'block';
}

// Event listeners
document.getElementById('joinBtn').addEventListener('click', openJoinModal);
document.getElementById('shareBtn').addEventListener('click', openShareModal);

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    const joinModal = document.getElementById('joinModal');
    const shareModal = document.getElementById('shareModal');
    
    if (event.target === joinModal) {
        closeJoinModal();
    }
    if (event.target === shareModal) {
        closeShareModal();
    }
});

// Utility functions
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
}
