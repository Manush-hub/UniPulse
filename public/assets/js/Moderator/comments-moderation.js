// Moderator Comments Moderation Page JavaScript

let allComments   = [];
let hidingId      = null;

document.addEventListener('DOMContentLoaded', function () {
    loadUniversityComments();
    setupFilters();
});

// ─── Load all comments ───────────────────────────────────────────
function loadUniversityComments() {
    const list = document.getElementById('commentsList');
    if (list) {
        list.innerHTML = `
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading comments…</p>
            </div>`;
    }

    fetch('/unipulse/public/moderator/comments/getUniversityComments')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                allComments = data.comments || [];
                updateStats(data.stats || {});
                populateEventFilter(allComments);
                renderFilteredComments();
            } else {
                showListError('Failed to load comments: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Error loading comments:', err);
            showListError('Error loading comments. Please try again.');
        });
}

// ─── Stats ───────────────────────────────────────────────────────
function updateStats(stats) {
    const total   = stats.total_comments   || 0;
    const visible = stats.visible_comments || 0;
    const hidden  = stats.hidden_comments  || 0;
    const today   = stats.moderated_today  || 0;

    setCount('totalComments',    total);
    setCount('visibleComments',  visible);
    setCount('hiddenComments',   hidden);
    setCount('moderatedToday',   today);
}

function setCount(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
}

// ─── Event filter population ─────────────────────────────────────
function populateEventFilter(comments) {
    const sel = document.getElementById('eventFilter');
    if (!sel) return;
    const seen = new Map();
    comments.forEach(c => { if (!seen.has(c.event_id)) seen.set(c.event_id, c.event_title); });
    sel.innerHTML = '<option value="">All Events</option>';
    seen.forEach((title, id) => {
        const opt = document.createElement('option');
        opt.value       = id;
        opt.textContent = title;
        sel.appendChild(opt);
    });
}

// ─── Filter setup ─────────────────────────────────────────────────
function setupFilters() {
    const searchInput  = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const eventFilter  = document.getElementById('eventFilter');
    const dateFilter   = document.getElementById('dateFilter');

    if (searchInput)  searchInput.addEventListener('input',  debounce(renderFilteredComments, 300));
    if (statusFilter) statusFilter.addEventListener('change', renderFilteredComments);
    if (eventFilter)  eventFilter.addEventListener('change',  renderFilteredComments);
    if (dateFilter)   dateFilter.addEventListener('change',   renderFilteredComments);
}

function clearFilters() {
    ['searchInput', 'statusFilter', 'eventFilter', 'dateFilter'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    renderFilteredComments();
}

function renderFilteredComments() {
    const search  = (document.getElementById('searchInput')?.value  || '').toLowerCase();
    const status  =  document.getElementById('statusFilter')?.value || '';
    const eventId =  document.getElementById('eventFilter')?.value  || '';
    const date    =  document.getElementById('dateFilter')?.value   || '';

    const today = new Date();
    const todayStr       = dateStr(today);
    const weekAgo        = new Date(today); weekAgo.setDate(weekAgo.getDate() - 7);
    const monthAgo       = new Date(today); monthAgo.setMonth(monthAgo.getMonth() - 1);

    const filtered = allComments.filter(c => {
        if (status === 'visible' && c.is_hidden)   return false;
        if (status === 'hidden'  && !c.is_hidden)  return false;
        if (eventId && String(c.event_id) !== eventId) return false;

        if (date) {
            const cDate = new Date(c.created_at);
            if (date === 'today'   && dateStr(cDate) !== todayStr)  return false;
            if (date === 'week'    && cDate < weekAgo)              return false;
            if (date === 'month'   && cDate < monthAgo)             return false;
        }

        if (search) {
            const haystack = [
                c.comment_text, c.user_name, c.user_email, c.event_title
            ].join(' ').toLowerCase();
            if (!haystack.includes(search)) return false;
        }

        return true;
    });

    const countEl = document.getElementById('commentsCount');
    if (countEl) countEl.textContent = filtered.length;

    displayComments(filtered);
}

// ─── Render comment cards ─────────────────────────────────────────
function displayComments(comments) {
    const list = document.getElementById('commentsList');
    if (!list) return;

    if (!comments.length) {
        list.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-comments"></i>
                <h3>No comments found</h3>
                <p>Try adjusting your search or filters.</p>
            </div>`;
        return;
    }

    list.innerHTML = comments.map(c => buildCard(c)).join('');
}

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

    const viewEventLink = c.event_id
        ? `<a href="/unipulse/public/moderator/eventview?id=${c.event_id}">${escapeHtml(c.event_title || 'Event')}</a>`
        : escapeHtml(c.event_title || 'Event');

    const actionBtn = c.is_hidden
        ? `<button class="action-btn unhide-btn" onclick="unhideComment(${c.id})">
               <i class="fas fa-eye"></i> Restore
           </button>`
        : `<button class="action-btn hide-btn" onclick="openHideModal(${c.id})">
               <i class="fas fa-eye-slash"></i> Hide
           </button>`;

    return `
        <div class="comment-card ${c.is_hidden ? 'is-hidden' : ''}" data-id="${c.id}">
            <div class="comment-event-label">
                <i class="fas fa-calendar-alt"></i> ${viewEventLink}
                &nbsp;·&nbsp; ${escapeHtml(c.publisher_name || '')}
                &nbsp;${statusBadge}
            </div>
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

// ─── Hide modal ───────────────────────────────────────────────────
function openHideModal(commentId) {
    hidingId = commentId;
    const modal = document.getElementById('hideModal');
    if (!modal) return;
    const textarea = document.getElementById('hideReason');
    const errEl    = document.getElementById('hideError');
    const btn      = document.getElementById('hideSubmitBtn');
    if (textarea) textarea.value = '';
    if (errEl)    errEl.style.display = 'none';
    if (btn)      { btn.disabled = false; btn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Comment'; }
    modal.classList.add('active');
}

function closeHideModal() {
    hidingId = null;
    const modal = document.getElementById('hideModal');
    if (modal) modal.classList.remove('active');
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
            loadUniversityComments();
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

// ─── Unhide ───────────────────────────────────────────────────────
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
            loadUniversityComments();
        } else {
            showToast(data.error || 'Failed to restore comment.', 'error');
        }
    })
    .catch(err => {
        console.error('Error unhiding:', err);
        showToast('An error occurred.', 'error');
    });
}

// ─── Utility ─────────────────────────────────────────────────────
function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}
function capitalizeFirst(str) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
}
function dateStr(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}
function debounce(fn, wait) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
}
function showListError(msg) {
    const list = document.getElementById('commentsList');
    if (list) list.innerHTML = `<div class="error-banner"><i class="fas fa-exclamation-circle"></i> ${escapeHtml(msg)}</div>`;
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
