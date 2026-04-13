(function () {
    function updateAdminMsgBadge() {
        Promise.all([
            fetch('/unipulse/public/admin/messages/unreadCount').then(r => r.json()).catch(() => ({ success: false, count: 0 })),
            fetch('/unipulse/public/admin/dashboard/getNotifications').then(r => r.json()).catch(() => ({ success: false, notifications: [] }))
        ])
            .then(([chatData, notifData]) => {
                const badge = document.getElementById('adminMsgBadge');
                if (!badge) return;

                const chatUnread = (chatData && chatData.success) ? (parseInt(chatData.count, 10) || 0) : 0;
                const supportUnread = (notifData && notifData.success && Array.isArray(notifData.notifications))
                    ? notifData.notifications.filter(n => n && n.type === 'support_message' && n.unread).length
                    : 0;

                const totalUnread = chatUnread + supportUnread;

                if (totalUnread > 0) {
                    badge.textContent = totalUnread;
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