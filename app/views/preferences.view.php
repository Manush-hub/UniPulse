<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Preferences | UniPulse</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/preferences-style.css">
</head>
<body>
    <div class="container">
        <!-- <div class="progress">
            <span class="step active"></span>
            <span class="step"></span>
        </div> -->
        <div class="form-section">
            <h2>Tell Us Your Event Interests</h2>
            <p class="subtitle">Help us recommend the best events for you. What is your preferred location?</p>
            <form id="preferencesForm">
                <div class="category">
                    <h3><span class="icon">🎵</span> Music Events</h3>
                    <div class="tags">
                        <label><input type="checkbox" name="music" value="Concerts">Concerts</label>
                        <label><input type="checkbox" name="music" value="Festivals">Festivals</label>
                        <label><input type="checkbox" name="music" value="EDM Parties">EDM Parties</label>
                        <label><input type="checkbox" name="music" value="Live Bands">Live Bands</label>
                        <label><input type="checkbox" name="music" value="Open Mic">Open Mic</label>
                    </div>
                </div>
                <div class="category">
                    <h3><span class="icon">🏅</span> Sports Events</h3>
                    <div class="tags">
                        <label><input type="checkbox" name="sport" value="Football Matches">Football Matches</label>
                        <label><input type="checkbox" name="sport" value="Basketball Games">Basketball Games</label>
                        <label><input type="checkbox" name="sport" value="Marathons">Marathons</label>
                        <label><input type="checkbox" name="sport" value="Swimming Competitions">Swimming Competitions</label>
                        <label><input type="checkbox" name="sport" value="Esports">Esports</label>
                    </div>
                </div>
                <div class="category">
                    <h3><span class="icon">💼</span> Business Events</h3>
                    <div class="tags">
                        <label><input type="checkbox" name="business" value="Trade Shows">Trade Shows</label>
                        <label><input type="checkbox" name="business" value="Product Launches">Product Launches</label>
                        <label><input type="checkbox" name="business" value="Networking">Networking</label>
                        <label><input type="checkbox" name="business" value="Workshops">Workshops</label>
                        <label><input type="checkbox" name="business" value="Investor Pitch Events">Investor Pitch Events</label>
                    </div>
                </div>
                <div class="category">
                    <h3><span class="icon">🖼️</span> Exhibitions & Fairs</h3>
                    <div class="tags">
                        <label><input type="checkbox" name="exhibition" value="Art Exhibitions">Art Exhibitions</label>
                        <label><input type="checkbox" name="exhibition" value="Tech Fairs">Tech Fairs</label>
                        <label><input type="checkbox" name="exhibition" value="Science Expos">Science Expos</label>
                        <label><input type="checkbox" name="exhibition" value="Fashion Shows">Fashion Shows</label>
                    </div>
                </div>
                <!-- <div class="location-section">
                    <label for="location">Preferred Location:</label>
                    <input type="text" id="location" name="location" placeholder="Enter your city or area">
                </div> -->
                <div class="form-actions">
                    <!-- <button type="button" id="skipBtn">Skip</button> -->
                    <button type="submit" id="nextBtn">Next →</button>
                </div>
            </form>
        </div>
    </div>
    <script src="/assets/js/preferences-app.js"></script>
</body>
</html>
