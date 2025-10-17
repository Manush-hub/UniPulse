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
    <div class="user-info">
      <?php if (isset($user) && $user): ?>
        <div class="user-details">
          <?php if (isset($userDetails) && $userDetails): ?>
            <span class="welcome-text">Welcome, <?= htmlspecialchars($userDetails->full_name) ?></span>
            <?php if ($user['type'] === 'university' && isset($userUniversityName)): ?>
              <span class="user-university"><?= htmlspecialchars($userUniversityName) ?></span>
            <?php else: ?>
              <span class="user-type"><?= ucfirst($user['type']) ?> User</span>
            <?php endif; ?>
          <?php else: ?>
            <span class="welcome-text">Welcome, <?= htmlspecialchars($user['name']) ?></span>
            <span class="user-type"><?= ucfirst($user['type']) ?> User</span>
          <?php endif; ?>
        </div>
        <a href="/unipulse/public/user/dashboard" class="btn btn-secondary">Dashboard</a>
        <a href="/unipulse/public/logout" class="btn btn-primary">Logout</a>
      <?php else: ?>
        <a href="/unipulse/public/signin" class="btn btn-primary">Sign In</a>
        <a href="/unipulse/public/signup" class="btn btn-secondary">Sign Up</a>
      <?php endif; ?>
    </div>
  </div>
</header>
  <main>
    <h1>Find Events</h1>
    <?php if (isset($user) && $user): ?>
      <?php if ($user['type'] === 'university'): ?>
        <h4>Discover events from your university and public inter-university events.</h4>
      <?php else: ?>
        <h4>Discover public university events and activities.</h4>
      <?php endif; ?>
    <?php else: ?>
      <h4>Discover public university events. Sign in to see university-specific events.</h4>
    <?php endif; ?>
    
    <!-- Search Bar -->
    <section class="search-filter">
    <form method="GET" action="/unipulse/public/find_events">
      <input type="text" name="search" placeholder="Search for events..." class="search-bar" 
             value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

      <!-- Filters -->
      <div class="filters">
        <select name="category" id="filterCategory">
          <option value="">All Categories</option>
          <option value="academic" <?= ($_GET['category'] ?? '') === 'academic' ? 'selected' : '' ?>>Academic</option>
          <option value="cultural" <?= ($_GET['category'] ?? '') === 'cultural' ? 'selected' : '' ?>>Cultural</option>
          <option value="sports" <?= ($_GET['category'] ?? '') === 'sports' ? 'selected' : '' ?>>Sports</option>
          <option value="technology" <?= ($_GET['category'] ?? '') === 'technology' ? 'selected' : '' ?>>Technology</option>
          <option value="social" <?= ($_GET['category'] ?? '') === 'social' ? 'selected' : '' ?>>Social</option> 
          <option value="workshop" <?= ($_GET['category'] ?? '') === 'workshop' ? 'selected' : '' ?>>Workshop</option> 
        </select>

        <select name="university" id="filterLocation">
          <option value="">All Universities</option>
          <option value="university-of-colombo" <?= ($_GET['university'] ?? '') === 'university-of-colombo' ? 'selected' : '' ?>>University of Colombo</option>
          <option value="university-of-peradeniya" <?= ($_GET['university'] ?? '') === 'university-of-peradeniya' ? 'selected' : '' ?>>University of Peradeniya</option>
          <option value="university-of-sri-jayewardenepura" <?= ($_GET['university'] ?? '') === 'university-of-sri-jayewardenepura' ? 'selected' : '' ?>>University of Sri Jayewardenepura</option>
          <option value="university-of-kelaniya" <?= ($_GET['university'] ?? '') === 'university-of-kelaniya' ? 'selected' : '' ?>>University of Kelaniya</option>
          <option value="university-of-moratuwa" <?= ($_GET['university'] ?? '') === 'university-of-moratuwa' ? 'selected' : '' ?>>University of Moratuwa</option>
          <option value="university-of-jaffna" <?= ($_GET['university'] ?? '') === 'university-of-jaffna' ? 'selected' : '' ?>>University of Jaffna</option>
          <option value="university-of-ruhuna" <?= ($_GET['university'] ?? '') === 'university-of-ruhuna' ? 'selected' : '' ?>>University of Ruhuna</option>
        </select>
        
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="/unipulse/public/find_events" class="btn btn-secondary">Clear</a>
      </div>
    </form>
  </section>
  
  <!-- Events Display -->
  <div class="events-grid">
    <?php if (isset($events) && count($events) > 0): ?>
      <?php foreach ($events as $event): ?>
        <div class="event-card"
          data-title="<?= htmlspecialchars($event->title) ?>"
          data-date="<?= htmlspecialchars($event->event_date) ?>"
          data-category="<?= htmlspecialchars($event->category) ?>"
          data-location="<?= htmlspecialchars($event->university) ?>"
          data-university="<?= htmlspecialchars($event->university_name) ?>">
        <div class="event-info">
              <h3><?= htmlspecialchars($event->title) ?></h3>
              <p>📅 Date: <?= date('j M Y', strtotime($event->event_date)) ?> at <?= date('g:i A', strtotime($event->event_time)) ?></p>
              <p>📍 Location: <?= htmlspecialchars($event->location) ?></p>
              <p>🏛️ University: <?= htmlspecialchars($event->university_name) ?></p>
              <p>📂 Category: <?= ucfirst($event->category) ?></p>
              <p>👥 Participants: <?= $event->participants ?>/<?= $event->max_participants ?></p>
              <p>👨‍💼 Organizer: <?= htmlspecialchars($event->organizer) ?></p>
              <p>
                <span class="event-status status-<?= $event->status ?>"><?= ucfirst($event->status) ?></span>
                <?php if (isset($event->visibility)): ?>
                  <span class="event-visibility visibility-<?= $event->visibility ?>">
                    <?= $event->visibility === 'public' ? '🌐 Public' : '🏛️ University Only' ?>
                  </span>
                <?php endif; ?>
              </p>
              <button onclick="viewEventDetails(<?= $event->id ?>)">View Details</button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="no-events">
        <h3>No events found</h3>
        <p>
          <?php if (isset($user) && $user): ?>
            <?php if ($user['type'] === 'university'): ?>
              No events available for your university or public events matching your criteria.
            <?php else: ?>
              No public events found matching your criteria.
            <?php endif; ?>
          <?php else: ?>
            No public events found. <a href="/unipulse/public/signin">Sign in</a> to see university-specific events.
          <?php endif; ?>
        </p>
      </div>
    <?php endif; ?>
  </div>
  
  <script>
    function viewEventDetails(eventId) {
      // Navigate to event details page
      window.location.href = '/unipulse/public/event/view/' + eventId;
    }
    
    // Filter functionality for search
    document.querySelector('.search-bar').addEventListener('input', function() {
      // Auto-submit search after typing delay
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.form.submit();
      }, 500);
    });
  </script>
  
  <style>
    .event-status {
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.8em;
      font-weight: bold;
      text-transform: uppercase;
    }
    
    .status-upcoming { background-color: #e3f2fd; color: #1976d2; }
    .status-ongoing { background-color: #e8f5e8; color: #388e3c; }
    .status-completed { background-color: #f3e5f5; color: #7b1fa2; }
    .status-cancelled { background-color: #ffebee; color: #d32f2f; }
    
    .event-visibility {
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.8em;
      margin-left: 8px;
    }
    
    .visibility-public { background-color: #e8f5e8; color: #2e7d32; }
    .visibility-university { background-color: #fff3e0; color: #f57c00; }
    
    .no-events {
      text-align: center;
      padding: 40px;
      color: #666;
    }
    
    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .btn {
      padding: 8px 16px;
      text-decoration: none;
      border-radius: 4px;
      font-size: 14px;
    }
    
    .btn-primary {
      background-color: #1976d2;
      color: white;
    }
    
    .btn-secondary {
      background-color: #757575;
      color: white;
    }
    
    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .user-details {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      margin-right: 10px;
    }
    
    .welcome-text {
      font-weight: bold;
      font-size: 14px;
      color: #333;
    }
    
    .user-university {
      font-size: 12px;
      color: #1976d2;
      font-weight: 500;
      margin-top: 2px;
    }
    
    .user-type {
      font-size: 12px;
      color: #666;
      margin-top: 2px;
    }
  </style>
  
</main>
</body>
</html>