const eventviewActionConfig = window.eventviewActionConfig || {};

if (eventviewActionConfig.purchaseTicketEnabled) {
    function purchaseTicket() {
        const eventId = window.currentEvent && window.currentEvent.id;
        if (!eventId) {
            alert('Event information not available');
            return;
        }

        if (!eventviewActionConfig.isLoggedIn) {
            alert('Please log in to purchase tickets');
            window.location.href = `${eventviewActionConfig.signinUrl}?redirect=` + encodeURIComponent(window.location.pathname);
            return;
        }

        const ticketType = window.currentEvent && window.currentEvent.ticket_type;
        if (ticketType === 'free-all') {
            alert('This is a free event. No ticket purchase required.');
            return;
        }

        window.location.href = `${eventviewActionConfig.ticketPaymentBaseUrl}${eventId}`;
    }

    window.purchaseTicket = purchaseTicket;
}

if (eventviewActionConfig.visitProfileEnabled) {
    function visitPublisherProfile() {
        const publisherId =
            (window.currentEvent && window.currentEvent.publisher_id) ||
            (window.currentEvent && window.currentEvent.created_by);

        if (publisherId) {
            window.location.href = `${eventviewActionConfig.publisherProfileBaseUrl}${publisherId}`;
        } else {
            alert('Publisher profile not available');
        }
    }

    window.visitPublisherProfile = visitPublisherProfile;
}
