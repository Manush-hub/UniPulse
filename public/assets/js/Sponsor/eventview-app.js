// Get server data or use fallback
let currentEvent = window.serverData?.event || null;
let similarEvents = window.serverData?.similarEvents || [];
const hasError = window.serverData?.error || null;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/user/eventview/getEvent';
const joinEndpoint = window.serverData?.joinEndpoint || '/unipulse/public/user/eventview/joinEvent';
let isUserRegistered = window.serverData?.isRegistered || false;

// Initialize the page
document.addEventListener('DOMContentLoaded', function () {
    loadEventDetails();
});

// Get event ID from URL parameters
function getEventIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

// Load event details
function loadEventDetails() {
    if (hasError) {
        hideLoading();
        showError();
        return;
    }

    if (currentEvent) {
        // Use server data directly
        displayEventDetails(currentEvent);
        loadSimilarEvents(similarEvents);
        hideLoading();
        showEventContainer();
    } else {
        // Fallback to AJAX if no server data
        const eventId = getEventIdFromURL();

        if (!eventId) {
            showError();
            return;
        }

        showLoading();

        fetch(`${apiEndpoint}?id=${eventId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentEvent = data.event;
                    similarEvents = data.similarEvents || [];
                    displayEventDetails(currentEvent);
                    loadSimilarEvents(similarEvents);
                    hideLoading();
                    showEventContainer();
                } else {
                    hideLoading();
                    showError();
                }
            })
            .catch(error => {
                console.error('Error loading event:', error);
                hideLoading();
                showError();
            });
    }
}

// Display event details
function displayEventDetails(event) {
    // Handle different field names from database vs JavaScript
    const universityName = event.university_name || event.universityName;
    const maxParticipants = event.max_participants || event.maxParticipants;
    const currentParticipants = event.current_participants || event.currentParticipants || 0;
    const organizerEmail = event.organizer_email || event.organizerEmail;
    const targetAudience = event.target_audience || event.targetAudience;
    const ticketType = event.ticket_type || event.ticketType;
    const imageUrl = event.image_url || event.imageUrl || event.cover_image || event.image;

    // Display hero image if available
    if (imageUrl) {
        const heroImageContainer = document.getElementById('heroImageContainer');
        const heroImage = document.getElementById('heroImage');
        if (heroImageContainer && heroImage) {
            // Build correct image path
            let imagePath = '';
            if (imageUrl.startsWith('http')) {
                imagePath = imageUrl;
            } else if (imageUrl.startsWith('/unipulse/')) {
                imagePath = imageUrl;
            } else if (imageUrl.startsWith('/')) {
                imagePath = imageUrl;
            } else {
                // Relative path from database - add /unipulse/public/ prefix
                imagePath = `/unipulse/public/${imageUrl}`;
            }

            console.log('Image URL from DB:', imageUrl);
            console.log('Constructed image path:', imagePath);

            heroImage.src = imagePath;
            heroImage.alt = event.title + ' Cover Image';
            heroImageContainer.style.display = 'block';

            // Add error handler
            heroImage.onerror = function () {
                console.error('Failed to load image:', imagePath);
                heroImageContainer.style.display = 'none';
            };

            // Add load handler for debugging
            heroImage.onload = function () {
                console.log('Image loaded successfully:', imagePath);
            };
        }
    }

    // Basic event info
    const eventCategoryElement = document.getElementById('eventCategory');
    if (eventCategoryElement) {
        eventCategoryElement.textContent = capitalizeFirstLetter(event.category);
    }

    const eventStatusElement = document.getElementById('eventStatus');
    // Calculate event status dynamically based on event date
    const eventDate = event.event_date || event.date;
    const eventTime = event.event_time || event.time;
    if (eventStatusElement) {
        const calculatedStatus = getEventStatus(eventDate, eventTime, event.event_end_time);
        eventStatusElement.textContent = capitalizeFirstLetter(calculatedStatus);
        // Update the class for proper styling
        eventStatusElement.className = `event-status ${calculatedStatus}`;
    }

    const eventTitleElement = document.getElementById('eventTitle');
    if (eventTitleElement) {
        eventTitleElement.textContent = event.title;
    }

    // Event details grid
    const locationType = event.location_type || 'inside-university';

    const eventDateTimeElement = document.getElementById('eventDateTime');
    if (eventDateTimeElement) {
        eventDateTimeElement.textContent = `${formatDate(eventDate)} at ${eventTime}`;
    }

    // Display location fields based on location type
    if (locationType === 'outside-university') {
        // Outside university: show venue and city
        const venueName = event.venue_name || event.venueName || '';
        const city = event.city || '';
        let venueCity = '';

        if (venueName && city) {
            venueCity = `${venueName}, ${city}`;
        } else if (venueName) {
            venueCity = venueName;
        } else if (city) {
            venueCity = city;
        } else {
            venueCity = 'Location TBA';
        }

        const venueInfoElement = document.getElementById('venueInfo');
        const eventVenueCityElement = document.getElementById('eventVenueCity');
        if (venueInfoElement) venueInfoElement.style.display = 'flex';
        if (eventVenueCityElement) eventVenueCityElement.textContent = venueCity;

        // Hide inside university fields
        const universityInfoElement = document.getElementById('universityInfo');
        const facultyInfoElement = document.getElementById('facultyInfo');
        const exactLocationInfoElement = document.getElementById('exactLocationInfo');
        if (universityInfoElement) universityInfoElement.style.display = 'none';
        if (facultyInfoElement) facultyInfoElement.style.display = 'none';
        if (exactLocationInfoElement) exactLocationInfoElement.style.display = 'none';
    } else {
        // Inside university: show university, faculty, and exact location
        const universityInfoElement = document.getElementById('universityInfo');
        const eventUniversityElement = document.getElementById('eventUniversity');
        if (universityInfoElement) universityInfoElement.style.display = 'flex';
        if (eventUniversityElement) eventUniversityElement.textContent = universityName;

        // Show faculty/department if available
        if (event.faculty_department) {
            const facultyInfoElement = document.getElementById('facultyInfo');
            const eventFacultyElement = document.getElementById('eventFaculty');
            if (facultyInfoElement) facultyInfoElement.style.display = 'flex';
            if (eventFacultyElement) eventFacultyElement.textContent = event.faculty_department;
        }

        const exactLocationInfoElement = document.getElementById('exactLocationInfo');
        const eventLocationElement = document.getElementById('eventLocation');
        if (exactLocationInfoElement) exactLocationInfoElement.style.display = 'flex';
        if (eventLocationElement) eventLocationElement.textContent = event.location;

        // Hide outside university field
        const venueInfoElement = document.getElementById('venueInfo');
        if (venueInfoElement) venueInfoElement.style.display = 'none';
    }

    // Show participants info only if max_participants is set
    const participantsInfoElement = document.getElementById('participantsInfo');
    const eventParticipantsElement = document.getElementById('eventParticipants');
    if (maxParticipants !== null && maxParticipants !== undefined) {
        if (participantsInfoElement) {
            participantsInfoElement.style.display = 'flex';
        }
        if (eventParticipantsElement) {
            eventParticipantsElement.textContent = `${currentParticipants}/${maxParticipants}`;
        }
    } else {
        if (participantsInfoElement) {
            participantsInfoElement.style.display = 'none';
        }
    }

    // Target audience
    const eventAudienceElement = document.getElementById('eventAudience');
    if (eventAudienceElement) {
        eventAudienceElement.textContent = formatAudience(targetAudience);
    }

    // Ticket type - Always show with appropriate display
    const ticketInfoElement = document.getElementById('ticketInfo');
    const eventTicketTypeElement = document.getElementById('eventTicketType');
    if (ticketInfoElement) ticketInfoElement.style.display = 'block';
    if (eventTicketTypeElement) {
        if (ticketType === 'free-all') {
            eventTicketTypeElement.innerHTML = '<span style="color: #10B981; font-weight: 600;">Free Event</span>';
        } else {
            eventTicketTypeElement.textContent = formatTicketType(ticketType);
        }
    }

    // Full description
    const eventDescriptionElement = document.getElementById('eventDescription');
    if (eventDescriptionElement) {
        eventDescriptionElement.textContent = event.description;
    }

    // Registration Period
    displayRegistrationPeriod(event);

    // Schedule - hide card if no schedule data
    const scheduleCard = document.getElementById('scheduleCard');
    if (scheduleCard) {
        if (event.schedule && Array.isArray(event.schedule) && event.schedule.length > 0) {
            displaySchedule(event.schedule);
            scheduleCard.style.display = 'block';
        } else {
            scheduleCard.style.display = 'none';
        }
    }

    // Requirements - hide card if no requirements
    const requirementsCard = document.getElementById('requirementsCard');
    if (requirementsCard) {
        if (event.requirements && Array.isArray(event.requirements) && event.requirements.length > 0) {
            displayRequirements(event.requirements);
            requirementsCard.style.display = 'block';
        } else {
            requirementsCard.style.display = 'none';
        }
    }

    // Location details
    displayLocationDetails(event);

    // Ticket details
    displayTicketDetails(event);

    // Custom fields - hide card if no custom fields
    const customFieldsCard = document.getElementById('customFieldsCard');
    if (customFieldsCard) {
        if (event.custom_fields && Array.isArray(event.custom_fields) && event.custom_fields.length > 0) {
            displayCustomFields(event.custom_fields);
        } else {
            customFieldsCard.style.display = 'none';
        }
    }

    const eventStatus = getEventStatus(event.event_date || event.date, event.event_time || event.time, event.event_end_time);
    if (eventStatus !== 'upcoming') {
        const volunteerCard = document.getElementById('volunteerCard');
        const volunteerInvolvementCard = document.getElementById('volunteerInvolvementCard');
        if (volunteerCard) {
            volunteerCard.style.display = 'none';
        }
        if (volunteerInvolvementCard) {
            volunteerInvolvementCard.style.display = 'none';
        }
    } else {

        // Volunteer information - hide card if not accepting volunteers
        const volunteerCard = document.getElementById('volunteerCard');
        if (volunteerCard) {
            if (event.needs_volunteers && event.needs_volunteers == 1) {
                displayVolunteerInfo(event);
            } else {
                volunteerCard.style.display = 'none';
            }
        }
    }

    // Donation information - hide card if not accepting donations
    const donationCard = document.getElementById('donationCard');
    if (donationCard) {
        if (eventStatus === 'upcoming' && event.accepts_donations && event.accepts_donations == 1) {
            donationCard.style.display = 'block';
        } else {
            donationCard.style.display = 'none';
        }
    }

    // Show volunteer/donation section wrapper if either card is available
    const volunteerDonationHeader = document.getElementById('volunteerDonationHeader');
    const volunteerDonationGrid = document.getElementById('volunteerDonationGrid');
    const hasVolunteer = eventStatus === 'upcoming' && event.needs_volunteers && event.needs_volunteers == 1;
    const hasDonation = eventStatus === 'upcoming' && event.accepts_donations && event.accepts_donations == 1;

    if (hasVolunteer || hasDonation) {
        if (volunteerDonationHeader) volunteerDonationHeader.style.display = 'block';
        if (volunteerDonationGrid) volunteerDonationGrid.style.display = 'grid';
    } else {
        if (volunteerDonationHeader) volunteerDonationHeader.style.display = 'none';
        if (volunteerDonationGrid) volunteerDonationGrid.style.display = 'none';
    }

    // Organizer info - use organizer_name from publisher profile for live updates
    const organizerNameElement = document.getElementById('organizerName');
    if (organizerNameElement) {
        organizerNameElement.textContent = event.organizer_name || event.organizer;
    }

    // Set organizer role if available, otherwise use default
    const organizerRoleElement = document.getElementById('organizerRole');
    if (organizerRoleElement) {
        organizerRoleElement.textContent = event.organizer_role || 'Event Organizer';
    }

    // Display organizer profile photo if available
    const organizerAvatar = document.getElementById('organizerAvatar');
    if (organizerAvatar) {
        if (event.organizer_photo) {
            organizerAvatar.innerHTML = `<img src="${event.organizer_photo}" alt="${event.organizer_name || event.organizer}" />`;
        } else {
            organizerAvatar.innerHTML = '<i class="fas fa-user-circle"></i>';
        }
    }

    // Store organizer email for contact function
    currentEvent.organizerEmail = organizerEmail;

    // Statistics - only show if max_participants is set
    const eventStatsCardElement = document.getElementById('eventStatsCard');
    if (maxParticipants !== null && maxParticipants !== undefined && eventStatsCardElement) {
        eventStatsCardElement.style.display = 'block';
        const totalParticipantsElement = document.getElementById('totalParticipants');
        const availableSpotsElement = document.getElementById('availableSpots');
        const participationPercentageElement = document.getElementById('participationPercentage');
        const participationFillElement = document.getElementById('participationFill');

        if (totalParticipantsElement) {
            totalParticipantsElement.textContent = currentParticipants;
        }
        if (availableSpotsElement) {
            availableSpotsElement.textContent = maxParticipants - currentParticipants;
        }

        // Participation percentage
        const percentage = maxParticipants > 0 ? Math.round((currentParticipants / maxParticipants) * 100) : 0;
        if (participationPercentageElement) {
            participationPercentageElement.textContent = `${percentage}%`;
        }
        if (participationFillElement) {
            participationFillElement.style.width = `${percentage}%`;
        }
    } else if (eventStatsCardElement) {
        eventStatsCardElement.style.display = 'none';
    }

    // Set event link for sharing
    const shareLinkElement = document.getElementById('shareLink');
    if (shareLinkElement) {
        shareLinkElement.value = window.location.href;
    }

    // Update status styling
    updateStatusStyling(event.status, currentParticipants, maxParticipants);

    // Display sponsorship packages for sponsors
    if (userRole === 'Sponsor') {
        displaySponsorshipPackages();
    }
}

// Display registration period
function displayRegistrationPeriod(event) {
    const registrationCard = document.getElementById('registrationPeriodCard');
    const registrationContainer = document.getElementById('registrationPeriod');

    if (!registrationCard || !registrationContainer) {
        return;
    }

    // Only show if registration dates exist
    if (event.registration_start_date && event.registration_end_date) {
        registrationCard.style.display = 'block';

        let html = '<div class="registration-info">';

        // Registration start
        html += '<div class="registration-item">';
        html += '<i class="fas fa-calendar-plus"></i>';
        html += '<div class="registration-details">';
        html += '<strong>Registration Opens:</strong> ';
        html += formatDate(event.registration_start_date);
        if (event.registration_start_time) {
            html += ` at ${event.registration_start_time}`;
        }
        html += '</div>';
        html += '</div>';

        // Registration end
        html += '<div class="registration-item">';
        html += '<i class="fas fa-calendar-times"></i>';
        html += '<div class="registration-details">';
        html += '<strong>Registration Closes:</strong> ';
        html += formatDate(event.registration_end_date);
        if (event.registration_end_time) {
            html += ` at ${event.registration_end_time}`;
        }
        html += '</div>';
        html += '</div>';

        // Registration limit
        if (event.registration_limit) {
            html += '<div class="registration-item">';
            html += '<i class="fas fa-users"></i>';
            html += '<div class="registration-details">';
            html += `<strong>Registration Limit:</strong> ${event.registration_limit} participants`;
            html += '</div>';
            html += '</div>';
        }

        html += '</div>';
        registrationContainer.innerHTML = html;
    } else {
        registrationCard.style.display = 'none';
    }
}

// Display event schedule
function displaySchedule(schedule) {
    const scheduleContainer = document.getElementById('eventSchedule');
    if (!scheduleContainer) {
        return;
    }
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
    if (!requirementsContainer) {
        return;
    }
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
function loadSimilarEvents(events) {
    const similarEventsContainer = document.getElementById('similarEvents');

    if (!similarEventsContainer) {
        return;
    }

    if (!events || events.length === 0) {
        similarEventsContainer.innerHTML = '<p>No similar events found.</p>';
        return;
    }

    similarEventsContainer.innerHTML = '';
    events.forEach(event => {
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
function updateStatusStyling(status, participants, maxParticipants) {
    const statusElement = document.getElementById('eventStatus');
    if (statusElement) {
        statusElement.className = `event-status ${status}`;
    }

    const joinBtn = document.getElementById('joinBtn');
    if (!joinBtn) {
        return;
    }

    if (isUserRegistered) {
        joinBtn.disabled = true;
        joinBtn.innerHTML = '<i class="fas fa-check"></i> Already Registered';
        joinBtn.style.cursor = 'not-allowed';
        joinBtn.style.opacity = '0.6';
    } else if (status === 'completed' || status === 'cancelled') {
        joinBtn.disabled = true;
        joinBtn.innerHTML = '<i class="fas fa-calendar-times"></i> Event Ended';
    } else if (maxParticipants !== null && maxParticipants !== undefined && participants >= maxParticipants) {
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

    if (!currentEvent) {
        alert('Event data not available');
        return;
    }

    // Show loading state
    const confirmBtn = document.querySelector('#joinModal .btn-primary');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Joining...';
    confirmBtn.disabled = true;

    // Make API call to join event
    const formData = new FormData();
    formData.append('id', currentEvent.id);
    formData.append('notes', notes);

    fetch(joinEndpoint, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Successfully joined "${currentEvent.title}"!`);

                // Mark user as registered
                isUserRegistered = true;

                // Update join button state
                const joinBtn = document.getElementById('joinBtn');
                if (joinBtn) {
                    joinBtn.innerHTML = '<i class="fas fa-check"></i> Already Registered';
                    joinBtn.classList.add('disabled');
                    joinBtn.style.cursor = 'not-allowed';
                    joinBtn.style.opacity = '0.6';
                    joinBtn.disabled = true;
                    joinBtn.removeEventListener('click', openJoinModal);
                }

                // Update UI with new participant count
                const newCurrentParticipants = data.current_participants || data.participants || 0;
                const maxParticipants = currentEvent.max_participants || currentEvent.maxParticipants;
                currentEvent.current_participants = newCurrentParticipants;

                // Update participant count in detail section if visible
                if (maxParticipants !== null && maxParticipants !== undefined) {
                    document.getElementById('eventParticipants').textContent = `${newCurrentParticipants}/${maxParticipants}`;
                    document.getElementById('totalParticipants').textContent = newCurrentParticipants;
                    document.getElementById('availableSpots').textContent = maxParticipants - newCurrentParticipants;

                    // Update participation percentage
                    const percentage = maxParticipants > 0 ? Math.round((newCurrentParticipants / maxParticipants) * 100) : 0;
                    document.getElementById('participationPercentage').textContent = `${percentage}%`;
                    document.getElementById('participationFill').style.width = `${percentage}%`;
                }

                closeJoinModal();
            } else if (data.alreadyRegistered) {
                // User is already registered
                alert('You have already registered for this event.');
                isUserRegistered = true;

                // Update join button state
                const joinBtn = document.getElementById('joinBtn');
                if (joinBtn) {
                    joinBtn.innerHTML = '<i class="fas fa-check"></i> Already Registered';
                    joinBtn.classList.add('disabled');
                    joinBtn.style.cursor = 'not-allowed';
                    joinBtn.style.opacity = '0.6';
                    joinBtn.disabled = true;
                    joinBtn.removeEventListener('click', openJoinModal);
                }

                closeJoinModal();
            } else {
                alert('Failed to join event: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error joining event:', error);
            alert('Failed to join event. Please try again.');
        })
        .finally(() => {
            // Reset button state
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        });
}

function contactOrganizer() {
    const organizerEmail = currentEvent?.organizerEmail || currentEvent?.organizer_email;
    if (organizerEmail) {
        window.location.href = `mailto:${organizerEmail}?subject=Inquiry about ${currentEvent.title}`;
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

// New helper functions for additional fields

function formatAudience(audience) {
    const audienceMap = {
        'university-students': 'University Students',
        'public-users': 'Public Users',
        'both': 'University Students & Public'
    };
    return audienceMap[audience] || audience;
}

function formatTicketType(ticketType) {
    const ticketMap = {
        'free-all': 'Free for All',
        'paid-all': 'Paid for All',
        'mixed': 'Free for Students, Paid for Others'
    };
    return ticketMap[ticketType] || ticketType;
}

function displayLocationDetails(event) {
    const locationType = event.location_type || 'inside-university';
    const universityName = event.university_name || event.universityName;
    let locationHTML = '';

    if (locationType === 'outside-university') {
        locationHTML = '';

        if (event.venue_name) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="location-content">
                        <strong>Venue</strong>
                        <span>${event.venue_name}</span>
                    </div>
                </div>
            `;
        }

        if (event.street_address) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-road"></i>
                    </div>
                    <div class="location-content">
                        <strong>Address</strong>
                        <span>${event.street_address}</span>
                    </div>
                </div>
            `;
        }

        if (event.city) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-city"></i>
                    </div>
                    <div class="location-content">
                        <strong>City</strong>
                        <span>${event.city}</span>
                    </div>
                </div>
            `;
        }

        if (event.district_province) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-map"></i>
                    </div>
                    <div class="location-content">
                        <strong>District/Province</strong>
                        <span>${event.district_province}</span>
                    </div>
                </div>
            `;
        }

        if (locationHTML) {
            const locationDetailsCard = document.getElementById('locationDetailsCard');
            const locationDetails = document.getElementById('locationDetails');
            if (locationDetailsCard && locationDetails) {
                locationDetailsCard.style.display = 'block';
                locationDetails.innerHTML = locationHTML;
            }
        }
    } else {
        // Inside university - show university, faculty/department, and exact location
        locationHTML = '';

        if (universityName) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="location-content">
                        <strong>UNIVERSITY</strong>
                        <span>${universityName}</span>
                    </div>
                </div>
            `;
        }

        if (event.faculty_department) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="location-content">
                        <strong>FACULTY/DEPARTMENT</strong>
                        <span>${event.faculty_department}</span>
                    </div>
                </div>
            `;
        }

        if (event.location) {
            locationHTML += `
                <div class="location-box">
                    <div class="location-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="location-content">
                        <strong>EXACT LOCATION</strong>
                        <span>${event.location}</span>
                    </div>
                </div>
            `;
        }

        // Only show if there's actual content
        if (universityName || event.faculty_department || event.location) {
            const locationDetailsCard = document.getElementById('locationDetailsCard');
            const locationDetails = document.getElementById('locationDetails');
            if (locationDetailsCard && locationDetails) {
                locationDetailsCard.style.display = 'block';
                locationDetails.innerHTML = locationHTML;
            }
        }
    }
}

function displayTicketDetails(event) {
    const ticketType = event.ticket_type || 'free-all';

    if (ticketType === 'free-all' || ticketType === 'free-students') {
        // Show free registration section
        displayRegistrationTicketing(event);
        return;
    }

    const ticketDetailsCard = document.getElementById('ticketDetailsCard');
    const ticketDetailsDiv = document.getElementById('ticketDetails');

    if (!ticketDetailsCard || !ticketDetailsDiv) {
        console.log('displayTicketDetails: ticketDetailsCard or ticketDetails element not found');
        return;
    }

    // For paid or mixed events
    let ticketHTML = '<div class="ticket-detail-item">';
    ticketHTML += `<div style="margin-bottom: 15px;"><strong>Ticket Type:</strong> <span style="color: #3b82f6; font-weight: 600;">${formatTicketType(ticketType)}</span></div>`;

    if (event.registration_start_date && event.registration_end_date) {
        ticketHTML += `<div style="margin-bottom: 10px;"><strong>Registration Period:</strong> ${formatDate(event.registration_start_date)} to ${formatDate(event.registration_end_date)}</div>`;
    }

    if (event.registration_limit) {
        ticketHTML += `<div style="margin-bottom: 15px;"><strong>Registration Limit:</strong> ${event.registration_limit} participants</div>`;
    }

    // Display ticket types and prices
    if (event.ticket_types) {
        let tickets = event.ticket_types;

        // Parse if it's a JSON string
        if (typeof tickets === 'string') {
            try {
                tickets = JSON.parse(tickets);
            } catch (e) {
                console.error('Error parsing ticket_types:', e);
                tickets = [];
            }
        }

        if (Array.isArray(tickets) && tickets.length > 0) {
            ticketHTML += '<div style="margin-top: 25px; margin-bottom: 20px;"><strong style="font-size: 22px; color: #1f2937; letter-spacing: 0.5px;">Available Tickets:</strong></div>';
            ticketHTML += '<div class="ticket-types-list" style="margin-top: 15px;">';

            tickets.forEach((ticket, index) => {
                // Calculate capacity percentage and determine colors
                const totalCapacity = parseInt(ticket.total_capacity || ticket.quantity);
                const available = parseInt(ticket.quantity);
                const sold = totalCapacity - available;
                const soldPercentage = totalCapacity > 0 ? ((sold / totalCapacity) * 100).toFixed(1) : '0.0';
                const availablePercentage = totalCapacity > 0 ? ((available / totalCapacity) * 100).toFixed(1) : '100.0';

                // Determine progress bar color based on availability
                let progressColor, progressBg, statusText;
                if (availablePercentage >= 50) {
                    progressColor = '#10B981';
                    progressBg = '#D1FAE5';
                    statusText = 'Good Availability';
                } else if (availablePercentage >= 25) {
                    progressColor = '#F59E0B';
                    progressBg = '#FEF3C7';
                    statusText = 'Selling Fast';
                } else if (availablePercentage > 0) {
                    progressColor = '#EF4444';
                    progressBg = '#FEE2E2';
                    statusText = 'Almost Sold Out';
                } else {
                    progressColor = '#6B7280';
                    progressBg = '#F3F4F6';
                    statusText = 'Sold Out';
                }

                ticketHTML += `
                    <div class="ticket-option" data-ticket-index="${index}" data-ticket-name="${ticket.name}" data-ticket-price="${ticket.price}" data-ticket-quantity="${ticket.quantity}" style="background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%); border: 3px solid #d1d5db; border-radius: 16px; padding: 24px; margin-bottom: 18px; transition: all 0.3s; box-shadow: 0 4px 8px rgba(0,0,0,0.08);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                            <div style="flex: 1;">
                                <div style="font-weight: 700; font-size: 24px; color: #1f2937; margin-bottom: 10px; letter-spacing: 0.5px;">${ticket.name}</div>
                                ${ticket.description ? `<div style="color: #6b7280; font-size: 16px; margin-bottom: 12px; line-height: 1.5;">${ticket.description}</div>` : ''}
                                
                                <!-- Capacity Indicator -->
                                <div style="margin-bottom: 12px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                        <span style="font-size: 13px; font-weight: 600; color: ${progressColor};">${statusText}</span>
                                        <span style="font-size: 13px; font-weight: 600; color: #6b7280;">${soldPercentage}% Sold</span>
                                    </div>
                                    <div style="width: 100%; height: 12px; background: ${progressBg}; border-radius: 6px; overflow: hidden; position: relative;">
                                        <div style="height: 100%; background: ${progressColor}; width: ${soldPercentage}%; transition: width 0.5s ease; border-radius: 6px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);"></div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-top: 4px; font-size: 12px; color: #9ca3af;">
                                        <span>${sold} sold</span>
                                        <span>${available} available</span>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 16px; font-size: 15px; color: #6b7280; margin-top: 8px;">
                                    <span style="display: inline-flex; align-items: center; gap: 8px; background: #e0f2fe; padding: 8px 14px; border-radius: 8px; font-weight: 600; color: #0369a1;"><i class="fas fa-users" style="font-size: 16px;"></i> Available: ${ticket.quantity}</span>
                                    ${ticket.benefits ? `<span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 500; color: #f59e0b;"><i class="fas fa-star" style="font-size: 14px;"></i> ${ticket.benefits}</span>` : ''}
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
                                <span style="font-size: 26px; font-weight: 800; color: #3b82f6; white-space: nowrap; letter-spacing: 0.5px;">LKR ${parseFloat(ticket.price).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                `;
            });

            ticketHTML += '</div>';
        }
    }

    ticketHTML += '</div>';

    ticketDetailsCard.style.display = 'block';
    ticketDetailsDiv.innerHTML = ticketHTML;

    // Also display registration ticketing based on ticket type
    displayRegistrationTicketing(event);
}

// Display Registration and Ticketing Section
function displayRegistrationTicketing(event) {
    const ticketType = event.ticket_type || 'free-students';
    const requiresRegistration = event.requires_registration === 1 || event.requires_registration === '1' || event.requires_registration === true;
    const registrationHeader = document.getElementById('registrationSectionHeader');
    const ticketingWrapper = document.getElementById('ticketingSectionWrapper');
    const eventDate = event.event_date || event.date;
    const eventTime = event.event_time || event.time;
    const eventStatus = getEventStatus(eventDate, eventTime, event.event_end_time);

    const freeSection = document.getElementById('freeRegistrationSection');
    const paidSection = document.getElementById('paidTicketingSection');
    const mixedSection = document.getElementById('mixedTicketingSection');

    if (!freeSection || !paidSection || !mixedSection) {
        console.log('displayRegistrationTicketing: registration/ticketing section elements not found');
        return;
    }

    if (eventStatus !== 'upcoming') {
        if (registrationHeader) registrationHeader.style.display = 'none';
        if (ticketingWrapper) ticketingWrapper.style.display = 'none';
        return;
    }

    if (registrationHeader) registrationHeader.style.display = '';
    if (ticketingWrapper) ticketingWrapper.style.display = '';

    // Hide all sections first
    freeSection.style.display = 'none';
    paidSection.style.display = 'none';
    mixedSection.style.display = 'none';

    // Scenario 1: Free for University Students
    if (ticketType === 'free-students' || ticketType === 'free-all') {
        freeSection.style.display = 'block';

        const freeRegRequired = document.getElementById('freeRegRequired');
        const freeNoRegRequired = document.getElementById('freeNoRegRequired');
        const freeEntrySubtitle = document.getElementById('freeEntrySubtitle');

        if (requiresRegistration) {
            if (freeRegRequired) freeRegRequired.style.display = 'block';
            if (freeNoRegRequired) freeNoRegRequired.style.display = 'none';
            if (freeEntrySubtitle) freeEntrySubtitle.textContent = 'Free entry with registration required';
        } else {
            if (freeRegRequired) freeRegRequired.style.display = 'none';
            if (freeNoRegRequired) freeNoRegRequired.style.display = 'block';
            if (freeEntrySubtitle) freeEntrySubtitle.textContent = 'Open entry - no registration needed';
        }

    } else if (ticketType === 'paid-all') {
        paidSection.style.display = 'block';

        const ticketPriceElement = document.getElementById('ticketPrice');
        if (ticketPriceElement) {
            ticketPriceElement.textContent = 'LKR 0.00';
        }

    } else if (ticketType === 'mixed') {
        mixedSection.style.display = 'block';

        const studentRegRequired = document.getElementById('studentRegRequired');
        const studentNoRegRequired = document.getElementById('studentNoRegRequired');

        if (requiresRegistration) {
            if (studentRegRequired) studentRegRequired.style.display = 'block';
            if (studentNoRegRequired) studentNoRegRequired.style.display = 'none';
        } else {
            if (studentRegRequired) studentRegRequired.style.display = 'none';
            if (studentNoRegRequired) studentNoRegRequired.style.display = 'block';
        }
    }
}

function displayCustomFields(customFields) {
    if (!Array.isArray(customFields) || customFields.length === 0) {
        return;
    }

    const customFieldsCard = document.getElementById('customFieldsCard');
    const customFieldsElement = document.getElementById('customFields');

    if (!customFieldsCard || !customFieldsElement) {
        return;
    }

    let fieldsHTML = '<div class="custom-fields-list">';
    customFields.forEach(field => {
        fieldsHTML += `<div class="custom-field-item">
            <strong>${field.label}:</strong> 
            <span>${field.type === 'select' ? field.options.join(', ') : field.type}</span>
        </div>`;
    });
    fieldsHTML += '</div>';

    customFieldsCard.style.display = 'block';
    customFieldsElement.innerHTML = fieldsHTML;
}

function displayVolunteerInfo(event) {
    const volunteerCard = document.getElementById('volunteerCard');
    const volunteerInfo = document.getElementById('volunteerInfo');

    if (!volunteerCard || !volunteerInfo) {
        return;
    }

    let volunteerHTML = '<div class="volunteer-detail-item">';

    if (event.volunteers_needed) {
        volunteerHTML += `<div><strong>Volunteers Needed:</strong> ${event.volunteers_needed}</div>`;
    }

    if (event.volunteer_sources && Array.isArray(event.volunteer_sources)) {
        volunteerHTML += '<div><strong>Recruiting From:</strong></div>';
        volunteerHTML += '<ul class="volunteer-sources-list">';
        event.volunteer_sources.forEach(source => {
            const sourceMap = {
                'faculty': 'Faculty Members',
                'university': 'University Students',
                'public': 'Public Users'
            };
            volunteerHTML += `<li>${sourceMap[source] || source}</li>`;
        });
        volunteerHTML += '</ul>';
    }

    if (event.volunteer_positions && Array.isArray(event.volunteer_positions)) {
        volunteerHTML += '<div><strong>Available Positions:</strong></div>';
        volunteerHTML += '<ul class="volunteer-positions-list">';
        event.volunteer_positions.forEach(position => {
            volunteerHTML += `<li>${position}</li>`;
        });
        volunteerHTML += '</ul>';
    }

    volunteerHTML += '<div style="margin-top: 15px;">';
    volunteerHTML += '<button class="btn btn-primary" onclick="applyAsVolunteer()">Apply as Volunteer</button>';
    volunteerHTML += '</div>';

    volunteerHTML += '</div>';

    volunteerCard.style.display = 'block';
    volunteerInfo.innerHTML = volunteerHTML;
}

// Modal functions
function openDonationModal() {
    document.getElementById('donationModal').style.display = 'flex';
}

function closeDonationModal() {
    document.getElementById('donationModal').style.display = 'none';
}

function processDonation() {
    const selectedAmount = document.querySelector('.donation-amount.selected');
    const customAmount = document.getElementById('customDonationAmount').value;

    const amount = selectedAmount ? selectedAmount.dataset.amount : customAmount;

    if (!amount || amount < 100) {
        alert('Please select or enter a valid donation amount (minimum LKR 100)');
        return;
    }

    // Here you would integrate with payment gateway
    alert(`Thank you for your donation of LKR ${amount}! Payment integration would be implemented here.`);
    closeDonationModal();
}

function applyAsVolunteer() {
    // Volunteer registration feature is currently unavailable
    alert('Volunteer registration feature is not yet available. Please contact the event organizer directly.');
}

// Event listeners for donation amounts
document.addEventListener('DOMContentLoaded', function () {
    const donationAmounts = document.querySelectorAll('.donation-amount');
    donationAmounts.forEach(button => {
        button.addEventListener('click', function () {
            donationAmounts.forEach(btn => btn.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('customDonationAmount').value = '';
        });
    });
});

function showEventContainer() {
    const eventContainer = document.getElementById('eventContainer');
    const errorContainer = document.getElementById('errorContainer');

    if (eventContainer) {
        eventContainer.style.display = 'block';
    }
    if (errorContainer) {
        errorContainer.style.display = 'none';
    }
}

// Event listeners
const joinBtn = document.getElementById('joinBtn');
if (joinBtn && !isUserRegistered) {
    joinBtn.addEventListener('click', openJoinModal);
}

const shareBtn = document.getElementById('shareBtn');
if (shareBtn) {
    shareBtn.addEventListener('click', openShareModal);
}

// Close modals when clicking outside
window.addEventListener('click', function (event) {
    const joinModal = document.getElementById('joinModal');
    const shareModal = document.getElementById('shareModal');
    const bankDetailsModal = document.getElementById('bankDetailsModal');
    const uploadTranscriptModal = document.getElementById('uploadTranscriptModal');

    if (event.target === joinModal) {
        closeJoinModal();
    }
    if (event.target === shareModal) {
        closeShareModal();
    }
    if (event.target === bankDetailsModal) {
        closeBankDetailsModal();
    }
    if (event.target === uploadTranscriptModal) {
        closeUploadTranscriptModal();
    }
});

// Utility functions
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Display sponsorship packages for sponsor role
function displaySponsorshipPackages() {
    const sponsorshipPackages = window.serverData?.sponsorshipPackages || [];
    const sponsorshipCard = document.getElementById('sponsorshipPackagesCard');

    if (!sponsorshipCard) {
        console.log('Sponsorship packages card not found in DOM');
        return;
    }

    if (!sponsorshipPackages || sponsorshipPackages.length === 0) {
        sponsorshipCard.style.display = 'none';
        console.log('No sponsorship packages available');
        return;
    }

    sponsorshipCard.style.display = 'block';
    const packagesContainer = document.getElementById('sponsorshipPackagesContainer');

    if (!packagesContainer) {
        console.log('Sponsorship packages container not found');
        return;
    }

    packagesContainer.innerHTML = '';

    sponsorshipPackages.forEach(pkg => {
        const packageCard = document.createElement('div');
        packageCard.className = 'sponsorship-package-card';
        packageCard.dataset.packageId = pkg.id;

        const packageType = capitalizeFirstLetter(pkg.package_type);
        const availableSlots = pkg.available_slots - pkg.filled_slots;

        let benefitsHTML = '';
        if (pkg.benefits) {
            try {
                const benefits = typeof pkg.benefits === 'string' ? JSON.parse(pkg.benefits) : pkg.benefits;
                if (Array.isArray(benefits) && benefits.length > 0) {
                    benefitsHTML = '<ul class="benefits-list">';
                    benefits.forEach(benefit => {
                        benefitsHTML += `<li><i class="fas fa-check"></i> ${benefit}</li>`;
                    });
                    benefitsHTML += '</ul>';
                }
            } catch (e) {
                console.error('Error parsing benefits:', e);
            }
        }

        packageCard.innerHTML = `
            <div class="package-header ${pkg.package_type}">
                <h3>${packageType} Package</h3>
                <div class="package-price">LKR ${parseFloat(pkg.amount).toLocaleString()}</div>
            </div>
            <div class="package-body">
                ${pkg.description ? `<p class="package-description">${pkg.description}</p>` : ''}
                ${benefitsHTML}
                <div class="package-slots">
                    <i class="fas fa-users"></i>
                    <strong>${availableSlots}</strong> ${availableSlots === 1 ? 'Slot' : 'Slots'} Available
                </div>
            </div>
            <div class="package-footer">
                <button class="btn btn-primary btn-sponsor" onclick="requestSponsorship(${pkg.id}, '${packageType}', ${pkg.amount})">
                    <i class="fas fa-handshake"></i> Use This Package
                </button>
            </div>
        `;

        packagesContainer.appendChild(packageCard);
    });
}

// View proposal details - open proposal file if available, otherwise show event details
function viewProposalDetails() {
    const event = currentEvent || window.serverData?.event;

    if (!event) {
        alert('Event information not available');
        return;
    }

    // Check if there's a sponsorship proposal file
    if (event.sponsorship_proposal) {
        // Open the proposal file in a new tab
        const proposalPath = event.sponsorship_proposal.startsWith('http') || event.sponsorship_proposal.startsWith('/')
            ? event.sponsorship_proposal
            : `/unipulse/public/${event.sponsorship_proposal}`;
        window.open(proposalPath, '_blank');
    } else {
        // If no proposal file, scroll to event details
        const eventContainer = document.getElementById('eventContainer');
        if (eventContainer) {
            eventContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            alert('No proposal document available for this event.');
        }
    }
}

// View full event details - redirect to the event page (kept for backward compatibility)
function viewFullEventDetails() {
    const eventId = currentEvent?.id;
    if (eventId) {
        window.location.href = `/unipulse/public/sponsor/events/event/${eventId}`;
    } else {
        console.error('Event ID not available');
        alert('Unable to load event details. Please try again.');
    }
}

// Scroll to event details function (kept for backward compatibility)
function scrollToEventDetails() {
    const eventContainer = document.getElementById('eventContainer');
    if (eventContainer) {
        eventContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// Request sponsorship function - show bank details modal
function requestSponsorship(packageId, packageName, price) {
    const event = currentEvent || window.serverData?.event;

    if (!event) {
        alert('Event information not available');
        return;
    }

    // Check if bank details are available
    if (!event.sponsorship_bank_name || !event.sponsorship_account_number) {
        alert('Bank account details are not available for this event. Please contact the event organizer directly.');
        return;
    }

    // Store selected package details for later use
    window.selectedSponsorshipPackage = {
        packageId: packageId,
        packageName: packageName,
        price: price
    };

    // Populate modal with bank details
    document.getElementById('modalBankName').textContent = event.sponsorship_bank_name || 'N/A';
    document.getElementById('modalAccountName').textContent = event.sponsorship_account_name || 'N/A';
    document.getElementById('modalAccountNumber').textContent = event.sponsorship_account_number || 'N/A';
    document.getElementById('modalBranch').textContent = event.sponsorship_branch || 'N/A';

    // Show/hide SWIFT code if available
    const swiftCodeItem = document.getElementById('swiftCodeItem');
    const modalSwiftCode = document.getElementById('modalSwiftCode');
    if (event.sponsorship_swift_code) {
        swiftCodeItem.style.display = 'flex';
        modalSwiftCode.textContent = event.sponsorship_swift_code;
    } else {
        swiftCodeItem.style.display = 'none';
    }

    // Show/hide instructions if available
    const instructionsContainer = document.getElementById('bankInstructionsContainer');
    const modalInstructions = document.getElementById('modalInstructions');
    if (event.sponsorship_instructions) {
        instructionsContainer.style.display = 'block';
        modalInstructions.textContent = event.sponsorship_instructions;
    } else {
        instructionsContainer.style.display = 'none';
    }

    // Set package details
    document.getElementById('modalPackageName').textContent = `${packageName} Package`;
    document.getElementById('modalPackageAmount').textContent = `LKR ${parseFloat(price).toLocaleString()}`;

    // Show the modal
    openBankDetailsModal();
}

// Open bank details modal
function openBankDetailsModal() {
    const modal = document.getElementById('bankDetailsModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

// Close bank details modal
function closeBankDetailsModal() {
    const modal = document.getElementById('bankDetailsModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Confirm bank transfer and redirect to sponsorship request page
function confirmBankTransfer() {
    const packageData = window.selectedSponsorshipPackage;
    const eventId = currentEvent?.id;

    if (!eventId || !packageData) {
        alert('Unable to process sponsorship request. Please try again.');
        return;
    }

    // Close bank details modal
    closeBankDetailsModal();

    // Show upload transcript modal
    openUploadTranscriptModal();
}

// Open upload transcript modal
function openUploadTranscriptModal() {
    const packageData = window.selectedSponsorshipPackage;

    if (!packageData) {
        alert('Package information not available');
        return;
    }

    // Populate modal with package details
    document.getElementById('uploadPackageName').textContent = `${packageData.packageName} Package`;
    document.getElementById('uploadPackageAmount').textContent = `LKR ${parseFloat(packageData.price).toLocaleString()}`;

    // Show modal
    const modal = document.getElementById('uploadTranscriptModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

// Close upload transcript modal
function closeUploadTranscriptModal() {
    const modal = document.getElementById('uploadTranscriptModal');
    if (modal) {
        modal.style.display = 'none';
    }

    // Reset form
    const form = document.getElementById('uploadTranscriptForm');
    if (form) {
        form.reset();
    }
}

// Submit sponsorship request with file upload
function submitSponsorshipRequest() {
    const packageData = window.selectedSponsorshipPackage;
    const eventId = currentEvent?.id;

    if (!eventId || !packageData) {
        alert('Missing sponsorship information. Please try again.');
        return;
    }

    // Validate form
    const fileInput = document.getElementById('paymentProof');

    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Please upload your payment receipt/slip');
        return;
    }

    // Validate file size (5MB max)
    const file = fileInput.files[0];
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if (file.size > maxSize) {
        alert('File size must be less than 5MB');
        return;
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
        alert('Please upload a valid file (JPG, PNG, or PDF)');
        return;
    }

    // Prepare form data
    const formData = new FormData();
    formData.append('event_id', eventId);
    formData.append('package_id', packageData.packageId);
    formData.append('amount', packageData.price);
    formData.append('payment_proof', file);
    formData.append('notes', document.getElementById('sponsorshipNotes')?.value || '');

    // Show loading state
    const submitBtn = document.querySelector('#uploadTranscriptModal .btn-primary');
    const originalHTML = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitBtn.disabled = true;

    // Submit request
    fetch('/unipulse/public/sponsor/sponsorship/submit', {
        method: 'POST',
        body: formData
    })
        .then(async response => {
            const text = await response.text();
            console.log('Server response:', text);

            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON:', text);
                throw new Error('Server returned invalid response. Please check your login status and try again.');
            }

            if (!response.ok) {
                throw new Error(data.message || `Server error (${response.status})`);
            }

            return data;
        })
        .then(data => {
            if (data.success) {
                closeUploadTranscriptModal();
                alert('Sponsorship request submitted successfully! The event organizer will review your request.');

                // Redirect to sponsorship requests page
                setTimeout(() => {
                    window.location.href = '/unipulse/public/sponsor/sponsorships';
                }, 1500);
            } else {
                alert(data.message || 'Failed to submit sponsorship request. Please try again.');
                submitBtn.innerHTML = originalHTML;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error submitting sponsorship:', error);
            alert('An error occurred: ' + error.message);
            submitBtn.innerHTML = originalHTML;
            submitBtn.disabled = false;
        });
}


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

// Calculate event status based on event date
function getEventStatus(eventDate, eventTime, eventEndTime) {
    if (!eventDate) return 'upcoming';

    const now = new Date();
    const y = now.getFullYear();
    const mo = String(now.getMonth() + 1).padStart(2, '0');
    const d = String(now.getDate()).padStart(2, '0');
    const todayStr = `${y}-${mo}-${d}`;
    const eventDateStr = String(eventDate).slice(0, 10);

    if (eventDateStr > todayStr) {
        return 'upcoming';
    } else if (eventDateStr < todayStr) {
        return 'completed';
    } else {
        const hh = String(now.getHours()).padStart(2, '0');
        const mm = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        const nowTimeStr = `${hh}:${mm}:${ss}`;
        const startTime = eventTime ? String(eventTime).slice(0, 8) : '00:00:00';
        const endTime = eventEndTime ? String(eventEndTime).slice(0, 8) : null;

        if (startTime > nowTimeStr) {
            return 'upcoming';
        } else if (endTime && endTime <= nowTimeStr) {
            return 'completed';
        } else {
            return 'ongoing';
        }
    }
}

// Buy Tickets - redirect to payment page
function buyTickets() {
    // Sponsors cannot buy tickets - they should log in as regular users
    showOrganizerIneligibilityModal(
        'Cannot Purchase Tickets as Organizer',
        'As a sponsor, you cannot purchase tickets using your sponsor account. ' +
        'Tickets are available only for university and public users. ' +
        'Please log out and sign in with a regular user account to purchase tickets for this event.',
        'signin'
    );
    return;
}

// Helper function to show modal for organizers who try to register/buy tickets
function showOrganizerIneligibilityModal(title, message, actionType) {
    // Create modal HTML
    const modalHTML = `
        <div id="organizerIneligibilityModal" class="modal-overlay">
            <div class="modal-content" style="background: white; border-radius: 12px; padding: 30px; max-width: 500px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                <div style="text-align: center;">
                    <div style="font-size: 48px; margin-bottom: 15px;">⚠️</div>
                    <h2 style="color: #1f2937; margin-bottom: 15px; font-size: 20px; font-weight: 600;">${title}</h2>
                    <p style="color: #6b7280; margin: 20px 0; line-height: 1.6; font-size: 14px;">${message}</p>
                    <div style="margin-top: 25px; display: flex; gap: 10px; justify-content: center;">
                        <button onclick="document.getElementById('organizerIneligibilityModal').remove();" 
                                style="padding: 10px 20px; border: 1px solid #d1d5db; border-radius: 6px; background: white; color: #374151; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                            Close
                        </button>
                        <button onclick="window.location.href='/unipulse/public/logout';" 
                                style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.2s;">
                            Logout & Sign In
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Add modal styling if not already present
    if (!document.getElementById('organizerIneligibilityModal')) {
        const style = document.createElement('style');
        style.textContent = `
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            }
        `;
        document.head.appendChild(style);
    }

    // Add modal to DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}
