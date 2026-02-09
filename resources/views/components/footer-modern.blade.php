@php
    $primary = config('mills.primary_color', '#002642');
    $footerBg = $primary;
    $footerFg = '#ffffff';
@endphp
<footer class="footer-modern color-scheme-2 mt-12" style="--color-background: 26, 38, 66; --color-foreground: 255, 255, 255; background-color: {{ $footerBg }}; color: {{ $footerFg }};">
    <div class="footer-modern__wrapper" style="padding: 3rem 2rem 0;">
        <div class="footer-modern__grid">
            {{-- Logo + Nav --}}
            <div class="footer-modern__logo-column">
                <p class="footer-modern__logo-text">{{ strtolower(config('app.name')) }}</p>
                <ul class="footer-modern__links">
                    <li class="footer-modern__link-item"><a href="https://millsdailypacks.com" class="footer-modern__link" target="_blank" rel="noopener">Customize Your Pack</a></li>
                    <li class="footer-modern__link-item"><a href="https://millsdailypacks.com/pages/our-story" class="footer-modern__link" target="_blank" rel="noopener">Our Story</a></li>
                    <li class="footer-modern__link-item"><a href="https://millsdailypacks.com/pages/switching" class="footer-modern__link" target="_blank" rel="noopener">Switching to Mills</a></li>
                    <li class="footer-modern__link-item"><a href="https://millsdailypacks.com/blogs/recipes" class="footer-modern__link" target="_blank" rel="noopener">Recipes</a></li>
                    <li class="footer-modern__link-item"><a href="https://millsdailypacks.com/pages/sustainability" class="footer-modern__link" target="_blank" rel="noopener">Sustainability</a></li>
                    <li class="footer-modern__link-item"><a href="https://millsdailypacks.com/pages/partner" class="footer-modern__link" target="_blank" rel="noopener">Partner With Us</a></li>
                    <li class="footer-modern__link-item"><a href="https://millsdailypacks.com" class="footer-modern__link" target="_blank" rel="noopener">Reviews</a></li>
                    <li class="footer-modern__link-item"><a href="https://millsdailypacks.com/pages/faq" class="footer-modern__link" target="_blank" rel="noopener">FAQ</a></li>
                    <li class="footer-modern__link-item"><a href="{{ route('account.dashboard') }}" class="footer-modern__link">Log In</a></li>
                </ul>
            </div>
            {{-- Contact --}}
            <div class="footer-modern__contact-column">
                <h3 class="footer-modern__heading">Contact Us</h3>
                <div class="footer-modern__contact-info">
                    <a href="mailto:hello@millsdailypacks.com" class="footer-modern__contact-link">hello@millsdailypacks.com</a>
                    <a href="tel:1-888-443-8953" class="footer-modern__contact-link">1-888-443-8953</a>
                </div>
            </div>
            {{-- Newsletter --}}
            <div class="footer-modern__newsletter-column">
                <h3 class="footer-modern__heading">Sign up for updates</h3>
                <form class="footer-modern__newsletter-form" action="https://millsdailypacks.com/contact#contact_form" method="post" target="_blank" rel="noopener">
                    <div class="footer-modern__newsletter-wrapper kl-private-reset-css-Xuajs1">
                        <input type="email" name="contact[email]" class="footer-modern__newsletter-input" placeholder="Enter your email" aria-label="Email for newsletter">
                        <button type="submit" class="footer-modern__newsletter-button">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- Social --}}
        <div class="footer-modern__social">
            <ul class="footer-modern__social-list">
                <li><a href="https://www.facebook.com/millsdailypacks" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a></li>
                <li><a href="https://www.instagram.com/millsdailypacks" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a></li>
                <li><a href="https://www.youtube.com/@millsdailypacks" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a></li>
                <li><a href="https://www.tiktok.com/@millsdailypacks" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg></a></li>
            </ul>
        </div>
        {{-- Legal + Copyright --}}
        <div class="footer-modern__bottom">
            <ul class="footer-modern__policy-links">
                <li class="footer-modern__policy-item"><a href="https://millsdailypacks.com/policies/privacy-policy" class="footer-modern__policy-link" target="_blank" rel="noopener">Privacy policy</a></li>
                <li class="footer-modern__policy-item"><a href="https://millsdailypacks.com/policies/refund-policy" class="footer-modern__policy-link" target="_blank" rel="noopener">Refund policy</a></li>
                <li class="footer-modern__policy-item"><a href="https://millsdailypacks.com/policies/terms-of-service" class="footer-modern__policy-link" target="_blank" rel="noopener">Terms of service</a></li>
                <li class="footer-modern__policy-item"><a href="https://millsdailypacks.com/policies/shipping-policy" class="footer-modern__policy-link" target="_blank" rel="noopener">Shipping policy</a></li>
            </ul>
            <p class="footer-modern__copyright"><span>© {{ date('Y') }} Mills. All rights reserved.</span></p>
        </div>
    </div>
</footer>
