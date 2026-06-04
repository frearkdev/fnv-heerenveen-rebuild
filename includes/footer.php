</main>

<!-- Footer -->
<footer class="footer">
    <div class="footer__hoofd">
        <div class="container footer__grid">

            <div class="footer__col footer__col--breed">

                <p>FNV Heerenveen is de lokale afdeling van de grootste vakbond van Nederland. Wij ondersteunen werkenden, uitkeringsgerechtigden en gepensioneerden in de regio Heerenveen-Opsterland.</p>
            </div>

            <div class="footer__col">
                <h4>Navigatie</h4>
                <ul>
                    <li><a href="<?= SITE_URL ?>/index.php">Home</a></li>
                    <li><a href="<?= SITE_URL ?>/nieuws.php">Nieuws</a></li>
                    <li><a href="<?= SITE_URL ?>/agenda.php">Agenda</a></li>
                    <li><a href="<?= SITE_URL ?>/pagina.php?slug=over-ons">Over ons</a></li>
                    <li><a href="<?= SITE_URL ?>/pagina.php?slug=diensten">Diensten</a></li>
                </ul>
            </div>

            <div class="footer__col">
                <h4>Diensten</h4>
                <ul>
                    <li><a href="<?= SITE_URL ?>/pagina.php?slug=diensten">Juridische hulp</a></li>
                    <li><a href="<?= SITE_URL ?>/pagina.php?slug=diensten">Belastingaangifte</a></li>
                    <li><a href="<?= SITE_URL ?>/pagina.php?slug=diensten">Cao-informatie</a></li>
                    <li><a href="<?= SITE_URL ?>/agenda.php">Spreekuur</a></li>
                    <li><a href="<?= SITE_URL ?>/pagina.php?slug=lid-worden">Lid worden</a></li>
                </ul>
            </div>

            <div class="footer__col">
                <h4>Contact</h4>
                <ul class="footer__contact">
                    <li>
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <a href="mailto:info@fnvheerenveen.nl">info@fnvheerenveen.nl</a>
                    </li>
                    <li>
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <a href="tel:0513000000">0513 – 000 000</a>
                    </li>
                    <li>
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <span>Heerenveen, Friesland</span>
                    </li>
                </ul>
                <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary" style="color:white;margin-top:1rem;font-size:.85rem;">Stuur een bericht</a>
            </div>
        </div>
    </div>

    <div class="footer__onder">
        <div class="container footer__onder-inner">
            <p>&copy; <?= date('Y') ?> FNV Heerenveen – Lokaal Netwerk. Alle rechten voorbehouden.</p>
            <div class="footer__onder-links">
                <a href="#">Privacy</a>
                <a href="#">Disclaimer</a>
                <a href="https://www.fnv.nl" target="_blank" rel="noopener">Landelijke FNV</a>
                <a href="<?= SITE_URL ?>/admin/login.php">Beheer</a>
            </div>
        </div>
    </div>
</footer>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>

</html>