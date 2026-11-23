import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const togglePanel = (panelId) => {
        const panel = document.getElementById(panelId);
        if (!panel) return;

        const isOpen = panel.dataset.open === 'true';
        panel.dataset.open = String(!isOpen);
        panel.classList.toggle('hidden', isOpen);
    };

    document.querySelectorAll('[data-toggle-panel]').forEach((button) => {
        button.addEventListener('click', () => togglePanel(button.dataset.togglePanel));
    });

    document.querySelectorAll('[data-share-native]').forEach((button) => {
        button.addEventListener('click', async () => {
            const sharePayload = {
                title: button.dataset.shareTitle || document.title,
                text: button.dataset.shareText || document.title,
                url: button.dataset.shareUrl || window.location.href,
            };

            if (navigator.share) {
                try {
                    await navigator.share(sharePayload);
                    return;
                } catch {
                    // Fall through to copy-link fallback.
                }
            }

            if (navigator.clipboard?.writeText) {
                try {
                    await navigator.clipboard.writeText(sharePayload.url);
                    return;
                } catch {
                    // Fall through to prompt fallback.
                }
            }

            window.prompt('Copiar enlace', sharePayload.url);
        });
    });

    const heroSlider = document.querySelector('[data-hero-slider]');
    if (heroSlider) {
        const slides = Array.from(heroSlider.querySelectorAll('[data-hero-slide]'));
        const dots = Array.from(heroSlider.querySelectorAll('[data-hero-dot]'));
        const previousButton = heroSlider.querySelector('[data-hero-prev]');
        const nextButton = heroSlider.querySelector('[data-hero-next]');
        const heroTitle = heroSlider.querySelector('[data-hero-title-display]');
        const heroSubtitle = heroSlider.querySelector('[data-hero-subtitle-display]');
        const heroButton = heroSlider.querySelector('[data-hero-button-display]');
        const heroButtonText = heroSlider.querySelector('[data-hero-button-text-display]');
        const heroSecondary = heroSlider.querySelector('[data-hero-secondary-display]');
        const heroSecondaryText = heroSlider.querySelector('[data-hero-secondary-text-display]');
        let activeIndex = 0;

        const escapeHtml = (value) =>
            String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

        const renderHeroTitle = (title) => {
            const words = String(title || '').trim().split(/\s+/).filter(Boolean);
            if (words.length <= 1) {
                return `<span class="text-white">${escapeHtml(title || '')}</span>`;
            }

            const accentSize = words.length >= 5 ? 2 : 1;
            const mainWords = words.slice(0, Math.max(1, words.length - accentSize)).join(' ');
            const accentWords = words.slice(-accentSize).join(' ');

            return `
                <span class="block text-white">${escapeHtml(mainWords)}</span>
                <span class="block text-secondary-container">${escapeHtml(accentWords)}</span>
            `;
        };

        const showSlide = (index) => {
            activeIndex = (index + slides.length) % slides.length;
            const activeSlide = slides[activeIndex];

            slides.forEach((slide, slideIndex) => {
                const isActive = slideIndex === activeIndex;
                slide.classList.toggle('opacity-100', isActive);
                slide.classList.toggle('opacity-0', !isActive);
                slide.classList.toggle('pointer-events-none', !isActive);
                slide.setAttribute('aria-hidden', String(!isActive));
                slide.setAttribute('data-active', String(isActive));
            });

            if (activeSlide) {
                const title = activeSlide.dataset.heroTitle;
                const subtitle = activeSlide.dataset.heroSubtitle;
                const buttonText = activeSlide.dataset.heroButtonText;
                const buttonUrl = activeSlide.dataset.heroButtonUrl;
                const secondaryText = activeSlide.dataset.heroSecondaryText;
                const secondaryUrl = activeSlide.dataset.heroSecondaryUrl;

                if (heroTitle && title) heroTitle.innerHTML = renderHeroTitle(title);
                if (heroSubtitle && subtitle) heroSubtitle.textContent = subtitle;
                if (heroButton && buttonUrl) heroButton.setAttribute('href', buttonUrl);
                if (heroButtonText && buttonText) heroButtonText.textContent = buttonText;
                if (heroSecondary && secondaryUrl) heroSecondary.setAttribute('href', secondaryUrl);
                if (heroSecondaryText && secondaryText) heroSecondaryText.textContent = secondaryText;
            }

            dots.forEach((dot, dotIndex) => {
                dot.classList.toggle('bg-secondary-container', dotIndex === activeIndex);
                dot.classList.toggle('bg-white/30', dotIndex !== activeIndex);
                dot.setAttribute('aria-current', String(dotIndex === activeIndex));
            });
        };

        previousButton?.addEventListener('click', () => showSlide(activeIndex - 1));
        nextButton?.addEventListener('click', () => showSlide(activeIndex + 1));

        dots.forEach((dot, dotIndex) => {
            dot.addEventListener('click', () => showSlide(dotIndex));
        });

        showSlide(0);

        if (slides.length > 1) {
            setInterval(() => showSlide(activeIndex + 1), 7000);
        }
    }

    document.querySelectorAll('[data-page-slider]').forEach((slider) => {
        const track = slider.querySelector('[data-page-slider-track]');
        const slides = Array.from(slider.querySelectorAll('[data-page-slider-slide]'));
        const previousButton = slider.parentElement?.querySelector('[data-page-slider-prev]');
        const nextButton = slider.parentElement?.querySelector('[data-page-slider-next]');
        const dots = Array.from(slider.parentElement?.querySelectorAll('[data-page-slider-dot]') ?? []);

        if (!track || slides.length <= 1) {
            return;
        }

        const getActiveIndex = () => {
            const width = track.clientWidth || 1;
            return Math.max(0, Math.min(slides.length - 1, Math.round(track.scrollLeft / width)));
        };

        const scrollToSlide = (index) => {
            const targetIndex = Math.max(0, Math.min(slides.length - 1, index));
            track.scrollTo({
                left: targetIndex * track.clientWidth,
                behavior: 'smooth',
            });
        };

        const updateControls = () => {
            const activeIndex = getActiveIndex();
            const maxScrollLeft = track.scrollWidth - track.clientWidth - 2;

            if (previousButton) {
                previousButton.toggleAttribute('disabled', track.scrollLeft <= 2);
            }

            if (nextButton) {
                nextButton.toggleAttribute('disabled', track.scrollLeft >= maxScrollLeft);
            }

            dots.forEach((dot, dotIndex) => {
                dot.classList.toggle('bg-primary', dotIndex === activeIndex);
                dot.classList.toggle('bg-outline-variant', dotIndex !== activeIndex);
                dot.setAttribute('aria-current', String(dotIndex === activeIndex));
            });
        };

        previousButton?.addEventListener('click', () => scrollToSlide(getActiveIndex() - 1));
        nextButton?.addEventListener('click', () => scrollToSlide(getActiveIndex() + 1));

        dots.forEach((dot, dotIndex) => {
            dot.addEventListener('click', () => scrollToSlide(dotIndex));
        });

        track.addEventListener(
            'scroll',
            () => {
                window.requestAnimationFrame(updateControls);
            },
            { passive: true },
        );

        window.addEventListener('resize', updateControls);

        updateControls();
    });
});
