<?php
// Help Center Page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UniPulse Help Center - Find answers to your questions about event management, ticketing, registration, sponsorships and more.">
    <title>Help Center - UniPulse Event Management System</title>
    <link rel="stylesheet" href="/UniPulse/public/assets/css/help-center-style.css">
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/Components/role_header.php'; ?>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <h1>Help Center</h1>
            <p class="subtitle">Find answers to your questions about UniPulse</p>
        </div>

        <!-- Category Grid -->
        <section>
            <h2>Browse by Category</h2>
            <div class="category-grid">
                <div class="category-card" onclick="document.getElementById('getting-started').scrollIntoView({behavior: 'smooth'})">
                    <span class="category-icon">🚀</span>
                    <h3>Getting Started</h3>
                    <p>Learn the basics of creating an account and getting started with UniPulse.</p>
                    <a href="#getting-started" class="category-link">View Articles →</a>
                </div>

                <div class="category-card" onclick="document.getElementById('events').scrollIntoView({behavior: 'smooth'})">
                    <span class="category-icon">🎪</span>
                    <h3>Managing Events</h3>
                    <p>Create, edit, and manage your events on the UniPulse platform.</p>
                    <a href="#events" class="category-link">View Articles →</a>
                </div>

                <div class="category-card" onclick="document.getElementById('tickets').scrollIntoView({behavior: 'smooth'})">
                    <span class="category-icon">🎫</span>
                    <h3>Tickets & Registration</h3>
                    <p>Everything you need to know about tickets and event registration.</p>
                    <a href="#tickets" class="category-link">View Articles →</a>
                </div>

                <div class="category-card" onclick="document.getElementById('account').scrollIntoView({behavior: 'smooth'})">
                    <span class="category-icon">👤</span>
                    <h3>Account & Profile</h3>
                    <p>Manage your account settings, profile, and preferences.</p>
                    <a href="#account" class="category-link">View Articles →</a>
                </div>

                <div class="category-card" onclick="document.getElementById('sponsorships').scrollIntoView({behavior: 'smooth'})">
                    <span class="category-icon">💼</span>
                    <h3>Sponsorships</h3>
                    <p>How to request and manage sponsorships for your events.</p>
                    <a href="#sponsorships" class="category-link">View Articles →</a>
                </div>

                <div class="category-card" onclick="document.getElementById('troubleshooting').scrollIntoView({behavior: 'smooth'})">
                    <span class="category-icon">🔧</span>
                    <h3>Troubleshooting</h3>
                    <p>Common issues and solutions to help you resolve problems.</p>
                    <a href="#troubleshooting" class="category-link">View Articles →</a>
                </div>
            </div>
        </section>

        <!-- Getting Started Section -->
        <section id="getting-started">
            <h2>Getting Started</h2>
            <div class="faq-container">
                <div class="faq-item active">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I create an account on UniPulse?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>To create an account on UniPulse, click the "Register" button on the home page. Enter your email address, create a strong password, and fill in your basic information. University students can use their university email for instant verification. External users will receive a verification email to confirm their account.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>What is the difference between user types?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>UniPulse has several user types: Students can browse and register for events, Event Organizers can create and manage events, Sponsors can view and fulfill sponsorship opportunities, and Administrators manage the platform. Your user type determines what features you can access.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I reset my password?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Click "Forgot Password" on the login page. Enter your email address and click "Send Reset Link". Check your email for a password reset link (it may take a few minutes). Click the link and follow the instructions to create a new password.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>Is my personal information safe on UniPulse?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, UniPulse uses enterprise-grade encryption and security measures to protect your personal information. We comply with GDPR, CCPA, and other privacy regulations. Your data is never sold to third parties. For more details, please review our Privacy Policy.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Events Section -->
        <section id="events">
            <h2>Managing Events</h2>
            <div class="faq-container">
                <div class="faq-item active">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I create a new event?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Log in to your account and click "Create Event". Fill in the event details including title, description, date, time, location, and event category. Upload event images and set ticket information. Review your event details and click "Publish" to make it live on the platform.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>Can I edit my event after publishing?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, you can edit most event details after publishing. Go to your event dashboard, click "Edit Event", and make your changes. Some fields like the event date can only be modified if there are no active registrations. Save your changes and they will be updated immediately.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I cancel my event?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Go to your event dashboard and click "Cancel Event". You'll be prompted to provide a cancellation reason that will be shared with registered attendees. Ticket refunds will be automatically processed according to your refund policy. An automated email will be sent to all registered attendees.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>What information should I include in my event description?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>A good event description should include: what the event is about, why attendees should come, what to expect, any prerequisites or requirements, what to bring, parking information, and contact details for questions. Be detailed and engaging to encourage registrations.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tickets & Registration Section -->
        <section id="tickets">
            <h2>Tickets & Registration</h2>
            <div class="faq-container">
                <div class="faq-item active">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I register for an event?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Find the event you want to attend, click "Register" or "Buy Ticket". Follow the registration form (provide your name, email, and any other required information). Choose your ticket type if multiple options are available. Complete payment if required and confirm your registration. You'll receive a confirmation email with your ticket details.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>Can I set different ticket prices for different attendee types?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, you can create multiple ticket types with different prices. For example, you can have "Student", "General", and "VIP" tickets with different price points. When setting up your event, specify the ticket types, quantities available, and pricing for each tier.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>What is your refund policy?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Event organizers can set their own refund policies. Some events offer full refunds up to a certain date, while others may have no refunds. Always check the event details for the specific refund policy. Processing times typically range from 3-7 business days.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I download my ticket?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>After registering, go to "My Events" in your account and find the event. Click "View Ticket" to see your digital ticket. You can take a screenshot or print it. Some events use QR codes that will be scanned at the door for entry.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Account & Profile Section -->
        <section id="account">
            <h2>Account & Profile</h2>
            <div class="faq-container">
                <div class="faq-item active">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I update my profile information?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Click your profile icon in the top-right corner and select "Profile Settings". Edit your name, email, phone number, and other information. Upload or change your profile picture. Click "Save Changes" to update your profile.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I change my notification preferences?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Go to Settings > Notifications. You can control email notifications, in-app notifications, and SMS alerts. Choose which types of events and announcements you want to receive notifications about. Your preferences will be saved automatically.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I delete my account?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Go to Settings > Account > Delete Account. Note that deleting your account will remove all personal data (after the retention period) and cancel your event registrations. This action cannot be undone. We'll send you a confirmation email before proceeding.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I save my favorite events?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>On any event page, click the heart icon to save it to your favorites. You can view all saved events in "My Events" > "Favorites". This is a great way to remember events you're interested in but aren't ready to register for yet.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sponsorships Section -->
        <section id="sponsorships">
            <h2>Sponsorships</h2>
            <div class="faq-container">
                <div class="faq-item active">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I request a sponsor for my event?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>When creating or editing your event, there's a "Sponsorship" section. Specify the sponsorship opportunities you're offering (monetary, in-kind, etc.) and benefits for sponsors. Click "Request Sponsor" to send invitations to specific companies or make your request public for all sponsors to see.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>What should I include in a sponsorship request?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Your sponsorship request should include: event details, expected attendance, sponsorship levels available, specific benefits for each level, what you're looking for (monetary amount or in-kind contribution), timeline, and contact information. The more detailed, the more likely sponsors will respond.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How do I accept or decline a sponsorship offer?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>When a sponsor responds to your request, you'll receive a notification. Go to your event dashboard and review the sponsorship offer. Click "Accept" to confirm the sponsorship or "Decline" if you can't work together. The sponsor will be notified of your decision.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>How can I find sponsorship opportunities?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>If you're a company interested in sponsoring events, go to "Sponsorship Opportunities" in the main menu. Browse available events, view their sponsorship needs, and submit an offer. Event organizers will review your offer and get back to you.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Troubleshooting Section -->
        <section id="troubleshooting">
            <h2>Troubleshooting</h2>
            <div class="faq-container">
                <div class="faq-item active">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>I can't log in to my account. What should I do?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>First, check that you're using the correct email address and password. If you forgot your password, click "Forgot Password" and follow the reset instructions. If you still can't log in, try clearing your browser cache and cookies. If the problem persists, contact our support team at support@unipulse.edu.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>Why is my payment failing?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Payment failures can be caused by: insufficient funds, incorrect card information, expired card, or security blocks from your bank. Double-check your card details. If your card is correct, contact your bank to verify they're not blocking the transaction. Contact our support team if the problem continues.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>I didn't receive my confirmation email. What do I do?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Check your spam or junk folder - the email may have been filtered. Add support@unipulse.edu to your contacts and try registering again. If you still don't receive the email, contact support. You may not be using the correct email address on your account.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="this.parentElement.classList.toggle('active')">
                        <span>The website is loading slowly. How can I fix this?</span>
                        <div class="faq-toggle">▼</div>
                    </div>
                    <div class="faq-answer">
                        <p>Try: clearing your browser cache, disabling browser extensions, updating your browser, checking your internet connection, or trying a different browser. If the site is still slow, contact us as it may be a temporary server issue. We'll work to resolve it quickly.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Links Section -->
        <section>
            <h2>Quick Links</h2>
            <div class="quick-links">
                <div class="quick-link-item">
                    <h4>📚 Documentation</h4>
                    <p>Access our detailed documentation for in-depth guides.</p>
                    <a href="#" class="quick-link-btn">View Docs →</a>
                </div>

                <div class="quick-link-item">
                    <h4>🎓 Tutorials</h4>
                    <p>Watch step-by-step video tutorials on key features.</p>
                    <a href="#" class="quick-link-btn">Watch Videos →</a>
                </div>

                <div class="quick-link-item">
                    <h4>🐛 Report a Bug</h4>
                    <p>Found a problem? Help us improve by reporting it.</p>
                    <a href="/UniPulse/contact" class="quick-link-btn">Report Bug →</a>
                </div>

                <div class="quick-link-item">
                    <h4>💡 Feature Request</h4>
                    <p>Suggest new features you'd like to see in UniPulse.</p>
                    <a href="/UniPulse/contact" class="quick-link-btn">Submit Idea →</a>
                </div>
            </div>
        </section>

        <!-- Contact CTA -->
        <section class="contact-cta">
            <h2>Still Need Help?</h2>
            <p>Can't find the answer you're looking for? Our support team is here to help. Contact us and we'll get back to you within 24 hours.</p>
            <a href="/UniPulse/contact" class="contact-cta-btn">Contact Support</a>
        </section>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/Components/footer.php'; ?>

        <script src="/unipulse/public/assets/js/help_center-app.js?v=<?= time() ?>"></script>
</body>
</html>
