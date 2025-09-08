<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Find Events | UniPulse</title>
  <link rel="stylesheet" href="/unipulse/public/assets/css/find_events-style.css">
</head>
<body>
<header class="header">
  <div class="header-container">
    <div class="logo">
      <a href="index.php">
      <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="logo-image">
      </a>
    </div>
  </div>
</header>
  <main>
    <h1>Find Events</h1>
    <h4>Discover upcoming university events, workshops, and activities.</h4>
    
    <!-- Search Bar -->
    <section class="search-filter">
    <input type="text" placeholder="Search for events..." class="search-bar">

    <!-- Filters -->
    <div class="filters">
      <select id="filterCategory">
        <option value="">Category</option>
        <option value="tech">Tech</option>
        <option value="cultural">Cultural</option>
        <option value="sports">Sports</option>
        <option value="musical">Musical</option>
        <option value="academic">Academic</option>
        <option value="social">Social</option> 
        <option value="workshop">Workshop</option> 
        <option value="other">Other</option>
      </select>

      <select id="filterLocation">
        <option value="">Location</option>
        <option value="ucsc">UCSC</option>
        <option value="colombo">University of Colombo</option>
        <option value="peradeniya">University of Peradeniya</option>
        <option value="japura">University of Sri Jayawardhanapura</option>
        <option value="kelaniya">University of Sri Kelaniya</option>
        <option value="moratuwa">University of Moratuwa</option>
        <option value="jaffna">University of Jaffna</option>
        <option value="ruhuna">University of Ruhuna</option>
        <option value="vpa">University of Visual & Performing Arts</option>
      </select>

      <select id="filterDept">
        <option value="">Faculty</option>
        <option value="cs">Computer Science</option>
        <option value="arts">Science</option>
        <option value="eng">Engineering</option>
        <option value="arts">Arts</option>
        <option value="med">Medicine</option>
        <option value="law">Law</option>
        <option value="management">Management</option>
      </select>
    </div>
  <div class="events-grid">
    <div class="event-card"
      data-title="Tech Workshop 2025"
      data-date="2025-09-15"
      data-category="tech"
      data-location="ucsc"
      data-faculty="cs">
    <div class="event-info">
          <h3>Tech Workshop 2025</h3>
          <p>📅 Date: 15 Sept 2025 | 📍 Location: UCSC</p>
          <p>Category: Tech | Faculty: Computer Science</p>
          <p>Organizer: UCSC IEEE Student Branch</p>
          <button>View Details</button>
      </div>
    </div>
    <div class="event-card"
        data-title="Geethanjali 2025"
        data-date="2025-11-22"
        data-category="musical"
        data-location="peradeniya"
        data-faculty="science">
      <div class="event-info">
          <h3>Geethanjali 2025</h3>
          <p>📅 Date: 22 Nov 2025 | 📍 Location: University of Peradeniya, Gymnasium</p>
          <p>Category: Musical | Faculty: Science</p>
          <p>Organizer: University of Peradeniya Science Students</p>
          <button>View Details</button>
      </div>
    </div>

    <div class="event-card"
     data-title="Mora Tech Summit 2025"
     data-date="2025-10-05"
     data-category="tech"
     data-location="moratuwa"
     data-faculty="engineering">
    <div class="event-info">
          <h3>Mora Tech Summit 2025</h3>
          <p>📅 Date: 05 Oct 2025 | 📍 Location: University of Moratuwa</p>
          <p>Category: Tech | Faculty: Engineering</p>
          <p>Organizer: University of Moratuwa IT Society</p>
          <button>View Details</button>
      </div>
    </div>
    <div class="event-card"
     data-title="Sports Day 2024"
     data-date="2025-12-10"
     data-category="sports"
     data-location="kelaniya"
     data-faculty="arts">
  <div class="event-info">
          <h3>Sports Day 2024</h3>
          <p>📅 Date: 10 Dec 2024 | 📍 Location: University of Kaleniya</p>
          <p>Category: Sports | Faculty: Arts</p>
          <p>Organizer: University of Kelaniya Sports Club</p>
          <button>View Details</button>
      </div>
    </div>
    <div class="event-card"
     data-title="UCSC Padura 2025"
     data-date="2025-08-07"
     data-category="musical"
     data-location="ucsc"
     data-faculty="cs">
    <div class="event-info">
        <h3>UCSC Padura 2025</h3>
          <p>📅 Date: 07 Aug 2025 | 📍 Location: UCSC</p>
          <p>Category: Musical | Faculty: Computer Science</p>
          <p>Organizer: UCSC Student's Union</p>
        <button>View Details</button>
      </div>
    </div>
  </div>
  </main>
  <footer class="footer">
      <div class="logo">
        <img src="/unipulse/public/assets/images/logo.png" alt="UniPulse Logo" class="unp-logo">
      </div>
      <p>&copy; 2025 UniPulse. All rights reserved.</p>           
  </footer>
  <script src="/unipulse/public/assets/js/find_events.js"></script>
</body>
</html>