(function () {
    if (typeof window.initializeRegisteredEventsCalendar === 'function') {
        return;
    }

    const defaultConfig = {
        triggerId: 'openRegisteredEventsCalendar',
        apiEndpoint: '/unipulse/public/user/dashboard/getUpcomingEvents',
        eventDetailsBasePath: '/unipulse/public/user/eventview?id=',
        fallbackEventsPath: '/unipulse/public/user/events',
        modalTitle: 'My Registered Events Calendar',
        emptyMessage: 'No future registered events found.'
    };

    const state = {
        config: { ...defaultConfig },
        events: [],
        currentCalendarDate: new Date(),
        selectedCalendarDate: null,
        initialized: false,
        listenersBound: false
    };

    function createModalIfMissing() {
        if (document.getElementById('registeredEventsCalendarModal')) {
            return;
        }

        const modalHtml = `
            <div class="calendar-modal-overlay" id="registeredEventsCalendarModal" aria-hidden="true">
                <div class="calendar-modal" role="dialog" aria-modal="true" aria-labelledby="registeredEventsCalendarTitle">
                    <div class="calendar-modal-header">
                        <h3 id="registeredEventsCalendarTitle"></h3>
                        <button type="button" class="calendar-modal-close" id="closeRegisteredEventsCalendar" aria-label="Close calendar">&times;</button>
                    </div>
                    <div class="registered-calendar-controls">
                        <button type="button" id="calendarPrevMonth" class="calendar-nav-btn">&#8249;</button>
                        <div id="calendarMonthLabel" class="calendar-month-label">Month Year</div>
                        <button type="button" id="calendarNextMonth" class="calendar-nav-btn">&#8250;</button>
                    </div>
                    <div class="registered-calendar-grid" id="registeredCalendarGrid"></div>
                    <div class="registered-events-panel">
                        <h4 id="selectedDateLabel">Upcoming events</h4>
                        <div class="registered-events-list" id="registeredEventsList"></div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    function bindListenersOnce() {
        if (state.listenersBound) {
            return;
        }

        const closeButton = document.getElementById('closeRegisteredEventsCalendar');
        const prevMonthButton = document.getElementById('calendarPrevMonth');
        const nextMonthButton = document.getElementById('calendarNextMonth');

        if (closeButton) {
            closeButton.addEventListener('click', function () {
                closeCalendar();
            });
        }

        if (prevMonthButton) {
            prevMonthButton.addEventListener('click', function () {
                state.currentCalendarDate = new Date(
                    state.currentCalendarDate.getFullYear(),
                    state.currentCalendarDate.getMonth() - 1,
                    1
                );
                renderCalendar();
            });
        }

        if (nextMonthButton) {
            nextMonthButton.addEventListener('click', function () {
                state.currentCalendarDate = new Date(
                    state.currentCalendarDate.getFullYear(),
                    state.currentCalendarDate.getMonth() + 1,
                    1
                );
                renderCalendar();
            });
        }

        document.addEventListener('click', function (e) {
            const modal = document.getElementById('registeredEventsCalendarModal');
            if (!modal) {
                return;
            }

            if (e.target && e.target.id === 'registeredEventsCalendarModal') {
                closeCalendar();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeCalendar();
            }
        });

        state.listenersBound = true;
    }

    function wireTrigger() {
        const trigger = document.getElementById(state.config.triggerId);
        if (!trigger || trigger.dataset.calendarBound === 'true') {
            return;
        }

        trigger.addEventListener('click', async function (e) {
            e.preventDefault();
            await openCalendar();
        });

        trigger.dataset.calendarBound = 'true';
    }

    async function openCalendar() {
        const modal = document.getElementById('registeredEventsCalendarModal');
        const userDropdown = document.getElementById('userDropdown');
        if (!modal) {
            return;
        }

        if (userDropdown) {
            userDropdown.classList.remove('show');
        }

        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');

        await loadEvents();
    }

    function closeCalendar() {
        const modal = document.getElementById('registeredEventsCalendarModal');
        if (!modal) {
            return;
        }

        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
    }

    async function loadEvents() {
        const eventsList = document.getElementById('registeredEventsList');
        if (eventsList) {
            eventsList.innerHTML = '<p class="calendar-message">Loading your registered events...</p>';
        }

        try {
            const response = await fetch(state.config.apiEndpoint);
            if (!response.ok) {
                throw new Error('Failed to fetch upcoming events');
            }

            const data = await response.json();
            state.events = data.success && Array.isArray(data.events) ? data.events : [];

            const title = document.getElementById('registeredEventsCalendarTitle');
            if (title) {
                title.textContent = state.config.modalTitle;
            }

            if (!state.initialized) {
                const today = new Date();
                state.currentCalendarDate = new Date(today.getFullYear(), today.getMonth(), 1);
                state.initialized = true;
            }

            const firstEventDate = getNextUpcomingEventDate();
            if (firstEventDate) {
                state.selectedCalendarDate = firstEventDate;
                state.currentCalendarDate = new Date(firstEventDate.getFullYear(), firstEventDate.getMonth(), 1);
            } else {
                state.selectedCalendarDate = null;
            }

            renderCalendar();
        } catch (error) {
            console.error('Error loading registered future events:', error);
            if (eventsList) {
                eventsList.innerHTML = '<p class="calendar-message error">Unable to load events right now.</p>';
            }
        }
    }

    function renderCalendar() {
        const grid = document.getElementById('registeredCalendarGrid');
        const monthLabel = document.getElementById('calendarMonthLabel');

        if (!grid || !monthLabel) {
            return;
        }

        const year = state.currentCalendarDate.getFullYear();
        const month = state.currentCalendarDate.getMonth();

        monthLabel.textContent = new Intl.DateTimeFormat('en-US', {
            month: 'long',
            year: 'numeric'
        }).format(state.currentCalendarDate);

        const eventsByDate = groupEventsByDate();
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        grid.innerHTML = '';

        const dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayLabels.forEach(label => {
            const dayLabelElement = document.createElement('div');
            dayLabelElement.className = 'calendar-day-label';
            dayLabelElement.textContent = label;
            grid.appendChild(dayLabelElement);
        });

        for (let i = 0; i < firstDay; i += 1) {
            const blankCell = document.createElement('div');
            blankCell.className = 'calendar-day empty';
            grid.appendChild(blankCell);
        }

        const today = new Date();
        const todayKey = buildDateKey(today.getFullYear(), today.getMonth(), today.getDate());

        for (let day = 1; day <= daysInMonth; day += 1) {
            const cellDateKey = buildDateKey(year, month, day);
            const dayEvents = eventsByDate[cellDateKey] || [];

            const dayCell = document.createElement('button');
            dayCell.type = 'button';
            dayCell.className = 'calendar-day';
            dayCell.innerHTML = `<span class="calendar-day-number">${day}</span>`;

            if (cellDateKey === todayKey) {
                dayCell.classList.add('today');
            }

            if (dayEvents.length > 0) {
                dayCell.classList.add('has-events');
                const marker = document.createElement('span');
                marker.className = 'calendar-event-marker';
                marker.textContent = dayEvents.length > 1 ? `${dayEvents.length} events` : '1 event';
                dayCell.appendChild(marker);
            }

            if (state.selectedCalendarDate && buildDateKey(state.selectedCalendarDate.getFullYear(), state.selectedCalendarDate.getMonth(), state.selectedCalendarDate.getDate()) === cellDateKey) {
                dayCell.classList.add('selected');
            }

            dayCell.addEventListener('click', function () {
                state.selectedCalendarDate = new Date(year, month, day);
                renderCalendar();
                renderSelectedDateEvents();
            });

            grid.appendChild(dayCell);
        }

        renderSelectedDateEvents();
    }

    function renderSelectedDateEvents() {
        const selectedDateLabel = document.getElementById('selectedDateLabel');
        const eventsList = document.getElementById('registeredEventsList');

        if (!selectedDateLabel || !eventsList) {
            return;
        }

        if (!state.selectedCalendarDate) {
            selectedDateLabel.textContent = 'Upcoming events';
            if (state.events.length === 0) {
                eventsList.innerHTML = `<p class="calendar-message">${escapeHtml(state.config.emptyMessage)}</p>`;
            } else {
                const nextEvents = [...state.events].slice(0, 5);
                eventsList.innerHTML = nextEvents.map(formatEventItemHtml).join('');
            }
            return;
        }

        const selectedDateString = state.selectedCalendarDate.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        selectedDateLabel.textContent = selectedDateString;

        const selectedKey = buildDateKey(
            state.selectedCalendarDate.getFullYear(),
            state.selectedCalendarDate.getMonth(),
            state.selectedCalendarDate.getDate()
        );

        const eventsForDate = state.events.filter(event => normalizeDateString(event.date) === selectedKey);

        if (eventsForDate.length === 0) {
            eventsList.innerHTML = '<p class="calendar-message">No registered events on this date.</p>';
            return;
        }

        eventsList.innerHTML = eventsForDate.map(formatEventItemHtml).join('');
    }

    function formatEventItemHtml(event) {
        const eventId = Number(event.id || 0);
        const eventLink = eventId > 0
            ? `${state.config.eventDetailsBasePath}${eventId}`
            : state.config.fallbackEventsPath;

        const eventTitle = escapeHtml(event.title || 'Untitled Event');
        const eventTime = formatEventTime(event.time);
        const eventLocation = event.location ? escapeHtml(event.location) : 'Location not specified';

        return `
            <a href="${eventLink}" class="calendar-event-item">
                <div class="calendar-event-title">${eventTitle}</div>
                <div class="calendar-event-meta">${eventTime} | ${eventLocation}</div>
            </a>
        `;
    }

    function groupEventsByDate() {
        return state.events.reduce((acc, event) => {
            const key = normalizeDateString(event.date);
            if (!key) {
                return acc;
            }

            if (!acc[key]) {
                acc[key] = [];
            }
            acc[key].push(event);
            return acc;
        }, {});
    }

    function getNextUpcomingEventDate() {
        if (!state.events.length) {
            return null;
        }

        let closestEventDate = null;
        state.events.forEach(event => {
            const dateKey = normalizeDateString(event.date);
            if (!dateKey) {
                return;
            }

            const parts = dateKey.split('-');
            const date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
            if (!closestEventDate || date < closestEventDate) {
                closestEventDate = date;
            }
        });

        return closestEventDate;
    }

    function normalizeDateString(dateValue) {
        if (!dateValue) {
            return '';
        }

        const raw = String(dateValue).trim();
        const directMatch = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (directMatch) {
            const year = Number(directMatch[1]);
            const month = Number(directMatch[2]);
            const day = Number(directMatch[3]);
            if (year > 0 && month >= 1 && month <= 12 && day >= 1 && day <= 31) {
                return `${String(year).padStart(4, '0')}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            }
        }

        const parsedDate = new Date(raw);
        if (Number.isNaN(parsedDate.getTime())) {
            return '';
        }

        return buildDateKey(parsedDate.getFullYear(), parsedDate.getMonth(), parsedDate.getDate());
    }

    function buildDateKey(year, monthIndex, day) {
        const month = String(monthIndex + 1).padStart(2, '0');
        const dayStr = String(day).padStart(2, '0');
        return `${year}-${month}-${dayStr}`;
    }

    function formatEventTime(timeValue) {
        if (!timeValue) {
            return 'Time TBD';
        }

        const [hoursRaw, minutesRaw] = String(timeValue).split(':');
        const hours = Number(hoursRaw);
        const minutes = Number(minutesRaw);

        if (Number.isNaN(hours) || Number.isNaN(minutes)) {
            return 'Time TBD';
        }

        const hour12 = ((hours + 11) % 12) + 1;
        const amPm = hours >= 12 ? 'PM' : 'AM';
        return `${hour12}:${String(minutes).padStart(2, '0')} ${amPm}`;
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    window.initializeRegisteredEventsCalendar = function (config) {
        state.config = {
            ...defaultConfig,
            ...(config || {})
        };

        createModalIfMissing();
        bindListenersOnce();
        wireTrigger();
    };
})();
