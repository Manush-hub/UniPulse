<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find Events | UniPulse</title>
  <link rel="stylesheet" href="/unipulse/public/assets/css/find-events.css">
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
        <option value="musical">Musical</option>
      </select>
      <select id="filterLocation">
        <option value="">Location</option>
        <option value="ucsc">UCSC</option>
        <option value="colombo">Colombo Uni Ground</option>
        <option value="peradeniya">University of Peradeniya</option>
        <option value="jayawardhanapura">University of Sri Jayawardhanapura</option>
        <option value="moratuwa">University of Moratuwa</option>
        <option value="kelaniya">University of Kelaniya</option>
      </select>
      <select id="filterDept">
        <option value="">Faculty</option>
        <option value="cs">Computer Science</option>
        <option value="arts">Science</option>
        <option value="eng">Engineering</option>
        <option value="arts">Arts</option>
        <option value="arts">Medicine</option>
        <option value="arts">Law</option>
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
        <div class="event-card-content">
          <p>Date: 15 Sept 2025 | Location: UCSC</p>
          <p>Category: Tech | Faculty: Computer Science</p>
        </div>
        <button>Register</button>
      </div>
      <div class="event-card">
        <h3>Musical Night</h3>
        <div class="event-card-content">
          <p>Date: 22 Sept 2025 | Location: Colombo Uni Grounds</p>
          <p>Category: Cultural | Faculty: Arts</p>
        </div>
        <button>Register</button>
      </div>
    </div>

    <!-- Past Events -->
    <div id="past" class="tab-content">
      <div class="event-card">
        <h3>Sports Day 2024</h3>
        <div class="event-card-content">
          <p>Date: 10 Dec 2024 | Location: Peradeniya</p>
          <p>Category: Sports | Faculty: Engineering</p>
        </div>
        <button disabled>Event Ended</button>
      </div>
      <div class="event-card">
        <h3>UCSC Padura 2025</h3>
        <div class="event-card-content">
          <p>Date: 07 Aug 2025 | Location: UCSC</p>
          <p>Category: Musical | Faculty: Computer Science</p>
        </div>
        <button disabled>Event Ended</button>
      </div>
    </div>
  </main>
  <footer class="footer">
      <div class="logo">
        <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="unp-logo">
      </div>
      <p>&copy; 2025 UniPulse. All rights reserved.</p>           
  </footer>
</body>
</html>