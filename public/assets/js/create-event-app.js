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