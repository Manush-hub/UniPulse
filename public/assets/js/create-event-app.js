// Section toggle functionality
document.querySelectorAll('.section-header').forEach(header => {
    header.addEventListener('click', () => {
        const section = header.parentElement;
        section.classList.toggle('collapsed');
    });
});

// Sidebar navigation linked to sections
document.querySelectorAll('.sidebar-item[data-target]').forEach(item => {
    item.addEventListener('click', () => {
        // remove previous active
        document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
        item.classList.add('active');

        // scroll to section
        const targetId = item.getAttribute('data-target');
        const targetSection = document.getElementById(targetId);

        if (targetSection) {
            // Expand section if collapsed
            targetSection.classList.remove('collapsed');

            // Smooth scroll
            targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// COVER UPLOAD FUNCTIONALITY
const coverUploadArea = document.getElementById('coverUploadArea');
const coverFileInput = document.getElementById('coverFileInput');
const coverImageContainer = document.getElementById('coverImageContainer');

// Handle drag and drop events
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    coverUploadArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    coverUploadArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    coverUploadArea.addEventListener(eventName, unhighlight, false);
});

function highlight() {
    coverUploadArea.classList.add('active');
}

function unhighlight() {
    coverUploadArea.classList.remove('active');
}

// Handle file drop
coverUploadArea.addEventListener('drop', handleCoverDrop, false);

function handleCoverDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleCoverFiles(files);
}

// Handle file input change
coverFileInput.addEventListener('change', function () {
    handleCoverFiles(this.files);
});

// Process the uploaded files
function handleCoverFiles(files) {
    if (files.length > 0) {
        const file = files[0];
        if (file.type.match('image.*')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                // Remove any existing image
                const existingImg = coverImageContainer.querySelector('img');
                if (existingImg) {
                    existingImg.remove();
                }

                // Hide the placeholder
                const placeholder = coverImageContainer.querySelector('.cover-image-placeholder');
                placeholder.style.display = 'none';

                // Create and append the new image
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Event Cover';
                coverImageContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    }
}

// LOCATION TYPE TOGGLE FUNCTIONALITY
const locationTypeRadios = document.querySelectorAll('input[name="location-type"]');
const insideUniversityLocation = document.getElementById('insideUniversityLocation');
const outsideUniversityLocation = document.getElementById('outsideUniversityLocation');

function toggleLocationFields() {
    const selectedType = document.querySelector('input[name="location-type"]:checked').value;

    if (selectedType === 'inside-university') {
        insideUniversityLocation.classList.remove('hidden');
        outsideUniversityLocation.classList.add('hidden');
    } else {
        insideUniversityLocation.classList.add('hidden');
        outsideUniversityLocation.classList.remove('hidden');
    }
}

// Add event listeners to location type radio buttons
locationTypeRadios.forEach(radio => {
    radio.addEventListener('change', toggleLocationFields);
});

// Initialize with current selection
toggleLocationFields();

// VOLUNTEER TOGGLE FUNCTIONALITY
const volunteerToggle = document.getElementById('volunteerToggle');
const volunteerDetails = document.getElementById('volunteerDetails');

function toggleVolunteerDetails() {
    if (volunteerToggle.checked) {
        volunteerDetails.classList.remove('hidden');
    } else {
        volunteerDetails.classList.add('hidden');
    }
}

// Add event listener to volunteer toggle
volunteerToggle.addEventListener('change', toggleVolunteerDetails);

// Initialize volunteer details visibility
toggleVolunteerDetails();

// Remove old ticket functionality (lines 150-170)
// TICKET TYPE TOGGLE FUNCTIONALITY - REMOVE THIS SECTION

// CUSTOM FIELDS FUNCTIONALITY
const dynamicFields = document.getElementById("dynamicFields");
let fieldCounter = 0;

function addCustomField() {
    const label = document.getElementById("fieldLabel").value.trim();
    const type = document.getElementById("fieldType").value;
    const options = document.getElementById("fieldOptions").value.trim();

    if (!label) {
        alert("Please enter a field label!");
        return;
    }

    if (type === 'select' && !options) {
        alert("Please enter options for the dropdown field!");
        return;
    }

    // Remove the "no fields" message if it exists
    const noFieldsMessage = dynamicFields.querySelector('p');
    if (noFieldsMessage) {
        noFieldsMessage.remove();
    }

    fieldCounter++;
    const fieldWrapper = document.createElement("div");
    fieldWrapper.classList.add("field-item");
    fieldWrapper.setAttribute('data-field-id', fieldCounter);

    const fieldLabel = document.createElement("label");
    fieldLabel.classList.add("form-label");
    fieldLabel.textContent = label;

    let input;
    if (type === "select") {
        input = document.createElement("select");
        input.classList.add("form-select");

        // Add default option
        const defaultOption = document.createElement("option");
        defaultOption.value = "";
        defaultOption.textContent = `Select ${label}`;
        input.appendChild(defaultOption);

        // Add user-defined options
        options.split(",").forEach(opt => {
            const optionElement = document.createElement("option");
            optionElement.value = opt.trim();
            optionElement.textContent = opt.trim();
            input.appendChild(optionElement);
        });
    } else if (type === "textarea") {
        input = document.createElement("textarea");
        input.classList.add("form-textarea");
        input.placeholder = `Enter ${label}`;
    } else {
        input = document.createElement("input");
        input.type = type;
        input.classList.add("form-input");
        input.placeholder = `Enter ${label}`;
    }
    input.name = `custom_${fieldCounter}`;

    // Create remove button
    const removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.classList.add("remove-field-btn");
    removeBtn.innerHTML = "×";
    removeBtn.title = "Remove field";
    removeBtn.onclick = function () {
        fieldWrapper.remove();
        // Show "no fields" message if no fields remain
        if (dynamicFields.children.length === 0) {
            const noFieldsMsg = document.createElement('p');
            noFieldsMsg.style.cssText = 'color: #999; font-style: italic; text-align: center; padding: 20px;';
            noFieldsMsg.textContent = 'No custom fields added yet. Add fields above to see them here.';
            dynamicFields.appendChild(noFieldsMsg);
        }
    };

    fieldWrapper.appendChild(removeBtn);
    fieldWrapper.appendChild(fieldLabel);
    fieldWrapper.appendChild(input);
    dynamicFields.appendChild(fieldWrapper);

    // Clear the form inputs
    document.getElementById("fieldLabel").value = "";
    document.getElementById("fieldOptions").value = "";
    document.getElementById("fieldType").value = "text";
}

// Show/hide options field based on field type
document.getElementById("fieldType").addEventListener('change', function () {
    const optionsField = document.getElementById("fieldOptions").parentElement;
    if (this.value === 'select') {
        optionsField.style.display = 'block';
    } else {
        optionsField.style.display = 'none';
        document.getElementById("fieldOptions").value = '';
    }
});

// Initialize options field visibility
document.getElementById("fieldType").dispatchEvent(new Event('change'));

// Handle adding new volunteer positions
const addPositionBtn = document.getElementById('addPositionBtn');
const newOptionInput = document.getElementById('newOption');

if (addPositionBtn && newOptionInput) {
    addPositionBtn.addEventListener('click', function () {
        const newValue = newOptionInput.value.trim();

        if (newValue !== "") {
            // Get existing options from localStorage or empty array
            let options = JSON.parse(localStorage.getItem('customOptions')) || [];

            // Add new value
            options.push(newValue);

            // Save back to localStorage
            localStorage.setItem('customOptions', JSON.stringify(options));

            alert('Position "' + newValue + '" added successfully!');
            newOptionInput.value = "";
        } else {
            alert('Please enter a position name.');
        }
    });
}

// NEW TICKET FUNCTIONALITY
const freeAllRadio = document.getElementById("free-all");
const paidAllRadio = document.getElementById("paid-all");
const mixedRadio = document.getElementById("mixed");
const freeAllDetails = document.getElementById("freeAllDetails");
const paidAllDetails = document.getElementById("paidAllDetails");
const mixedDetails = document.getElementById("mixedDetails");

// Ticket type counters
let ticketTypeCounter = 1;
let mixedTicketTypeCounter = 1;

// Handle radio button logic
function updateTicketOptions() {
    // Reset all details sections
    freeAllDetails.classList.add("hidden");
    paidAllDetails.classList.add("hidden");
    mixedDetails.classList.add("hidden");

    // Show details based on selection
    if (freeAllRadio.checked) {
        freeAllDetails.classList.remove("hidden");
    } else if (paidAllRadio.checked) {
        paidAllDetails.classList.remove("hidden");
    } else if (mixedRadio.checked) {
        mixedDetails.classList.remove("hidden");
    }
}

// Add event listeners
freeAllRadio.addEventListener("change", updateTicketOptions);
paidAllRadio.addEventListener("change", updateTicketOptions);
mixedRadio.addEventListener("change", updateTicketOptions);

// Setup discount functionality for a ticket type
function setupTicketDiscount(ticketElement) {
    const discountToggle = ticketElement.querySelector('.discount-toggle');
    const discountDetails = ticketElement.querySelector('.discount-details');
    const discountPercent = ticketElement.querySelector('.discount-percent');
    const discountedPrice = ticketElement.querySelector('.discounted-price');
    const ticketPrice = ticketElement.querySelector('.ticket-price');

    // Toggle discount details
    discountToggle.addEventListener('change', function () {
        if (this.checked) {
            discountDetails.classList.remove('hidden');
            calculateDiscount();
        } else {
            discountDetails.classList.add('hidden');
            discountedPrice.value = '';
        }
    });

    // Calculate discount when values change
    function calculateDiscount() {
        const price = parseFloat(ticketPrice.value) || 0;
        const percent = parseFloat(discountPercent.value) || 0;
        const discounted = price - (price * percent / 100);
        discountedPrice.value = discounted > 0 ? discounted.toFixed(2) : 0;
    }

    discountPercent.addEventListener('input', calculateDiscount);
    ticketPrice.addEventListener('input', calculateDiscount);
}

// Add ticket type functionality
function addTicketType(containerId, counterVar) {
    const container = document.getElementById(containerId);
    const newTicketType = document.createElement("div");
    newTicketType.classList.add("ticket-type-item");

    const typeId = counterVar++;

    newTicketType.innerHTML = `
        <div class="ticket-type-header">
            <input type="text" class="form-input ticket-type-name" placeholder="Ticket Type Name (e.g., VIP, 1st Class)" style="max-width: 250px; margin-bottom: 0;">
            <button type="button" class="remove-ticket-type-btn">×</button>
        </div>
        <div class="ticket-type-details">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label class="form-label">Quantity Available</label>
                    <input type="number" class="form-input ticket-quantity" placeholder="Enter quantity" min="1" value="100">
                </div>
                <div>
                    <label class="form-label">Price (USD)</label>
                    <input type="number" class="form-input ticket-price" placeholder="Enter price" min="0" step="0.01" value="10">
                </div>
            </div>
            
            <!-- Discount Section for Ticket Type -->
            <div class="ticket-discount-section">
                <div class="toggle-container">
                    <span><i class="fas fa-tag" style="color: #FF6B35; margin-right: 8px;"></i>Discount for University Students?</span>
                    <label class="switch">
                        <input type="checkbox" class="discount-toggle">
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="discount-details hidden">
                    <div class="info-note" style="background: #f0f9ff; border-color: #0ea5e9; margin-bottom: 15px;">
                        <i class="fas fa-info-circle"></i>
                        Discount will be applied to university students only
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label class="form-label">Discount Percentage</label>
                            <input type="number" class="form-input discount-percent" placeholder="Enter discount %" min="0" max="100">
                        </div>
                        <div>
                            <label class="form-label">Discounted Price</label>
                            <input type="number" class="form-input discounted-price" placeholder="Calculated price" readonly>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="form-label">Description (Optional)</label>
                <textarea class="form-textarea" placeholder="Describe this ticket type" style="min-height: 60px;"></textarea>
            </div>
        </div>
    `;

    // Add remove functionality
    const removeBtn = newTicketType.querySelector(".remove-ticket-type-btn");
    removeBtn.addEventListener("click", function () {
        newTicketType.remove();
    });

    container.appendChild(newTicketType);

    // Setup discount functionality for the new ticket type
    setupTicketDiscount(newTicketType);

    return counterVar;
}

// Set up add ticket type buttons
document.getElementById("addTicketTypeBtn").addEventListener("click", function () {
    ticketTypeCounter = addTicketType("ticketTypesList", ticketTypeCounter);
});

document.getElementById("addMixedTicketTypeBtn").addEventListener("click", function () {
    mixedTicketTypeCounter = addTicketType("mixedTicketTypesList", mixedTicketTypeCounter);
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    updateTicketOptions();

    // Set up discount functionality for existing ticket types
    document.querySelectorAll('.ticket-type-item').forEach(setupTicketDiscount);

    // Set up remove buttons for default ticket types
    document.querySelectorAll(".remove-ticket-type-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            // Don't remove if it's the only ticket type
            const container = this.closest(".ticket-types-container");
            const items = container.querySelectorAll(".ticket-type-item");
            if (items.length > 1) {
                this.closest(".ticket-type-item").remove();
            } else {
                alert("You need at least one ticket type.");
            }
        });
    });
});


// get donation
document.addEventListener('DOMContentLoaded', function () {
    // Add sidebar navigation for donation section
    const sidebar = document.querySelector('.sidebar');
    const donationSidebarItem = document.createElement('div');
    donationSidebarItem.classList.add('sidebar-item');
    donationSidebarItem.setAttribute('data-target', 'donation');
    donationSidebarItem.textContent = 'Donations';

    // Insert after the ticket item
    const ticketItem = document.querySelector('.sidebar-item[data-target="ticket"]');
    ticketItem.parentNode.insertBefore(donationSidebarItem, ticketItem.nextSibling);

    // Add click event to scroll to donation section
    donationSidebarItem.addEventListener('click', () => {
        // remove previous active
        document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
        donationSidebarItem.classList.add('active');

        // scroll to section
        const donationSection = document.getElementById('donation');

        if (donationSection) {
            // Expand section if collapsed
            donationSection.classList.remove('collapsed');

            // Smooth scroll
            donationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});