<!-- resources/views/layouts/navhead.blade.php -->
<!-- Ce fichier définit la barre de navigation supérieure (Navbar Header) de l'application -->

<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">
        {{-- Section de recherche à gauche (actuellement vide, placeholder pour une future implémentation si nécessaire) --}}
        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
            <!-- Le formulaire de recherche global peut être placé ici. -->
            {{-- Exemple de barre de recherche commentée :
            <form class="input-group">
                <input type="text" placeholder="Rechercher dans l'application..." class="form-control">
                <button class="btn btn-info" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>
            --}}
        </nav>

        {{-- Barre d'outils de navigation supérieure droite (Topbar) --}}
        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

            {{-- Inclusion de la section des notifications --}}
            {{-- La logique d'affichage des notifications et de leur dropdown est gérée dans `layouts.notification` --}}
            @include('layouts.notification')

            {{-- Menu déroulant de l'utilisateur authentifié --}}
            @auth {{-- Condition pour afficher cette section uniquement si un utilisateur est authentifié --}}
            <li class="nav-item topbar-user dropdown hidden-caret">
                {{-- Élément cliquable pour ouvrir le dropdown du profil utilisateur --}}
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                        {{-- Affiche la photo de profil de l'utilisateur. Si absente, affiche un avatar par défaut. --}}
                        <img src="{{ Auth::user()->photo_url }}" alt="Image de Profil" class="avatar-img rounded-circle" onerror="this.onerror=null;this.src='{{ asset('assets/img/profile.jpg') }}';" />
                    </div>
                    <span class="profile-username">
                        {{-- Affiche le nom de l'utilisateur connecté --}}
                        <span class="fw-bold">{{ Auth::user()->name ?? 'Utilisateur' }}</span>
                    </span>
                </a>
                {{-- Contenu du menu déroulant utilisateur --}}
                <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                        <li>
                            {{-- Section d'information de l'utilisateur dans le dropdown --}}
                            <div class="user-box d-flex align-items-center">
                                <div class="avatar-lg me-3">
                                     {{-- Affiche une version plus grande de la photo de profil --}}
                                     <img src="{{ Auth::user()->photo_url }}" alt="Image de Profil" class="avatar-img rounded" onerror="this.onerror=null;this.src='{{ asset('assets/img/profile.jpg') }}';" />
                                </div>
                                <div class="u-text">
                                    {{-- Nom et email de l'utilisateur --}}
                                    <h4>{{ Auth::user()->name ?? 'Utilisateur' }}</h4>
                                    <p class="text-muted mb-1">{{ Auth::user()->email ?? '' }}</p>
                                    {{-- Lien vers la page de profil de l'utilisateur --}}
                                    <a href="{{ route('users.show', Auth::user()->id) }}" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-user-circle me-1"></i> Mon Profil
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                            {{-- Lien vers les paramètres du compte (actuellement un placeholder) --}}
                            {{-- TODO: Définir la route et la fonctionnalité pour les "Paramètres du compte" --}}
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cog me-2"></i> Paramètres du compte
                            </a>
                            <div class="dropdown-divider"></div>
                            {{-- Lien de déconnexion --}}
                            @if (Route::has('logout'))
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                   <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                </a>
                                {{-- Formulaire de déconnexion (caché, soumis via JavaScript) --}}
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            @endif
                        </li>
                    </div>
                </ul>
            </li>
            @endauth {{-- Fin de la condition @auth --}}
        </ul>
    </div>
</nav>
<!-- End Navbar -->
