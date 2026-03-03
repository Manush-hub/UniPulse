// Moderator Event View JavaScript
// Handles event display + comment moderation (hide/unhide)

let currentEvent = window.serverData?.event || null;
const hasError   = window.serverData?.error || null;
const apiEndpoint = window.serverData?.apiEndpoint || '/unipulse/public/moderator/eventview/getEvent';

console.log('=== Moderator EventView ===');
console.log('currentEvent:', currentEvent);
console.log('hasError:', hasError);

document.addEventListener('DOMContentLoaded', function () {
    loadEventDetails();
});

// ─── URL helpers ────────────────────────────────────────────────
function getEventIdFromURL() {
    return new URLSearchParams(window.location.search).get('id');
}

// ─── Load event ─────────────────────────────────────────────────
function loadEventDetails() {
    if (hasError) {
        hideLoading();
        showError();
        return;
    }

    if (currentEvent) {
        displayEventDetails(currentEvent);
        hideLoading();
        showEventContainer();
        return;
    }

    const eventId = getEventIdFromURL();
    if (!eventId) {
        hideLoading();
        showError();
        return;
    }

    fetch(`${apiEndpoint}?id=${eventId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                currentEvent = data.event;
                displayEventDetails(currentEvent);
                hideLoading();
                showEventContainer();
            } else {
                hideLoading();
                showError();
            }
        })
        .catch(err => {
            console.error('Error loading event:', err);
            hideLoading();
            showError();
        });
}

// ─── Display event details ───────────────────────────────────────
function displayEventDetails(event) {
    if (!event) { showError(); return; }

    try {
        const universityName      = event.university_name || event.universityName;
        const maxParticipants     = event.max_participants || event.maxParticipants;
        const currentParticipants = event.current_participants || event.currentParticipants || 0;
        const ticketType          = event.ticket_type  || event.ticketType;
        const imageUrl            = event.image_url || event.imageUrl || event.cover_image || event.image;

        // Hero image
        if (imageUrl) {
            const heroContainer = document.getElementById('heroImageContainer');
            const heroImg       = document.getElementById('heroImage');
            if (heroContainer && heroImg) {
                let imagePath = imageUrl.startsWith('http') || imageUrl.startsWith('/') ? imageUrl : `/unipulse/public/${imageUrl}`;
                heroImg.src = imagePath;
                heroImg.alt = event.title + ' Cover Image';
                heroContainer.style.display = 'block';
                heroImg.onerror = () => { heroContainer.style.display = 'none'; };
            }
        }

        setEl('eventCategory', el => el.textContent = capitalizeFirstLetter(event.category));

        const eventDate = event.event_date || event.date;
        const eventTime = event.event_time || event.time;
        const computedStatus = getEventStatus(eventDate, eventTime, event.event_end_time);

        setEl('eventStatus', el => {
            el.textContent = capitalizeFirstLetter(computedStatus);
            el.className = `event-status ${computedStatus}`;
        });
        setEl('eventTitle',    el => el.textContent = event.title);
        setEl('eventDateTime', el => el.textContent = `${formatDate(eventDate)} at ${eventTime}`);

        // Location
        const locationType = event.location_type || 'inside-university';
        if (locationType === 'outside-university') {
            const venue   = event.venue_name || event.venueName || '';
            const city    = event.city || '';
            const display = venue && city ? `${venue}, ${city}` : (venue || city || 'Location TBA');
            setStyle('venueInfo',           'display', 'flex');
            setEl('eventVenueCity',         el => el.textContent = display);
            setStyle('universityInfo',      'display', 'none');
            setStyle('facultyInfo',         'display', 'none');
            setStyle('exactLocationInfo',   'display', 'none');
        } else {
            setStyle('universityInfo', 'display', 'flex');
            setEl('eventUniversity', el => el.textContent = universityName);
            if (event.faculty_department) {
                setStyle('facultyInfo', 'display', 'flex');
                setEl('eventFaculty',   el => el.textContent = event.faculty_department);
            }
            setStyle('exactLocationInfo', 'display', 'flex');
            setEl('eventLocation',        el => el.textContent = event.location);
            setStyle('venueInfo',         'display', 'none');
        }

        setEl('eventAudience', el => el.textContent = formatAudience(event.target_audience || event.targetAudience));

        setStyle('ticketInfo', 'display', 'block');
        setEl('eventTicketType', el => {
            el.innerHTML = ticketType === 'free-all'
                ? '<span style="color:#10B981;font-weight:600;">Free Event</span>'
                : formatTicketType(ticketType);
        });

        setEl('eventDescription', el => el.textContent = event.description);

        displayRegistrationTicketPeriods(event);

        // Schedule
        const scheduleCard = document.getElementById('scheduleCard');
        if (event.schedule && Array.isArray(event.schedule) && event.schedule.length) {
            displaySchedule(event.schedule);
            if (scheduleCard) scheduleCard.style.display = 'block';
        } else if (scheduleCard) scheduleCard.style.display = 'none';

        // Requirements
        const reqCard = document.getElementById('requirementsCard');
        if (event.requirements && Array.isArray(event.requirements) && event.requirements.length) {
            displayRequirements(event.requirements);
            if (reqCard) reqCard.style.display = 'block';
        } else if (reqCard) reqCard.style.display = 'none';

        // Custom fields
        const cfCard = document.getElementById('customFieldsCard');
        if (event.custom_fields && Array.isArray(event.custom_fields) && event.custom_fields.length) {
            displayCustomFields(event.custom_fields);
        } else if (cfCard) cfCard.style.display = 'none';

        // Hide registration / ticketing / volunteer / donation cards for moderator
        ['registrationCard', 'ticketingCard', 'volunteerCard', 'volunteerInvolvementCard', 'donationCard',
         'volunteerDonationHeader', 'volunteerDonationGrid', 'joinCard'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });

        // Organizer
        setEl('organizerName', el => el.textContent = event.organizer_name || event.organizer);
        setEl('organizerRole', el => el.textContent = event.organizer_role || 'Event Organizer');
        const orgAvatar = document.getElementById('organizerAvatar');
        if (orgAvatar) {
            orgAvatar.innerHTML = event.organizer_photo
                ? `<img src="${event.organizer_photo}" alt="${event.organizer_name || event.organizer}" />`
                : '<i class="fas fa-user-circle"></i>';
        }
        const callBtn = document.getElementById('callOrganizerBtn');
        if (callBtn) {
            if (event.organizer_phone) {
                callBtn.onclick = () => window.location.href = `tel:${event.organizer_phone}`;
                callBtn.setAttribute('title', `Call: ${event.organizer_phone}`);
            } else {
                callBtn.disabled = true;
                callBtn.style.opacity = '0.5';
                callBtn.setAttribute('title', 'Phone not available');
            }
        }

        // Capacity stats
        if (maxParticipants !== null && maxParticipants !== undefined) {
            const statsReg = document.getElementById('eventStatsRegistration');
            if (statsReg) {
                statsReg.style.display = 'block';
                setEl('totalParticipantsReg', el => el.textContent = currentParticipants);
                setEl('availableSpotsReg',    el => el.textContent = maxParticipants - currentParticipants);
                setEl('maxCapacityReg',       el => el.textContent = maxParticipants);
                const pct = maxParticipants > 0 ? Math.round((currentParticipants / maxParticipants) * 100) : 0;
                setEl('capacityPercentage', el => el.textContent = `${pct}%`);
                const fill = document.getElementById('capacityFill');
                if (fill) {
                    fill.style.width = `${pct}%`;
                    fill.style.background = pct >= 90
                        ? 'linear-gradient(90deg,#ef4444,#dc2626)'
                        : pct >= 70
                        ? 'linear-gradient(90deg,#f59e0b,#d97706)'
                        : 'linear-gradient(90deg,#10b981,#059669)';
                }
            }
        } else {
            const statsReg = document.getElementById('eventStatsRegistration');
            if (statsReg) statsReg.style.display = 'none';
        }

        const shareLink = document.getElementById('shareLink');
        if (shareLink) shareLink.value = window.location.href;

        // Initialize comments
        initializeComments();

    } catch (err) {
        console.error('Error in displayEventDetails:', err);
        showError();
    }
}

// ─── Registration / Ticket periods ──────────────────────────────
function displayRegistrationTicketPeriods(event) {
    const ticketType        = event.ticket_type || 'free-all';
    const hasRegDates       = event.registration_start_date && event.registration_end_date;
    const hasTicketDates    = event.ticket_sale_start_date  && event.ticket_sale_end_date;
    const periodCard        = document.getElementById('registrationTicketPeriodCard');
    const freeRegSection    = document.getElementById('freeRegistrationPeriod');
    const ticketBuySection  = document.getElementById('ticketBuyingPeriod');
    const divider           = document.getElementById('periodDivider');

    if (!periodCard || !freeRegSection || !ticketBuySection) return;

    let showAny = false;

    if (ticketType === 'free-all' && hasRegDates) {
        showAny = true;
        freeRegSection.style.display   = 'block';
        ticketBuySection.style.display = 'none';
        const s = getRegistrationStatus(event.registration_start_date, event.registration_end_date);
        setEl('freeRegPeriodDates', el => el.innerHTML  = buildPeriodDatesHTML(event.registration_start_date, event.registration_end_date));
        setEl('freeRegPeriodStatus', el => { el.innerHTML = `<i class="fas fa-${s.icon}"></i> ${s.text}`; el.className = `period-status status-${s.class}`; });
    } else { freeRegSection.style.display = 'none'; }

    if ((ticketType === 'paid-all' || ticketType === 'mixed') && hasTicketDates) {
        showAny = true;
        ticketBuySection.style.display = 'block';
        const s = getRegistrationStatus(event.ticket_sale_start_date, event.ticket_sale_end_date);
        setEl('ticketPeriodDates',  el => el.innerHTML  = buildPeriodDatesHTML(event.ticket_sale_start_date, event.ticket_sale_end_date));
        setEl('ticketPeriodStatus', el => { el.innerHTML = `<i class="fas fa-${s.icon}"></i> ${s.text}`; el.className = `period-status status-${s.class}`; });
        if (ticketType === 'mixed' && hasRegDates) {
            freeRegSection.style.display = 'block';
            const fs = getRegistrationStatus(event.registration_start_date, event.registration_end_date);
            setEl('freeRegPeriodDates',  el => el.innerHTML  = buildPeriodDatesHTML(event.registration_start_date, event.registration_end_date));
            setEl('freeRegPeriodStatus', el => { el.innerHTML = `<i class="fas fa-${fs.icon}"></i> ${fs.text}`; el.className = `period-status status-${fs.class}`; });
        }
    }

    periodCard.style.display = showAny ? 'block' : 'none';
    if (divider) divider.style.display = (freeRegSection.style.display !== 'none' && ticketBuySection.style.display !== 'none') ? 'block' : 'none';
}

function buildPeriodDatesHTML(start, end) {
    return `
        <span class="period-date-item"><i class="fas fa-calendar-plus"></i> <strong>Opens:</strong> ${formatDate(start)}</span>
        <span class="period-date-separator">→</span>
        <span class="period-date-item"><i class="fas fa-calendar-times"></i> <strong>Closes:</strong> ${formatDate(end)}</span>`;
}

function getRegistrationStatus(startDate, endDate) {
    const now   = new Date();
    const start = new Date(startDate);
    const end   = new Date(endDate);
    if (now < start)              return { text: 'Opening Soon', icon: 'clock',        class: 'upcoming' };
    if (now >= start && now <= end) return { text: 'Open Now',   icon: 'check-circle', class: 'open' };
    return                               { text: 'Closed',       icon: 'times-circle', class: 'closed' };
}

// ─── Sub-display helpers ─────────────────────────────────────────
function displaySchedule(schedule) {
    const container = document.getElementById('eventSchedule');
    if (!container) return;
    container.innerHTML = schedule.map(item => `
        <div class="schedule-item">
            <span class="time">${item.time}</span>
            <span class="activity">${item.activity}</span>
        </div>`).join('');
}

function displayRequirements(requirements) {
    const container = document.getElementById('eventRequirements');
    if (!container) return;
    container.innerHTML = '<ul class="requirements-list">' +
        requirements.map(r => `<li><i class="fas fa-check"></i><span>${r}</span></li>`).join('') +
        '</ul>';
}

function displayLocationDetails(event) {
    // Additional map / detailed-location block (if present in view)
    const mapCard = document.getElementById('mapCard');
    if (mapCard) {
        if (event.google_maps_link || event.latitude) {
            mapCard.style.display = 'block';
        } else {
            mapCard.style.display = 'none';
        }
    }
}

function displayTicketDetails(event) {
    const ticketDetailsCard = document.getElementById('ticketDetailsCard');
    if (ticketDetailsCard) ticketDetailsCard.style.display = 'none';
}

function displayRegistrationTicketing(event) {
    // Moderator sees no join / ticket purchase UI
    ['joinSection', 'ticketSection', 'registrationSection'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });
}

function displayCustomFields(customFields) {
    const card = document.getElementById('customFieldsCard');
    const container = document.getElementById('customFields');
    if (!card || !container) return;
    container.innerHTML = customFields.map(f => `
        <div class="custom-field">
            <span class="field-label">${escapeHtml(f.label)}</span>
            <span class="field-value">${escapeHtml(f.value)}</span>
        </div>`).join('');
    card.style.display = 'block';
}

function displayVolunteerInfo()     { /* no-op for moderator */ }
function displayVolunteerInvolvement() { /* no-op */ }

// ─── UI state helpers ────────────────────────────────────────────
function hideLoading() {
    const el = document.getElementById('loadingContainer');
    if (el) el.style.display = 'none';
}
function showEventContainer() {
    const el = document.getElementById('eventContainer');
    if (el) el.style.display = 'block';
}
function showError() {
    const el = document.getElementById('errorContainer');
    if (el) el.style.display = 'flex';
}

// ─── Utility ────────────────────────────────────────────────────
function setEl(id, fn) {
    const el = document.getElementById(id);
    if (el) fn(el);
}
function setStyle(id, prop, val) {
    const el = document.getElementById(id);
    if (el) el.style[prop] = val;
}
function capitalizeFirstLetter(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}
function formatDate(dateString) {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}
function formatAudience(audience) {
    return { 'university-students': 'University Students', 'public-users': 'Public Users', 'both': 'University Students & Public' }[audience] || audience;
}
function formatTicketType(type) {
    return {
        'free-students': 'Free for University Students',
        'free-all':      'Free for All',
        'paid-all':      'Paid Tickets Required',
        'mixed':         'Free for Students, Paid for Others'
    }[type] || type;
}
function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}
function getEventStatus(eventDate, eventTime, eventEndTime) {
    if (!eventDate) return 'upcoming';
    const now      = new Date();
    const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
    const evStr    = String(eventDate).slice(0, 10);
    if (evStr > todayStr) return 'upcoming';
    if (evStr < todayStr) return 'completed';
    const nowTime  = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;
    const start    = eventTime ? String(eventTime).slice(0, 8) : '00:00:00';
    const end      = eventEndTime ? String(eventEndTime).slice(0, 8) : null;
    if (start > nowTime) return 'upcoming';
    if (end && end <= nowTime) return 'completed';
    return 'ongoing';
}
function showMessage(message, type = 'info') {
    const div = document.createElement('div');
    div.style.cssText = `
        position:fixed;top:20px;right:20px;
        background:${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
        color:#fff;padding:15px 20px;border-radius:8px;
        box-shadow:0 4px 12px rgba(0,0,0,.15);z-index:10000;
        font-size:14px;max-width:400px;opacity:0;
        transform:translateX(100%);transition:all .3s ease;`;
    div.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i> ${escapeHtml(message)}`;
    document.body.appendChild(div);
    setTimeout(() => { div.style.opacity = '1'; div.style.transform = 'translateX(0)'; }, 100);
    setTimeout(() => {
        div.style.opacity = '0'; div.style.transform = 'translateX(100%)';
        setTimeout(() => div.parentNode && div.parentNode.removeChild(div), 300);
    }, 4000);
}

// ─── Share / Contact stubs ───────────────────────────────────────
function openShareModal()  { const el = document.getElementById('shareModal');  if (el) el.style.display = 'flex'; }
function closeShareModal() { const el = document.getElementById('shareModal');  if (el) el.style.display = 'none'; }
function shareViaFacebook() { window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(location.href)}`, '_blank'); }
function shareViaTwitter()  { window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(location.href)}&text=${encodeURIComponent('Check out: ' + (currentEvent?.title || ''))}`, '_blank'); }
function shareViaWhatsApp() { window.open(`https://wa.me/?text=${encodeURIComponent((currentEvent?.title || '') + ' ' + location.href)}`, '_blank'); }
function copyEventLink()    { const el = document.getElementById('shareLink');  if (el) { el.select(); document.execCommand('copy'); } }
function contactOrganizer() {
    const email = currentEvent?.organizer_email || currentEvent?.organizerEmail;
    if (email) window.location.href = `mailto:${email}?subject=Regarding: ${currentEvent.title}`;
    else alert('Organizer contact information not available.');
}

// ─── Comment moderation ──────────────────────────────────────────
function initializeComments() {
    if (!currentEvent) return;
    const eventDate   = currentEvent.event_date || currentEvent.date;
    const eventTime   = currentEvent.event_time || currentEvent.time;
    const status      = getEventStatus(eventDate, eventTime, currentEvent.event_end_time);
    const commentsSection = document.getElementById('commentsSection');
    if (commentsSection && status === 'completed') {
        commentsSection.style.display = 'block';
        loadComments();
    }
}

function loadComments() {
    if (!currentEvent) return;
    const commentsList = document.getElementById('commentsList');
    if (!commentsList) return;

    commentsList.innerHTML = `
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading comments…</p>
        </div>`;

    fetch(`/unipulse/public/moderator/comments/getEventComments?event_id=${currentEvent.id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const stats = {
                    total: data.stats?.total_comments || 0,
                    averageRating: data.stats?.average_rating || null
                };
                displayComments(data.comments, stats);
            } else {
                commentsList.innerHTML = '<div class="error-message"><p>Failed to load comments</p></div>';
            }
        })
        .catch(err => {
            console.error('Error loading comments:', err);
            commentsList.innerHTML = '<div class="error-message"><p>Failed to load comments</p></div>';
        });
}

function displayComments(comments, statistics) {
    const commentsList        = document.getElementById('commentsList');
    const totalCommentsCount  = document.getElementById('totalCommentsCount');
    const avgDisplay          = document.getElementById('averageRatingDisplay');
    const avgValue            = document.getElementById('averageRatingValue');

    if (totalCommentsCount) totalCommentsCount.textContent = statistics.total || 0;

    if (statistics.averageRating && statistics.averageRating > 0 && avgDisplay && avgValue) {
        avgDisplay.style.display = 'inline-flex';
        avgValue.textContent     = statistics.averageRating.toFixed(1);
    } else if (avgDisplay) {
        avgDisplay.style.display = 'none';
    }

    if (!comments || !comments.length) {
        commentsList.innerHTML = `
            <div class="empty-comments">
                <i class="fas fa-comments"></i>
                <h4>No comments yet</h4>
                <p>No one has commented on this event yet.</p>
            </div>`;
        return;
    }

    commentsList.innerHTML = comments.map(comment => {
        const userName   = comment.user_name || 'Anonymous';
        const initials   = userName.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        const stars      = comment.rating ? ('★'.repeat(comment.rating) + '☆'.repeat(5 - comment.rating)) : '';
        const editBadge  = comment.is_edited ? '<span class="edited-badge">Edited</span>' : '';
        const userLabel  = comment.user_type ? capitalizeFirstLetter(comment.user_type) + ' User' : 'User';

        const hiddenBanner = comment.is_hidden ? `
            <div class="comment-hidden-banner" style="
                background:#fff3cd;border:1px solid #ffc107;border-radius:6px;
                padding:8px 12px;margin-bottom:8px;font-size:0.85rem;color:#856404;">
                <i class="fas fa-eye-slash"></i>
                <strong>Hidden</strong> by ${escapeHtml(comment.hidden_by_name || 'Moderator')}
                ${comment.hidden_at ? ` on ${new Date(comment.hidden_at).toLocaleDateString()}` : ''}
                ${comment.hidden_reason ? `<br><em>Reason: ${escapeHtml(comment.hidden_reason)}</em>` : ''}
            </div>` : '';

        const actionBtn = comment.is_hidden
            ? `<button class="action-btn unhide-btn" onclick="unhideComment(${comment.id})" title="Restore this comment">
                   <i class="fas fa-eye"></i> Unhide
               </button>`
            : `<button class="action-btn hide-btn" onclick="openHideCommentModal(${comment.id})" title="Hide this comment">
                   <i class="fas fa-eye-slash"></i> Hide
               </button>`;

        return `
            <div class="comment-card ${comment.is_hidden ? 'comment-is-hidden' : ''}" data-comment-id="${comment.id}"
                 style="${comment.is_hidden ? 'opacity:.7;border-left:3px solid #ffc107;' : ''}">
                ${hiddenBanner}
                <div class="comment-header">
                    <div class="comment-user">
                        <div class="user-avatar">${initials}</div>
                        <div class="user-info">
                            <h4>${escapeHtml(userName)}</h4>
                            <p>${userLabel} ${editBadge}</p>
                        </div>
                    </div>
                    <div class="comment-meta">
                        ${comment.rating ? `<div class="comment-rating"><span class="stars">${stars}</span><span class="rating-value">${comment.rating}/5</span></div>` : ''}
                        <p>${comment.formatted_date || ''}</p>
                    </div>
                </div>
                <div class="comment-content">${escapeHtml(comment.comment_text)}</div>
                <div class="comment-actions">${actionBtn}</div>
            </div>`;
    }).join('');
}

// ─── Hide / Unhide ───────────────────────────────────────────────
let hidingCommentId = null;

function openHideCommentModal(commentId) {
    hidingCommentId = commentId;
    const modal = document.getElementById('hideCommentModal');
    if (!modal) return;
    const textarea = document.getElementById('hideCommentReason');
    const errEl    = document.getElementById('hideCommentError');
    if (textarea) textarea.value = '';
    if (errEl)    errEl.style.display = 'none';
    const btn = document.getElementById('hideCommentSubmitBtn');
    if (btn)  { btn.disabled = false; btn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Comment'; }
    modal.style.display = 'flex';
}

function closeHideCommentModal() {
    hidingCommentId = null;
    const modal = document.getElementById('hideCommentModal');
    if (modal) modal.style.display = 'none';
}

function confirmHideComment() {
    if (!hidingCommentId) return;
    const reason  = (document.getElementById('hideCommentReason')?.value || '').trim();
    const errEl   = document.getElementById('hideCommentError');
    const btn     = document.getElementById('hideCommentSubmitBtn');

    if (!reason || reason.length < 10) {
        if (errEl) { errEl.textContent = 'Please provide a reason (min. 10 characters).'; errEl.style.display = 'block'; }
        return;
    }

    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Hiding…'; }
    if (errEl) errEl.style.display = 'none';

    fetch('/unipulse/public/moderator/comments/hideComment', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ comment_id: hidingCommentId, reason })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeHideCommentModal();
            showMessage('Comment hidden successfully.', 'success');
            loadComments();
        } else {
            if (errEl) { errEl.textContent = data.error || 'Failed to hide comment.'; errEl.style.display = 'block'; }
            if (btn)   { btn.disabled = false; btn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Comment'; }
        }
    })
    .catch(err => {
        console.error('Error hiding comment:', err);
        if (errEl) { errEl.textContent = 'An error occurred. Please try again.'; errEl.style.display = 'block'; }
        if (btn)   { btn.disabled = false; btn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Comment'; }
    });
}

function unhideComment(commentId) {
    if (!confirm('Restore this comment so users can see it again?')) return;

    fetch('/unipulse/public/moderator/comments/unhideComment', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ comment_id: commentId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showMessage('Comment restored.', 'success');
            loadComments();
        } else {
            showMessage(data.error || 'Failed to restore comment.', 'error');
        }
    })
    .catch(err => {
        console.error('Error unhiding comment:', err);
        showMessage('An error occurred.', 'error');
    });
}

// ─── Stubs kept for HTML onclick compatibility ───────────────────
function openJoinModal()    {}
function closeJoinModal()   {}
function confirmJoinEvent() {}
function openVolunteerConsentModal()  {}
function closeVolunteerConsentModal() {}
function confirmVolunteerConsent()    {}
function showCommentForm()  {}
