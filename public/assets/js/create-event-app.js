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

// NEW COMPACT ALBUM UPLOAD FUNCTIONALITY
const uploadAreaCompact = document.getElementById('uploadAreaCompact');
const fileInputCompact = document.getElementById('fileInputCompact');
const previewGridCompact = document.getElementById('previewGridCompact');
const uploadBtnCompact = document.getElementById('uploadBtnCompact');
let filesCompact = [];

// Handle drag and drop events
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    uploadAreaCompact.addEventListener(eventName, preventDefaults, false);
});

['dragenter', 'dragover'].forEach(eventName => {
    uploadAreaCompact.addEventListener(eventName, highlightCompact, false);
});

['dragleave', 'drop'].forEach(eventName => {
    uploadAreaCompact.addEventListener(eventName, unhighlightCompact, false);
});

function highlightCompact() {
    uploadAreaCompact.classList.add('active');
}

function unhighlightCompact() {
    uploadAreaCompact.classList.remove('active');
}

// Handle file drop
uploadAreaCompact.addEventListener('drop', handleDropCompact, false);

function handleDropCompact(e) {
    const dt = e.dataTransfer;
    const newFiles = dt.files;
    handleFilesCompact(newFiles);
}

// Handle file input change
fileInputCompact.addEventListener('change', function () {
    handleFilesCompact(this.files);
});

// Process the uploaded files
function handleFilesCompact(newFiles) {
    if (newFiles.length > 0) {
        // Remove empty state if it exists
        const emptyState = previewGridCompact.querySelector('.empty-state-compact');
        if (emptyState) {
            emptyState.remove();
        }

        Array.from(newFiles).forEach(file => {
            if (file.type.match('image.*')) {
                filesCompact.push(file);

                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item-compact';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-img-compact';
                    img.alt = 'Preview';

                    const removeBtn = document.createElement('div');
                    removeBtn.className = 'remove-btn-compact';
                    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    removeBtn.addEventListener('click', function () {
                        const index = filesCompact.indexOf(file);
                        if (index > -1) {
                            filesCompact.splice(index, 1);
                        }
                        previewItem.remove();

                        // Show empty state if no files left
                        if (filesCompact.length === 0) {
                            showEmptyStateCompact();
                            uploadBtnCompact.disabled = true;
                        }
                    });

                    previewItem.appendChild(img);
                    previewItem.appendChild(removeBtn);
                    previewGridCompact.appendChild(previewItem);
                    uploadBtnCompact.disabled = false;
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

function showEmptyStateCompact() {
    const emptyState = document.createElement('div');
    emptyState.className = 'empty-state-compact';
    emptyState.innerHTML = '<i class="fas fa-images"></i><p>No photos selected yet</p>';
    previewGridCompact.appendChild(emptyState);
}

// Upload button functionality
uploadBtnCompact.addEventListener('click', function () {
    if (filesCompact.length > 0) {
        // Simulate upload process
        const originalText = uploadBtnCompact.innerHTML;
        uploadBtnCompact.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        uploadBtnCompact.disabled = true;

        setTimeout(() => {
            alert('Album uploaded successfully!');
            uploadBtnCompact.innerHTML = originalText;
            uploadBtnCompact.disabled = false;
        }, 2000);
    }
});

// Initialize map
function initMap() {
    const mapContainer = document.getElementById('mapPreview');
    const mapPlaceholder = mapContainer.querySelector('.map-placeholder');

    // Create a simple map with a marker
    const map = L.map('mapPreview').setView([6.9271, 79.8612], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add a marker
    L.marker([6.9271, 79.8612]).addTo(map)
        .bindPopup('Event Location')
        .openPopup();

    // Hide the placeholder
    mapPlaceholder.style.display = 'none';
}

// Initialize map when location section is in view
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            initMap();
            observer.disconnect();
        }
    });
}, { threshold: 0.5 });

observer.observe(document.getElementById('location-time'));