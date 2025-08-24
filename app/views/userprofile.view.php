<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile - Event Marketing</title>
  <link rel="stylesheet" href="profile-style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.css">
</head>
<body>
  <div class="profile-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-section active">
        <i data-feather="user"></i>
        <span>Personal Data</span>
      </div>
      <div class="sidebar-section">
        <i data-feather="tag"></i>
        <span>Selected Categories</span>
      </div>
      <div class="sidebar-section">
        <i data-feather="calendar"></i>
        <span>Registered Events</span>
      </div>
      <div class="sidebar-section">
        <i data-feather="settings"></i>
        <span>Account Settings</span>
        <ul>
          <li><i data-feather="shield"></i> Privacy</li>
          <li><i data-feather="eye"></i> Public/Private</li>
          <li><i data-feather="lock"></i> Security</li>
        </ul>
      </div>
    </aside>

    <!-- Main Editable Profile -->
    <main class="profile-main">
      <div class="cover-photo">
        <img id="coverImg" src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=cover&w=800&q=80" alt="Cover">
        <button class="edit-btn" onclick="uploadCover()">Edit Cover</button>
        <input type="file" id="coverInput" accept="image/*" style="display:none" onchange="changeCover(event)">
      </div>
      <div class="profile-pic-wrapper">
        <img id="profileImg" class="profile-pic" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Profile">
        <button class="edit-btn" onclick="uploadProfile()">Edit</button>
        <input type="file" id="profileInput" accept="image/*" style="display:none" onchange="changeProfile(event)">
      </div>
      <form class="profile-form" autocomplete="off">
        <section>
          <h3>Personal Info</h3>
          <div class="form-row">
            <label>Name</label>
            <input type="text" value="Ayman Shaltoni">
            <button type="button" class="toggle-btn" onclick="toggleField(this)" title="Toggle Public/Private">
              <i data-feather="eye"></i>
            </button>
          </div>
          <div class="form-row">
            <label>Role</label>
            <input type="text" value="Senior Product Designer">
            <button type="button" class="toggle-btn" onclick="toggleField(this)">
              <i data-feather="eye"></i>
            </button>
          </div>
        </section>
        <section>
          <h3>Contact Info</h3>
          <div class="form-row">
            <label>Email</label>
            <input type="email" value="Aymanshaltoni@gmail.com">
            <button type="button" class="toggle-btn" onclick="toggleField(this)">
              <i data-feather="eye"></i>
            </button>
          </div>
          <div class="form-row">
            <label>Phone</label>
            <input type="tel" value="+966 5502938123">
            <button type="button" class="toggle-btn" onclick="toggleField(this)">
              <i data-feather="eye"></i>
            </button>
          </div>
        </section>
        <section>
          <h3>Preferences / Interests</h3>
          <div class="tags">
            <span class="tag">UI Design</span>
            <span class="tag">Music</span>
            <span class="tag">Tech</span>
            <span class="tag">Sports</span>
            <input type="text" class="tag-input" placeholder="+ Add">
          </div>
        </section>
        <div class="form-actions">
          <button type="submit" class="save-btn">Save Changes</button>
          <button type="button" class="preview-btn" onclick="previewProfile()">Preview as Public</button>
        </div>
      </form>
    </main>

    <!-- Public Preview Sidebar -->
    <aside class="public-preview">
      <h4>Public Profile Preview</h4>
      <div class="public-card">
        <img class="public-profile-pic" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Profile">
        <div class="public-name">Ayman Shaltoni</div>
        <div class="public-role">Senior Product Designer</div>
        <div class="public-bio">
          Hey, I'm a product designer specialized in user interface designs (Web & Mobile) with 10 years of experience. Last year I have been ranked as a top-rated designer on Upwork working for over +3,750 hours with high clients satisfaction, on-time delivery and top quality output.
        </div>
        <div class="public-tags">
          <span class="tag">UI Design</span>
          <span class="tag">Music</span>
          <span class="tag">Tech</span>
          <span class="tag">Sports</span>
        </div>
        <div class="public-socials">
          <a href="https://twitter.com/Shalt0ni" target="_blank"><i data-feather="twitter"></i></a>
          <a href="https://instagram.com/shaltoni" target="_blank"><i data-feather="instagram"></i></a>
          <a href="https://linkedin.com/in/aymanshaltoni/" target="_blank"><i data-feather="linkedin"></i></a>
        </div>
      </div>
    </aside>
  </div>
  <script src="https://unpkg.com/feather-icons"></script>
  <script src="profile-app.js"></script>
</body>
</html>