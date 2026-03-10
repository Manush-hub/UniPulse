<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniPulse - Publisher Dashboard</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/components/header-style.css">
    <link rel="stylesheet" href="/UniPulse/public/assets/css/Publisher/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>
    <!-- Header -->
    <?php
    $pageConfig = ['activeNav' => 'dashboard'];
    include __DIR__ . '/components/header.php';
    ?>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="container">
                <div class="welcome-content">
                    <div class="welcome-text">
                        <h1>Welcome back, <span id="welcomeUsername"><?php echo htmlspecialchars($data['user']['name'] ?? 'Organization'); ?></span>! 👋</h1>
                        <p>Manage your events and track performance from your organizer dashboard.</p>
                        <!-- <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="upcomingEvents">7</span>
                                <span class="stat-label">Upcoming Events</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="ticketsSold">1,280</span>
                                <span class="stat-label">Tickets Sold</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="totalVolunteers">45</span>
                                <span class="stat-label">Volunteers</span>
                            </div>
                        </div> -->
                    </div>
                    <div class="welcome-actions">
                        <button class="btn btn-primary" onclick="window.location.href='/UniPulse/public/publisher/createevent'">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Create Event
                        </button>
                        <button class="btn btn-primary" onclick="window.location.href='/UniPulse/public/publisher/sponsors'">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Browse Sponsors
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Actions
        <section class="quick-actions">
            <div class="container">
                <h2>Quick Actions</h2>
                <div class="actions-grid">
                    <div class="action-card" onclick="window.location.href='my-events.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <h3>Manage Events</h3>
                        <p>View and edit your events</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='volunteers.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <h3>Volunteers</h3>
                        <p>Manage volunteer applications</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='/unipulse/public/publisher/sponsorships'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path>
                                <path d="M12 18V6"></path>
                            </svg>
                        </div>
                        <h3>Sponsorships</h3>
                        <p>Track donations & sponsors</p>
                    </div>
                    <div class="action-card" onclick="window.location.href='analytics.html'">
                        <div class="action-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M3 3v18h18"></path>
                                <path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"></path>
                            </svg>
                        </div>
                        <h3>Analytics</h3>
                        <p>View performance reports</p>
                    </div>
                </div>
            </div>
        </section> -->

        <!-- Event Management -->
        <section class="event-management">
            <div class="container">
                <div class="section-header">
                    <h2>Your Events</h2>
                    <div class="event-filters">
                        <button class="filter-btn active" data-filter="all">All Events</button>
                        <button class="filter-btn" data-filter="upcoming">Upcoming</button>
                        <button class="filter-btn" data-filter="past">Completed</button>
                    </div>
                    <div class="event-slider-controls" style="margin-top: 1rem;">
                        <button class="btn btn-outline" id="prevEventsBtn" type="button"><i class="fas fa-chevron-left"></i> Prev</button>
                        <button class="btn btn-outline" id="nextEventsBtn" type="button">Next <i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="events-grid" id="eventsManagementList">
                    <!-- Events will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Event Boosting Section -->
        <section class="event-boosting-section">
            <div class="container">
                <div class="section-header">
                    <h2>🚀 Boost Your Events</h2>
                    <p>Increase visibility and reach more participants by boosting your events</p>
                </div>
                <div class="boost-content">
                    <div class="boost-info-panel">
                        <div class="boost-benefits">
                            <h3>Benefits of Boosting</h3>
                            <ul class="benefits-list">
                                <li>
                                    <i class="fas fa-rocket"></i>
                                    <span>Priority placement in search results</span>
                                </li>
                                <li>
                                    <i class="fas fa-users"></i>
                                    <span>Reach up to 10x more students</span>
                                </li>
                                <li>
                                    <i class="fas fa-chart-line"></i>
                                    <span>Enhanced visibility on homepage</span>
                                </li>
                                <li>
                                    <i class="fas fa-star"></i>
                                    <span>Featured badge on your event</span>
                                </li>
                                <li>
                                    <i class="fas fa-bell"></i>
                                    <span>Priority in push notifications</span>
                                </li>
                            </ul>
                        </div>
                        <div class="active-boosts">
                            <h3>Your Active Boosts</h3>
                            <div id="activeBoostsList" class="active-boosts-list">
                                <div class="loading-boosts">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <p>Loading active boosts...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="boost-action-panel">
                        <div class="boost-form-container">
                            <h3>Select Event to Boost</h3>
                            <div class="boost-rules-info">
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <strong>Boosting Rules:</strong>
                                    <ul>
                                        <li>Each event can only have one active boost at a time</li>
                                        <li>You can re-boost an event after its current boost expires</li>
                                        <li>Only upcoming events can be boosted</li>
                                    </ul>
                                </div>
                            </div>
                            <form id="boostEventForm" class="boost-form">
                                <div class="form-group">
                                    <label class="boost-label">
                                        <i class="fas fa-calendar-alt"></i>
                                        Choose Event
                                    </label>
                                    <select id="eventSelect" name="event_id" class="form-control" required>
                                        <option value="">Loading events...</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="boost-label">
                                        <i class="fas fa-clock"></i>
                                        Boost Duration
                                    </label>
                                    <div class="duration-options" id="durationOptions">
                                        <div class="duration-card" data-days="1" data-price="500">
                                            <div class="duration-header">
                                                <span class="duration-days">1 Day</span>
                                            </div>
                                            <div class="duration-price">LKR 500</div>
                                            <div class="duration-badge">Quick Boost</div>
                                        </div>
                                        <div class="duration-card popular" data-days="3" data-price="1350">
                                            <div class="duration-header">
                                                <span class="duration-days">3 Days</span>
                                                <span class="discount-badge">10% OFF</span>
                                            </div>
                                            <div class="duration-price">LKR 1,350</div>
                                            <div class="duration-badge">Popular</div>
                                        </div>
                                        <div class="duration-card" data-days="7" data-price="2800">
                                            <div class="duration-header">
                                                <span class="duration-days">7 Days</span>
                                                <span class="discount-badge">20% OFF</span>
                                            </div>
                                            <div class="duration-price">LKR 2,800</div>
                                            <div class="duration-badge">Best Value</div>
                                        </div>
                                        <div class="duration-card" data-days="14" data-price="4900">
                                            <div class="duration-header">
                                                <span class="duration-days">14 Days</span>
                                                <span class="discount-badge">30% OFF</span>
                                            </div>
                                            <div class="duration-price">LKR 4,900</div>
                                            <div class="duration-badge">Extended</div>
                                        </div>
                                        <div class="duration-card premium" data-days="30" data-price="9000">
                                            <div class="duration-header">
                                                <span class="duration-days">30 Days</span>
                                                <span class="discount-badge">40% OFF</span>
                                            </div>
                                            <div class="duration-price">LKR 9,000</div>
                                            <div class="duration-badge">Premium</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="boost-summary">
                                    <div class="summary-item">
                                        <span>Selected Duration:</span>
                                        <strong id="selectedDuration">Not selected</strong>
                                    </div>
                                    <div class="summary-item total">
                                        <span>Total Amount:</span>
                                        <strong id="totalAmount">LKR 0</strong>
                                    </div>
                                </div>

                                <input type="hidden" id="boostDuration" name="duration_days" required>
                                <input type="hidden" id="boostPrice" name="amount" required>

                                <button type="submit" class="btn btn-boost" id="boostSubmitBtn" disabled>
                                    <i class="fas fa-rocket"></i>
                                    Proceed to Payment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sales & Analytics -->
        <section class="sales-analytics">
            <div class="container">
                <div class="sales-layout">
                    <div class="sales-summary">
                        <h2>Registration & Ticketing</h2>
                        <div id="registrationTicketingContainer" class="registration-ticketing-container">
                            <div class="loading-events">
                                <div class="spinner"></div>
                                <p>Loading registration data...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Volunteer Management -->
        <section class="volunteer-management">
            <div class="container">
                <div class="section-header">
                    <h2>Volunteer Management</h2>
                </div>
                <div class="volunteer-layout">
                    <div class="volunteer-applications registration-event-section">
                        <div class="volunteer-list" id="volunteerApplicationsList">
                            <!-- Volunteer applications will be loaded here -->
                        </div>
                    </div>
                    <div class="volunteer-shifts registration-event-section">
                        <div class="volunteer-list" id="volunteerShiftsList">
                            <!-- Volunteer shifts will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Comments -->
        <section class="recent-comments">
            <div class="container">
                <div class="section-header">
                    <h2>Recent Comments on Your Events</h2>
                    <div class="comment-stats">
                        <span class="stat-item">
                            <span class="stat-number" id="totalComments">0</span>
                            <span class="stat-label">Total Comments</span>
                        </span>
                        <span class="stat-item">
                            <span class="stat-number" id="averageRating">0.0</span>
                            <span class="stat-label">Avg Rating</span>
                        </span>
                    </div>
                </div>
                <div class="comments-container" id="recentCommentsContainer">
                    <!-- Comments will be loaded here -->
                    <div class="loading-comments">
                        <div class="spinner"></div>
                        <p>Loading recent comments...</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeDeleteModal()">&times;</span>
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete this event? This action cannot be undone.</p>
            <div class="modal-buttons">
                <button class="confirm-delete" onclick="confirmDelete()">Yes, Delete</button>
                <button class="cancel-delete" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Boost Payment Modal -->
    <!-- Payment Modal - Hidden (using payment page instead) -->
    <div id="boostPaymentModal" class="modal" style="display: none !important;">
        <div class="modal-content boost-modal-content">
            <span class="close-button" onclick="closeBoostPaymentModal()">&times;</span>
            <h3><i class="fas fa-credit-card"></i> Complete Payment</h3>
            <div class="payment-summary">
                <div class="payment-detail">
                    <span>Event:</span>
                    <strong id="paymentEventName">-</strong>
                </div>
                <div class="payment-detail">
                    <span>Boost Duration:</span>
                    <strong id="paymentDuration">-</strong>
                </div>
                <div class="payment-detail total">
                    <span>Total Amount:</span>
                    <strong id="paymentAmount">-</strong>
                </div>
            </div>

            <form id="paymentForm" class="payment-form">
                <div class="form-group">
                    <label>Payment Method</label>
                    <div class="payment-methods">
                        <div class="payment-method-option" data-method="card">
                            <input type="radio" name="payment_method" id="cardPayment" value="card" checked>
                            <label for="cardPayment">
                                <i class="fas fa-credit-card"></i>
                                <span>Credit/Debit Card</span>
                            </label>
                        </div>
                        <div class="payment-method-option" data-method="bank">
                            <input type="radio" name="payment_method" id="bankPayment" value="bank_transfer">
                            <label for="bankPayment">
                                <i class="fas fa-university"></i>
                                <span>Bank Transfer</span>
                            </label>
                        </div>
                        <!-- <div class="payment-method-option" data-method="mobile">
                            <input type="radio" name="payment_method" id="mobilePayment" value="mobile_payment">
                            <label for="mobilePayment">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Mobile Payment</span>
                            </label>
                        </div> -->
                    </div>
                </div>

                <div id="cardPaymentFields" class="payment-fields">
                    <div class="form-group">
                        <label for="cardName">Cardholder Name</label>
                        <input type="text" id="cardName" class="form-control" placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label for="cardNumber">Card Number</label>
                        <input type="text" id="cardNumber" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiryDate">Expiry Date</label>
                            <input type="text" id="expiryDate" class="form-control" placeholder="MM/YY" maxlength="5">
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" class="form-control" placeholder="123" maxlength="4">
                        </div>
                    </div>
                </div>

                <div id="bankPaymentFields" class="payment-fields" style="display: none;">
                    <div class="bank-instructions">
                        <h4>Bank Transfer Details</h4>
                        <p><strong>Bank:</strong> Commercial Bank</p>
                        <p><strong>Account Name:</strong> UniPulse Events</p>
                        <p><strong>Account Number:</strong> 8001234567</p>
                        <p><strong>Branch:</strong> Colombo</p>
                        <div class="form-group">
                            <label for="transactionRef">Transaction Reference</label>
                            <input type="text" id="transactionRef" class="form-control" placeholder="Enter reference number">
                        </div>
                    </div>
                </div>

                <!-- <div id="mobilePaymentFields" class="payment-fields" style="display: none;">
                    <div class="form-group">
                        <label for="mobileNumber">Mobile Number</label>
                        <input type="text" id="mobileNumber" class="form-control" placeholder="+94 77 123 4567">
                    </div>
                    <div class="form-group">
                        <label for="mobileProvider">Provider</label>
                        <select id="mobileProvider" class="form-control">
                            <option value="">Select Provider</option>
                            <option value="dialog">Dialog</option>
                            <option value="mobitel">Mobitel</option>
                            <option value="hutch">Hutch</option>
                            <option value="airtel">Airtel</option>
                        </select>
                    </div>
                </div> -->

                <div class="modal-buttons">
                    <button type="submit" class="btn btn-primary btn-pay">
                        <i class="fas fa-lock"></i> Pay Now
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeBoostPaymentModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/unipulse/public/assets/js/Publisher/dashboard-app.js"></script>
</body>

</html>