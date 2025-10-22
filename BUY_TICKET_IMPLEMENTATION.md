# Buy Ticket Button Implementation

## Overview
This implementation adds a "Buy Ticket" button for events that have paid tickets, which redirects users to a payment gateway page.

## Changes Made

### 1. Event View Page (`app/views/User/eventview.view.php`)
- Added a "Buy Ticket" button next to the "Join Event" button
- The button is initially hidden and will be shown via JavaScript for events with paid tickets
- Button has ID `buyTicketBtn` and uses Font Awesome ticket icon

### 2. Event View JavaScript (`public/assets/js/User/eventview-app.js`)

#### Display Logic (Line ~148-163)
- Added logic to show/hide the Buy Ticket button based on ticket type
- Button is displayed for events with `ticket_type` that is NOT 'free-all'
- Button is hidden for free events

#### Event Listener (Line ~837-845)
- Added click event listener for the Buy Ticket button
- When clicked, redirects to payment gateway with event ID as parameter
- URL format: `/unipulse/public/user/paymentgateway?event_id={eventId}`

### 3. Payment Gateway Controller (`app/controllers/User/Paymentgateway.php`)
**New File Created**

#### Main Features:
- **Authentication Check**: Ensures user is logged in before accessing payment page
- **Event Validation**: Verifies event exists and requires payment
- **Free Event Redirect**: Automatically redirects back to event view if tickets are free
- **Process Payment Method**: Placeholder for actual payment gateway integration

#### Methods:
1. `index()` - Displays the payment gateway page
   - Validates user authentication
   - Checks event exists and requires payment
   - Loads payment view

2. `processPayment()` - Handles payment processing (AJAX endpoint)
   - Validates payment data
   - Processes payment (currently simulated)
   - Registers user for event on successful payment
   - Returns JSON response

### 4. Payment Gateway View (`app/views/User/paymentgateway.view.php`)
**New File Created**

#### Features:
- **Event Summary Section**: Displays event name, date, ticket type, and total amount
- **Payment Method Selection**: Credit Card, PayPal, and Bank Transfer options
- **Payment Form**: 
  - Cardholder name
  - Card number (auto-formatted)
  - Expiry date (MM/YY format)
  - CVV
  - Email address
- **Form Validation**: 
  - Card number formatting (adds spaces every 4 digits)
  - Expiry date formatting (MM/YY)
  - CVV numeric validation
- **Security Notice**: Shows encryption message
- **Cancel & Submit Buttons**: Cancel returns to event view, submit processes payment

#### JavaScript Functions:
- `loadEventDetails()` - Fetches event data via AJAX
- `displayEventDetails()` - Populates event summary
- `setupPaymentMethodSelection()` - Handles payment method toggling
- `setupFormValidation()` - Formats and validates form inputs
- `processPayment()` - Simulates payment processing (2-second delay)
- `showError()` / `showSuccess()` - Display feedback messages

## Styling
The payment gateway includes:
- Modern card-based design
- Responsive layout
- Smooth transitions and hover effects
- Color-coded messages (error in red, success in green)
- Professional payment form styling
- Secure badge/notice

## User Flow

1. User views an event with paid tickets
2. "Buy Ticket" button appears in the navigation bar
3. User clicks "Buy Ticket"
4. Redirected to payment gateway page
5. Event details and price are displayed
6. User selects payment method
7. User fills in payment information
8. User clicks "Complete Payment"
9. Payment is processed (currently simulated)
10. On success, user is redirected back to event view
11. User is automatically registered for the event

## Future Enhancements

### Payment Gateway Integration
To implement real payment processing, you need to:

1. **Choose a Payment Provider** (e.g., Stripe, PayPal, PayHere for Sri Lanka)

2. **Create Payments Table** in database:
```sql
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'refunded'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);
```

3. **Create Payment Model** (`app/models/Payment.php`)

4. **Integrate Payment Gateway API**:
   - Add payment provider SDK
   - Implement secure payment processing
   - Handle callbacks/webhooks
   - Store transaction records

5. **Add Ticket Pricing**:
   - Add price fields to events table
   - Support multiple ticket tiers
   - Implement quantity selection

6. **Email Confirmation**:
   - Send payment receipt
   - Send event registration confirmation
   - Include QR code for check-in

7. **Error Handling**:
   - Handle payment failures gracefully
   - Implement retry mechanism
   - Log all transactions

## Testing

To test the implementation:

1. Navigate to an event view page
2. Ensure the event has a `ticket_type` other than 'free-all'
3. Verify "Buy Ticket" button appears
4. Click the button
5. Verify redirection to payment gateway
6. Check event details load correctly
7. Try filling the form and submitting
8. Verify success message and redirection

## Notes

- Currently, payment processing is simulated
- No actual charges are made
- Transaction IDs are randomly generated
- Payment data is not stored in database yet
- Real payment gateway integration requires additional setup
- Consider PCI compliance for handling card data
- Use HTTPS in production for security
