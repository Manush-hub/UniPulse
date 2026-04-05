
/* Extracted from Admin/components/header.php */

(function () {
    function updateAdminMsgBadge() {
        fetch('/unipulse/public/admin/messages/unreadCount')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('adminMsgBadge');
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
    updateAdminMsgBadge();
    setInterval(updateAdminMsgBadge, 30000);
})();

