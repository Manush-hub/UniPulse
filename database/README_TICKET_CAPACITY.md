# Ticket Capacity Tracking System

## Overview
The ticket system now tracks the original total capacity and displays a visual progress bar showing how many tickets have been sold vs. available.

## How It Works

### Backend (Database)
Each ticket in the `ticket_types` JSON field now has:
- `quantity`: Current available tickets (decreases as tickets are sold)
- `total_capacity`: Original total number of tickets (stays constant)
- `name`: Ticket type name (e.g., "VVIP", "VIP", "Normal")
- `price`: Ticket price
- `description`: Optional description

### Calculation
- **Sold** = total_capacity - quantity
- **Sold Percentage** = (Sold / total_capacity) × 100
- **Available Percentage** = (quantity / total_capacity) × 100

### Progress Bar Display
The progress bar shows:
- **Green** (50%+ available): "Good Availability"
- **Amber** (25-50% available): "Selling Fast"
- **Red** (<25% available): "Almost Sold Out"
- **Gray** (0% available): "Sold Out"

## Example
If a ticket has:
- `total_capacity`: 100
- `quantity`: 50 (currently available)

Then:
- Sold: 50 tickets
- Progress bar: 50% filled (Amber - "Selling Fast")
- Display: "50 sold | 50 available"

## When Tickets Are Purchased
When a user buys tickets, only the `quantity` field is decreased. The `total_capacity` remains the same, allowing accurate tracking of sales progress.

## Migration
Run `add_total_capacity_to_tickets.php` to add `total_capacity` to existing events (already completed).
