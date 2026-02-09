<?php

/**
 * Debug script to test gallery backend endpoints
 * Visit: /unipulse/debug_gallery.php
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current request
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Colors for output
$colors = [
    'success' => '#27ae60',
    'error' => '#e74c3c',
    'warning' => '#f39c12',
    'info' => '#3498db'
];

function log_output($message, $type = 'info')
{
    global $colors;
    echo "<div style='background: {$colors[$type]}; color: white; padding: 10px; margin: 5px 0; border-radius: 4px;'>";
    echo htmlspecialchars($message);
    echo "</div>";
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Gallery Debug Tool</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
            background: #f5f5f5;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 900px;
            margin: 0 auto;
        }

        .section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #3498db;
        }

        button {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #2980b9;
        }

        textarea {
            width: 100%;
            height: 300px;
            font-family: monospace;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
        }

        .log {
            background: #f5f5f5;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            max-height: 400px;
            overflow-y: auto;
            margin: 10px 0;
        }

        .success {
            color: #27ae60;
        }

        .error {
            color: #e74c3c;
        }

        .warning {
            color: #f39c12;
        }

        .info {
            color: #3498db;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🔍 Gallery Debug Tool</h1>

        <?php if (empty($_SESSION['user_id'] ?? '')): ?>
            <div class="section error" style="border-left-color: #e74c3c;">
                <strong>⚠️ Not Authenticated</strong><br>
                You are not logged in. Please <a href="/unipulse/public/signin">sign in</a> first to debug gallery functionality.
            </div>
        <?php else: ?>

            <div class="section">
                <h3>Session Info</h3>
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($_SESSION['user_id'] ?? 'N/A'); ?></p>
                <p><strong>User Name:</strong> <?php echo htmlspecialchars($_SESSION['name'] ?? 'N/A'); ?></p>
                <p><strong>User Type:</strong> <?php echo htmlspecialchars($_SESSION['type'] ?? 'university'); ?></p>
            </div>

            <div class="section">
                <h3>1. Test Get Gallery</h3>
                <p>Fetch current gallery data from backend</p>
                <button onclick="testGetGallery()">Test GET Gallery</button>
                <div id="getGalleryLog" class="log"></div>
            </div>

            <div class="section">
                <h3>2. Test Save Gallery</h3>
                <p>Send test gallery data to backend</p>
                <textarea id="testGalleryData" placeholder="Gallery JSON">[
    {
        "id": 1,
        "title": "Test Gallery",
        "description": "Test description",
        "images": ["data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=="]
    }
]</textarea>
                <button onclick="testUpdateGallery()">Test UPDATE Gallery</button>
                <div id="updateGalleryLog" class="log"></div>
            </div>

            <div class="section">
                <h3>3. Database Check</h3>
                <button onclick="testDatabaseColumn()">Check Database Column</button>
                <div id="databaseLog" class="log"></div>
            </div>

            <div class="section">
                <h3>4. Clear LocalStorage</h3>
                <button onclick="clearLocalStorage()" style="background: #e74c3c;">Clear All Local Data</button>
                <div id="clearLog" class="log"></div>
            </div>

        <?php endif; ?>
    </div>

    <script>
        const baseUrl = '/unipulse/public/user/profile';

        function log(id, message, type = 'info') {
            const logEl = document.getElementById(id);
            const time = new Date().toLocaleTimeString();
            const className = type;
            logEl.innerHTML += `<div class="${className}"><strong>[${time}]</strong> ${message}</div>`;
            logEl.scrollTop = logEl.scrollHeight;
        }

        function clearLog(id) {
            document.getElementById(id).innerHTML = '';
        }

        async function testGetGallery() {
            clearLog('getGalleryLog');
            log('getGalleryLog', '⏳ Sending GET request to ' + baseUrl + '/getGallery', 'info');

            try {
                const response = await fetch(baseUrl + '/getGallery', {
                    credentials: 'same-origin'
                });

                log('getGalleryLog', 'HTTP Status: ' + response.status, 'info');

                const text = await response.text();
                log('getGalleryLog', 'Response body: ' + text, 'info');

                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        log('getGalleryLog', `✓ Success! Gallery has ${data.gallery?.length ?? 0} albums`, 'success');
                        if (data.gallery?.length > 0) {
                            log('getGalleryLog', 'Gallery data: ' + JSON.stringify(data.gallery), 'info');
                        }
                    } else {
                        log('getGalleryLog', '✗ Error: ' + (data.error || 'Unknown error'), 'error');
                    }
                } catch (e) {
                    log('getGalleryLog', '✗ Failed to parse response: ' + e.message, 'error');
                }
            } catch (error) {
                log('getGalleryLog', '✗ Network error: ' + error.message, 'error');
            }
        }

        async function testUpdateGallery() {
            clearLog('updateGalleryLog');
            log('updateGalleryLog', '⏳ Parsing gallery data...', 'info');

            const dataInput = document.getElementById('testGalleryData').value;
            let gallery;

            try {
                gallery = JSON.parse(dataInput);
                log('updateGalleryLog', `✓ Parsed ${gallery.length} albums`, 'success');
            } catch (e) {
                log('updateGalleryLog', '✗ Invalid JSON: ' + e.message, 'error');
                return;
            }

            log('updateGalleryLog', '⏳ Sending POST request to ' + baseUrl + '/updateGallery', 'info');

            try {
                const response = await fetch(baseUrl + '/updateGallery', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        gallery: gallery
                    })
                });

                log('updateGalleryLog', 'HTTP Status: ' + response.status, 'info');

                const text = await response.text();
                log('updateGalleryLog', 'Response body: ' + text, 'info');

                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        log('updateGalleryLog', '✓ Success! ' + data.message, 'success');
                    } else {
                        log('updateGalleryLog', '✗ Error: ' + (data.error || 'Unknown error'), 'error');
                    }
                } catch (e) {
                    log('updateGalleryLog', '✗ Failed to parse response: ' + e.message, 'error');
                }
            } catch (error) {
                log('updateGalleryLog', '✗ Network error: ' + error.message, 'error');
            }
        }

        async function testDatabaseColumn() {
            clearLog('databaseLog');
            log('databaseLog', '⏳ Checking database...', 'info');

            try {
                const response = await fetch(baseUrl + '/getGallery', {
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    log('databaseLog', '✓ Backend is accessible', 'success');
                    const data = await response.json();
                    if (data.success) {
                        log('databaseLog', '✓ Database column exists and is accessible', 'success');
                    } else {
                        log('databaseLog', '⚠️ Backend error: ' + data.error, 'warning');
                    }
                } else {
                    log('databaseLog', '✗ Backend returned HTTP ' + response.status, 'error');
                }
            } catch (error) {
                log('databaseLog', '✗ Cannot reach backend: ' + error.message, 'error');
            }
        }

        function clearLocalStorage() {
            clearLog('clearLog');
            try {
                localStorage.removeItem('galleryPhotos');
                log('clearLog', '✓ Cleared localStorage[galleryPhotos]', 'success');
            } catch (e) {
                log('clearLog', '✗ Error: ' + e.message, 'error');
            }
        }

        // Auto-run checks on page load
        window.addEventListener('DOMContentLoaded', () => {
            log('getGalleryLog', 'Ready. Click buttons to test endpoints.', 'info');
        });
    </script>
</body>

</html>