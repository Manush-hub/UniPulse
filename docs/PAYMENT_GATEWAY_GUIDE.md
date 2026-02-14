# Payment Gateway Integration

## Overview
This payment gateway allows users to make secure payments for event tickets, donations, and other transactions within UniPulse.

## Files Created

### Controller
- **Location**: `/app/controllers/Payment.php`
- **Routes**:
  - `GET /payment` - Display payment form
  - `POST /payment` - Process payment
  - `GET /payment/success` - Payment success page

### Views
- `/app/views/payment.view.php` - Main payment form
- `/app/views/payment_success.view.php` - Success confirmation page

### Styles
- `/public/assets/css/payment-style.css` - Payment gateway styling

### Database
- **Table**: `payments`
- **Migration**: `/database/create_payments_table.php`

## Features

### Payment Methods Supported
1. **Credit/Debit Card**
   - 16-digit card number validation
   - Expiry date (MM/YY format)
   - CVV verification (3-4 digits)
   - Cardholder name

2. **Bank Transfer**
   - Bank details displayed for manual transfer
   - Transaction reference system

3. **Mobile Payment**
   - Mobile number validation
   - OTP verification system (to be integrated)

### Security Features
- Server-side validation for all payment data
- Card number format validation
- CVV security checks
- Transaction ID generation
- 256-bit SSL encryption ready
- Secure session management

### User Experience
- Responsive design (mobile & desktop)
- Real-time form validation
- Card number auto-formatting
- Processing fee calculation (2%)
- Payment method switching
- Success confirmation page

## Usage

### Integrating Payment into Your Flow

#### Option 1: Direct Link
```php
<a href="<?= ROOT ?>/payment?amount=1500&description=Event Ticket - Tech Conference">
    Pay Now
</a>
```

#### Option 2: Session-based (Recommended)
```php
// In your controller
$_SESSION['payment_amount'] = 1500;
$_SESSION['payment_description'] = 'Event Ticket - Tech Conference';
header('Location: ' . ROOT . '/payment');
```

#### Option 3: Form Submission
```html
<form action="<?= ROOT ?>/payment" method="GET">
    <input type="hidden" name="amount" value="1500">
    <input type="hidden" name="description" value="Event Ticket">
    <button type="submit">Proceed to Payment</button>
</form>
```

## Database Schema

```sql
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100) UNIQUE NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    description TEXT,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

## Configuration

### Payment Gateway API Integration
To integrate with a real payment processor (Stripe, PayPal, etc.), update the `processPayment()` method in `/app/controllers/Payment.php`:

```php
private function processPayment($data) {
    // TODO: Replace with actual payment gateway API calls
    // Example: Stripe API integration
    // $stripe = new \Stripe\StripeClient('your_secret_key');
    // $charge = $stripe->charges->create([...]);
    
    // Current implementation is a simulation
}
```

### Supported Payment Gateways (Future Integration)
- Stripe
- PayPal
- PayHere (Sri Lankan payment gateway)
- Razorpay
- Square

## Testing

### Test Card Numbers (Simulation Mode)
Since the current implementation is in simulation mode, any valid format card number will work:
- Card Number: `4111 1111 1111 1111` (16 digits)
- Expiry: Any future date (MM/YY)
- CVV: Any 3-4 digit number
- Name: Any text

### Test Flow
1. Navigate to `/payment?amount=100&description=Test Payment`
2. Select payment method (Card/Bank/Mobile)
3. Fill in required details
4. Click "Pay Now"
5. Verify success page displays transaction ID
6. Check database for payment record

## Access Control
- **Required**: User must be logged in (`$_SESSION['user_id']`)
- **Redirect**: Unauthenticated users redirected to `/signin`

## Customization

### Styling
Modify `/public/assets/css/payment-style.css` to match your brand:
- Primary color: `#667eea`
- Secondary color: `#764ba2`
- Success color: `#4caf50`

### Processing Fee
Currently set to 2% in the JavaScript. Update in `/app/views/payment.view.php`:
```javascript
const processingFee = (amount * 0.02).toFixed(2); // Change 0.02 to desired percentage
```

### Transaction ID Format
Modify in `/app/controllers/Payment.php`:
```php
$transactionId = 'TXN' . time() . rand(1000, 9999); // Customize prefix and format
```

## Future Enhancements
- [ ] Real payment gateway integration (Stripe/PayPal/PayHere)
- [ ] Webhook handling for payment confirmations
- [ ] Refund processing
- [ ] Payment history page for users
- [ ] Email notifications with receipt
- [ ] PDF receipt generation
- [ ] Multi-currency support
- [ ] Subscription/recurring payments
- [ ] Payment analytics dashboard
- [ ] Failed payment retry mechanism

## Troubleshooting

### Database Issues
If the payments table doesn't exist, run:
```bash
/Applications/MAMP/bin/php/php8.4.1/bin/php /Applications/MAMP/htdocs/UniPulse/database/create_payments_table.php
```

### Session Issues
Ensure session is started before accessing payment page. Check `SessionMiddleware.php` is active.

### Styling Not Loading
Verify the CSS path matches your server configuration:
```html
<link rel="stylesheet" href="/unipulse/public/assets/css/payment-style.css">
```

## Security Recommendations
1. **Never store full card numbers** - Use tokenization with payment gateway
2. **Use HTTPS** - Always encrypt data in transit
3. **PCI DSS Compliance** - If storing card data, ensure compliance
4. **Rate Limiting** - Prevent brute force attacks
5. **Audit Logging** - Log all payment attempts
6. **Input Sanitization** - Already implemented, maintain strict validation

## Support
For issues or questions about the payment gateway implementation, contact the development team.

---

**Note**: This implementation is currently in **simulation mode** for development and testing. Before going to production, you must integrate with a real payment gateway and implement proper security measures.
