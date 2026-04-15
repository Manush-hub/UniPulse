<?php
$files = [
    'public/assets/js/Moderator/events-app.js',
    'public/assets/js/Publisher/events-app.js',
    'public/assets/js/Sponsor/events-app.js',
    'public/assets/js/User/events-app.js',
    'public/assets/js/Moderator/hidden-events-app.js',
    'public/assets/js/home-app.js',
    'public/assets/js/Sponsor/browse-events-app.js'
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "Skipping $file, does not exist.\n";
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Pattern to look for the end of the event-image div and start of event-content
    $pattern = '/(div class="event-status [^"]*">.*?<\/div>\n\s*)<\/div>\n\s*<div class="event-content">/s';
    
    $replacement = '$1${event.postponed_count > 0 ? `<div style="position: absolute; top: 3.5rem; right: 1rem; background: rgba(234, 179, 8, 0.95); color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; box-shadow: 0 2px 4px rgba(0,0,0,0.1); backdrop-filter: blur(4px);">POSTPONED</div>` : \'\'}' . "\n        </div>\n        <div class=\"event-content\">";
    
    $newContent = preg_replace($pattern, $replacement, $content);
    
    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        echo "Updated $file\n";
    } else {
        echo "No match found in $file\n";
    }
}
