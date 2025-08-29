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

// TICKET TYPE TOGGLE FUNCTIONALITY
const ticketTypeRadios = document.querySelectorAll('input[name="ticket-type"]');
const paidTicketDetails = document.getElementById('paidTicketDetails');
const freeTicketDetails = document.getElementById('freeTicketDetails');

function toggleTicketDetails() {
    const selectedType = document.querySelector('input[name="ticket-type"]:checked').value;

    if (selectedType === 'paid') {
        paidTicketDetails.classList.remove('hidden');
        freeTicketDetails.classList.add('hidden');
    } else {
        paidTicketDetails.classList.add('hidden');
        freeTicketDetails.classList.remove('hidden');
    }
}

// Add event listeners to ticket type radio buttons
ticketTypeRadios.forEach(radio => {
    radio.addEventListener('change', toggleTicketDetails);
});

// Initialize with current selection
toggleTicketDetails();

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