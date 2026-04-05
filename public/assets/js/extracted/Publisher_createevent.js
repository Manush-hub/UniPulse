console.log('=== Create Event Page Script Loading ===');
        console.log('Script started at:', new Date().toLocaleTimeString());

        // Dropdown scroll functionality for university and faculty selects
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded fired!');
            console.log('Form element:', document.getElementById('create-event'));
            console.log('Publish button:', document.querySelector('.publish-btn'));

            const categorySelect = document.querySelector('select[name="event_category"]');

            if (categorySelect) {
                categorySelect.addEventListener('focus', function() {
                    this.size = 5;
                });

                categorySelect.addEventListener('blur', function() {
                    this.size = 1;
                });

                categorySelect.addEventListener('change', function() {
                    this.size = 1;
                    this.blur();
                });
            }

            const fieldTypeSelect = document.getElementById('fieldType');
            if (fieldTypeSelect) {
                fieldTypeSelect.addEventListener('focus', function() {
                    this.size = 5;
                });

                fieldTypeSelect.addEventListener('blur', function() {
                    this.size = 1;
                });

                fieldTypeSelect.addEventListener('change', function() {
                    this.size = 1;
                    this.blur();
                });
            }

            // Handle visibility option selection styling
            const visibilityOptions = document.querySelectorAll('.visibility-option');
            const visibilityRadios = document.querySelectorAll('input[name="event_visibility"]');

            visibilityRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Reset all options
                    visibilityOptions.forEach(option => {
                        option.style.borderColor = '#e5e7eb';
                        option.style.background = 'transparent';
                    });

                    // Highlight selected option
                    if (this.checked) {
                        const parent = this.closest('.visibility-option');
                        parent.style.borderColor = '#3b82f6';
                        parent.style.background = '#eff6ff';
                    }
                });
            });

            // Click on option div to select radio
            visibilityOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    // Allow clicking anywhere in the option box except directly on the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio && e.target !== radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                });
            });

            // Set initial selected state
            const checkedRadio = document.querySelector('input[name="event_visibility"]:checked');
            if (checkedRadio) {
                const parent = checkedRadio.closest('.visibility-option');
                parent.style.borderColor = '#3b82f6';
                parent.style.background = '#eff6ff';
            }
        });

        // Function to show success message
        function showSuccessMessage(message) {
            // Remove any existing messages
            const existingMessage = document.querySelector('.success-message, .error-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            // Create success message element
            const successDiv = document.createElement('div');
            successDiv.className = 'success-message';
            successDiv.style.cssText = `
            background: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 16px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            animation: slideDown 0.3s ease-out;
        `;
            successDiv.innerHTML = `
            <strong>✓ ${message}</strong>
            <button onclick="this.parentElement.remove()" style="
                background: none; 
                border: none; 
                color: white; 
                float: right; 
                cursor: pointer; 
                font-size: 18px;
                margin-top: -2px;
            ">×</button>
        `;

            // Insert at the top of the form
            const form = document.getElementById('create-event');
            form.insertBefore(successDiv, form.firstChild);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (successDiv.parentElement) {
                    successDiv.remove();
                }
            }, 5000);
        }

        // Function to show error message
        function showErrorMessage(message, errors = null) {
            // Remove any existing messages
            const existingMessage = document.querySelector('.success-message, .error-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            // Create error message element
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.style.cssText = `
            background: #f44336;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            animation: slideDown 0.3s ease-out;
        `;

            let errorContent = `<strong>✗ ${message}</strong>`;

            if (errors && typeof errors === 'object') {
                errorContent += '<ul style="margin: 10px 0 0 20px; text-align: left;">';
                for (const [field, errorMsg] of Object.entries(errors)) {
                    errorContent += `<li>${errorMsg}</li>`;
                }
                errorContent += '</ul>';
            }

            errorContent += `
            <button onclick="this.parentElement.remove()" style="
                background: none; 
                border: none; 
                color: white; 
                float: right; 
                cursor: pointer; 
                font-size: 18px;
                margin-top: -2px;
                position: absolute;
                right: 15px;
                top: 15px;
            ">×</button>
        `;

            errorDiv.innerHTML = errorContent;

            // Insert at the top of the form
            const form = document.getElementById('create-event');
            form.insertBefore(errorDiv, form.firstChild);

            // Scroll to top to show error
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Form Validation Functions
        function validateForm() {
            try {
                console.log('validateForm() called');
                const errors = [];

                // Validate Event Name
                const eventNameInput = document.querySelector('input[name="event_name"]');
                const eventName = eventNameInput ? eventNameInput.value.trim() : '';
                console.log('Event name:', eventName);
                if (!eventName) {
                    errors.push('Event name is required');
                } else if (eventName.length < 3) {
                    errors.push('Event name must be at least 3 characters long');
                } else if (eventName.length > 200) {
                    errors.push('Event name must be less than 200 characters');
                }

                // Validate Event Description
                const eventDescriptionInput = document.querySelector('textarea[name="event_description"]');
                const eventDescription = eventDescriptionInput ? eventDescriptionInput.value.trim() : '';
                console.log('Event description length:', eventDescription.length);
                if (!eventDescription) {
                    errors.push('Event description is required');
                } else if (eventDescription.length < 10) {
                    errors.push('Event description must be at least 10 characters long');
                } else if (eventDescription.length > 5000) {
                    errors.push('Event description must be less than 5000 characters');
                }

                // Validate Category
                const categorySelect = document.querySelector('select[name="event_category"]');
                const category = categorySelect ? categorySelect.value : '';
                console.log('Category:', category);
                if (!category) {
                    errors.push('Event category is required');
                }

                // Validate Cover Image
                const coverImageInput = document.querySelector('input[name="cover_image"]');
                console.log('Cover image input found:', !!coverImageInput);
                if (!coverImageInput || !coverImageInput.files || coverImageInput.files.length === 0) {
                    errors.push('Event cover image is required');
                } else {
                    const file = coverImageInput.files[0];
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    const maxSize = 5 * 1024 * 1024; // 5MB

                    console.log('Cover image type:', file.type, 'size:', file.size);

                    if (!allowedTypes.includes(file.type)) {
                        errors.push('Cover image must be JPEG, PNG, GIF, or WebP');
                    }

                    if (file.size > maxSize) {
                        errors.push('Cover image size must not exceed 5MB');
                    }
                }

                // Validate Event Date
                const eventDateInput = document.querySelector('input[name="event_date"]');
                const eventDate = eventDateInput ? eventDateInput.value : '';
                if (!eventDate) {
                    errors.push('Event date is required');
                } else {
                    const selectedDate = new Date(eventDate);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate < today) {
                        errors.push('Event date cannot be in the past');
                    }

                    // Check if date is too far in the future (e.g., more than 2 years)
                    const twoYearsFromNow = new Date();
                    twoYearsFromNow.setFullYear(twoYearsFromNow.getFullYear() + 2);
                    if (selectedDate > twoYearsFromNow) {
                        errors.push('Event date cannot be more than 2 years in the future');
                    }
                }

                // Validate Event Time
                const eventTimeInput = document.querySelector('input[name="event_time"]');
                const eventTime = eventTimeInput ? eventTimeInput.value : '';
                if (!eventTime) {
                    errors.push('Event time is required');
                } else if (eventDate) {
                    // If event is today, check if time is in the future
                    const selectedDate = new Date(eventDate);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate.getTime() === today.getTime()) {
                        const [hours, minutes] = eventTime.split(':');
                        const eventDateTime = new Date();
                        eventDateTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);

                        if (eventDateTime < new Date()) {
                            errors.push('Event time cannot be in the past for today\'s events');
                        }
                    }
                }

                // Validate Location
                const locationType = document.querySelector('input[name="location-type"]:checked')?.value || 'inside-university';
                const eventLocationInput = document.querySelector('input[name="event_location"]');
                const eventLocation = eventLocationInput ? eventLocationInput.value.trim() : '';

                if (locationType === 'inside-university') {
                    // Validate University
                    const universityInput = document.querySelector('input[name="selected_university"]');
                    const university = universityInput ? universityInput.value : '';
                    if (!university) {
                        errors.push('University is required for inside university events');
                    }

                    // Validate Faculty/Department
                    const facultyInput = document.querySelector('input[name="faculty_department"]');
                    const faculty = facultyInput ? facultyInput.value : '';
                    if (!faculty) {
                        errors.push('Faculty/Department is required for inside university events');
                    }

                    // Validate Event Location
                    if (!eventLocation) {
                        errors.push('Event location is required (e.g., Main Auditorium, Hall A)');
                    }
                } else {
                    const venueNameInput = document.querySelector('input[name="venue_name"]');
                    const cityInput = document.querySelector('input[name="city"]');
                    const venueName = venueNameInput ? venueNameInput.value.trim() : '';
                    const city = cityInput ? cityInput.value.trim() : '';

                    if (!venueName) {
                        errors.push('Venue name is required for outside university events');
                    }
                    if (!city) {
                        errors.push('City is required for outside university events');
                    }
                }

                // Validate Registration/Sale Dates based on ticket type
                const ticketType = document.querySelector('input[name="ticketType"]:checked')?.value || 'free-all';

                if (ticketType === 'free-all') {
                    // For free-all, registration period is optional
                    // Only validate if any registration date field is filled
                    const regStartDate = document.querySelector('input[name="registration_start_date"]')?.value;
                    const regEndDate = document.querySelector('input[name="registration_end_date"]')?.value;

                    if (regStartDate || regEndDate) {
                        // If any field is filled, validate the registration period
                        validateRegistrationPeriod(errors, eventDate, '#freeAllDetails');
                    }
                } else if (ticketType === 'paid-all') {
                    // Check if paid-all details section is visible
                    const paidAllDetails = document.getElementById('paidAllDetails');
                    if (!paidAllDetails || !paidAllDetails.classList.contains('hidden')) {
                        // Validate ticket types
                        validateTicketTypes(errors, '#ticketTypesList');

                        // Require sale period
                        const saleStartDate = document.querySelector('input[name="sale_start_date"]');
                        const saleStartTime = document.querySelector('input[name="sale_start_time"]');
                        const saleEndDate = document.querySelector('input[name="sale_end_date"]');
                        const saleEndTime = document.querySelector('input[name="sale_end_time"]');

                        if (!saleStartDate || !saleStartDate.value) {
                            errors.push('Sale start date is required for paid events');
                        }
                        if (!saleStartTime || !saleStartTime.value) {
                            errors.push('Sale start time is required for paid events');
                        }
                        if (!saleEndDate || !saleEndDate.value) {
                            errors.push('Sale end date is required for paid events');
                        }
                        if (!saleEndTime || !saleEndTime.value) {
                            errors.push('Sale end time is required for paid events');
                        }

                        validateSalePeriod(errors, eventDate, '#paidAllDetails');
                    }
                } else if (ticketType === 'mixed') {
                    // Check if mixed details section is visible
                    const mixedDetails = document.getElementById('mixedDetails');
                    if (!mixedDetails || !mixedDetails.classList.contains('hidden')) {
                        // Registration period for university students is optional
                        validateRegistrationPeriod(errors, eventDate, '#mixedDetails');

                        // Validate sale period for outside users - REQUIRED
                        const saleStartDate = document.querySelector('input[name="mixed_sale_start_date"]');
                        const saleStartTime = document.querySelector('input[name="mixed_sale_start_time"]');
                        const saleEndDate = document.querySelector('input[name="mixed_sale_end_date"]');
                        const saleEndTime = document.querySelector('input[name="mixed_sale_end_time"]');

                        if (!saleStartDate || !saleStartDate.value) {
                            errors.push('Sale start date is required for outside users');
                        }
                        if (!saleStartTime || !saleStartTime.value) {
                            errors.push('Sale start time is required for outside users');
                        }
                        if (!saleEndDate || !saleEndDate.value) {
                            errors.push('Sale end date is required for outside users');
                        }
                        if (!saleEndTime || !saleEndTime.value) {
                            errors.push('Sale end time is required for outside users');
                        }

                        validateSalePeriod(errors, eventDate, '#mixedDetails');
                        validateTicketTypes(errors, '#mixedTicketTypesList');
                    }
                }

                // Validate Volunteers
                const volunteerToggle = document.getElementById('volunteerToggle');
                const needsVolunteers = volunteerToggle ? volunteerToggle.checked : false;
                console.log('Volunteer validation - needsVolunteers:', needsVolunteers);

                if (needsVolunteers) {
                    const volunteersNeededInput = document.querySelector('input[name="volunteers_needed"]');
                    const volunteersNeeded = volunteersNeededInput ? volunteersNeededInput.value : '';
                    console.log('volunteersNeeded:', volunteersNeeded);

                    if (!volunteersNeeded) {
                        errors.push('Number of volunteers needed is required');
                    } else if (parseInt(volunteersNeeded) < 1) {
                        errors.push('Number of volunteers must be at least 1');
                    } else if (parseInt(volunteersNeeded) > 1000) {
                        errors.push('Number of volunteers cannot exceed 1,000');
                    }

                    // Check if at least one volunteer source is selected
                    const volunteerSources = document.querySelectorAll('input[name="volunteer-source[]"]:checked');
                    console.log('volunteerSources found:', volunteerSources.length);
                    volunteerSources.forEach((source, index) => {
                        console.log(`Source ${index}:`, source.value, source.checked);
                    });

                    if (volunteerSources.length === 0) {
                        errors.push('Please select at least one volunteer source');
                    }
                }

                // Show errors if any
                if (errors.length > 0) {
                    const errorObj = {};
                    errors.forEach((error, index) => {
                        errorObj[`error${index + 1}`] = error;
                    });
                    showErrorMessage('Please fix the following validation errors:', errorObj);
                    return false;
                }

                return true;

            } catch (error) {
                console.error('Validation error:', error);
                showErrorMessage('An error occurred during validation', {
                    'error': error.message
                });
                return false;
            }
        }

        function validateRegistrationPeriod(errors, eventDate, containerSelector) {
            const container = document.querySelector(containerSelector);
            if (!container || container.classList.contains('hidden')) return;

            // Get date inputs properly
            const dateInputs = container.querySelectorAll('input[type="date"]');
            const startDate = dateInputs[0]?.value;
            const endDate = dateInputs[1]?.value;

            // Validate if only end date is provided without start date
            if (endDate && !startDate) {
                errors.push('Please provide a registration start date');
                return;
            }

            if (startDate && eventDate) {
                const regStart = new Date(startDate);
                const event = new Date(eventDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                // Registration start cannot be in the past
                if (regStart < today) {
                    errors.push('Registration start date cannot be in the past (must be today or later)');
                }

                // Registration start cannot be after event date
                if (regStart > event) {
                    errors.push('Registration start date cannot be after the event date');
                }
            }

            // Validate registration end date
            if (endDate && startDate) {
                const regEnd = new Date(endDate);
                const regStart = new Date(startDate);
                const event = new Date(eventDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                // End date must be after start date
                if (regEnd < regStart) {
                    errors.push('Registration end date must be after start date');
                }

                // End date cannot be after event date
                if (regEnd > event) {
                    errors.push('Registration period must close before or on the event date');
                }

                // End date cannot be in the past
                if (regEnd < today) {
                    errors.push('Registration end date cannot be in the past');
                }
            }

            // If both dates are provided, validate the complete period
            if (startDate && endDate && eventDate) {
                const regStart = new Date(startDate);
                const regEnd = new Date(endDate);
                const event = new Date(eventDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                // Registration period must be between today and event date
                if (regStart < today || regEnd > event) {
                    errors.push('Registration period must be between today and the event date');
                }
            }
        }

        function validateSalePeriod(errors, eventDate, containerSelector) {
            const container = document.querySelector(containerSelector);
            if (!container || container.classList.contains('hidden')) return;

            const saleDates = container.querySelectorAll('.sale-dates input[type="date"]');
            if (saleDates.length >= 2) {
                const startDate = saleDates[0].value;
                const endDate = saleDates[1].value;

                if (startDate && endDate) {
                    const saleStart = new Date(startDate);
                    const saleEnd = new Date(endDate);
                    const event = new Date(eventDate);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    // Sale start cannot be in the past
                    if (saleStart < today) {
                        errors.push('Ticket sale start date cannot be in the past (must be today or later)');
                    }

                    // Sale start must be before sale end
                    if (saleStart > saleEnd) {
                        errors.push('Ticket sale start date must be before end date');
                    }

                    // Sale end cannot be after event date
                    if (saleEnd > event) {
                        errors.push('Ticket sale must end before or on the event date');
                    }

                    // Complete validation: sale period between today and event
                    if (saleStart < today || saleEnd > event) {
                        errors.push('Ticket sale period must be between today and the event date');
                    }
                }
            }
        }

        function validateTicketTypes(errors, containerSelector) {
            const container = document.querySelector(containerSelector);
            if (!container) return;

            const ticketItems = container.querySelectorAll('.ticket-type-item');

            if (ticketItems.length === 0) {
                errors.push('At least one ticket type is required for paid events');
                return;
            }

            if (ticketItems.length > 10) {
                errors.push('You cannot add more than 10 ticket types');
                return;
            }

            ticketItems.forEach((item, index) => {
                const typeName = item.querySelector('.ticket-type-name')?.value.trim();
                const quantity = item.querySelector('.ticket-quantity')?.value;
                const price = item.querySelector('.ticket-price')?.value;

                if (!typeName) {
                    errors.push(`Ticket type ${index + 1}: Name is required`);
                }

                if (!quantity || parseInt(quantity) < 1) {
                    errors.push(`Ticket type ${index + 1}: Quantity must be at least 1`);
                } else if (parseInt(quantity) > 100000) {
                    errors.push(`Ticket type ${index + 1}: Quantity cannot exceed 100,000`);
                }

                if (!price || parseFloat(price) < 0) {
                    errors.push(`Ticket type ${index + 1}: Price must be 0 or greater`);
                } else if (parseFloat(price) > 1000000) {
                    errors.push(`Ticket type ${index + 1}: Price cannot exceed 1,000,000`);
                }

                // Validate discount if enabled
                const discountToggle = item.querySelector('.discount-toggle');
                if (discountToggle && discountToggle.checked) {
                    const discountPercent = item.querySelector('.discount-percent')?.value;
                    if (!discountPercent || parseFloat(discountPercent) < 0 || parseFloat(discountPercent) > 100) {
                        errors.push(`Ticket type ${index + 1}: Discount percentage must be between 0 and 100`);
                    }
                }
            });
        }

        // Real-time validation helpers
        function setupRealtimeValidation() {
            // Event Date validation
            const eventDateInput = document.querySelector('input[name="event_date"]');
            if (eventDateInput) {
                eventDateInput.addEventListener('change', function() {
                    const selectedDate = new Date(this.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate < today) {
                        this.style.borderColor = '#dc3545';
                        showTooltip(this, 'Event date cannot be in the past');
                    } else {
                        this.style.borderColor = '#10B981';
                        removeTooltip(this);
                    }

                    // Update max date for registration and sale periods
                    updateRegistrationAndSalePeriodLimits(this.value);
                });
            }

            // Event Time validation
            const eventTimeInput = document.querySelector('input[name="event_time"]');
            if (eventTimeInput && eventDateInput) {
                eventTimeInput.addEventListener('change', function() {
                    const eventDate = eventDateInput.value;
                    if (!eventDate) return;

                    const selectedDate = new Date(eventDate);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (selectedDate.getTime() === today.getTime()) {
                        const [hours, minutes] = this.value.split(':');
                        const eventDateTime = new Date();
                        eventDateTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);

                        if (eventDateTime < new Date()) {
                            this.style.borderColor = '#dc3545';
                            showTooltip(this, 'Event time cannot be in the past');
                        } else {
                            this.style.borderColor = '#10B981';
                            removeTooltip(this);
                        }
                    }
                });
            }

            // Max Participants validation
            const maxParticipantsInput = document.querySelector('input[name="max_participants"]');
            if (maxParticipantsInput) {
                maxParticipantsInput.addEventListener('input', function() {
                    const value = parseInt(this.value);
                    if (value < 1 || value > 100000) {
                        this.style.borderColor = '#dc3545';
                    } else {
                        this.style.borderColor = '#10B981';
                    }
                });
            }
        }

        // Update registration and sale period date limits based on event date
        function updateRegistrationAndSalePeriodLimits(eventDate) {
            if (!eventDate) return;

            const today = new Date().toISOString().split('T')[0];

            // Update all registration date inputs
            const registrationStartDates = document.querySelectorAll('.registration-start-date');
            const registrationEndDates = document.querySelectorAll('.registration-end-date');

            registrationStartDates.forEach(input => {
                input.setAttribute('min', today);
                input.setAttribute('max', eventDate);
            });

            registrationEndDates.forEach(input => {
                input.setAttribute('min', today);
                input.setAttribute('max', eventDate);
            });

            // Update all sale date inputs
            const saleStartDates = document.querySelectorAll('.sale-start-date');
            const saleEndDates = document.querySelectorAll('.sale-end-date');

            saleStartDates.forEach(input => {
                input.setAttribute('min', today);
                input.setAttribute('max', eventDate);
            });

            saleEndDates.forEach(input => {
                input.setAttribute('min', today);
                input.setAttribute('max', eventDate);
            });
        }

        function showTooltip(element, message) {
            removeTooltip(element);
            const tooltip = document.createElement('div');
            tooltip.className = 'validation-tooltip';
            tooltip.textContent = message;
            tooltip.style.cssText = `
            position: absolute;
            background: #dc3545;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            margin-top: 5px;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        `;
            element.parentElement.style.position = 'relative';
            element.parentElement.appendChild(tooltip);
        }

        function removeTooltip(element) {
            const tooltip = element.parentElement.querySelector('.validation-tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        }

        // Initialize real-time validation when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Setting up real-time validation...');
            setupRealtimeValidation();
        });

        // Collect and store all dynamic form data
        function collectAndStoreFormData() {
            try {
                console.log('collectAndStoreFormData() called');
                // Collect ticket types based on ticket type selected
                const ticketType = document.querySelector('input[name="ticketType"]:checked')?.value || 'free-all';
                let ticketTypes = [];

                if (ticketType === 'paid-all') {
                    // Collect from paid-all ticket types list
                    const ticketCards = document.querySelectorAll('#ticketTypesList .ticket-type-item');
                    ticketCards.forEach(card => {
                        const name = card.querySelector('.ticket-type-name')?.value || '';
                        const price = card.querySelector('.ticket-price')?.value || '0';
                        const quantity = card.querySelector('.ticket-quantity')?.value || '0';
                        const description = card.querySelector('textarea')?.value || '';
                        const discountPercent = card.querySelector('.discount-percent')?.value || '';
                        const discountedPrice = card.querySelector('.discounted-price')?.value || '';

                        if (name && price && quantity) {
                            const ticketData = {
                                name: name,
                                price: parseFloat(price),
                                quantity: parseInt(quantity),
                                description: description
                            };

                            // Add discount info if provided
                            if (discountPercent && discountedPrice) {
                                ticketData.discount_percent = parseFloat(discountPercent);
                                ticketData.discounted_price = parseFloat(discountedPrice);
                            }

                            ticketTypes.push(ticketData);
                        }
                    });
                } else if (ticketType === 'mixed') {
                    // Collect from mixed ticket types list
                    const ticketCards = document.querySelectorAll('#mixedTicketTypesList .ticket-type-item');
                    ticketCards.forEach(card => {
                        const name = card.querySelector('.ticket-type-name')?.value || '';
                        const price = card.querySelector('.ticket-price')?.value || '0';
                        const quantity = card.querySelector('.ticket-quantity')?.value || '0';
                        const description = card.querySelector('textarea')?.value || '';
                        const discountPercent = card.querySelector('.discount-percent')?.value || '';
                        const discountedPrice = card.querySelector('.discounted-price')?.value || '';

                        if (name && price && quantity) {
                            const ticketData = {
                                name: name,
                                price: parseFloat(price),
                                quantity: parseInt(quantity),
                                description: description
                            };

                            // Add discount info if provided
                            if (discountPercent && discountedPrice) {
                                ticketData.discount_percent = parseFloat(discountPercent);
                                ticketData.discounted_price = parseFloat(discountedPrice);
                            }

                            ticketTypes.push(ticketData);
                        }
                    });
                }

                // Store ticket types in hidden input
                document.getElementById('ticket_types_input').value = JSON.stringify(ticketTypes);

                // Collect schedule
                const scheduleItems = [];
                document.querySelectorAll('#scheduleList .schedule-item-card').forEach(item => {
                    const time = item.querySelector('[data-schedule-time]')?.textContent || '';
                    const activity = item.querySelector('[data-schedule-activity]')?.textContent || '';
                    if (time && activity) {
                        scheduleItems.push({
                            time,
                            activity
                        });
                    }
                });
                document.getElementById('schedule_input').value = JSON.stringify(scheduleItems);

                // Collect custom fields
                const customFields = [];
                document.querySelectorAll('#customFieldsList .custom-field-item').forEach(field => {
                    const label = field.querySelector('[data-field-label]')?.textContent || '';
                    const type = field.querySelector('[data-field-type]')?.textContent || '';
                    if (label && type) {
                        customFields.push({
                            label,
                            type
                        });
                    }
                });
                document.getElementById('custom_fields_input').value = JSON.stringify(customFields);

                // Collect volunteer positions
                const volunteerPositions = [];
                document.querySelectorAll('#volunteerPositionsList .position-item').forEach(position => {
                    const text = position.querySelector('span')?.textContent || position.textContent.replace('×', '').trim();
                    if (text) {
                        volunteerPositions.push(text);
                    }
                });
                document.getElementById('volunteer_positions_input').value = JSON.stringify(volunteerPositions);

                console.log('Collected data:', {
                    ticketTypes: ticketTypes,
                    schedule: scheduleItems,
                    customFields: customFields,
                    volunteerPositions: volunteerPositions
                });

            } catch (error) {
                console.error('Error collecting form data:', error);
                // Continue even if there's an error - some data collection might fail but form can still submit
            }
        }

        // Handle form submission - Setup after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Setting up form submission handler...');
            const createEventForm = document.getElementById('create-event');

            if (!createEventForm) {
                console.error('Form with id "create-event" not found!');
                return;
            } else {
                console.log('Form found, attaching submit event listener');
            }

            createEventForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submit event triggered!');

                try {
                    console.log('Form submission started...');

                    // Get ticket type
                    const ticketType = document.querySelector('input[name="ticketType"]:checked')?.value || 'free-all';
                    console.log('Ticket type:', ticketType);

                    // Create a hidden input for registration_limit if it doesn't exist
                    let registrationLimitInput = this.querySelector('input[name="registration_limit"]');
                    if (!registrationLimitInput) {
                        registrationLimitInput = document.createElement('input');
                        registrationLimitInput.type = 'hidden';
                        registrationLimitInput.name = 'registration_limit';
                        this.appendChild(registrationLimitInput);
                    }

                    // Set the value based on ticket type
                    if (ticketType === 'free-all') {
                        const freeLimit = document.querySelector('input[name="free_registration_limit"]')?.value;
                        registrationLimitInput.value = freeLimit || '';
                    } else if (ticketType === 'mixed') {
                        const mixedLimit = document.querySelector('input[name="mixed_registration_limit"]')?.value;
                        registrationLimitInput.value = mixedLimit || '';
                    } else {
                        registrationLimitInput.value = '';
                    }

                    // Collect all dynamic data before validation
                    console.log('Collecting form data...');
                    collectAndStoreFormData();

                    // Validate form before submission
                    console.log('Starting validation...');
                    if (!validateForm()) {
                        console.log('Validation failed');
                        return;
                    }
                    console.log('Validation passed');

                    const submitBtn = document.querySelector('.publish-btn');
                    const originalText = submitBtn.textContent;

                    // Show loading state
                    submitBtn.textContent = 'Creating Event...';
                    submitBtn.disabled = true;

                    // Get form data
                    const formData = new FormData(this);

                    // Ensure AJAX detection
                    formData.set('ajax', '1');

                    // Submit form
                    fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            console.log('Response headers:', response.headers.get('content-type'));

                            if (!response.ok && response.status !== 200) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }

                            // Check if response is JSON
                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                return response.text().then(text => {
                                    console.error('Non-JSON response received:', text);
                                    throw new Error('Server returned non-JSON response: ' + text.substring(0, 200));
                                });
                            }

                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Clear draft on successful submission
                                localStorage.removeItem('event_draft');
                                console.log('Draft cleared after successful submission');

                                // Show success message
                                showSuccessMessage('Event created successfully! Redirecting to events page...');

                                // Redirect to events page after a short delay
                                setTimeout(() => {
                                    if (data.redirect) {
                                        window.location.href = data.redirect;
                                    } else {
                                        window.location.href = '/unipulse/public/publisher/events';
                                    }
                                }, 1500); // 1.5 second delay to show success message
                            } else {
                                // Show error messages in styled message box
                                const errorMsg = data.message || 'Failed to create event. Please check the errors below.';
                                showErrorMessage(errorMsg, data.errors);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showErrorMessage('Network or server error occurred', {
                                'error': error.message
                            });
                        })
                        .finally(() => {
                            // Reset button
                            submitBtn.textContent = originalText;
                            submitBtn.disabled = false;
                        });

                } catch (error) {
                    console.error('Form submission error:', error);
                    showErrorMessage('An error occurred while processing the form', {
                        'error': error.message
                    });

                    // Reset button if it was modified
                    const submitBtn = document.querySelector('.publish-btn');
                    if (submitBtn) {
                        submitBtn.textContent = 'Publish Event';
                        submitBtn.disabled = false;
                    }
                }
            }); // End of createEventForm.addEventListener('submit')

        }); // End of DOMContentLoaded for form submission

        // AUTO-SAVE DRAFT FUNCTIONALITY
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Setting up auto-save functionality...');
            const DRAFT_KEY = 'event_draft';
            const form = document.getElementById('create-event');
            let saveTimeout;

            // Save draft to localStorage
            function saveDraft() {
                const formData = new FormData(form);
                const draft = {};

                // Save all form fields
                for (let [key, value] of formData.entries()) {
                    if (key !== 'cover_image') { // Skip file inputs
                        draft[key] = value;
                    }
                }

                // Save checkboxes separately (FormData doesn't include unchecked boxes)
                const checkboxes = form.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => {
                    draft[cb.name || cb.id] = cb.checked;
                });

                // Save hidden inputs (ticket_types, schedule, custom_fields, etc.)
                draft.ticket_types = document.getElementById('ticket_types_input')?.value || '';
                draft.schedule = document.getElementById('schedule_input')?.value || '';
                draft.custom_fields = document.getElementById('custom_fields_input')?.value || '';
                draft.volunteer_positions = document.getElementById('volunteer_positions_input')?.value || '';
                draft.sponsorship_packages = document.getElementById('sponsorship_packages_input')?.value || '';

                // Save timestamp
                draft.saved_at = new Date().toISOString();

                localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));
                console.log('Draft saved at', new Date().toLocaleTimeString());
            }

            // Load draft from localStorage
            function loadDraft() {
                const draftJson = localStorage.getItem(DRAFT_KEY);
                if (!draftJson) return;

                try {
                    const draft = JSON.parse(draftJson);

                    // Restore text inputs, textareas, and selects
                    Object.keys(draft).forEach(key => {
                        if (key === 'saved_at') return;

                        const field = form.querySelector(`[name="${key}"]`);
                        if (field) {
                            if (field.type === 'file') {
                                // Cannot restore file inputs programmatically - skip
                            } else if (field.type === 'checkbox') {
                                field.checked = draft[key] === true || draft[key] === '1';
                            } else if (field.type === 'radio') {
                                if (field.value === draft[key]) {
                                    field.checked = true;
                                }
                            } else {
                                field.value = draft[key];
                            }
                        }
                    });

                    // Restore checkboxes by ID
                    if (draft.volunteerToggle) {
                        const volToggle = document.getElementById('volunteerToggle');
                        if (volToggle) volToggle.checked = true;
                    }
                    if (draft.donationToggle) {
                        const donToggle = document.getElementById('donationToggle');
                        if (donToggle) donToggle.checked = true;
                    }
                    if (draft.sponsorshipToggle) {
                        const sponsorToggle = document.getElementById('sponsorshipToggle');
                        if (sponsorToggle) sponsorToggle.checked = true;
                    }

                    // Restore ticket types if exists
                    if (draft.ticket_types && draft.ticket_types !== '[]' && draft.ticket_types !== '') {
                        document.getElementById('ticket_types_input').value = draft.ticket_types;
                        // Trigger display of saved tickets
                        try {
                            const tickets = JSON.parse(draft.ticket_types);
                            // You may need to call a function to display these tickets
                        } catch (e) {
                            console.error('Error parsing saved tickets:', e);
                        }
                    }

                    // Restore sponsorship packages
                    if (draft.sponsorship_packages && draft.sponsorship_packages !== '[]' && draft.sponsorship_packages !== '') {
                        document.getElementById('sponsorship_packages_input').value = draft.sponsorship_packages;
                        if (typeof sponsorshipPackages !== 'undefined' && typeof displaySponsorshipPackages === 'function') {
                            try {
                                sponsorshipPackages = JSON.parse(draft.sponsorship_packages);
                                displaySponsorshipPackages();
                            } catch (e) {
                                console.error('Error restoring sponsorship packages:', e);
                            }
                        }
                    }

                    // Trigger toggle functions to show/hide sections
                    if (typeof toggleVolunteerDetails === 'function') toggleVolunteerDetails();
                    if (typeof toggleSponsorshipDetails === 'function') toggleSponsorshipDetails();

                    // Show notification
                    const savedDate = new Date(draft.saved_at);
                    console.log('Draft restored from', savedDate.toLocaleString());

                    // Show a subtle notification to user
                    const notification = document.createElement('div');
                    notification.style.cssText = 'position: fixed; top: 80px; right: 20px; background: #10B981; color: white; padding: 12px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999; animation: slideIn 0.3s ease-out;';
                    notification.innerHTML = `<i class="fas fa-check-circle"></i> Draft restored from ${savedDate.toLocaleTimeString()}`;
                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.style.animation = 'slideOut 0.3s ease-out';
                        setTimeout(() => notification.remove(), 300);
                    }, 3000);

                } catch (e) {
                    console.error('Error loading draft:', e);
                }
            }

            // Auto-save on input change (debounced)
            form.addEventListener('input', function(e) {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(saveDraft, 2000); // Save 2 seconds after last input
            });

            // Save on form field change
            form.addEventListener('change', function(e) {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(saveDraft, 500); // Save 0.5 seconds after change
            });

            // Clear draft on successful submission
            form.addEventListener('submit', function(e) {
                // Don't clear immediately - wait for success response
                // This is handled in the fetch success callback
            });

            // Load draft when page loads
            setTimeout(loadDraft, 500); // Small delay to ensure all elements are loaded

            // Show/hide clear draft button based on draft existence
            const clearDraftBtn = document.getElementById('clearDraftBtn');
            if (clearDraftBtn) {
                if (localStorage.getItem(DRAFT_KEY)) {
                    clearDraftBtn.style.display = 'inline-block';
                }

                clearDraftBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to clear the saved draft? This cannot be undone.')) {
                        localStorage.removeItem(DRAFT_KEY);
                        clearDraftBtn.style.display = 'none';

                        // Show notification
                        const notification = document.createElement('div');
                        notification.style.cssText = 'position: fixed; top: 80px; right: 20px; background: #EF4444; color: white; padding: 12px 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999; animation: slideIn 0.3s ease-out;';
                        notification.innerHTML = `<i class="fas fa-trash"></i> Draft cleared successfully`;
                        document.body.appendChild(notification);

                        setTimeout(() => {
                            notification.style.animation = 'slideOut 0.3s ease-out';
                            setTimeout(() => notification.remove(), 300);
                        }, 2000);

                        // Optionally reload the page to clear all fields
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                });
            }

        }); // End of DOMContentLoaded for auto-save

        // Load draft when page loads - REMOVED DUPLICATE
        // This is now handled in the auto-save DOMContentLoaded block above

        // Add CSS for animations
        const style = document.createElement('style');
        style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(400px); opacity: 0; }
        }
    `;
        document.head.appendChild(style);

        // Handle sponsorship proposal file selection
        function handleProposalFileSelect(event) {
            const file = event.target.files[0];
            const fileNameSpan = document.getElementById('proposalFileName');

            if (file) {
                // Check file size (10MB limit)
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if (file.size > maxSize) {
                    alert('File size exceeds 10MB limit. Please choose a smaller file.');
                    event.target.value = '';
                    fileNameSpan.textContent = 'Upload proposal document (PDF, DOC, PPT)';
                    return;
                }

                // Update file name display
                fileNameSpan.innerHTML = `<i class="fas fa-file-alt"></i> ${file.name}`;
            } else {
                fileNameSpan.textContent = 'Upload proposal document (PDF, DOC, PPT)';
            }
        }

        // Make function globally accessible
        window.handleProposalFileSelect = handleProposalFileSelect;

        // Auto-fill university and faculty when inside-university is selected
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown size listeners for university and faculty removed because they're now read-only text inputs
        });