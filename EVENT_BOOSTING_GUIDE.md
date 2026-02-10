# Event Boosting Feature - Implementation Guide

## Overview
The Event Boosting feature allows publishers to promote their events by paying for enhanced visibility on the UniPulse platform. Boosted events receive priority placement in search results, featured sections, and notifications.

## Features Implemented

### 1. Database Structure
- **event_boosts** table: Tracks all boost transactions
- **boost_pricing** table: Configurable pricing tiers
- **events** table updates: Added boost-related columns (is_boosted, boost_expires_at, boost_priority)

### 2. Pricing Tiers
Default pricing structure with volume discounts:
- 1 Day: LKR 500
- 3 Days: LKR 1,350 (10% discount)
- 7 Days: LKR 2,800 (20% discount)
- 14 Days: LKR 4,900 (30% discount)
- 30 Days: LKR 9,000 (40% discount)

### 3. Publisher Dashboard Integration
Location: `app/views/Publisher/dashboard.view.php`

New section includes:
- Benefits showcase
- Active boosts display
- Event selection dropdown
- Duration selection cards
- Payment modal with multiple payment methods

### 4. Backend API Endpoints
Controller: `app/controllers/Publisher/Dashboard.php`

New methods:
- `getBoostPricing()` - Fetch available pricing tiers
- `getEventsForBoosting()` - Get publisher's eligible events
- `getActiveBoosts()` - Display currently active boosts
- `createBoost()` - Process new boost request
- `cancelBoost()` - Cancel an active boost

### 5. Frontend Implementation
Files modified:
- **CSS**: `public/assets/css/Publisher/dashboard-style.css`
- **JavaScript**: `public/assets/js/Publisher/dashboard-app.js`

Features:
- Interactive duration selection
- Real-time price calculation
- Payment method selection (Card, Bank Transfer, Mobile Payment)
- Success/error notifications
- Auto-refresh of boost status

## How to Use (Publisher Perspective)

1. **Navigate to Dashboard**
   - Log in as a publisher
   - Scroll to "Boost Your Events" section

2. **Select Event**
   - Choose an upcoming event from the dropdown
   - Only upcoming/ongoing events are available

3. **Choose Duration**
   - Click on a duration card (1 day to 30 days)
   - See real-time price updates with discount badges

4. **Complete Payment**
   - Click "Proceed to Payment"
   - Select payment method
   - Enter payment details
   - Click "Pay Now"

5. **Track Boosts**
   - View active boosts in the left panel
   - See remaining time and impressions
   - Monitor boost performance

## Database Schema

### event_boosts table
```sql
- id (PRIMARY KEY)
- event_id (FOREIGN KEY -> events)
- publisher_id (FOREIGN KEY -> publishers)
- boost_start_date
- boost_end_date
- duration_days
- amount_paid
- payment_status (pending, completed, failed, refunded)
- payment_method
- transaction_id
- boost_status (active, expired, cancelled)
- priority_level
- impressions
- clicks
- created_at
- updated_at
```

### boost_pricing table
```sql
- id (PRIMARY KEY)
- duration_days (UNIQUE)
- price_per_day
- total_price
- discount_percentage
- priority_multiplier
- is_active
- description
```

## Installation Steps

1. **Run Database Migration**
   ```bash
   php database/create_event_boosts_table.php
   ```

2. **Verify Tables Created**
   - event_boosts
   - boost_pricing
   - events (with new columns)

3. **Access Publisher Dashboard**
   - Navigate to `/publisher/dashboard`
   - Scroll to boosting section

## Configuration

### Modify Pricing Tiers
Update the `boost_pricing` table in the database:
```sql
UPDATE boost_pricing 
SET total_price = 1000, discount_percentage = 15 
WHERE duration_days = 3;
```

### Add New Duration
```sql
INSERT INTO boost_pricing 
(duration_days, price_per_day, total_price, discount_percentage, description) 
VALUES (60, 250, 15000, 50, '2 Months Boost - 50% discount');
```

## Payment Integration
Currently supports three payment methods:
1. **Credit/Debit Card** - Card details form
2. **Bank Transfer** - Bank account details displayed
3. **Mobile Payment** - Provider selection (Dialog, Mobitel, Hutch, Airtel)

> Note: This is a frontend implementation. Real payment gateway integration (Stripe, PayPal, or local LKR gateways) should be added for production.

## Future Enhancements

1. **Analytics Dashboard**
   - Track impressions and clicks
   - ROI calculation
   - Conversion rates

2. **Auto-renewal**
   - Optional automatic boost renewal
   - Email notifications before expiry

3. **A/B Testing**
   - Test different boost placements
   - Optimize visibility strategies

4. **Batch Boosting**
   - Boost multiple events at once
   - Bulk discount offers

5. **Real Payment Gateway**
   - Integrate Stripe, PayPal
   - Local LKR payment processors
   - Mobile money integration

## Security Considerations

- All boost transactions are logged with transaction IDs
- Payment status tracked separately from boost status
- Publisher verification required before boosting
- Event ownership validated before allowing boost

## API Reference

### GET /publisher/dashboard/getEventsForBoosting
Returns list of publisher's upcoming events eligible for boosting.

**Response:**
```json
{
  "success": true,
  "events": [
    {
      "id": 1,
      "title": "Tech Summit 2025",
      "event_date": "2025-09-15",
      "is_boosted": false
    }
  ]
}
```

### GET /publisher/dashboard/getActiveBoosts
Returns currently active boosts for the publisher.

**Response:**
```json
{
  "success": true,
  "boosts": [
    {
      "id": 1,
      "event_title": "Tech Summit 2025",
      "boost_end_date": "2025-02-05 12:00:00",
      "amount_paid": "2800.00",
      "time_remaining": "3 days"
    }
  ]
}
```

### POST /publisher/dashboard/createBoost
Creates a new boost request.

**Request Body:**
```json
{
  "event_id": 1,
  "duration_days": 7,
  "amount": 2800,
  "payment_method": "card"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Event boosted successfully!",
  "transaction_id": "BOOST-1738012345-1234",
  "boost_end_date": "2025-02-08 12:00:00"
}
```

## Support
For issues or questions:
- Check console logs for errors
- Verify database connection
- Ensure all tables are created properly
- Check PHP error logs in MAMP

---

**Version:** 1.0  
**Last Updated:** January 2026  
**Developer:** UniPulse Development Team
