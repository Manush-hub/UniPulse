<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Find and discover university events on UniPulse - Browse upcoming events, filter by category, date, and location.">
  <title>Find Events | UniPulse</title>
  <link rel="stylesheet" href="/unipulse/public/assets/css/find_events-style.css">
</head>
<body>
  <?php include __DIR__ . '/Components/role_header.php'; ?>

  <div class="container">
    <section class="hero" id="hero">
      <h1>Find Events</h1>
      <p class="subtitle">Discover and register for exciting events happening on campus</p>
    </section>

    <div class="featured-section">
      <h2>📌 Why Browse Events on UniPulse?</h2>
      <div class="featured-grid">
        <article class="featured-card">
          <span class="featured-icon">🎯</span>
          <h3>Easy Discovery</h3>
          <p>Find events by category, date, time, or location. Advanced filters help you discover events that match your interests.</p>
        </article>

        <article class="featured-card">
          <span class="featured-icon">📱</span>
          <h3>Quick Registration</h3>
          <p>Register for events in seconds with our streamlined registration process. Save your favorite events for later.</p>
        </article>

        <article class="featured-card">
          <span class="featured-icon">🔔</span>
          <h3>Get Notifications</h3>
          <p>Receive reminders and updates about events you're interested in. Never miss an event you want to attend.</p>
        </article>

        <article class="featured-card">
          <span class="featured-icon">💬</span>
          <h3>Community Feedback</h3>
          <p>See ratings and reviews from other attendees. Make informed decisions about which events to join.</p>
        </article>

        <article class="featured-card">
          <span class="featured-icon">🎫</span>
          <h3>Flexible Tickets</h3>
          <p>Multiple ticket options available for different attendee types. Affordable pricing for all students.</p>
        </article>

        <article class="featured-card">
          <span class="featured-icon">🌍</span>
          <h3>All Campus Events</h3>
          <p>All university events in one place - sports, clubs, academic, cultural, and special events.</p>
        </article>
      </div>
    </div>

    <div class="events-container">
      <aside class="sidebar">
        <div class="filter-section">
          <div class="filter-title">Category</div>
          <div class="filter-option">
            <input type="checkbox" id="cat-all" name="category" value="">
            <label for="cat-all">All Events</label>
          </div>
          <div class="filter-option">
            <input type="checkbox" id="cat-sports" name="category" value="sports" checked>
            <label for="cat-sports">Sports</label>
          </div>
          <div class="filter-option">
            <input type="checkbox" id="cat-cultural" name="category" value="cultural">
            <label for="cat-cultural">Cultural</label>
          </div>
          <div class="filter-option">
            <input type="checkbox" id="cat-academic" name="category" value="academic">
            <label for="cat-academic">Academic</label>
          </div>
          <div class="filter-option">
            <input type="checkbox" id="cat-social" name="category" value="social">
            <label for="cat-social">Social</label>
          </div>
          <div class="filter-option">
            <input type="checkbox" id="cat-workshops" name="category" value="workshops">
            <label for="cat-workshops">Workshops</label>
          </div>
        </div>

        <div class="filter-section">
          <div class="filter-title">When</div>
          <div class="filter-option">
            <input type="radio" id="date-all" name="date" value="" checked>
            <label for="date-all">All Dates</label>
          </div>
          <div class="filter-option">
            <input type="radio" id="date-today" name="date" value="today">
            <label for="date-today">Today</label>
          </div>
          <div class="filter-option">
            <input type="radio" id="date-thisweek" name="date" value="thisweek">
            <label for="date-thisweek">This Week</label>
          </div>
          <div class="filter-option">
            <input type="radio" id="date-thismonth" name="date" value="thismonth">
            <label for="date-thismonth">This Month</label>
          </div>
          <div class="filter-option">
            <input type="radio" id="date-upcoming" name="date" value="upcoming">
            <label for="date-upcoming">Upcoming</label>
          </div>
        </div>

        <div class="filter-section">
          <div class="filter-title">Cost</div>
          <div class="filter-option">
            <input type="checkbox" id="cost-free" name="cost" value="free" checked>
            <label for="cost-free">Free</label>
          </div>
          <div class="filter-option">
            <input type="checkbox" id="cost-paid" name="cost" value="paid">
            <label for="cost-paid">Paid</label>
          </div>
        </div>
      </aside>

      <main>
        <div class="events-grid" id="eventsGrid">
          <div class="event-card" data-category="sports" data-date="upcoming" data-cost="free">
            <div class="event-image">🏀</div>
            <div class="event-content">
              <span class="event-date">March 15, 2026</span>
              <h3>Basketball Championship</h3>
              <div class="event-category">Sports</div>
              <div class="event-location">📍 Sports Arena</div>
              <div class="event-attendance">2,500+ attendees</div>
              <a href="/unipulse/public/signup" class="event-button">Register Now</a>
            </div>
          </div>

          <div class="event-card" data-category="cultural" data-date="upcoming" data-cost="free">
            <div class="event-image">🎭</div>
            <div class="event-content">
              <span class="event-date">March 18, 2026</span>
              <h3>Cultural Festival 2026</h3>
              <div class="event-category">Cultural</div>
              <div class="event-location">📍 Main Campus</div>
              <div class="event-attendance">1,200+ attendees</div>
              <a href="/unipulse/public/signup" class="event-button">Register Now</a>
            </div>
          </div>

          <div class="event-card" data-category="academic" data-date="thismonth" data-cost="free">
            <div class="event-image">🎓</div>
            <div class="event-content">
              <span class="event-date">March 20, 2026</span>
              <h3>Tech Innovation Seminar</h3>
              <div class="event-category">Academic</div>
              <div class="event-location">📍 Auditorium Hall</div>
              <div class="event-attendance">300+ attendees</div>
              <a href="/unipulse/public/signup" class="event-button">Register Now</a>
            </div>
          </div>

          <div class="event-card" data-category="workshops" data-date="upcoming" data-cost="free">
            <div class="event-image">🔧</div>
            <div class="event-content">
              <span class="event-date">March 22, 2026</span>
              <h3>Web Development Workshop</h3>
              <div class="event-category">Workshops</div>
              <div class="event-location">📍 Computer Lab</div>
              <div class="event-attendance">50+ attendees</div>
              <a href="/unipulse/public/signup" class="event-button">Register Now</a>
            </div>
          </div>

          <div class="event-card" data-category="social" data-date="thisweek" data-cost="free">
            <div class="event-image">🎉</div>
            <div class="event-content">
              <span class="event-date">March 25, 2026</span>
              <h3>Student Welcome Night</h3>
              <div class="event-category">Social</div>
              <div class="event-location">📍 Student Center</div>
              <div class="event-attendance">500+ attendees</div>
              <a href="/unipulse/public/signup" class="event-button">Register Now</a>
            </div>
          </div>

          <div class="event-card" data-category="sports" data-date="upcoming" data-cost="paid">
            <div class="event-image">⚽</div>
            <div class="event-content">
              <span class="event-date">March 28, 2026</span>
              <h3>Football Championship Final</h3>
              <div class="event-category">Sports</div>
              <div class="event-location">📍 Stadium</div>
              <div class="event-attendance">5,000+ attendees</div>
              <a href="/unipulse/public/signup" class="event-button">Register Now</a>
            </div>
          </div>

          <div class="event-card" data-category="cultural" data-date="upcoming" data-cost="free">
            <div class="event-image">🎵</div>
            <div class="event-content">
              <span class="event-date">April 1, 2026</span>
              <h3>Music Festival</h3>
              <div class="event-category">Cultural</div>
              <div class="event-location">📍 Open Air Stage</div>
              <div class="event-attendance">2,000+ attendees</div>
              <a href="/unipulse/public/signup" class="event-button">Register Now</a>
            </div>
          </div>

          <div class="event-card" data-category="academic" data-date="upcoming" data-cost="free">
            <div class="event-image">💡</div>
            <div class="event-content">
              <span class="event-date">April 5, 2026</span>
              <h3>Research Symposium</h3>
              <div class="event-category">Academic</div>
              <div class="event-location">📍 Conference Hall</div>
              <div class="event-attendance">400+ attendees</div>
              <a href="/unipulse/public/signup" class="event-button">Register Now</a>
            </div>
          </div>

          <div class="event-card" data-category="workshops" data-date="upcoming" data-cost="paid">
            <div class="event-image">🎨</div>
            <div class="event-content">
              <span class="event-date">April 8, 2026</span>
              <h3>Graphic Design Masterclass</h3>
              <div class="event-category">Workshops</div>
              <div class="event-location">📍 Design Studio</div>
              <div class="event-attendance">25+ attendees</div>
              <a href="/unipulse/public/signup" class="event-button">Register Now</a>
            </div>
          </div>
        </div>

        <div class="no-results" id="noResults" style="display: none;">
          <div class="no-results-icon">🔍</div>
          <h3>No Events Found</h3>
          <p>Try adjusting your filters or search terms to find more events.</p>
          <button class="clear-filters-btn" onclick="clearAllFilters()">Clear All Filters</button>
        </div>
      </main>
    </div>

    <section class="cta-section">
      <h2>Can't Find What You're Looking For?</h2>
      <p>Subscribe to event notifications or contact our support team to learn about upcoming events and special opportunities.</p>
      <a href="/unipulse/public/contact" class="cta-btn">Contact Us</a>
    </section>
  </div>

  <?php include __DIR__ . '/Components/footer.php'; ?>

  <script src="/unipulse/public/assets/js/find-events-app.js"></script>
</body>
</html>