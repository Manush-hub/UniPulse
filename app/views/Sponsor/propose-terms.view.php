<?php
// Sponsor Proposal Form View
$user = $data['user'] ?? [];
$event = $data['event'] ?? null;
$page_title = $data['page_title'] ?? 'Propose Sponsorship Terms';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="/unipulse/public/assets/css/sponsor/propose-terms-style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .event-info {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        .event-info h2 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 18px;
        }
        .event-info p {
            margin: 3px 0;
            color: #666;
            font-size: 14px;
        }
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .form-section h3 {
            color: #667eea;
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        .required::after {
            content: " *";
            color: #e74c3c;
        }
        input[type="text"],
        input[type="email"],
        input[type="phone"],
        input[type="tel"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
            max-width: 100%;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="phone"]:focus,
        input[type="tel"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            width: 100%;
            box-sizing: border-box;
        }
        .form-row.full {
            grid-template-columns: 1fr;
        }
        .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px 20px;
            margin-top: 10px;
        }
        .radio-group label {
            display: flex;
            align-items: center;
            margin-bottom: 0;
            margin-right: 10px;
        }
        .radio-group input[type="radio"] {
            width: auto;
            margin-right: 8px;
        }
        .hint {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        .type-section {
            display: none;
            margin-top: 20px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }
        .type-section.active {
            display: block;
        }
        .list-input {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .list-input input {
            flex: 1;
        }
        .list-input button {
            padding: 8px 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .list-input button:hover {
            background: #764ba2;
        }
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 30px;
        }
        button {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #ddd;
            color: #333;
        }
        .btn-secondary:hover {
            background: #ccc;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        .alert.error {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
            display: block;
        }
        .alert.success {
            background: #efe;
            color: #0a0;
            border: 1px solid #cfc;
            display: block;
        }
        @media (max-width: 768px) {
            .container {
                padding: 28px 18px;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/unipulse/public/sponsor/events?view=sponsor" style="color: #667eea; text-decoration: none; font-size: 14px;">← Back to Events</a>
        
        <h1><?php echo htmlspecialchars($page_title); ?></h1>
        
        <?php if ($event): ?>
        <div class="event-info">
            <h2><?php echo htmlspecialchars($event->title); ?></h2>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($event->event_date); ?></p>
            <p><strong>Venue:</strong> <?php echo htmlspecialchars($event->venue_name ?? 'Not specified'); ?></p>
            <p><strong>Expected Attendees:</strong> <?php echo htmlspecialchars($event->max_participants ?? 'Not specified'); ?></p>
        </div>
        <?php endif; ?>
        
        <div id="alertContainer"></div>
        
        <form id="proposalForm" method="POST">
            <!-- Basic Information -->
            <div class="form-section">
                <h3>Proposal Title & Description</h3>
                
                <div class="form-group">
                    <label for="title" class="required">Proposal Title</label>
                    <input type="text" id="title" name="title" placeholder="e.g., Gold Sponsor Package" required>
                    <div class="hint">Give your sponsorship proposal a clear, descriptive title</div>
                </div>
                
                <div class="form-group">
                    <label for="description" class="required">Proposal Description</label>
                    <textarea id="description" name="description" placeholder="Describe what you're proposing to sponsor and why..." required></textarea>
                    <div class="hint">Provide details about your sponsorship intentions and benefits</div>
                </div>
            </div>
            
            <!-- Proposal Type -->
            <div class="form-section">
                <h3>Type of Sponsorship</h3>
                
                <div class="form-group">
                    <label class="required">Select sponsorship type:</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="proposal_type" value="monetary" checked> Monetary
                        </label>
                        <label>
                            <input type="radio" name="proposal_type" value="in-kind"> In-Kind (Products/Services)
                        </label>
                        <label>
                            <input type="radio" name="proposal_type" value="service"> Service
                        </label>
                        <label>
                            <input type="radio" name="proposal_type" value="mixed"> Mixed
                        </label>
                    </div>
                </div>
                
                <!-- Monetary Section -->
                <div id="monetary-section" class="type-section active">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="monetary_amount">Sponsorship Amount</label>
                            <input type="number" id="monetary_amount" name="monetary_amount" placeholder="0.00" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select id="currency" name="currency">
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                                <option value="LKR">LKR (Rs.)</option>
                                <option value="CAD">CAD</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="payment_schedule">Payment Schedule</label>
                        <input type="text" id="payment_schedule" name="payment_schedule" placeholder="e.g., 50% upfront, 50% at event">
                    </div>
                </div>
                
                <!-- In-Kind Section -->
                <div id="in-kind-section" class="type-section">
                    <div class="form-group">
                        <label for="in_kind_items">Products/Services (one per line)</label>
                        <textarea id="in_kind_items" name="in_kind_items" placeholder="E.g.&#10;50 branded T-shirts&#10;Sound system rental&#10;Catering for 200 people"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="estimated_value">Estimated Total Value ($)</label>
                        <input type="number" id="estimated_value" name="estimated_value" placeholder="0.00" step="0.01">
                    </div>
                </div>
                
                <!-- Service Section -->
                <div id="service-section" class="type-section">
                    <div class="form-group">
                        <label for="service_description">Service Description</label>
                        <textarea id="service_description" name="service_description" placeholder="Describe the services you're offering..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="service_duration">Service Duration</label>
                        <input type="text" id="service_duration" name="service_duration" placeholder="e.g., 3 months, 6 sessions">
                    </div>
                </div>
            </div>
            
            <!-- Deliverables & Benefits -->
            <div class="form-section">
                <h3>Deliverables & Expectations</h3>
                
                <div class="form-group">
                    <label for="deliverables">What will you deliver? (one per line)</label>
                    <textarea id="deliverables" name="deliverables" placeholder="E.g.&#10;Company logo on event materials&#10;Booth space at event&#10;Social media mentions&#10;Speaking opportunity"></textarea>
                    <div class="hint">List all items and services you're committing to provide</div>
                </div>
                
                <div class="form-group">
                    <label for="expected_benefits">What benefits are you expecting? (one per line)</label>
                    <textarea id="expected_benefits" name="expected_benefits" placeholder="E.g.&#10;Logo placement on website and materials&#10;Social media shout-outs&#10;Booth at event&#10;Networking opportunities&#10;Recognition in press release"></textarea>
                    <div class="hint">What do you expect to receive in return for your sponsorship?</div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="form-section">
                <h3>Contact Information</h3>
                
                <div class="form-group">
                    <label for="contact_person" class="required">Contact Person Name</label>
                    <input type="text" id="contact_person" name="contact_person" required>
                </div>
                
                <div class="form-group">
                    <label for="contact_email" class="required">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email" required>
                </div>

                <div class="form-group">
                    <label for="contact_phone" class="required">Contact Phone</label>
                    <input type="tel" id="contact_phone" name="contact_phone" required>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="button-group">
                <button type="button" class="btn-secondary" onclick="window.history.back()">Cancel</button>
                <button type="submit" class="btn-primary" style="background: #1e3a8a;">Save as Draft</button>
                <button type="button" class="btn-primary" id="submitBtn" style="background: #15803d;">Submit for Review</button>
            </div>
        </form>
    </div>
    
    <script>
        const form = document.getElementById('proposalForm');
        const proposalTypeRadios = document.querySelectorAll('input[name="proposal_type"]');
        const alertContainer = document.getElementById('alertContainer');
        const eventId = '<?php echo htmlspecialchars($event->id ?? ''); ?>';
        const sponsorId = '<?php echo htmlspecialchars($user['id'] ?? ''); ?>';
        
        // Handle proposal type changes
        proposalTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Hide all type sections
                document.querySelectorAll('.type-section').forEach(section => {
                    section.classList.remove('active');
                });
                
                // Show selected section
                const sectionId = this.value + '-section';
                const section = document.getElementById(sectionId);
                if (section) {
                    section.classList.add('active');
                }
            });
        });
        
        // Initial setup
        document.querySelector('input[name="proposal_type"]:checked').dispatchEvent(new Event('change'));
        
        // Handle form submission
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const data = {
                event_id: eventId,
                sponsor_id: sponsorId,
                ...Object.fromEntries(formData)
            };
            
            try {
                const response = await fetch('/unipulse/public/sponsor/events/proposeTerms/' + eventId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', 'Proposal saved as draft successfully!');
                    setTimeout(() => {
                        if (result.redirect) {
                            window.location.href = result.redirect;
                        }
                    }, 1500);
                } else {
                    showAlert('error', result.message || 'Failed to save proposal');
                }
            } catch (error) {
                showAlert('error', 'Error saving proposal: ' + error.message);
            }
        });
        
        // Handle submit for review
        document.getElementById('submitBtn').addEventListener('click', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            formData.append('submit_for_review', '1');
            
            try {
                const response = await fetch('/unipulse/public/sponsor/events/proposeTerms/' + eventId, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', 'Proposal submitted for review!');
                    setTimeout(() => {
                        window.location.href = '/unipulse/public/sponsor/events/myProposals';
                    }, 1500);
                } else {
                    showAlert('error', result.message || 'Failed to submit proposal');
                }
            } catch (error) {
                showAlert('error', 'Error submitting proposal: ' + error.message);
            }
        });
        
        function showAlert(type, message) {
            const alert = document.createElement('div');
            alert.className = 'alert ' + type;
            alert.textContent = message;
            alertContainer.innerHTML = '';
            alertContainer.appendChild(alert);
            
            if (type === 'success') {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 4000);
            }
        }
    </script>
</body>
</html>
