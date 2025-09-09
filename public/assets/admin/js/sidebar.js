// JavaScript for Mechanical Dashboard

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    // Create overlay for mobile
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    
    // Toggle sidebar function
    function toggleSidebar() {
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        } else {
            // Desktop behavior - slide content
            if (sidebar.style.transform === 'translateX(-100%)') {
                sidebar.style.transform = 'translateX(0)';
                mainContent.style.marginLeft = '280px';
            } else {
                sidebar.style.transform = 'translateX(-100%)';
                mainContent.style.marginLeft = '0';
            }
        }
    }
    
    // Event listeners
    sidebarToggle.addEventListener('click', toggleSidebar);
    
    // Close sidebar when clicking overlay (mobile)
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const isMobile = window.innerWidth <= 768;
        
        if (!isMobile) {
            // Reset mobile states
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            
            // Reset desktop states
            sidebar.style.transform = '';
            mainContent.style.marginLeft = '';
        }
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.dashboard-card, .course-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Progress bar animation
    const progressBars = document.querySelectorAll('.progress-bar');
    const animateProgressBars = () => {
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    };
    
    // Animate progress bars on page load
    setTimeout(animateProgressBars, 500);
    
    // Search functionality
    const searchInput = document.querySelector('input[placeholder*="Search"]');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                // Add search functionality here
                console.log('Searching for:', this.value);
            }
        });
        
        // Handle Cmd/Ctrl + K shortcut
        document.addEventListener('keydown', function(e) {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    }
    
    // Notification badge animation
    const notificationBadge = document.querySelector('.badge');
    if (notificationBadge) {
        setInterval(() => {
            notificationBadge.style.animation = 'pulse 1s ease-in-out';
            setTimeout(() => {
                notificationBadge.style.animation = '';
            }, 1000);
        }, 5000);
    }
    
    // Add loading states for buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.classList.contains('course-btn') || this.classList.contains('btn-primary')) {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-arrow-clockwise spin me-2"></i>Carregando...';
                this.disabled = true;
                
                // Simulate loading
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 1500);
            }
        });
    });
    
    // Add CSS for spin animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin {
            animation: spin 1s linear infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    `;
    document.head.appendChild(style);
    
    // Initialize tooltips if Bootstrap is loaded
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Add keyboard navigation for sidebar
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        }
    });
    
    // Add focus management for accessibility
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach((link, index) => {
        link.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const nextLink = sidebarLinks[index + 1];
                if (nextLink) nextLink.focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prevLink = sidebarLinks[index - 1];
                if (prevLink) prevLink.focus();
            }
        });
    });
    
    console.log('Mechanical Dashboard initialized successfully!');
});
