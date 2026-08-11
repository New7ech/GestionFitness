<footer class="footer">
    <div class="container-fluid">
        <nav class="pull-left">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('notifications.index') }}">
                        Notifications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('statistiques.index') }}">
                        Statistiques
                    </a>
                </li>
            </ul>
        </nav>
        <div class="copyright ms-auto">
            &copy; {{ date('Y') }}, fait avec <i class="fa fa-heart heart text-danger"></i> par
            <a href="https://www.sinaremohamed.com" target="_blank" rel="noopener">SINARE Mohamed</a>
            &amp; New7ech Entreprise
        </div>
    </div>
</footer>
