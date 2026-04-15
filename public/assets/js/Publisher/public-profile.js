/**
 * Publisher Public Profile Page JavaScript
 * Handles tab switching and gallery carousel functionality
 */

// Gallery carousel state management
const carouselStates = {};

/**
 * Initialize a gallery carousel
 * @param {number} galleryId - The ID of the gallery
 * @returns {Object|null} The carousel state object or null if not found
 */
function initCarousel(galleryId) {
    if (!carouselStates[galleryId]) {
        const container = document.querySelector(`[data-gallery-id="${galleryId}"]`);
        if (!container) {
            console.error('Gallery container not found for ID:', galleryId);
            return null;
        }
        const items = container.querySelectorAll('.carousel-item');
        carouselStates[galleryId] = {
            currentIndex: 0,
            totalImages: items.length,
            container: container
        };
    }
    return carouselStates[galleryId];
}

/**
 * Initialize all carousels on the page
 */
function initAllCarousels() {
    const carouselContainers = document.querySelectorAll('.gallery-carousel-container');
    carouselContainers.forEach(container => {
        const galleryId = parseInt(container.getAttribute('data-gallery-id'));
        if (galleryId) {
            initCarousel(galleryId);
        }
    });
}

/**
 * Show a specific image in the carousel
 * @param {number} galleryId - The ID of the gallery
 * @param {number} index - The index of the image to show
 */
function showImage(galleryId, index) {
    const state = initCarousel(galleryId);
    if (!state) return;
    
    const items = state.container.querySelectorAll('.carousel-item');
    const currentDisplay = state.container.querySelector('.current-image');
    
    // Remove active class from all items
    items.forEach(item => item.classList.remove('active'));
    
    // Wrap around if needed
    if (index < 0) {
        index = state.totalImages - 1;
    } else if (index >= state.totalImages) {
        index = 0;
    }
    
    // Show new active item
    items[index].classList.add('active');
    state.currentIndex = index;
    
    // Update counter
    if (currentDisplay) {
        currentDisplay.textContent = index + 1;
    }
}

/**
 * Navigate to the next image in the carousel
 * @param {number} galleryId - The ID of the gallery
 */
function nextImage(galleryId) {
    const state = initCarousel(galleryId);
    if (state) {
        showImage(galleryId, state.currentIndex + 1);
    }
}

/**
 * Navigate to the previous image in the carousel
 * @param {number} galleryId - The ID of the gallery
 */
function previousImage(galleryId) {
    const state = initCarousel(galleryId);
    if (state) {
        showImage(galleryId, state.currentIndex - 1);
    }
}

/**
 * Switch between event tabs (Gallery, Upcoming Events, Past Events)
 * @param {string} tabName - The name of the tab to switch to
 */
function switchEventTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.event-tab-content');
    tabContents.forEach(content => {
        content.classList.remove('active');
    });
    
    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.event-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected tab content
    const selectedContent = document.getElementById(tabName + '-events');
    if (selectedContent) {
        selectedContent.classList.add('active');
    }
    
    // Add active class to selected tab
    const selectedTab = document.querySelector(`.event-tab[data-tab="${tabName}"]`);
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
    
    // Initialize carousels when gallery tab is opened
    if (tabName === 'gallery') {
        setTimeout(() => {
            initAllCarousels();
        }, 100);
    }
}

/**
 * Initialize page on DOM ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Ensure upcoming events tab is active on load
    switchEventTab('upcoming');
    
    // Also initialize carousels in case user clicks gallery tab
    initAllCarousels();
    
    console.log('Publisher public profile page loaded successfully');
});
