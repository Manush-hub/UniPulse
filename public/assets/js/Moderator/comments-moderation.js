// Moderator Comments Moderation – 3-panel chat-style layout
// Flow: Publishers list → Events list → Comments panel

let allComments       = [];
let selectedPublisher = null;   // publisher_name string
let selectedEventId   = null;   // event_id number/string
let hidingId          = null;

document.addEventListener('DOMContentLoaded', () => {
    loadUniversityComments();
    document.getElementById('statusFilter')?.addEventListener('change', renderComments);
});

// ─── API ──────────────────────────────────────────────────────────────────────
function loadUniversityComments() {
    fetch('/unipulse/public/moderator/comments/getUniversityComments')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                allComments = data.comments || [];
                updateStats(data.stats || {});
                renderPublisherList();
            } else {
                showPanelError('publisherList', 'Failed to load: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            showPanelError('publisherList', 'Error loading data. Please refresh.');
        });
}

// ─── Stats ────────────────────────────────────────────────────────────────────
function updateStats(stats) {
    setEl('totalComments',   stats.total_comments   || 0);
    setEl('visibleComments', stats.visible_comments || 0);
    setEl('hiddenComments',  stats.hidden_comments  || 0);
    setEl('moderatedToday',  stats.moderated_today  || 0);
}
function setEl(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
}

// ─── Panel 1 · Publishers ─────────────────────────────────────────────────────
function renderPublisherList(query = '') {
    const list = document.getElementById('publisherList');
    if (!list) return;

    // Build unique publishers with comment/event counts
    const pubMap = {};
    allComments.forEach(c => {
        const pub = c.publisher_name || 'Unknown Publisher';
        if (!pubMap[pub]) pubMap[pub] = { events: new Set(), total: 0, hidden: 0 };
        pubMap[pub].events.add(c.event_id);
        pubMap[pub].total++;
        if (c.is_hidden) pubMap[pub].hidden++;
    });

    const publishers = Object.entries(pubMap).sort((a, b) => a[0].localeCompare(b[0]));
    const lq = query.toLowerCase();
    const filtered = lq ? publishers.filter(([name]) => name.toLowerCase().includes(lq)) : publishers;

    setEl('publisherCount', filtered.length);

    if (!filtered.length) {
        list.innerHTML = `<div class="panel-empty"><i class="fas fa-building"></i><p>No publishers found</p></div>`;
        return;
    }

    list.innerHTML = filtered.map(([name, meta]) => {
        const initials  = name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
        const isActive  = name === selectedPublisher;
        const hiddenChip = meta.hidden > 0
            ? `<span class="item-chip chip-hidden">${meta.hidden} hidden</span>` : '';
        return `
        <div class="panel-item ${isActive ? 'panel-item--active' : ''}" data-pub="${escapeHtml(name)}" onclick="selectPublisher(this.dataset.pub)">
            <div class="item-avatar item-avatar--pub">${initials}</div>
            <div class="item-body">
                <div class="item-name">${escapeHtml(name)}</div>
                <div class="item-meta">
                    <span class="item-chip">${meta.events.size} event${meta.events.size !== 1 ? 's' : ''}</span>
                    <span class="item-chip">${meta.total} comment${meta.total !== 1 ? 's' : ''}</span>
                    ${hiddenChip}
                </div>
            </div>
            <i class="fas fa-chevron-right item-arrow"></i>
        </div>`;
    }).join('');
}

function filterPublisherList(query) {
    renderPublisherList(query);
}

function selectPublisher(name) {
    selectedPublisher = name;
    selectedEventId   = null;

    renderPublisherList(document.getElementById('publisherSearch')?.value || '');

    const label = document.getElementById('selectedPublisherLabel');
    if (label) label.textContent = name;

    const es = document.getElementById('eventSearch');
    if (es) es.value = '';

    renderEventList();
    resetCommentsPanel();
}

// ─── Panel 2 · Events ─────────────────────────────────────────────────────────
function renderEventList(query = '') {
    const list = document.getElementById('eventList');
    if (!list) return;

    if (!selectedPublisher) {
        list.innerHTML = `<div class="panel-placeholder"><i class="fas fa-arrow-left"></i><p>Pick a publisher first</p></div>`;
        setEl('eventCount', '–');
        return;
    }

    const evMap = {};
    allComments
        .filter(c => c.publisher_name === selectedPublisher)
        .forEach(c => {
            const eid = String(c.event_id ?? 'unknown');
            if (!evMap[eid]) evMap[eid] = { id: c.event_id, title: c.event_title || 'Untitled Event', total: 0, hidden: 0 };
            evMap[eid].total++;
            if (c.is_hidden) evMap[eid].hidden++;
        });

    const events = Object.values(evMap).sort((a, b) => a.title.localeCompare(b.title));
    const lq = query.toLowerCase();
    const filtered = lq ? events.filter(e => e.title.toLowerCase().includes(lq)) : events;

    setEl('eventCount', filtered.length);

    if (!filtered.length) {
        list.innerHTML = `<div class="panel-empty"><i class="fas fa-calendar-alt"></i><p>No events found</p></div>`;
        return;
    }

    list.innerHTML = filtered.map(ev => {
        const isActive   = String(ev.id) === String(selectedEventId);
        const hiddenChip = ev.hidden > 0
            ? `<span class="item-chip chip-hidden">${ev.hidden} hidden</span>` : '';
        return `
        <div class="panel-item ${isActive ? 'panel-item--active' : ''}" data-eid="${escapeHtml(String(ev.id))}" onclick="selectEvent(this.dataset.eid)">
            <div class="item-avatar item-avatar--event"><i class="fas fa-calendar-alt"></i></div>
            <div class="item-body">
                <div class="item-name">${escapeHtml(ev.title)}</div>
                <div class="item-meta">
                    <span class="item-chip">${ev.total} comment${ev.total !== 1 ? 's' : ''}</span>
                    ${hiddenChip}
                </div>
            </div>
            <i class="fas fa-chevron-right item-arrow"></i>
        </div>`;
    }).join('');
}

function filterEventList(query) {
    renderEventList(query);
}

function selectEvent(eventId) {
    selectedEventId = eventId;

    renderEventList(document.getElementById('eventSearch')?.value || '');

    const ev    = allComments.find(c => String(c.event_id) === String(eventId) && c.publisher_name === selectedPublisher);
    const title = ev?.event_title || 'Event';

    const titleEl = document.getElementById('commentsEventTitle');
    if (titleEl) titleEl.innerHTML = `<i class="fas fa-comment-dots"></i><span>${escapeHtml(title)}</span>`;

    const ctxLabel = document.getElementById('commentsContextLabel');
    if (ctxLabel) ctxLabel.textContent = selectedPublisher || '';

    const si = document.getElementById('searchInput');
    const sf = document.getElementById('statusFilter');
    if (si) si.value = '';
    if (sf) sf.value = '';

    renderComments();
}

function resetCommentsPanel() {
    const titleEl = document.getElementById('commentsEventTitle');
    if (titleEl) titleEl.innerHTML = `<i class="fas fa-comment-dots"></i><span>Comments</span>`;

    const ctxLabel = document.getElementById('commentsContextLabel');
    if (ctxLabel) ctxLabel.textContent = 'No event selected';

    setEl('commentsCount', '–');

    const cl = document.getElementById('commentsList');
    if (cl) cl.innerHTML = `
        <div class="panel-placeholder">
            <i class="fas fa-calendar-alt"></i>
            <p>Select an event to view comments</p>
        </div>`;
}

// ─── Panel 3 · Comments ───────────────────────────────────────────────────────
function renderComments() {
    const cl = document.getElementById('commentsList');
    if (!cl) return;

    if (!selectedEventId) { resetCommentsPanel(); return; }

    const search = (document.getElementById('searchInput')?.value || '').toLowerCase();
    const status =  document.getElementById('statusFilter')?.value || '';

    const filtered = allComments.filter(c => {
        if (String(c.event_id) !== String(selectedEventId)) return false;
        if (c.publisher_name !== selectedPublisher)         return false;
        if (status === 'visible' && c.is_hidden)            return false;
        if (status === 'hidden'  && !c.is_hidden)           return false;
        if (search) {
            const hay = [c.comment_text, c.user_name, c.user_email].join(' ').toLowerCase();
            if (!hay.includes(search))                      return false;
        }
        return true;
    });

    setEl('commentsCount', filtered.length);

    if (!filtered.length) {
        cl.innerHTML = `
            <div class="panel-empty">
                <i class="fas fa-comment-slash"></i>
                <p>No comments match your filters</p>
            </div>`;
        return;
    }

    cl.innerHTML = filtered.map(c => buildCard(c)).join('');
}

// ─── Comment card ─────────────────────────────────────────────────────────────
function buildCard(c) {
    const userName  = c.user_name || 'Anonymous';
    const initials  = userName.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
    const stars     = c.rating ? ('★'.repeat(c.rating) + '☆'.repeat(5 - c.rating)) : '';
    const editBadge = c.is_edited ? '<span class="edited-badge">Edited</span>' : '';
    const userLabel = c.user_type ? capitalizeFirst(c.user_type) + ' User' : 'User';

    const statusBadge = c.is_hidden
        ? '<span class="status-badge hidden-badge"><i class="fas fa-eye-slash"></i> Hidden</span>'
        : '<span class="status-badge visible-badge"><i class="fas fa-eye"></i> Visible</span>';

    const hiddenInfo = c.is_hidden ? `
        <div class="hidden-banner">
            <i class="fas fa-eye-slash"></i>
            Hidden by <strong>${escapeHtml(c.hidden_by_name || 'Moderator')}</strong>
            ${c.hidden_at ? ` on ${new Date(c.hidden_at).toLocaleDateString()}` : ''}
            ${c.hidden_reason ? `<br><em>Reason: ${escapeHtml(c.hidden_reason)}</em>` : ''}
        </div>` : '';

    const actionBtn = c.is_hidden
        ? `<button class="action-btn unhide-btn" onclick="unhideComment(${c.id})">
               <i class="fas fa-eye"></i> Restore
           </button>`
        : `<button class="action-btn hide-btn" onclick="openHideModal(${c.id})">
               <i class="fas fa-eye-slash"></i> Hide
           </button>`;

    return `
    <div class="comment-card ${c.is_hidden ? 'is-hidden' : ''}" data-id="${c.id}">
        <div class="comment-card-topbar">${statusBadge}</div>
        ${hiddenInfo}
        <div class="comment-header">
            <div class="comment-user">
                <div class="user-avatar">${initials}</div>
                <div class="user-info">
                    <h4>${escapeHtml(userName)}</h4>
                    <p>${userLabel} ${editBadge}</p>
                    <small style="color:#94a3b8;">${escapeHtml(c.user_email || '')}</small>
                </div>
            </div>
            <div class="comment-meta">
                ${c.rating ? `<div class="comment-rating"><span class="stars">${stars}</span><span class="rating-value">${c.rating}/5</span></div>` : ''}
                <p>${c.formatted_date || ''}</p>
            </div>
        </div>
        <div class="comment-content">${escapeHtml(c.comment_text)}</div>
        <div class="comment-actions">${actionBtn}</div>
    </div>`;
}

// ─── Hide modal ───────────────────────────────────────────────────────────────
function openHideModal(commentId) {
    hidingId = commentId;
    const modal    = document.getElementById('hideModal');
    const textarea = document.getElementById('hideReason');
    const errEl    = document.getElementById('hideError');
    const btn      = document.getElementById('hideSubmitBtn');
    if (textarea) textarea.value = '';
    if (errEl)    errEl.style.display = 'none';
    if (btn)      { btn.disabled = false; btn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Comment'; }
    if (modal)    modal.classList.add('active');
}

function closeHideModal() {
    hidingId = null;
    document.getElementById('hideModal')?.classList.remove('active');
}

function confirmHideComment() {
    if (!hidingId) return;
    const reason = (document.getElementById('hideReason')?.value || '').trim();
    const errEl  = document.getElementById('hideError');
    const btn    = document.getElementById('hideSubmitBtn');

    if (!reason || reason.length < 10) {
        if (errEl) { errEl.textContent = 'Please provide a reason (at least 10 characters).'; errEl.style.display = 'block'; }
        return;
    }

    if (btn)  { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Hiding…'; }
    if (errEl)  errEl.style.display = 'none';

    fetch('/unipulse/public/moderator/comments/hideComment', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ comment_id: hidingId, reason })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeHideModal();
            showToast('Comment hidden successfully.', 'success');
            reloadAndRefresh();
        } else {
            if (errEl) { errEl.textContent = data.error || 'Failed to hide comment.'; errEl.style.display = 'block'; }
            if (btn)   { btn.disabled = false; btn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Comment'; }
        }
    })
    .catch(() => {
        if (errEl) { errEl.textContent = 'An error occurred. Please try again.'; errEl.style.display = 'block'; }
        if (btn)   { btn.disabled = false; btn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Comment'; }
    });
}

// ─── Unhide ───────────────────────────────────────────────────────────────────
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
            showToast('Comment restored.', 'success');
            reloadAndRefresh();
        } else {
            showToast(data.error || 'Failed to restore comment.', 'error');
        }
    })
    .catch(() => showToast('An error occurred.', 'error'));
}

// ─── Reload & keep selection ──────────────────────────────────────────────────
function reloadAndRefresh() {
    const savedPub   = selectedPublisher;
    const savedEvent = selectedEventId;

    fetch('/unipulse/public/moderator/comments/getUniversityComments')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                allComments       = data.comments || [];
                selectedPublisher = savedPub;
                selectedEventId   = savedEvent;

                updateStats(data.stats || {});
                renderPublisherList(document.getElementById('publisherSearch')?.value || '');

                if (savedPub) {
                    const lb = document.getElementById('selectedPublisherLabel');
                    if (lb) lb.textContent = savedPub;
                    renderEventList(document.getElementById('eventSearch')?.value || '');
                }

                if (savedEvent) renderComments();
            }
        })
        .catch(console.error);
}

// ─── Utilities ────────────────────────────────────────────────────────────────
function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}
function capitalizeFirst(str) { return str ? str.charAt(0).toUpperCase() + str.slice(1) : ''; }
function showPanelError(panelId, msg) {
    const el = document.getElementById(panelId);
    if (el) el.innerHTML = `<div class="panel-error"><i class="fas fa-exclamation-circle"></i> ${escapeHtml(msg)}</div>`;
}
function showToast(message, type = 'info') {
    const div = document.createElement('div');
    div.style.cssText = `
        position:fixed;top:20px;right:20px;
        background:${type === 'success' ? '#22c55e' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color:#fff;padding:14px 20px;border-radius:8px;
        box-shadow:0 4px 16px rgba(0,0,0,.15);z-index:10000;
        font-size:.9rem;opacity:0;transform:translateX(100%);
        transition:all .3s ease;`;
    div.textContent = message;
    document.body.appendChild(div);
    setTimeout(() => { div.style.opacity = '1'; div.style.transform = 'translateX(0)'; }, 50);
    setTimeout(() => {
        div.style.opacity = '0'; div.style.transform = 'translateX(100%)';
        setTimeout(() => div.parentNode?.removeChild(div), 300);
    }, 3500);
}
