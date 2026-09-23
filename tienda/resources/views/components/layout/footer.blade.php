<footer role="contentinfo">
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">

                <div class="footer-col">
                    <h4>Sobre {{ config('app.name') }}</h4>
                    <ul>
                        <li><a href="#">Acerca de nosotros</a></li>
                        <li><a href="#">Noticias</a></li>
                        <li><a href="#">Trabaja con nosotros</a></li>
                        <li><a href="#">Sustentabilidad</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Información</h4>
                    <ul>
                        <li><a href="#">Centro de ayuda</a></li>
                        <li><a href="#">Cómo comprar</a></li>
                        <li><a href="#">Cómo vender</a></li>
                        <li><a href="#">Resolver un problema</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Servicios</h4>
                    <ul>
                        <li><a href="#">Pagos</a></li>
                        <li><a href="#">Envíos</a></li>
                        <li><a href="#">Crédito</a></li>
                        <li><a href="#">Publicidad</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Mi cuenta</h4>
                    <ul>
                        <li><a href="#">Mis pedidos</a></li>
                        <li><a href="#">Mis favoritos</a></li>
                        <li><a href="#">Historial de pagos</a></li>
                        @auth
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" style="font-size:13px;color:#666;background:none;border:none;cursor:pointer;padding:0;">
                                        Cerrar sesión
                                    </button>
                                </form>
                            </li>
                        @endauth
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Descarga la app</h4>
                    <div class="app-badges" style="flex-direction:column;gap:8px;margin-bottom:16px;">
                        <a href="#" class="app-badge">
                            <span class="store-icon">🍎</span>
                            <div class="store-text">
                                <small>Descarga en</small>
                                <strong>App Store</strong>
                            </div>
                        </a>
                        <a href="#" class="app-badge">
                            <span class="store-icon">▶</span>
                            <div class="store-text">
                                <small>Disponible en</small>
                                <strong>Google Play</strong>
                            </div>
                        </a>
                    </div>
                    <h4 style="margin-top:8px;">Síguenos</h4>
                    <div class="flex gap-8" style="margin-top:8px;">
                        <a href="#" style="font-size:20px;" title="Instagram">📸</a>
                        <a href="#" style="font-size:20px;" title="Facebook">👥</a>
                        <a href="#" style="font-size:20px;" title="Twitter">🐦</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="container">
        <div class="footer-bottom">
            <p>© {{ date('Y') }} {{ config('app.name') }} — Todos los derechos reservados |
                <a href="#">Términos y condiciones</a> |
                <a href="#">Privacidad</a>
            </p>
            <div class="payment-logos">
                <span class="payment-logo">VISA</span>
                <span class="payment-logo">Mastercard</span>
                <span class="payment-logo">Webpay</span>
                <span class="payment-logo">Redcompra</span>
                <span class="payment-logo">PayPal</span>
            </div>
        </div>
    </div>
</footer>
