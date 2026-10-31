/**
 * Main JavaScript for Langgam Fikir Theme
 */

(function() {
    'use strict';

    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navigation = document.querySelector('.main-navigation');

        if (!menuToggle || !navigation) return;

        // The icon animation lives in CSS (.menu-toggle.is-active).
        function setOpen(open) {
            menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            menuToggle.classList.toggle('is-active', open);
            navigation.classList.toggle('active', open);
        }

        menuToggle.addEventListener('click', function() {
            setOpen(this.getAttribute('aria-expanded') !== 'true');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!menuToggle.contains(event.target) && !navigation.contains(event.target)) {
                setOpen(false);
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && navigation.classList.contains('active')) {
                setOpen(false);
                menuToggle.focus();
            }
        });
    }

    /**
     * Quantity Selector
     */
    function initQuantitySelector() {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        
        quantityInputs.forEach(function(input) {
            const decreaseBtn = input.previousElementSibling;
            const increaseBtn = input.nextElementSibling;
            
            if (decreaseBtn && decreaseBtn.classList.contains('quantity-decrease')) {
                decreaseBtn.addEventListener('click', function() {
                    const currentValue = parseInt(input.value) || 1;
                    const minValue = parseInt(input.getAttribute('min')) || 1;
                    
                    if (currentValue > minValue) {
                        input.value = currentValue - 1;
                        input.dispatchEvent(new Event('change'));
                    }
                });
            }
            
            if (increaseBtn && increaseBtn.classList.contains('quantity-increase')) {
                increaseBtn.addEventListener('click', function() {
                    const currentValue = parseInt(input.value) || 1;
                    const maxValue = parseInt(input.getAttribute('max')) || 99;
                    
                    if (currentValue < maxValue) {
                        input.value = currentValue + 1;
                        input.dispatchEvent(new Event('change'));
                    }
                });
            }
            
            // Validate input on change
            input.addEventListener('change', function() {
                const value = parseInt(this.value);
                const minValue = parseInt(this.getAttribute('min')) || 1;
                const maxValue = parseInt(this.getAttribute('max')) || 99;
                
                if (isNaN(value) || value < minValue) {
                    this.value = minValue;
                } else if (value > maxValue) {
                    this.value = maxValue;
                }
            });
        });
    }

    /**
     * Smooth Scroll for Anchor Links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                if (href === '#' || href === '#0') return;
                
                const target = document.querySelector(href);
                
                if (target) {
                    e.preventDefault();
                    
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Add to Cart (if WooCommerce is not active)
     */
    function initAddToCart() {
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        
        addToCartButtons.forEach(function(button) {
            if (!button.hasAttribute('data-product-id')) return;
            
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const productId = this.getAttribute('data-product-id');
                const quantityInput = document.querySelector('.quantity-input');
                const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
                
                // This is a placeholder for custom add-to-cart functionality
                // If WooCommerce is active, it handles this automatically
                
                console.log('Adding to cart:', {
                    productId: productId,
                    quantity: quantity
                });
                
                // You can implement custom cart functionality here
                // or redirect to a contact page
            });
        });
    }

    /**
     * Lazy Load Images (simple implementation)
     */
    function initLazyLoad() {
        if ('IntersectionObserver' in window) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            
            const imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        
                        observer.unobserve(img);
                    }
                });
            });
            
            images.forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Initialize all functions when DOM is ready
     */
    function init() {
        initMobileMenu();
        initQuantitySelector();
        initSmoothScroll();
        initAddToCart();
        initLazyLoad();
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
