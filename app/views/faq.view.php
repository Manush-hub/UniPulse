<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UniPulse FAQ - Frequently Asked Questions about event management, registration, tickets, and more.">
    <title>FAQ - UniPulse Event Management System</title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/faq-style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="container">
            <section class="hero">
                <h1>Frequently Asked Questions</h1>
                <p class="subtitle">Find quick answers to common questions about UniPulse</p>
            </section>

            <div class="category-tabs">
                <button class="category-tab active" data-category="general">General</button>
                <button class="category-tab" data-category="account">Account</button>
                <button class="category-tab" data-category="events">Events</button>
                <button class="category-tab" data-category="tickets">Tickets</button>
                <button class="category-tab" data-category="technical">Technical</button>
                <button class="category-tab" data-category="payment">Payment</button>
            </div>

            <div class="faq-content">
                <div class="faq-category active" data-category="general">
                    <h2 class="faq-category-title">General Questions</h2>

                    <div class="faq-item">
                        <div class="faq-question"><span>What is UniPulse?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">UniPulse is a comprehensive event management system designed specifically for universities. It allows students to discover and register for campus events, helps event organizers manage their events, and enables sponsors to connect with event opportunities.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><span>Who can use UniPulse?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">UniPulse is available to students, faculty, staff, and external users. Different user types have different capabilities including event registration, publishing, sponsorship, and platform management.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><span>Is UniPulse free to use?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">UniPulse is free to browse and register for most campus events. Some events may include ticket fees set by organizers.</div>
                    </div>
                </div>

                <div class="faq-category" data-category="account">
                    <h2 class="faq-category-title">Account & Profile</h2>

                    <div class="faq-item">
                        <div class="faq-question"><span>How do I create an account?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Click Sign Up, choose your user type, and complete your registration details. University users can register with institutional details while public users can register with general information.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><span>How do I reset my password?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Click Forgot Password on the login page, enter your email, and click Send Reset Link. Check your email for the reset link, then follow the instructions to set a new password.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><span>How do I update profile information?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Go to your profile page from the dashboard and use the edit options to update your details, then save your changes.</div>
                    </div>
                </div>

                <div class="faq-category" data-category="events">
                    <h2 class="faq-category-title">Events & Registration</h2>

                    <div class="faq-item">
                        <div class="faq-question"><span>How do I find events?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Use the Find Events page to browse, filter, and search for events by category, date, and type.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><span>How do I register for an event?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Open an event and click Register. Complete any required details and payment (if applicable), then check your confirmation.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><span>Can I cancel registration?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Yes, cancellation depends on organizer policy. Check event details for deadline and refund terms.</div>
                    </div>
                </div>

                <div class="faq-category" data-category="tickets">
                    <h2 class="faq-category-title">Tickets & Refunds</h2>

                    <div class="faq-item">
                        <div class="faq-question"><span>How do I access my ticket?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Go to your event registrations and open the event ticket. You can show the digital ticket at entry.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><span>What is the refund policy?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Refund eligibility and timing are set by each event organizer and shown on the event page.</div>
                    </div>
                </div>

                <div class="faq-category" data-category="technical">
                    <h2 class="faq-category-title">Technical Support</h2>

                    <div class="faq-item">
                        <div class="faq-question"><span>The website is slow. What can I do?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Try refreshing, clearing cache, and using an updated browser. If the issue persists, contact support.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><span>I’m not receiving emails. What should I check?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Check spam/junk folders and verify your account email address is correct. Then try again.</div>
                    </div>
                </div>

                <div class="faq-category" data-category="payment">
                    <h2 class="faq-category-title">Payment & Billing</h2>

                    <div class="faq-item">
                        <div class="faq-question"><span>What payment methods are accepted?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Accepted methods depend on the configured gateway and event setup. You’ll see available options during checkout.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><span>Why is my payment failing?</span><div class="faq-toggle">▼</div></div>
                        <div class="faq-answer">Payment may fail due to insufficient funds, invalid details, bank restrictions, or temporary gateway issues.</div>
                    </div>
                </div>

                <div class="info-box">
                    <h3>Didn’t find your answer?</h3>
                    <p>If you couldn’t find the answer to your question, contact support through the contact page and we’ll help you quickly.</p>
                </div>
            </div>

            <section class="contact-cta">
                <h2>Still Have Questions?</h2>
                <p>Our support team is ready to help. Contact us anytime and we’ll get back to you as soon as possible.</p>
                <a href="/unipulse/public/contact" class="contact-cta-btn">Contact Support</a>
            </section>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script src="/unipulse/public/assets/js/faq.js"></script>
</body>
</html>


