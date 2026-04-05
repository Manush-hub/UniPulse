
/* Extracted from Moderator/components/header.php */

(function () {
    function updateModeratorMsgBadge() {
        fetch('/unipulse/public/moderator/messages/unreadCount')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('moderatorMsgBadge');
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
    updateModeratorMsgBadge();
    setInterval(updateModeratorMsgBadge, 30000);
})();

