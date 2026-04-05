// Common event view functions extracted from inline scripts
let currentImageIndex = 0;
const galleryImages = document.querySelectorAll('.gallery-image');

function openImageModal(index) {
    if (!galleryImages || galleryImages.length === 0) return;
    currentImageIndex = index;
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    
    if (modal && modalImg && galleryImages[index]) {
        modal.style.display = "block";
        modalImg.src = galleryImages[index].src;
        document.body.style.overflow = 'hidden'; 
    }
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.style.display = "none";
        document.body.style.overflow = 'auto'; 
    }
}

function changeImage(direction) {
    if (!galleryImages || galleryImages.length === 0) return;
    currentImageIndex += direction;
    
    if (currentImageIndex >= galleryImages.length) {
        currentImageIndex = 0;
    } else if (currentImageIndex < 0) {
        currentImageIndex = galleryImages.length - 1;
    }
    
    const modalImg = document.getElementById('modalImage');
    if (modalImg && galleryImages[currentImageIndex]) {
        modalImg.src = galleryImages[currentImageIndex].src;
    }
}

// Map toggle functionality
function toggleMap() {
    const mapContent = document.getElementById('mapContent');
    const mapContainer = document.querySelector('.map-container');
    const expandBtn = document.querySelector('.map-expand-btn span');
    const expandIcon = document.querySelector('.map-expand-btn i');
    
    if (!mapContent || !mapContainer || !expandBtn || !expandIcon) return;
    
    mapContent.classList.toggle('expanded');
    mapContainer.classList.toggle('expanded');
    
    if (mapContent.classList.contains('expanded')) {
        expandBtn.textContent = 'Collapse Map';
        expandIcon.classList.replace('fa-expand', 'fa-compress');
        
        let iframe = mapContent.querySelector('iframe');
        if (!iframe) {
            const mapHtml = document.getElementById('hiddenMapData')?.value;
            if (mapHtml) {
                mapContent.innerHTML = mapHtml;
            }
        }
    } else {
        expandBtn.textContent = 'Expand Map';
        expandIcon.classList.replace('fa-compress', 'fa-expand');
    }
}

// Close image modal on outside click
window.onclick = function(event) {
    const imageModal = document.getElementById('imageModal');
    const shareModal = document.getElementById('shareModal');
    const reportModal = document.getElementById('reportModal');
    const messageModal = document.getElementById('messageModal');
    
    if (event.target == imageModal) {
        closeImageModal();
    }
    if (event.target == shareModal) {
        closeShareModal();
    }
    if (event.target == reportModal) {
        closeReportModal();
    }
    if (event.target == messageModal) {
        closeMessageModal();
    }
}

// Close modals on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeImageModal();
        if (typeof closeShareModal === 'function') closeShareModal();
        if (typeof closeReportModal === 'function') closeReportModal();
        if (typeof closeMessageModal === 'function') closeMessageModal();
    }
});
