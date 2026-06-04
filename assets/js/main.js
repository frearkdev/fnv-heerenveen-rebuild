document.addEventListener('DOMContentLoaded', function () {

    const header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 60);
        });
    }

    const hamburger   = document.getElementById('hamburger');
    const mobielMenu  = document.getElementById('mobielMenu');
    if (hamburger && mobielMenu) {
        hamburger.addEventListener('click', () => {
            const open = mobielMenu.classList.toggle('open');
            hamburger.classList.toggle('open', open);
            hamburger.setAttribute('aria-expanded', open);
        });
    }

    const zoekToggle = document.getElementById('zoekToggle');
    const zoekbalk   = document.getElementById('zoekbalk');
    if (zoekToggle && zoekbalk) {
        zoekToggle.addEventListener('click', () => {
            const isHidden = zoekbalk.hidden;
            zoekbalk.hidden = !isHidden;
            if (!isHidden === false) {
                const input = zoekbalk.querySelector('input');
                if (input) input.focus();
            }
        });
    }

    const adminHamburger = document.getElementById('adminHamburger');
    const adminSidebar   = document.querySelector('.admin-sidebar');
    const adminOverlay   = document.getElementById('adminOverlay');
    if (adminHamburger && adminSidebar) {
        adminHamburger.addEventListener('click', () => {
            adminSidebar.classList.toggle('open');
            if (adminOverlay) adminOverlay.style.display = adminSidebar.classList.contains('open') ? 'block' : 'none';
        });
        if (adminOverlay) {
            adminOverlay.addEventListener('click', () => {
                adminSidebar.classList.remove('open');
                adminOverlay.style.display = 'none';
            });
        }
    }

    const filterCats = document.querySelectorAll('.filter-cat');
    filterCats.forEach(btn => {
        btn.addEventListener('click', () => {
            filterCats.forEach(b => b.classList.remove('actief'));
            btn.classList.add('actief');
            const cat = btn.dataset.cat;
            const kaarten = document.querySelectorAll('.nieuws-kaart[data-cat]');
            kaarten.forEach(kaart => {
                kaart.style.display = (cat === 'alles' || kaart.dataset.cat === cat) ? '' : 'none';
            });
        });
    });

    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            const bericht = document.getElementById('bericht');
            if (bericht && bericht.value.trim().length < 10) {
                e.preventDefault();
                bericht.style.borderColor = 'var(--color-error)';
                const fout = document.createElement('p');
                fout.style.cssText = 'color:var(--color-error);font-size:.82rem;margin-top:.3rem;';
                fout.textContent = 'Vul minimaal 10 tekens in bij het berichtveld.';
                if (!bericht.nextElementSibling || !bericht.nextElementSibling.classList.contains('js-fout')) {
                    fout.classList.add('js-fout');
                    bericht.after(fout);
                }
            }
        });
    }

    const stats = document.querySelectorAll('.js-teller');
    if (stats.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el  = entry.target;
                    const end = parseInt(el.dataset.doel, 10);
                    let current = 0;
                    const step = Math.max(1, Math.floor(end / 60));
                    const timer = setInterval(() => {
                        current = Math.min(current + step, end);
                        el.textContent = current.toLocaleString('nl-NL');
                        if (current >= end) clearInterval(timer);
                    }, 20);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        stats.forEach(el => observer.observe(el));
    }

    const uploadZone = document.getElementById('uploadZone');
    if (uploadZone) {
        ['dragover', 'dragenter'].forEach(evt => {
            uploadZone.addEventListener(evt, e => {
                e.preventDefault();
                uploadZone.classList.add('dragover');
            });
        });
        ['dragleave', 'drop'].forEach(evt => {
            uploadZone.addEventListener(evt, () => uploadZone.classList.remove('dragover'));
        });
    }

    document.querySelectorAll('.js-verwijder').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm(this.dataset.bevestig || 'Weet u zeker dat u dit item wilt verwijderen?')) {
                e.preventDefault();
            }
        });
    });

    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity .5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    document.querySelectorAll('[data-max]').forEach(el => {
        const max     = parseInt(el.dataset.max, 10);
        const counter = document.querySelector(`[data-teller="${el.id}"]`);
        if (counter) {
            const update = () => {
                const rest = max - el.value.length;
                counter.textContent = `${el.value.length}/${max}`;
                counter.style.color = rest < 20 ? 'var(--color-warning)' : 'var(--color-text-secondary)';
            };
            el.addEventListener('input', update);
            update();
        }
    });

});
