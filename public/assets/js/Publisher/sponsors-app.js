
/* Extracted from Publisher/sponsors.view.php */

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const sponsorsGrid = document.getElementById('sponsorsGrid');

        searchInput.addEventListener('input', function() {
            searchSponsors();
        });

        function searchSponsors() {
            const searchTerm = searchInput.value.toLowerCase();
            const cards = sponsorsGrid.querySelectorAll('.sponsor-card');

            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.dataset.name;
                const matchesSearch = name.includes(searchTerm);

                if (matchesSearch) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show empty state if no results
            const emptyState = sponsorsGrid.querySelector('.empty-state');
            if (visibleCount === 0 && !emptyState) {
                const noResults = document.createElement('div');
                noResults.className = 'empty-state no-results';
                noResults.innerHTML = `
                    <i class="fas fa-search"></i>
                    <h3>No Sponsors Found</h3>
                    <p>Try adjusting your search criteria.</p>
                `;
                sponsorsGrid.appendChild(noResults);
            } else if (visibleCount > 0) {
                const noResults = sponsorsGrid.querySelector('.no-results');
                if (noResults) noResults.remove();
            }
        }

        function viewSponsorProfile(sponsorId) {
            window.location.href = `/unipulse/public/sponsor/public/${sponsorId}`;
        }

        function contactSponsor(sponsorId, sponsorName) {
            // Navigate to messages page with sponsor info in URL parameters
            window.location.href = `/unipulse/public/publisher/messages?recipient_id=${sponsorId}&recipient_type=sponsor&recipient_name=${encodeURIComponent(sponsorName)}`;
        }
    
