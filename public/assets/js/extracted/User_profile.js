window.profileApi = {
            get: '/unipulse/public/user/profile/getProfile',
            update: '/unipulse/public/user/profile/updateProfile'
        };
        
        // Debug: Test if functions are available
        console.log('Profile page loaded');
        console.log('uploadCover function:', typeof uploadCover);
        console.log('changeCoverImage function:', typeof changeCoverImage);
        console.log('coverInput element:', document.getElementById('coverInput'));
        
        // Add event listener to verify file selection
        document.addEventListener('DOMContentLoaded', function() {
            const coverInput = document.getElementById('coverInput');
            if (coverInput) {
                console.log('✓ coverInput found in DOM');
                coverInput.addEventListener('change', function(e) {
                    console.log('✓ File input changed event fired!');
                    console.log('Selected file:', e.target.files[0]);
                });
            } else {
                console.error('✗ coverInput NOT found in DOM');
            }
            
            const coverOverlay = document.querySelector('.cover-overlay');
            if (coverOverlay) {
                console.log('✓ cover-overlay found');
                coverOverlay.addEventListener('click', function() {
                    console.log('✓ Cover overlay clicked!');
                });
            }
        });