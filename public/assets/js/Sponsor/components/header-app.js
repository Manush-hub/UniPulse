
/* Extracted from Sponsor/components/header.php */

(function () {
    function updateSponsorMsgBadge() {
        fetch('/unipulse/public/sponsor/messages/unreadCount')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('sponsorMsgBadge');
                if (!badge) return;
                if (data.success && data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(() => {});
    }
    updateSponsorMsgBadge();
    setInterval(updateSponsorMsgBadge, 30000);
})();

