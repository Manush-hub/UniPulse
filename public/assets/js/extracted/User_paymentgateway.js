// Get event ID from URL
const urlParams = new URLSearchParams(window.location.search);
const eventId = urlParams.get('event_id');
let currentEventDetails = null;
let currentPaymentData = null;

// Load event details
document.addEventListener('DOMContentLoaded', function () {
    if (!eventId) {
        showError('No event ID provided');
        return;
    }

    loadEventDetails();
    setupCancelButton();

    // Setup form submission for PayHere redirect
    document.getElementById('paymentForm').addEventListener('submit', function (e) {
        e.preventDefault();
        processPayment();
    });
});

function loadEventDetails() {
    // Fetch event details
    fetch(`/unipulse/public/user/eventview/getEvent?id=${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.event) {
                currentEventDetails = data.event;
                displayEventDetails(data.event);
            } else {
                showError('Event not found');
            }
        })
        .catch(error => {
            console.error('Error loading event:', error);
            showError('Failed to load event details');
        });
}

function displayEventDetails(event) {
    const paymentData = getStoredPaymentData();

    document.getElementById('eventName').textContent =
        paymentData?.eventTitle || event.title || event.name || 'Event';
    document.getElementById('eventDate').textContent = event.date ? formatDate(event.date) : 'N/A';

    if (paymentData && Array.isArray(paymentData.tickets) && paymentData.tickets.length > 0) {
        currentPaymentData = paymentData;

        const totalQuantity = paymentData.tickets.reduce((sum, ticket) => {
            return sum + (parseInt(ticket.quantity, 10) || 0);
        }, 0);

        const ticketTypeLabel = paymentData.tickets.length === 1 ?
            paymentData.tickets[0].name :
            `Multiple (${paymentData.tickets.length} types)`;

        document.getElementById('ticketType').textContent = ticketTypeLabel;
        document.getElementById('ticketQuantity').textContent = String(totalQuantity || 1);
        document.getElementById('totalAmount').textContent = `LKR ${Number(paymentData.totalAmount || 0).toFixed(2)}`;
        updateSavingsDisplay(paymentData);
        return;
    }

    // Fallback for direct page access without event selection state.
    const ticketType = event.ticket_type || 'Standard';
    document.getElementById('ticketType').textContent = formatTicketType(ticketType);

    let amount = 100;
    if (Array.isArray(event.ticket_types) && event.ticket_types.length > 0) {
        const firstTicket = event.ticket_types[0];
        amount = Number(firstTicket.discounted_price || firstTicket.price || 100);
    }

    document.getElementById('ticketQuantity').textContent = '1';
    document.getElementById('totalAmount').textContent = `LKR ${amount.toFixed(2)}`;
    updateSavingsDisplay(null);
}

function updateSavingsDisplay(paymentData) {
    const savingsRow = document.getElementById('savingsRow');
    const savingsAmount = document.getElementById('savingsAmount');
    if (!savingsRow || !savingsAmount) {
        return;
    }

    let savings = 0;

    if (paymentData && Array.isArray(paymentData.tickets)) {
        savings = paymentData.tickets.reduce((sum, ticket) => {
            if (ticket && typeof ticket.discountAmount !== 'undefined') {
                return sum + (Number(ticket.discountAmount) || 0);
            }

            const qty = Number(ticket?.quantity) || 0;
            const originalPrice = Number(ticket?.originalPrice) || 0;
            const payablePrice = Number(ticket?.price) || 0;
            const lineSaving = Math.max(0, (originalPrice - payablePrice) * qty);
            return sum + lineSaving;
        }, 0);
    }

    if (savings > 0) {
        savingsAmount.textContent = `LKR ${savings.toFixed(2)}`;
        savingsRow.style.display = 'flex';
    } else {
        savingsAmount.textContent = 'LKR 0.00';
        savingsRow.style.display = 'none';
    }
}

function getStoredPaymentData() {
    try {
        const raw = sessionStorage.getItem('paymentData');
        if (!raw) {
            return null;
        }

        const parsed = JSON.parse(raw);
        const parsedEventId = parsed?.eventId != null ? String(parsed.eventId) : null;

        if (!parsedEventId || parsedEventId !== String(eventId)) {
            return null;
        }

        return parsed;
    } catch (error) {
        console.warn('Unable to parse paymentData from sessionStorage', error);
        return null;
    }
}

function formatDate(dateString) {
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

function formatTicketType(type) {
    const typeMap = {
        'free-all': 'Free for All',
        'free-limited': 'Free (Limited)',
        'paid': 'Paid',
        'donation-based': 'Donation Based'
    };
    return typeMap[type] || type;
}

function calculateTicketPrice(ticketType) {
    // Get price from ticket_types if available, or use default
    // This will be properly populated by the event data
    return window.currentEventPrice || 100; // Default fallback
    const priceMap = {
        'paid': 1000.00,
        'donation-based': 500.00,
        'free-limited': 0.00,
        'free-all': 0.00
    };
    return priceMap[ticketType] || 1000.00;
}

function setupCancelButton() {
    document.getElementById('cancelBtn').addEventListener('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to cancel this payment?')) {
            window.location.href = `/unipulse/public/user/eventview?id=${eventId}`;
        }
    });
}

function processPayment() {
    // Show loading state
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Redirecting to PayHere...';
    submitBtn.disabled = true;

    const paymentData = currentPaymentData || getStoredPaymentData();

    let amount = 0;
    let quantity = 1;
    let description = 'Event Ticket';
    let ticketTierName = 'General';

    if (paymentData && Array.isArray(paymentData.tickets) && paymentData.tickets.length > 0) {
        amount = Number(paymentData.totalAmount || 0);
        quantity = paymentData.tickets.reduce((sum, ticket) => {
            return sum + (parseInt(ticket.quantity, 10) || 0);
        }, 0) || 1;

        if (paymentData.tickets.length === 1) {
            description = `Event Ticket - ${paymentData.tickets[0].name}`;
            ticketTierName = paymentData.tickets[0].name;
        } else {
            description = `Event Tickets - ${paymentData.tickets.length} types`;
            ticketTierName = paymentData.tickets.map(t => t.name).join(', ');
        }
    } else {
        const totalAmountText = document.getElementById('totalAmount').textContent;
        amount = parseFloat(totalAmountText.replace('LKR', '').trim()) || 0;
    }

    if (amount <= 0) {
        showError('Invalid payment amount. Please go back and select at least one ticket.');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
    }

    // Redirect to PayHere payment with session setup
    // The Payment controller expects these params
    const ticketsMetadata = paymentData && paymentData.tickets ? JSON.stringify(paymentData.tickets) : '';
    const paymentUrl = `/unipulse/public/payment?amount=${encodeURIComponent(amount.toFixed(2))}&type=ticket&event_id=${encodeURIComponent(eventId)}&quantity=${encodeURIComponent(quantity)}&description=${encodeURIComponent(description)}&ticket_tier=${encodeURIComponent(ticketTierName)}&tickets_metadata=${encodeURIComponent(ticketsMetadata)}`;

    console.log('Redirecting to PayHere:', {
        event_id: eventId,
        amount: amount,
        quantity: quantity,
        description: description,
        ticket_tier: ticketTierName,
        tickets_metadata: ticketsMetadata,
        url: paymentUrl
    });

    // Redirect to payment page which will show PayHere option
    window.location.href = paymentUrl;
}

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';

    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 5000);
}

function showSuccess(message) {
    const successDiv = document.getElementById('successMessage');
    successDiv.textContent = message;
    successDiv.style.display = 'block';
}