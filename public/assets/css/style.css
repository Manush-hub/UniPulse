<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find Events | UniPulse</title>
  <link rel="stylesheet" href="/unipulse/public/assets/css/style.css">
</head>
<body>
  <header>
    <div class="logo">
        <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="unp-logo">
    </div>
    
  </header>
  <main>
    <h1>Find Events</h1>

    <!-- Search Bar -->
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search events by name...">
    </div>

    <!-- Filters -->
    <div class="filters">
      <input type="date" id="filterDate">
      <select id="filterCategory">
        <option value="">Category</option>
        <option value="tech">Tech</option>
        <option value="cultural">Cultural</option>
        <option value="sports">Sports</option>
      </select>
      <select id="filterLocation">
        <option value="">Location</option>
        <option value="ucsc">UCSC</option>
        <option value="colombo">Colombo Uni Grounds</option>
        <option value="peradeniya">Peradeniya</option>
      </select>
      <select id="filterDept">
        <option value="">Department</option>
        <option value="cs">Computer Science</option>
        <option value="eng">Engineering</option>
        <option value="arts">Arts</option>
      </select>
    </div>

    <!-- Tabs -->
    <div class="tabs">
      <button class="tab-btn active" onclick="showTab('upcoming')">Upcoming Events</button>
      <button class="tab-btn" onclick="showTab('past')">Past Events</button>
    </div>

    <!-- Upcoming Events -->
    <div id="upcoming" class="tab-content">
      <div class="event-card">
        <h3>Tech Hackathon 2025</h3>
        <p>Date: 15 Sept 2025 | Location: UCSC</p>
        <p>Category: Tech | Department: Computer Science</p>
        <button>Register</button>
      </div>
      <div class="event-card">
        <h3>Musical Night</h3>
        <p>Date: 22 Sept 2025 | Location: Colombo Uni Grounds</p>
        <p>Category: Cultural | Department: Arts</p>
        <button>Register</button>
      </div>
    </div>

    <!-- Past Events -->
    <div id="past" class="tab-content hidden">
      <div class="event-card">
        <h3>Sports Day 2024</h3>
        <p>Date: 10 Dec 2024 | Location: Peradeniya</p>
        <p>Category: Sports | Department: Engineering</p>
        <button disabled>Event Ended</button>
      </div>
    </div>
  </main>

  <script>
    // Tab switching
    function showTab(tabId) {
      document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
      document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
      document.getElementById(tabId).classList.remove('hidden');
      event.target.classList.add('active');
    }
  </script>
</body>
</html>