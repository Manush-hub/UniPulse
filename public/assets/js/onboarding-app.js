// Onboarding App JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initRoleSelection();
    initAnimations();
});

// Role Selection
function initRoleSelection() {
    const roleCards = document.querySelectorAll('.role-card');
    
    roleCards.forEach(card => {
        card.addEventListener('click', function() {
            const role = this.getAttribute('data-role');
            selectRole(role);
        });
        
        // Add hover animations
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

// Select Role Function
function selectRole(role) {
    console.log('Selected role:', role);
    
    // Store the selected role in localStorage
    localStorage.setItem('selectedRole', role);
    
    // Add selection animation
    const selectedCard = document.querySelector(`[data-role="${role}"]`);
    selectedCard.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
        selectedCard.style.transform = 'scale(1.05)';
        
        setTimeout(() => {
            // Navigate to role selection page
            window.location.href = `/index.php?url=onboarding/role_selection&role=${role}`;
        }, 200);
    }, 100);
}

// Initialize Animations
function initAnimations() {
    // Animate cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Animate role cards
    const roleCards = document.querySelectorAll('.role-card');
    roleCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(50px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.2}s, transform 0.6s ease ${index * 0.2}s`;
        observer.observe(card);
    });
    
    // Animate welcome section
    const welcomeSection = document.querySelector('.welcome-section');
    if (welcomeSection) {
        welcomeSection.style.opacity = '0';
        welcomeSection.style.transform = 'translateY(-30px)';
        welcomeSection.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        
        setTimeout(() => {
            welcomeSection.style.opacity = '1';
            welcomeSection.style.transform = 'translateY(0)';
        }, 300);
    }
}

// Utility Functions
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Console welcome message
console.log('%c🎓 UniPulse Onboarding', 'color: #FF6B35; font-size: 16px; font-weight: bold;');
console.log('%cWelcome to the enhanced registration experience!', 'color: #4A5BCC; font-size: 12px;');