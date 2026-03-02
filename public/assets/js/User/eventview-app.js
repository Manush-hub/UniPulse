// Get server data or use fallback
let currentEvent = window.serverData?.event || null;
let similarEvents = window.serverData?.similarEvents || [];
let isUserRegistered = window.serverData?.isRegistered || false;
const hasError = window.serverData?.error || null;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/user/eventview/getEvent';
const joinEndpoint = window.serverData?.joinEndpoint || '/unipulse/public/user/eventview/joinEvent';
const volunteerApplyEndpoint = window.serverData?.volunteerApplyEndpoint || '/unipulse/public/user/eventview/applyVolunteer';

// Initialize the page
document.addEventListener('DOMContentLoaded', function () {
    console.log('User Eventview Page Loaded');
    console.log('window.serverData:', window.serverData);
    console.log('currentEvent:', currentEvent);
    console.log('hasError:', hasError);
    console.log('Event ID from URL:', getEventIdFromURL());
    loadEventDetails();
});

// Get event ID from URL parameters
function getEventIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

// Load event details
function loadEventDetails() {
    console.log('loadEventDetails called');
    console.log('hasError:', hasError);
    console.log('currentEvent:', currentEvent);

    if (hasError) {
        console.log('Error detected, showing error page');
        hideLoading();
        showError();
        return;
    }

    if (currentEvent) {
        console.log('Using server data for event:', currentEvent.id);
        // Use server data directly
        displayEventDetails(currentEvent);
        loadSimilarEvents(similarEvents);
        hideLoading();
        showEventContainer();
    } else {
        console.log('No server data, fetching via AJAX');
        // Fallback to AJAX if no server data
        const eventId = getEventIdFromURL();
        console.log('Event ID from URL:', eventId);

        if (!eventId) {
            console.log('No event ID found, showing error');
            showError();
            return;
        }

        showLoading();

        const fetchUrl = `${apiEndpoint}?id=${eventId}`;
        console.log('Fetching event from:', fetchUrl);

        fetch(fetchUrl)
            .then(response => response.json())
            .then(data => {
                console.log('Fetch response:', data);
                if (data.success) {
                    currentEvent = data.event;
                    similarEvents = data.similarEvents || [];
                    displayEventDetails(currentEvent);
                    loadSimilarEvents(similarEvents);
                    hideLoading();
                    showEventContainer();
                } else {
                    console.log('Fetch failed:', data.error);
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
    const eventDate = event.event_date || event.date;
    const eventTimeRaw = event.event_time || event.time;
    const rawStatus = (event.status || '').toString().trim().toLowerCase();
    const calculatedStatus = getEventStatus(eventDate, eventTimeRaw, event.event_end_time);
    const normalizedStatus = (rawStatus === 'cancelled' || rawStatus === 'completed')
        ? rawStatus
        : calculatedStatus;
    event.status = normalizedStatus;

    if (eventStatusElement) {
        eventStatusElement.textContent = capitalizeFirstLetter(normalizedStatus);
        // Update the class for proper styling
        eventStatusElement.className = `event-status ${normalizedStatus}`;
    }

    const eventTitleElement = document.getElementById('eventTitle');
    if (eventTitleElement) {
        eventTitleElement.textContent = event.title;
    }

    // Event details grid
    const eventTime = event.event_time || event.time;
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
    if (participantsInfoElement) {
        if (maxParticipants !== null && maxParticipants !== undefined) {
            participantsInfoElement.style.display = 'flex';
            const eventParticipantsElement = document.getElementById('eventParticipants');
            if (eventParticipantsElement) {
                eventParticipantsElement.textContent = `${currentParticipants}/${maxParticipants}`;
            }
        } else {
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
    const buyTicketBtn = document.getElementById('buyTicketBtn');

    if (ticketInfoElement) ticketInfoElement.style.display = 'block';
    if (eventTicketTypeElement) {
        if (ticketType === 'free-all') {
            eventTicketTypeElement.innerHTML = '<span style="color: #10B981; font-weight: 600;">Free Event</span>';
        } else {
            eventTicketTypeElement.textContent = formatTicketType(ticketType);
        }
    }

    // Show/hide buy ticket button based on ticket type
    if (buyTicketBtn) {
        if (ticketType && ticketType !== 'free-all') {
            buyTicketBtn.style.display = 'inline-flex';
        } else {
            buyTicketBtn.style.display = 'none';
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
    const scheduleCardElement = document.getElementById('scheduleCard');
    if (event.schedule && Array.isArray(event.schedule) && event.schedule.length > 0) {
        displaySchedule(event.schedule);
        if (scheduleCardElement) {
            scheduleCardElement.style.display = 'block';
        }
    } else {
        if (scheduleCardElement) {
            scheduleCardElement.style.display = 'none';
        }
    }

    // Requirements - hide card if no requirements
    const requirementsCardElement = document.getElementById('requirementsCard');
    if (event.requirements && Array.isArray(event.requirements) && event.requirements.length > 0) {
        displayRequirements(event.requirements);
        if (requirementsCardElement) {
            requirementsCardElement.style.display = 'block';
        }
    } else {
        if (requirementsCardElement) {
            requirementsCardElement.style.display = 'none';
        }
    }

    // Location details
    displayLocationDetails(event);

    // Ticket details
    displayTicketDetails(event);

    // Custom fields - hide card if no custom fields
    if (event.custom_fields && Array.isArray(event.custom_fields) && event.custom_fields.length > 0) {
        displayCustomFields(event.custom_fields);
    } else {
        if (document.getElementById('customFieldsCard')) {
            document.getElementById('customFieldsCard').style.display = 'none';
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

        // Volunteer information - hide card if not accepting volunteers or no slots left
        if (event.needs_volunteers && event.needs_volunteers == 1) {
            const volunteersNeeded = event.volunteers_needed !== null && event.volunteers_needed !== undefined
                ? parseInt(event.volunteers_needed, 10)
                : null;

            if (volunteersNeeded === null || volunteersNeeded > 0) {
                displayVolunteerInfo(event);
            } else {
                if (document.getElementById('volunteerCard')) {
                    document.getElementById('volunteerCard').style.display = 'none';
                }
            }
        } else {
            if (document.getElementById('volunteerCard')) {
                document.getElementById('volunteerCard').style.display = 'none';
            }
        }
    }

    // Donation information - hide card if not accepting donations
    if (eventStatus === 'upcoming' && event.accepts_donations && event.accepts_donations == 1) {
        if (document.getElementById('donationCard')) {
            document.getElementById('donationCard').style.display = 'block';
        }
    } else {
        if (document.getElementById('donationCard')) {
            document.getElementById('donationCard').style.display = 'none';
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
    if (maxParticipants !== null && maxParticipants !== undefined) {
        if (eventStatsCardElement) {
            eventStatsCardElement.style.display = 'block';
        }

        const totalParticipantsElement = document.getElementById('totalParticipants');
        if (totalParticipantsElement) {
            totalParticipantsElement.textContent = currentParticipants;
        }

        const availableSpotsElement = document.getElementById('availableSpots');
        if (availableSpotsElement) {
            availableSpotsElement.textContent = maxParticipants - currentParticipants;
        }

        // Participation percentage
        const percentage = maxParticipants > 0 ? Math.round((currentParticipants / maxParticipants) * 100) : 0;
        const participationPercentageElement = document.getElementById('participationPercentage');
        if (participationPercentageElement) {
            participationPercentageElement.textContent = `${percentage}%`;
        }

        const participationFillElement = document.getElementById('participationFill');
        if (participationFillElement) {
            participationFillElement.style.width = `${percentage}%`;
        }
    } else {
        if (eventStatsCardElement) {
            eventStatsCardElement.style.display = 'none';
        }
    }

    // Set event link for sharing
    const shareLinkElement = document.getElementById('shareLink');
    if (shareLinkElement) {
        shareLinkElement.value = window.location.href;
    }

    // Update status styling
    updateStatusStyling(normalizedStatus, currentParticipants, maxParticipants);

    // Show comments section if event is completed
    if (normalizedStatus === 'completed') {
        const commentsSection = document.getElementById('commentsSection');
        if (commentsSection) {
            commentsSection.style.display = 'block';
            loadComments(); // Load comments for completed events
        }
    }
}

// Display registration period
function displayRegistrationPeriod(event) {
    const registrationPeriodCard = document.getElementById('registrationPeriodCard');
    const registrationPeriodContainer = document.getElementById('registrationPeriod');

    // Check if elements exist
    if (!registrationPeriodCard || !registrationPeriodContainer) return;

    // Check if registration dates are available
    if (event.registration_start_date && event.registration_end_date) {
        let registrationHTML = '<div class="registration-period-item">';

        // Registration Start
        registrationHTML += '<div style="margin-bottom: 15px;">';
        registrationHTML += '<div><strong>Registration Opens:</strong></div>';
        registrationHTML += `<div style="color: #666; margin-top: 5px;">`;
        registrationHTML += `<i class="fas fa-calendar"></i> ${formatDate(event.registration_start_date)}`;
        if (event.registration_start_time) {
            registrationHTML += ` <i class="fas fa-clock" style="margin-left: 15px;"></i> ${event.registration_start_time}`;
        }
        registrationHTML += '</div></div>';

        // Registration End
        registrationHTML += '<div style="margin-bottom: 15px;">';
        registrationHTML += '<div><strong>Registration Closes:</strong></div>';
        registrationHTML += `<div style="color: #666; margin-top: 5px;">`;
        registrationHTML += `<i class="fas fa-calendar"></i> ${formatDate(event.registration_end_date)}`;
        if (event.registration_end_time) {
            registrationHTML += ` <i class="fas fa-clock" style="margin-left: 15px;"></i> ${event.registration_end_time}`;
        }
        registrationHTML += '</div></div>';

        // Registration limit if available
        if (event.registration_limit) {
            registrationHTML += '<div>';
            registrationHTML += '<div><strong>Registration Limit:</strong></div>';
            registrationHTML += `<div style="color: #666; margin-top: 5px;"><i class="fas fa-users"></i> ${event.registration_limit} participants</div>`;
            registrationHTML += '</div>';
        }

        registrationHTML += '</div>';

        registrationPeriodContainer.innerHTML = registrationHTML;
        registrationPeriodCard.style.display = 'block';
    }
}

// Display event schedule
function displaySchedule(schedule) {
    const scheduleContainer = document.getElementById('eventSchedule');
    if (!scheduleContainer) return;

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
    if (!requirementsContainer) return;

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
    if (!similarEventsContainer) return;

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
    if (joinBtn) {
        if (status === 'completed' || status === 'cancelled') {
            joinBtn.disabled = true;
            joinBtn.innerHTML = '<i class="fas fa-calendar-times"></i> Event Ended';
        } else if (participants >= maxParticipants) {
            joinBtn.disabled = true;
            joinBtn.innerHTML = '<i class="fas fa-users"></i> Event Full';
        }
    }
}

// Navigation functions
function viewEvent(eventId) {
    window.location.href = `/unipulse/public/user/eventview?id=${eventId}`;
}

// Modal functions
function openJoinModal() {
    const joinModal = document.getElementById('joinModal');
    if (joinModal) {
        joinModal.style.display = 'flex';
    }
}

function closeJoinModal() {
    const joinModal = document.getElementById('joinModal');
    if (joinModal) {
        joinModal.style.display = 'none';
    }
}

function openShareModal() {
    const shareModal = document.getElementById('shareModal');
    if (shareModal) {
        shareModal.style.display = 'flex';
    }
}

function closeShareModal() {
    const shareModal = document.getElementById('shareModal');
    if (shareModal) {
        shareModal.style.display = 'none';
    }
}

function openVolunteerConsentModal() {
    const volunteerConsentModal = document.getElementById('volunteerConsentModal');
    if (volunteerConsentModal) {
        volunteerConsentModal.style.display = 'flex';
    }
}

function closeVolunteerConsentModal() {
    const volunteerConsentModal = document.getElementById('volunteerConsentModal');
    if (volunteerConsentModal) {
        volunteerConsentModal.style.display = 'none';
    }
}

function confirmVolunteerConsent() {
    if (!currentEvent || !currentEvent.id) {
        alert('Event data not available');
        return;
    }

    const confirmBtn = document.querySelector('#volunteerConsentModal .btn-primary');
    const originalText = confirmBtn ? confirmBtn.innerHTML : 'Confirm';

    if (confirmBtn) {
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        confirmBtn.disabled = true;
    }

    const formData = new FormData();
    formData.append('id', currentEvent.id);

    fetch(volunteerApplyEndpoint, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.volunteers_needed !== undefined && data.volunteers_needed !== null) {
                    currentEvent.volunteers_needed = parseInt(data.volunteers_needed, 10);
                }

                displayVolunteerInfo(currentEvent);
                closeVolunteerConsentModal();
                window.location.href = '/unipulse/public/user/dashboard?volunteer_applied=1';
            } else if (data.alreadyRegistered) {
                closeVolunteerConsentModal();
                alert('You have already applied as a volunteer for this event.');
                window.location.href = '/unipulse/public/user/dashboard';
            } else {
                alert(data.error || 'Failed to submit volunteer application. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error applying as volunteer:', error);
            alert('Failed to submit volunteer application. Please try again.');
        })
        .finally(() => {
            if (confirmBtn) {
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            }
        });
}

// Make modal functions globally accessible
window.openJoinModal = openJoinModal;
window.closeJoinModal = closeJoinModal;
window.openShareModal = openShareModal;
window.closeShareModal = closeShareModal;
window.openVolunteerConsentModal = openVolunteerConsentModal;
window.closeVolunteerConsentModal = closeVolunteerConsentModal;
window.confirmVolunteerConsent = confirmVolunteerConsent;

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

                // Update current event data
                const newCurrentParticipants = data.current_participants || data.participants || 0;
                const maxParticipants = data.max_participants || currentEvent.max_participants || currentEvent.maxParticipants;

                currentEvent.current_participants = newCurrentParticipants;
                currentEvent.participants = data.participants; // Legacy

                // Update UI with new participant count only if max_participants is set
                if (maxParticipants !== null && maxParticipants !== undefined) {
                    // Update participants in detail section
                    const participantsInfo = document.getElementById('participantsInfo');
                    if (participantsInfo) {
                        participantsInfo.style.display = 'flex';
                        document.getElementById('eventParticipants').textContent = `${newCurrentParticipants}/${maxParticipants}`;
                    }

                    // Update statistics card
                    const statsCard = document.getElementById('eventStatsCard');
                    if (statsCard && statsCard.style.display !== 'none') {
                        document.getElementById('totalParticipants').textContent = newCurrentParticipants;
                        document.getElementById('availableSpots').textContent = data.availableSpots !== null ? data.availableSpots : maxParticipants - newCurrentParticipants;

                        // Update participation percentage
                        const percentage = maxParticipants > 0 ? Math.round((newCurrentParticipants / maxParticipants) * 100) : 0;
                        document.getElementById('participationPercentage').textContent = `${percentage}%`;
                        document.getElementById('participationFill').style.width = `${percentage}%`;
                    }
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

// Register for free event (with or without registration requirement)
function registerForEvent() {
    if (!currentEvent) {
        alert('Event data not available');
        return;
    }

    // Check if user is already registered
    if (isUserRegistered) {
        alert('You are already registered for this event.');
        return;
    }

    // Check if event requires registration
    const requiresReg = currentEvent.requires_registration == 1 || currentEvent.requires_registration === '1';

    if (!requiresReg) {
        alert('This is an open event. No registration is required - just show up!');
        return;
    }

    // Show join modal for registration
    openJoinModal();
}

// Buy tickets for paid event
function buyTickets() {
    if (!currentEvent) {
        alert('Event data not available');
        return;
    }

    // Collect selected tickets
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const selectedTickets = [];
    let totalAmount = 0;

    quantityInputs.forEach(input => {
        const quantity = parseInt(input.value) || 0;
        if (quantity > 0) {
            const ticketOption = input.closest('.ticket-option');
            const ticketName = ticketOption.dataset.ticketName;
            const ticketPrice = parseFloat(ticketOption.dataset.ticketPrice);

            selectedTickets.push({
                name: ticketName,
                price: ticketPrice,
                quantity: quantity,
                subtotal: ticketPrice * quantity
            });

            totalAmount += ticketPrice * quantity;
        }
    });

    if (selectedTickets.length === 0) {
        alert('Please select at least one ticket.');
        return;
    }

    // TODO: Implement payment gateway integration
    alert(`Total: LKR ${totalAmount.toFixed(2)}\n\nPayment gateway integration coming soon!`);

    // For now, log the selected tickets
    console.log('Selected tickets:', selectedTickets);
    console.log('Total amount:', totalAmount);
}

function contactOrganizer() {
    const organizerEmail = currentEvent?.organizerEmail || currentEvent?.organizer_email;
    if (organizerEmail) {
        window.location.href = `mailto:${organizerEmail}?subject=Inquiry about ${currentEvent.title}`;
    } else {
        alert('Organizer contact information not available.');
    }
}

// Make function globally accessible
window.contactOrganizer = contactOrganizer;

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

function copyShareLink() {
    const shareLink = document.getElementById('shareLink');
    if (shareLink) {
        shareLink.select();
        shareLink.setSelectionRange(0, 99999); // For mobile devices

        // Try modern clipboard API first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(shareLink.value)
                .then(() => {
                    showToast('Link copied to clipboard!', 'success');
                })
                .catch(() => {
                    // Fallback to execCommand
                    document.execCommand('copy');
                    showToast('Link copied to clipboard!', 'success');
                });
        } else {
            // Fallback for older browsers
            document.execCommand('copy');
            showToast('Link copied to clipboard!', 'success');
        }
    }
}

// Make functions globally accessible
window.copyEventLink = copyEventLink;
window.copyShareLink = copyShareLink;
window.confirmJoinEvent = confirmJoinEvent;
window.registerForEvent = registerForEvent;
window.buyTickets = buyTickets;

// UI state management
function showLoading() {
    const loadingContainer = document.getElementById('loadingContainer');
    const errorContainer = document.getElementById('errorContainer');
    const eventContainer = document.getElementById('eventContainer');

    if (loadingContainer) loadingContainer.style.display = 'flex';
    if (errorContainer) errorContainer.style.display = 'none';
    if (eventContainer) eventContainer.style.display = 'none';
}

function hideLoading() {
    const loadingContainer = document.getElementById('loadingContainer');
    if (loadingContainer) {
        loadingContainer.style.display = 'none';
    }
}

function showError() {
    const errorContainer = document.getElementById('errorContainer');
    const eventContainer = document.getElementById('eventContainer');

    if (errorContainer) errorContainer.style.display = 'flex';
    if (eventContainer) eventContainer.style.display = 'none';
}

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
            document.getElementById('locationDetailsCard').style.display = 'block';
            document.getElementById('locationDetails').innerHTML = locationHTML;
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
            document.getElementById('locationDetailsCard').style.display = 'block';
            document.getElementById('locationDetails').innerHTML = locationHTML;
        }
    }
}

function displayTicketDetails(event) {
    const ticketType = event.ticket_type || 'free-all';

    if (ticketType === 'free-all') {
        renderFreeRegistration(event);
        return;
    }

    // Show appropriate ticket section based on ticket type
    if (ticketType === 'paid-all') {
        renderPaidTickets(event);
    } else if (ticketType === 'mixed') {
        renderMixedTickets(event);
    }
}

function toggleRegistrationSectionVisibility(event) {
    const registrationHeader = document.getElementById('registrationSectionHeader');
    const ticketingWrapper = document.getElementById('ticketingSectionWrapper');

    if (!registrationHeader || !ticketingWrapper) {
        return true;
    }

    const eventDate = event.event_date || event.date;
    const eventTime = event.event_time || event.time;
    const eventStatus = getEventStatus(eventDate, eventTime, event.event_end_time);
    const shouldHide = eventStatus !== 'upcoming';

    registrationHeader.style.display = shouldHide ? 'none' : '';
    ticketingWrapper.style.display = shouldHide ? 'none' : '';

    return !shouldHide;
}

function renderFreeRegistration(event) {
    if (!toggleRegistrationSectionVisibility(event)) {
        return;
    }

    const freeSection = document.getElementById('freeRegistrationSection');
    if (!freeSection) {
        console.error('freeRegistrationSection element not found');
        return;
    }

    console.log('Rendering free registration for event:', event.title);

    // Show the free registration section
    freeSection.style.display = 'block';
}

function renderPaidTickets(event) {
    if (!toggleRegistrationSectionVisibility(event)) {
        return;
    }

    const paidSection = document.getElementById('paidTicketingSection');
    if (!paidSection) return;

    // Parse ticket_types if it's a string
    let ticketTypes = event.ticket_types;
    if (typeof ticketTypes === 'string') {
        try {
            ticketTypes = JSON.parse(ticketTypes);
        } catch (e) {
            console.error('Failed to parse ticket_types:', e);
            return;
        }
    }

    if (!Array.isArray(ticketTypes) || ticketTypes.length === 0) {
        return;
    }

    // Render ticket options
    let ticketsHTML = '<div class="tickets-list" style="display: flex; flex-direction: column; gap: 1rem; margin: 1rem 0;">';

    ticketTypes.forEach((ticket, index) => {
        ticketsHTML += `
            <div class="ticket-option" 
                 data-ticket-index="${index}" 
                 data-ticket-name="${ticket.name}" 
                 data-ticket-price="${ticket.price}"
                 style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px;">
                <div class="ticket-info" style="flex: 1;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #1e293b; font-size: 1.1rem; font-weight: 600;">${ticket.name}</h4>
                    ${ticket.description ? `<p style="margin: 0 0 0.75rem 0; color: #64748b; font-size: 0.9rem;">${ticket.description}</p>` : ''}
                    <p style="margin: 0.5rem 0; color: #667eea; font-size: 1.25rem; font-weight: 700;">LKR ${parseFloat(ticket.price).toFixed(2)}</p>
                    <p style="margin: 0.25rem 0 0 0; color: #94a3b8; font-size: 0.85rem;">Available: ${ticket.quantity}</p>
                </div>
                <div class="ticket-quantity-control" style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                    <label for="ticket-quantity-${index}" style="color: #475569; font-size: 0.9rem; font-weight: 500;">Quantity:</label>
                    <input type="number" 
                           id="ticket-quantity-${index}" 
                           min="0" 
                           max="${ticket.quantity}" 
                           value="0" 
                           class="quantity-input"
                           style="width: 80px; padding: 0.5rem; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 1rem; text-align: center;">
                </div>
            </div>
        `;
    });

    ticketsHTML += '</div>';

    document.getElementById('ticketDetails').innerHTML = ticketsHTML;
    document.getElementById('ticketDetailsCard').style.display = 'block';
    paidSection.style.display = 'block';

    setupTicketQuantityListeners();
}

function renderMixedTickets(event) {
    if (!toggleRegistrationSectionVisibility(event)) {
        return;
    }

    const mixedSection = document.getElementById('mixedTicketingSection');
    if (!mixedSection) return;

    let ticketTypes = event.ticket_types;
    if (typeof ticketTypes === 'string') {
        try {
            ticketTypes = JSON.parse(ticketTypes);
        } catch (e) {
            console.error('Failed to parse ticket_types:', e);
            return;
        }
    }

    if (!Array.isArray(ticketTypes) || ticketTypes.length === 0) {
        return;
    }

    let ticketsHTML = '<div class="tickets-list" style="display: flex; flex-direction: column; gap: 1rem; margin: 1rem 0;">';

    ticketTypes.forEach((ticket, index) => {
        ticketsHTML += `
            <div class="ticket-option" 
                 data-ticket-index="${index}" 
                 data-ticket-name="${ticket.name}" 
                 data-ticket-price="${ticket.price}"
                 style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px;">
                <div class="ticket-info" style="flex: 1;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #1e293b; font-size: 1.1rem; font-weight: 600;">${ticket.name}</h4>
                    ${ticket.description ? `<p style="margin: 0 0 0.75rem 0; color: #64748b; font-size: 0.9rem;">${ticket.description}</p>` : ''}
                    <p style="margin: 0.5rem 0; color: #667eea; font-size: 1.25rem; font-weight: 700;">LKR ${parseFloat(ticket.price).toFixed(2)}</p>
                    <p style="margin: 0.25rem 0 0 0; color: #94a3b8; font-size: 0.85rem;">Available: ${ticket.quantity}</p>
                </div>
                <div class="ticket-quantity-control" style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                    <label for="mixed-ticket-quantity-${index}" style="color: #475569; font-size: 0.9rem; font-weight: 500;">Quantity:</label>
                    <input type="number" 
                           id="mixed-ticket-quantity-${index}" 
                           min="0" 
                           max="${ticket.quantity}" 
                           value="0" 
                           class="quantity-input"
                           style="width: 80px; padding: 0.5rem; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 1rem; text-align: center;">
                </div>
            </div>
        `;
    });

    ticketsHTML += '</div>';

    document.getElementById('mixedTicketDetails').innerHTML = ticketsHTML;
    document.getElementById('mixedTicketDetailsCard').style.display = 'block';
    mixedSection.style.display = 'block';

    const requiresReg = event.requires_registration == 1 || event.requires_registration === '1';
    if (document.getElementById('studentRegRequired')) {
        document.getElementById('studentRegRequired').style.display = requiresReg ? 'block' : 'none';
    }
    if (document.getElementById('studentNoRegRequired')) {
        document.getElementById('studentNoRegRequired').style.display = requiresReg ? 'none' : 'block';
    }

    setupTicketQuantityListeners();
}

function setupTicketQuantityListeners() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('input', updateTotalPrice);
        input.addEventListener('change', updateTotalPrice);
    });
    updateTotalPrice();
}

function updateTotalPrice() {
    const allTickets = document.querySelectorAll('.ticket-option');
    let totalPrice = 0;

    allTickets.forEach(ticket => {
        const index = ticket.dataset.ticketIndex;
        const quantityInput = document.getElementById(`ticket-quantity-${index}`) ||
            document.getElementById(`mixed-ticket-quantity-${index}`);
        const quantity = parseInt(quantityInput?.value) || 0;
        const price = parseFloat(ticket.dataset.ticketPrice) || 0;

        totalPrice += quantity * price;
    });

    const ticketPriceEl = document.getElementById('ticketPrice');
    const mixedTicketPriceEl = document.getElementById('mixedTicketPrice');

    if (ticketPriceEl) {
        ticketPriceEl.textContent = `LKR ${totalPrice.toFixed(2)}`;
    }
    if (mixedTicketPriceEl) {
        mixedTicketPriceEl.textContent = `LKR ${totalPrice.toFixed(2)}`;
    }
}

function buyTickets() {
    console.log('buyTickets called');

    if (!currentEvent) {
        console.error('currentEvent is not defined');
        alert('Event data not available. Please refresh the page and try again.');
        return;
    }

    console.log('Current event:', currentEvent);

    const allTickets = document.querySelectorAll('.ticket-option');
    console.log('Found tickets:', allTickets.length);

    if (allTickets.length === 0) {
        console.error('No ticket options found in the page');
        alert('No tickets available. Please refresh the page.');
        return;
    }

    const ticketSelections = [];
    let totalPrice = 0;

    allTickets.forEach(ticket => {
        const index = ticket.dataset.ticketIndex;
        const quantityInput = document.getElementById(`ticket-quantity-${index}`) ||
            document.getElementById(`mixed-ticket-quantity-${index}`);
        const quantity = parseInt(quantityInput?.value) || 0;

        if (quantity > 0) {
            const ticketName = ticket.dataset.ticketName;
            const ticketPrice = parseFloat(ticket.dataset.ticketPrice);
            const subtotal = ticketPrice * quantity;

            ticketSelections.push({
                index: index,
                name: ticketName,
                price: ticketPrice,
                quantity: quantity,
                subtotal: subtotal
            });

            totalPrice += subtotal;
        }
    });

    console.log('Ticket selections:', ticketSelections);
    console.log('Total price:', totalPrice);

    if (ticketSelections.length === 0) {
        alert('Please select at least one ticket by setting quantity greater than 0');
        return;
    }

    const paymentData = {
        eventId: currentEvent.id,
        eventTitle: currentEvent.title,
        tickets: ticketSelections,
        totalAmount: totalPrice,
        timestamp: Date.now()
    };

    sessionStorage.setItem('paymentData', JSON.stringify(paymentData));
    console.log('Payment data saved to sessionStorage');

    // Redirect to user payment gateway (PayHere only)
    const paymentUrl = `/unipulse/public/user/paymentgateway?event_id=${currentEvent.id}`;

    console.log('Redirecting to:', paymentUrl);
    window.location.href = paymentUrl;
}

function displayCustomFields(customFields) {
    if (!Array.isArray(customFields) || customFields.length === 0) {
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

    document.getElementById('customFieldsCard').style.display = 'block';
    document.getElementById('customFields').innerHTML = fieldsHTML;
}

function displayVolunteerInfo(event) {
    let volunteerHTML = '<div class="volunteer-detail-item">';

    const volunteersNeeded = event.volunteers_needed !== null && event.volunteers_needed !== undefined
        ? parseInt(event.volunteers_needed, 10)
        : null;
    const hasVolunteerSlots = volunteersNeeded === null || Number.isNaN(volunteersNeeded) || volunteersNeeded > 0;

    if (volunteersNeeded !== null && !Number.isNaN(volunteersNeeded) && volunteersNeeded <= 0) {
        document.getElementById('volunteerCard').style.display = 'none';
        return;
    }

    if (volunteersNeeded !== null && !Number.isNaN(volunteersNeeded)) {
        volunteerHTML += `<div><strong>Volunteers Needed:</strong> <span id="volunteersNeededCount">${volunteersNeeded}</span></div>`;
    }

    if (event.volunteer_sources && Array.isArray(event.volunteer_sources)) {
        const visibleSources = event.volunteer_sources.filter(source => source !== 'faculty');

        if (visibleSources.length > 0) {
            volunteerHTML += '<div><strong>Recruiting From:</strong></div>';
            volunteerHTML += '<ul class="volunteer-sources-list">';
            visibleSources.forEach(source => {
                const sourceMap = {
                    'university': 'University Students',
                    'public': 'Public Users'
                };
                volunteerHTML += `<li>${sourceMap[source] || source}</li>`;
            });
            volunteerHTML += '</ul>';
        }
    }

    volunteerHTML += '<div style="margin-top: 15px;">';
    volunteerHTML += `<button class="btn btn-primary" onclick="applyAsVolunteer()" ${hasVolunteerSlots ? '' : 'disabled style="opacity:0.6;cursor:not-allowed;"'}>${hasVolunteerSlots ? 'Apply as Volunteer' : 'Volunteer Positions Filled'}</button>`;
    volunteerHTML += '</div>';

    volunteerHTML += '</div>';

    document.getElementById('volunteerCard').style.display = 'block';
    document.getElementById('volunteerInfo').innerHTML = volunteerHTML;
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

// Make functions globally accessible
window.openDonationModal = openDonationModal;
window.closeDonationModal = closeDonationModal;
window.processDonation = processDonation;

function applyAsVolunteer() {
    openVolunteerConsentModal();
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

// Comments System Variables
let currentUserRating = 0;
let editingCommentId = null;
let editingCommentRating = 0;

// Event listeners
const joinBtn = document.getElementById('joinBtn');
if (joinBtn) {
    if (isUserRegistered) {
        joinBtn.innerHTML = '<i class="fas fa-check"></i> Already Registered';
        joinBtn.classList.add('disabled');
        joinBtn.style.cursor = 'not-allowed';
        joinBtn.style.opacity = '0.6';
        joinBtn.disabled = true;
    } else {
        joinBtn.addEventListener('click', openJoinModal);
    }
}

window.buyTickets = buyTickets;
window.openDonationModal = openDonationModal;
window.closeDonationModal = closeDonationModal;
window.processDonation = processDonation;
window.applyAsVolunteer = applyAsVolunteer;

const shareBtn = document.getElementById('shareBtn');
if (shareBtn) {
    shareBtn.addEventListener('click', openShareModal);
}

// Comments event listeners - check if DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        setupCommentsEventListeners();
    });
} else {
    // DOM is already loaded
    setupCommentsEventListeners();
}

function setupCommentsEventListeners() {
    // Character count for comment text
    const commentText = document.getElementById('commentText');
    const charCount = document.getElementById('charCount');

    if (commentText && charCount) {
        commentText.addEventListener('input', function () {
            const count = this.value.length;
            charCount.textContent = count;

            if (count > 900) {
                charCount.classList.add('danger');
                charCount.classList.remove('warning');
            } else if (count > 800) {
                charCount.classList.add('warning');
                charCount.classList.remove('danger');
            } else {
                charCount.classList.remove('warning', 'danger');
            }
        });
    }

    // Rating input
    setupRatingInput('ratingInput', (rating) => {
        currentUserRating = rating;
        updateRatingText('ratingText', rating);
    });

    // Edit comment modal rating
    setupRatingInput('editRatingInput', (rating) => {
        editingCommentRating = rating;
        updateRatingText('editRatingText', rating);
    });

    // Edit comment character count
    const editCommentText = document.getElementById('editCommentText');
    const editCharCount = document.getElementById('editCharCount');

    if (editCommentText && editCharCount) {
        editCommentText.addEventListener('input', function () {
            const count = this.value.length;
            editCharCount.textContent = count;

            if (count > 900) {
                editCharCount.classList.add('danger');
                editCharCount.classList.remove('warning');
            } else if (count > 800) {
                editCharCount.classList.add('warning');
                editCharCount.classList.remove('danger');
            } else {
                editCharCount.classList.remove('warning', 'danger');
            }
        });
    }

    // Form buttons
    const submitCommentBtn = document.getElementById('submitCommentBtn');
    const cancelCommentBtn = document.getElementById('cancelCommentBtn');
    const updateCommentBtn = document.getElementById('updateCommentBtn');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    if (submitCommentBtn) {
        submitCommentBtn.addEventListener('click', submitComment);
    }

    if (cancelCommentBtn) {
        cancelCommentBtn.addEventListener('click', hideCommentForm);
    }

    if (updateCommentBtn) {
        updateCommentBtn.addEventListener('click', updateComment);
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', confirmDeleteComment);
    }
}

// Setup rating input functionality
function setupRatingInput(containerId, callback) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const stars = container.querySelectorAll('.star');

    stars.forEach((star, index) => {
        star.addEventListener('mouseenter', function () {
            highlightStars(container, index + 1);
        });

        star.addEventListener('mouseleave', function () {
            const currentRating = containerId === 'editRatingInput' ? editingCommentRating : currentUserRating;
            highlightStars(container, currentRating);
        });

        star.addEventListener('click', function () {
            const rating = index + 1;
            callback(rating);
            highlightStars(container, rating);
        });
    });
}

// Highlight stars up to a certain rating
function highlightStars(container, rating) {
    const stars = container.querySelectorAll('.star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.textContent = '★';
            star.classList.add('active');
        } else {
            star.textContent = '☆';
            star.classList.remove('active');
        }
    });
}

// Update rating text display
function updateRatingText(textElementId, rating) {
    const textElement = document.getElementById(textElementId);
    if (!textElement) return;

    const ratingTexts = {
        1: 'Poor',
        2: 'Fair',
        3: 'Good',
        4: 'Very Good',
        5: 'Excellent'
    };

    if (rating > 0) {
        textElement.textContent = `${ratingTexts[rating]} (${rating}/5)`;
    } else {
        textElement.textContent = 'Click stars to rate';
    }
}

// Load comments for the current event
async function loadComments() {
    if (!currentEvent) return;

    try {
        const response = await fetch(`/unipulse/public/user/comments/getComments?event_id=${currentEvent.id}`, {
            method: 'GET',
            credentials: 'same-origin' // Include session cookies
        });
        const data = await response.json();

        if (data.success) {
            displayComments(data.comments);
            updateCommentsStats(data.stats);

            // Check if user can comment
            checkUserCommentStatus();
        } else {
            console.error('Failed to load comments:', data.error);
            displayEmptyComments();
        }
    } catch (error) {
        console.error('Error loading comments:', error);
        displayEmptyComments();
    }
}

// Check if current user can comment
async function checkUserCommentStatus() {
    if (!currentEvent) {
        console.log('No current event found');
        return;
    }

    // Check if user is logged in by looking for user info in the page
    const userElement = document.querySelector('.username, #username');
    const isLoggedInByPage = userElement && userElement.textContent.trim() !== '' && userElement.textContent.trim() !== 'Guest';

    console.log('User element found:', userElement);
    console.log('Is logged in by page check:', isLoggedInByPage);

    if (!isLoggedInByPage) {
        // User not logged in - show login prompt
        console.log('User not logged in - showing login prompt');
        const addCommentTrigger = document.getElementById('addCommentTrigger');
        const loginPrompt = document.getElementById('loginPrompt');
        if (addCommentTrigger) addCommentTrigger.style.display = 'none';
        if (loginPrompt) loginPrompt.style.display = 'block';
        return;
    }

    try {
        console.log(`Checking comment status for event ${currentEvent.id}`);
        const response = await fetch(`/unipulse/public/user/comments/checkUserComment/${currentEvent.id}`, {
            method: 'GET',
            credentials: 'same-origin' // Include session cookies
        });
        const data = await response.json();

        console.log('Comment status response:', data);

        const addCommentTrigger = document.getElementById('addCommentTrigger');
        const loginPrompt = document.getElementById('loginPrompt');

        console.log('Elements found:', {
            addCommentTrigger: !!addCommentTrigger,
            loginPrompt: !!loginPrompt
        });

        // If API says not logged in but page shows logged in, force show comment button
        if (!data.can_comment && data.debug === 'not_logged_in' && isLoggedInByPage) {
            console.log('API session issue detected - forcing comment button based on page auth');
            if (addCommentTrigger) addCommentTrigger.style.display = 'block';
            if (loginPrompt) loginPrompt.style.display = 'none';
            return;
        }

        if (data.can_comment) {
            // User can comment
            console.log('User can comment - showing add button');
            if (addCommentTrigger) addCommentTrigger.style.display = 'block';
            if (loginPrompt) loginPrompt.style.display = 'none';
        } else if (data.has_commented) {
            // User already commented
            console.log('User already commented - hiding both');
            if (addCommentTrigger) addCommentTrigger.style.display = 'none';
            if (loginPrompt) loginPrompt.style.display = 'none';
        } else {
            // User not logged in or can't comment
            console.log('User cannot comment - showing login prompt');
            if (addCommentTrigger) addCommentTrigger.style.display = 'none';
            if (loginPrompt) loginPrompt.style.display = 'block';
        }

    } catch (error) {
        console.error('Error checking user comment status:', error);
        // Fallback: if logged in by page, show comment button
        const addCommentTrigger = document.getElementById('addCommentTrigger');
        const loginPrompt = document.getElementById('loginPrompt');

        if (isLoggedInByPage) {
            console.log('API error but user is logged in - showing comment button');
            if (addCommentTrigger) addCommentTrigger.style.display = 'block';
            if (loginPrompt) loginPrompt.style.display = 'none';
        } else {
            if (addCommentTrigger) addCommentTrigger.style.display = 'none';
            if (loginPrompt) loginPrompt.style.display = 'block';
        }
    }
}

// Display comments
function displayComments(comments) {
    const commentsList = document.getElementById('commentsList');
    if (!commentsList) return;

    if (comments.length === 0) {
        displayEmptyComments();
        return;
    }

    const commentsHTML = comments.map(comment => createCommentHTML(comment)).join('');
    commentsList.innerHTML = commentsHTML;
}

// Create HTML for a single comment
function createCommentHTML(comment) {
    const userInitials = comment.user_name.split(' ').map(n => n[0]).join('').toUpperCase();
    const ratingStars = comment.rating ? '★'.repeat(comment.rating) + '☆'.repeat(5 - comment.rating) : '';
    const editedBadge = comment.is_edited ? '<span class="edited-badge">Edited</span>' : '';

    const actionButtons = comment.can_edit || comment.can_delete ? `
        <div class="comment-actions">
            ${comment.can_edit ? `
                <button class="action-btn edit" onclick="editComment(${comment.id})">
                    <i class="fas fa-edit"></i>
                    Edit
                </button>
            ` : ''}
            ${comment.can_delete ? `
                <button class="action-btn delete" onclick="deleteComment(${comment.id})">
                    <i class="fas fa-trash"></i>
                    Delete
                </button>
            ` : ''}
        </div>
    ` : '';

    return `
        <div class="comment-card" data-comment-id="${comment.id}">
            <div class="comment-header">
                <div class="comment-user">
                    <div class="user-avatar">${userInitials}</div>
                    <div class="user-info">
                        <h4>${escapeHtml(comment.user_name)}</h4>
                        <p>${comment.user_type.charAt(0).toUpperCase() + comment.user_type.slice(1)} User ${editedBadge}</p>
                    </div>
                </div>
                
                <div class="comment-meta">
                    ${comment.rating ? `
                        <div class="comment-rating">
                            <span class="stars">${ratingStars}</span>
                            <span class="rating-value">${comment.rating}/5</span>
                        </div>
                    ` : ''}
                    <p>${comment.formatted_date}</p>
                </div>
            </div>
            
            <div class="comment-content">
                ${escapeHtml(comment.comment_text)}
            </div>
            
            ${actionButtons}
        </div>
    `;
}

// Display empty comments state
function displayEmptyComments() {
    const commentsList = document.getElementById('commentsList');
    if (!commentsList) return;

    commentsList.innerHTML = `
        <div class="empty-comments">
            <i class="fas fa-comments"></i>
            <h4>No comments yet</h4>
            <p>Be the first to share your experience about this event.</p>
        </div>
    `;

    // Check if user can comment when displaying empty state
    checkUserCommentStatus();
}

// Update comments statistics
function updateCommentsStats(stats) {
    const totalCommentsCount = document.getElementById('totalCommentsCount');
    const averageRatingDisplay = document.getElementById('averageRatingDisplay');
    const averageRatingValue = document.getElementById('averageRatingValue');

    if (totalCommentsCount) {
        totalCommentsCount.textContent = stats.total_comments;
    }

    if (stats.average_rating && averageRatingDisplay && averageRatingValue) {
        averageRatingValue.textContent = stats.average_rating;
        averageRatingDisplay.style.display = 'inline-flex';
    } else if (averageRatingDisplay) {
        averageRatingDisplay.style.display = 'none';
    }
}

// Show comment form
function showCommentForm() {
    const addCommentSection = document.getElementById('addCommentSection');
    const addCommentTrigger = document.getElementById('addCommentTrigger');

    if (addCommentSection) addCommentSection.style.display = 'block';
    if (addCommentTrigger) addCommentTrigger.style.display = 'none';

    // Focus on textarea
    const commentText = document.getElementById('commentText');
    if (commentText) {
        commentText.focus();
    }
}

// Make function globally accessible
window.showCommentForm = showCommentForm;

// Hide comment form
function hideCommentForm() {
    const addCommentSection = document.getElementById('addCommentSection');
    const addCommentTrigger = document.getElementById('addCommentTrigger');

    if (addCommentSection) addCommentSection.style.display = 'none';
    if (addCommentTrigger) addCommentTrigger.style.display = 'block';

    // Reset form
    resetCommentForm();
}

// Reset comment form
function resetCommentForm() {
    const commentText = document.getElementById('commentText');
    const charCount = document.getElementById('charCount');
    const ratingText = document.getElementById('ratingText');

    if (commentText) commentText.value = '';
    if (charCount) {
        charCount.textContent = '0';
        charCount.classList.remove('warning', 'danger');
    }

    currentUserRating = 0;
    highlightStars(document.getElementById('ratingInput'), 0);
    updateRatingText('ratingText', 0);
}

// Submit comment
async function submitComment() {
    const commentText = document.getElementById('commentText').value.trim();

    if (!commentText) {
        showToast('Please enter a comment', 'error');
        return;
    }

    if (commentText.length < 5) {
        showToast('Comment must be at least 5 characters long', 'error');
        return;
    }

    const submitBtn = document.getElementById('submitCommentBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';
    submitBtn.disabled = true;

    try {
        // Get user info from the page as fallback for session issues
        const userElement = document.querySelector('.username, #username');
        const userName = userElement ? userElement.textContent.trim() : '';

        console.log('Submitting comment:', {
            event_id: currentEvent.id,
            comment_text: commentText,
            rating: currentUserRating,
            fallback_user_name: userName
        });

        const response = await fetch('/unipulse/public/user/comments/addComment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin', // Include session cookies
            body: JSON.stringify({
                event_id: currentEvent.id,
                comment_text: commentText,
                rating: currentUserRating || null,
                fallback_user_name: userName // Add fallback user identification
            })
        });

        console.log('Response status:', response.status);

        const responseText = await response.text();
        let data = null;

        try {
            data = JSON.parse(responseText);
        } catch (parseError) {
            console.error('Non-JSON addComment response:', responseText);
            if (!response.ok) {
                throw new Error(`Request failed (${response.status})`);
            }
            throw new Error('Invalid server response');
        }

        if (!response.ok) {
            throw new Error(data?.error || `HTTP error! status: ${response.status}`);
        }

        console.log('Response data:', data);

        if (data.success) {
            showToast('Comment posted successfully!', 'success');
            hideCommentForm();
            resetCommentForm();
            loadComments(); // Reload comments to show the new one
        } else {
            showToast(data.error || 'Failed to post comment', 'error');
            console.error('Comment submission error:', data);
        }
    } catch (error) {
        console.error('Error posting comment:', error);
        showToast(error.message || 'Failed to post comment', 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// Edit comment
function editComment(commentId) {
    // Find the comment
    const commentCard = document.querySelector(`[data-comment-id="${commentId}"]`);
    if (!commentCard) return;

    const commentContent = commentCard.querySelector('.comment-content').textContent.trim();
    const ratingElement = commentCard.querySelector('.rating-value');
    const currentRating = ratingElement ? parseInt(ratingElement.textContent.split('/')[0]) : 0;

    // Set editing state
    editingCommentId = commentId;
    editingCommentRating = currentRating;

    // Populate edit form
    const editCommentText = document.getElementById('editCommentText');
    const editCharCount = document.getElementById('editCharCount');

    if (editCommentText) {
        editCommentText.value = commentContent;
        editCharCount.textContent = commentContent.length;
    }

    // Set rating
    highlightStars(document.getElementById('editRatingInput'), currentRating);
    updateRatingText('editRatingText', currentRating);

    // Show modal
    document.getElementById('editCommentModal').style.display = 'flex';
}

// Make function globally accessible
window.editComment = editComment;

// Update comment
async function updateComment() {
    const commentText = document.getElementById('editCommentText').value.trim();

    if (!commentText) {
        showToast('Please enter a comment', 'error');
        return;
    }

    if (commentText.length < 5) {
        showToast('Comment must be at least 5 characters long', 'error');
        return;
    }

    const updateBtn = document.getElementById('updateCommentBtn');
    const originalText = updateBtn.innerHTML;
    updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    updateBtn.disabled = true;

    try {
        const response = await fetch(`/unipulse/public/user/comments/updateComment/${editingCommentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin', // Include session cookies
            body: JSON.stringify({
                comment_text: commentText,
                rating: editingCommentRating || null
            })
        });

        const data = await response.json();

        if (data.success) {
            showToast('Comment updated successfully!', 'success');
            closeEditCommentModal();
            loadComments(); // Reload comments
        } else {
            showToast(data.error || 'Failed to update comment', 'error');
        }
    } catch (error) {
        console.error('Error updating comment:', error);
        showToast('Failed to update comment', 'error');
    } finally {
        updateBtn.innerHTML = originalText;
        updateBtn.disabled = false;
    }
}

// Delete comment
function deleteComment(commentId) {
    editingCommentId = commentId;
    document.getElementById('deleteCommentModal').style.display = 'flex';
}

// Make function globally accessible
window.deleteComment = deleteComment;

// Confirm delete comment
async function confirmDeleteComment() {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
    deleteBtn.disabled = true;

    try {
        const response = await fetch(`/unipulse/public/user/comments/deleteComment/${editingCommentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin' // Include session cookies
        });

        const data = await response.json();

        if (data.success) {
            showToast('Comment deleted successfully!', 'success');
            closeDeleteCommentModal();
            loadComments(); // Reload comments
        } else {
            showToast(data.error || 'Failed to delete comment', 'error');
        }
    } catch (error) {
        console.error('Error deleting comment:', error);
        showToast('Failed to delete comment', 'error');
    } finally {
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
    }
}

// Modal functions for comments
function closeEditCommentModal() {
    document.getElementById('editCommentModal').style.display = 'none';
    editingCommentId = null;
    editingCommentRating = 0;
}

function closeDeleteCommentModal() {
    document.getElementById('deleteCommentModal').style.display = 'none';
    editingCommentId = null;
}

// Make functions globally accessible
window.closeEditCommentModal = closeEditCommentModal;
window.closeDeleteCommentModal = closeDeleteCommentModal;

// Close modals when clicking outside
window.addEventListener('click', function (event) {
    const joinModal = document.getElementById('joinModal');
    const shareModal = document.getElementById('shareModal');
    const volunteerConsentModal = document.getElementById('volunteerConsentModal');
    const editCommentModal = document.getElementById('editCommentModal');
    const deleteCommentModal = document.getElementById('deleteCommentModal');

    if (event.target === joinModal) {
        closeJoinModal();
    }
    if (event.target === shareModal) {
        closeShareModal();
    }
    if (event.target === volunteerConsentModal) {
        closeVolunteerConsentModal();
    }
    if (event.target === editCommentModal) {
        closeEditCommentModal();
    }
    if (event.target === deleteCommentModal) {
        closeDeleteCommentModal();
    }
});

// Toast notification system
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
        ${message}
    `;

    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add('show'), 100);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 3000);
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

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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

function visitPublisherProfile() {
    console.log('visitPublisherProfile called');
    console.log('currentEvent:', currentEvent);
    const publisherId = currentEvent?.organizerId || currentEvent?.created_by;
    console.log('publisherId:', publisherId);
    if (publisherId) {
        const url = `/unipulse/public/publisher/public?id=${publisherId}`;
        console.log('Redirecting to:', url);
        window.location.href = url;
    } else {
        console.error('No publisher ID found in event data');
        alert('Publisher profile not available.');
    }
}
