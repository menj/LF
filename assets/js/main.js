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

        menuToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            this.setAttribute('aria-expanded', !isExpanded);
            navigation.classList.toggle('active');
            
            // Toggle animation for hamburger icon
            const spans = this.querySelectorAll('span');
            if (spans.length === 3) {
                spans[0].style.transform = !isExpanded ? 'rotate(45deg) translate(5px, 5px)' : '';
                spans[1].style.opacity = !isExpanded ? '0' : '1';
                spans[2].style.transform = !isExpanded ? 'rotate(-45deg) translate(7px, -6px)' : '';
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!menuToggle.contains(event.target) && !navigation.contains(event.target)) {
                menuToggle.setAttribute('aria-expanded', 'false');
                navigation.classList.remove('active');
                
                const spans = menuToggle.querySelectorAll('span');
                if (spans.length === 3) {
                    spans[0].style.transform = '';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = '';
                }
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && navigation.classList.contains('active')) {
                menuToggle.setAttribute('aria-expanded', 'false');
                navigation.classList.remove('active');
                
                const spans = menuToggle.querySelectorAll('span');
                if (spans.length === 3) {
                    spans[0].style.transform = '';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = '';
                }
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
     * Scroll Reveal (Intersection Observer)
     */
    function initScrollReveal() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        var elements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .site-footer');
        if (!elements.length) return;

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        elements.forEach(function(el) {
            observer.observe(el);
        });
    }

    /**
     * Parallax Hero Background
     */
    function parallaxAllowed() {
        var body = document.body;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return false;
        if (body.classList.contains('lf-parallax-off')) return false;
        if (body.classList.contains('lf-parallax-desktop') && window.innerWidth < 1024) return false;
        return true;
    }

    function initParallax() {
        if (!parallaxAllowed()) return;

        var heroBg = document.querySelector('.hero-bg');
        if (!heroBg) return;

        var ticking = false;

        function updateParallax() {
            var scrolled = window.pageYOffset;
            var heroHeight = heroBg.parentElement.offsetHeight;

            if (scrolled <= heroHeight * 1.5) {
                heroBg.style.transform = 'translate3d(0, ' + (scrolled * 0.35) + 'px, 0)';
            }
            ticking = false;
        }

        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }, { passive: true });
    }

    /**
     * Sort menu on the Publications archive: submit as soon as the choice changes.
     * Without JavaScript the Apply button does the same.
     */
    function initBookSort() {
        document.querySelectorAll('select[data-autosubmit]').forEach(function(select) {
            select.addEventListener('change', function() {
                if (select.form) select.form.submit();
            });
        });
    }

    /**
     * Parallax for elements marked data-parallax="speed" (About image, About logo, book cover).
     * speed 0.1 drifts the element at 10% of the scroll, relative to its data-parallax-root
     * section. Only on-screen sections update, batched in requestAnimationFrame. Elements that
     * are scaled to cover their frame stay inside their overhang.
     */
    function initParallaxElements() {
        if (!parallaxAllowed() || !('IntersectionObserver' in window)) return;

        var items = document.querySelectorAll('[data-parallax]');
        if (!items.length) return;

        var roots = new Map();
        var visible = new Set();
        var ticking = false;

        function update() {
            ticking = false;
            var mid = window.innerHeight / 2;

            visible.forEach(function(root) {
                var rect = root.getBoundingClientRect();
                var center = rect.top + rect.height / 2 - mid;

                roots.get(root).forEach(function(el) {
                    var offset = center * -(parseFloat(el.getAttribute('data-parallax')) || 0);
                    var scale = parseFloat(window.getComputedStyle(el).getPropertyValue('--lf-ps')) || 1;

                    if (scale > 1) {
                        var overhang = Math.max(0, (el.offsetHeight * scale - root.offsetHeight) / 2);
                        offset = Math.max(-overhang, Math.min(overhang, offset));
                    }
                    el.style.setProperty('--lf-py', offset.toFixed(1) + 'px');
                });
            });
        }

        function request() {
            if (!ticking && visible.size) {
                ticking = true;
                window.requestAnimationFrame(update);
            }
        }

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) { visible.add(entry.target); } else { visible.delete(entry.target); }
            });
            request();
        }, { rootMargin: '10% 0px' });

        items.forEach(function(el) {
            var root = el.closest('[data-parallax-root]') || el.parentElement;
            if (!roots.has(root)) {
                roots.set(root, []);
                observer.observe(root);
            }
            roots.get(root).push(el);
        });

        window.addEventListener('scroll', request, { passive: true });
        window.addEventListener('resize', request);
    }

    /**
     * Copy Link button for book share bar
     */
    function initShareCopyLink() {
        var copyBtns = document.querySelectorAll('.share-copy');
        if (!copyBtns.length) return;

        copyBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var url = btn.getAttribute('data-url') || window.location.href;
                var textEl = btn.querySelector('.share-copy-text');

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function() {
                        if (textEl) textEl.textContent = 'Copied!';
                        btn.classList.add('copied');
                        setTimeout(function() {
                            if (textEl) textEl.textContent = 'Copy Link';
                            btn.classList.remove('copied');
                        }, 2000);
                    });
                } else {
                    // Fallback for older browsers
                    var textarea = document.createElement('textarea');
                    textarea.value = url;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    if (textEl) textEl.textContent = 'Copied!';
                    btn.classList.add('copied');
                    setTimeout(function() {
                        if (textEl) textEl.textContent = 'Copy Link';
                        btn.classList.remove('copied');
                    }, 2000);
                }
            });
        });
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
        initScrollReveal();
        initParallax();
        initParallaxElements();
        initBookSort();
        initShareCopyLink();
        initFaqAccordion();
    }

    /**
     * FAQ Accordion (Phase C — v2.5.0)
     *
     * Toggles aria-expanded on .faq-question-button and shows/hides the
     * paired .faq-answer panel. Multiple items can be open at once.
     */
    function initFaqAccordion() {
        var buttons = document.querySelectorAll('.faq-question-button');
        if (!buttons.length) return;

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var expanded = button.getAttribute('aria-expanded') === 'true';
                var panelId = button.getAttribute('aria-controls');
                var panel = panelId ? document.getElementById(panelId) : null;
                if (!panel) return;

                button.setAttribute('aria-expanded', !expanded);
                if (expanded) {
                    panel.setAttribute('hidden', '');
                } else {
                    panel.removeAttribute('hidden');
                }
            });
        });
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
