@php
    $route = Route::currentRouteName() ?? '';

    $isArticles     = str_starts_with($route, 'articles') || str_starts_with($route, 'categories') || str_starts_with($route, 'emplacements');
    $isFournisseurs = str_starts_with($route, 'fournisseurs');
    $isFactures     = str_starts_with($route, 'factures');
    $isFitness      = str_starts_with($route, 'participantes') || str_starts_with($route, 'challenges') || str_starts_with($route, 'presences') || str_starts_with($route, 'mesures') || str_starts_with($route, 'participant-media') || str_starts_with($route, 'payments') || str_starts_with($route, 'recus');
    $isAdmin        = str_starts_with($route, 'users') || str_starts_with($route, 'roles') || str_starts_with($route, 'permissions');
    $isUsers        = str_starts_with($route, 'users');
    $isRoles        = str_starts_with($route, 'roles');
    $isPermissions  = str_starts_with($route, 'permissions');
@endphp

<div class="sidebar sidebar-style-2" data-background-color="dark">
    <div class="sidebar-logo">
        @include('layouts.logoheader')
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">

                <li class="nav-item {{ $route === 'accueil' ? 'active' : '' }}">
                    <a href="{{ route('accueil') }}">
                        <i class="fas fa-home"></i>
                        <p>Accueil</p>
                    </a>
                </li>

                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                    <h4 class="text-section">Menu Principal</h4>
                </li>

                @can('show-participantes')
                <li class="nav-item {{ $isFitness ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#menu-fitness" class="{{ $isFitness ? '' : 'collapsed' }}" aria-expanded="{{ $isFitness ? 'true' : 'false' }}">
                        <i class="fas fa-heartbeat"></i>
                        <p>Fitness</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $isFitness ? 'show' : '' }}" id="menu-fitness">
                        <ul class="nav nav-collapse">
                            <li class="{{ str_starts_with($route, 'participantes') ? 'active' : '' }}">
                                <a href="{{ route('participantes.index') }}">
                                    <i class="fas fa-users"></i>
                                    <p>Participantes</p>
                                </a>
                            </li>
                            @can('show-challenges')
                                <li class="{{ str_starts_with($route, 'challenges') ? 'active' : '' }}">
                                    <a href="{{ route('challenges.index') }}">
                                        <i class="fas fa-dumbbell"></i>
                                        <p>Challenges</p>
                                    </a>
                                </li>
                            @endcan
                            @can('show-challenges')
                                <li class="{{ str_starts_with($route, 'mesures') ? 'active' : '' }}">
                                    <a href="{{ route('mesures.index') }}">
                                        <i class="fas fa-ruler"></i>
                                        <p>Mesures</p>
                                    </a>
                                </li>
                            @endcan
                            @can('show-challenges')
                                <li class="{{ str_starts_with($route, 'presences') ? 'active' : '' }}">
                                    <a href="{{ route('presences.index') }}">
                                        <i class="fas fa-calendar-check"></i>
                                        <p>Présences</p>
                                    </a>
                                </li>
                            @endcan
                            @can('manage-media')
                                <li class="{{ str_starts_with($route, 'participant-media') ? 'active' : '' }}">
                                    <a href="{{ route('participant-media.index') }}">
                                        <i class="fas fa-photo-video"></i>
                                        <p>Médias</p>
                                    </a>
                                </li>
                            @endcan
                            @can('show-payments')
                                <li class="{{ str_starts_with($route, 'payments') ? 'active' : '' }}">
                                    <a href="{{ route('payments.index') }}">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <p>Paiements</p>
                                    </a>
                                </li>
                            @endcan
                            @can('show-recus')
                                <li class="{{ str_starts_with($route, 'recus') ? 'active' : '' }}">
                                    <a href="{{ route('recus.index') }}">
                                        <i class="fas fa-receipt"></i>
                                        <p>Reçus</p>
                                    </a>
                                </li>
                            @endcan
                            @can('create-participantes')
                                <li class="{{ $route === 'participantes.create' ? 'active' : '' }}">
                                    <a href="{{ route('participantes.create') }}">
                                        <i class="fas fa-user-plus"></i>
                                        <p>Inscription</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcan

                {{-- Catalogue & Articles --}}
                <li class="nav-item {{ $isArticles ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#menu-articles" class="{{ $isArticles ? '' : 'collapsed' }}" aria-expanded="{{ $isArticles ? 'true' : 'false' }}">
                        <i class="fas fa-boxes"></i>
                        <p>Catalogue & Articles</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $isArticles ? 'show' : '' }}" id="menu-articles">
                        <ul class="nav nav-collapse">
                            <li class="{{ str_starts_with($route, 'articles.create') || $route === 'articles.create' ? 'active' : '' }}">
                                <a href="{{ route('articles.create') }}">
                                    <i class="fas fa-cart-plus"></i>
                                    <p>Ajouter un article</p>
                                </a>
                            </li>
                            <li class="{{ str_starts_with($route, 'articles.index') ? 'active' : '' }}">
                                <a href="{{ route('articles.index') }}">
                                    <i class="fas fa-list"></i>
                                    <p>Gérer les articles</p>
                                </a>
                            </li>
                            <li class="{{ str_starts_with($route, 'categories') ? 'active' : '' }}">
                                <a href="{{ route('categories.index') }}">
                                    <i class="fas fa-tags"></i>
                                    <p>Catégories</p>
                                </a>
                            </li>
                            <li class="{{ str_starts_with($route, 'emplacements') ? 'active' : '' }}">
                                <a href="{{ route('emplacements.index') }}">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <p>Emplacements</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Fournisseurs --}}
                <li class="nav-item {{ $isFournisseurs ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#menu-fournisseurs" class="{{ $isFournisseurs ? '' : 'collapsed' }}" aria-expanded="{{ $isFournisseurs ? 'true' : 'false' }}">
                        <i class="fas fa-shipping-fast"></i>
                        <p>Fournisseurs</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $isFournisseurs ? 'show' : '' }}" id="menu-fournisseurs">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ route('fournisseurs.create') }}">
                                    <i class="fas fa-user-plus"></i>
                                    <p>Ajouter un fournisseur</p>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('fournisseurs.index') }}">
                                    <i class="fas fa-users-cog"></i>
                                    <p>Gérer les fournisseurs</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Facturation --}}
                <li class="nav-item {{ $isFactures ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#menu-factures" class="{{ $isFactures ? '' : 'collapsed' }}" aria-expanded="{{ $isFactures ? 'true' : 'false' }}">
                        <i class="fas fa-money-check-alt"></i>
                        <p>Facturation</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $isFactures ? 'show' : '' }}" id="menu-factures">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ route('factures.create') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <p>Créer une facture</p>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('factures.index') }}">
                                    <i class="fas fa-folder-open"></i>
                                    <p>Gérer les factures</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Statistiques --}}
                <li class="nav-item {{ $route === 'statistiques.index' ? 'active' : '' }}">
                    <a href="{{ route('statistiques.index') }}">
                        <i class="fas fa-chart-line"></i>
                        <p>Statistiques</p>
                    </a>
                </li>

                {{-- Administration --}}
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                    <h4 class="text-section">Administration</h4>
                </li>

                <li class="nav-item {{ $isAdmin ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#menu-admin" class="{{ $isAdmin ? '' : 'collapsed' }}" aria-expanded="{{ $isAdmin ? 'true' : 'false' }}">
                        <i class="fas fa-cogs"></i>
                        <p>Paramètres</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $isAdmin ? 'show' : '' }}" id="menu-admin">
                        <ul class="nav nav-collapse">

                            {{-- Utilisateurs --}}
                            <li class="nav-item {{ $isUsers ? 'active' : '' }}">
                                <a data-bs-toggle="collapse" href="#menu-users" class="{{ $isUsers ? '' : 'collapsed' }}" aria-expanded="{{ $isUsers ? 'true' : 'false' }}">
                                    <i class="fas fa-users"></i>
                                    <p>Utilisateurs</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse {{ $isUsers ? 'show' : '' }}" id="menu-users">
                                    <ul class="nav nav-collapse">
                                        <li><a href="{{ route('users.index') }}"><p>Liste des utilisateurs</p></a></li>
                                        <li><a href="{{ route('users.create') }}"><p>Créer un utilisateur</p></a></li>
                                    </ul>
                                </div>
                            </li>

                            {{-- Rôles --}}
                            <li class="nav-item {{ $isRoles ? 'active' : '' }}">
                                <a data-bs-toggle="collapse" href="#menu-roles" class="{{ $isRoles ? '' : 'collapsed' }}" aria-expanded="{{ $isRoles ? 'true' : 'false' }}">
                                    <i class="fas fa-user-shield"></i>
                                    <p>Rôles</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse {{ $isRoles ? 'show' : '' }}" id="menu-roles">
                                    <ul class="nav nav-collapse">
                                        <li><a href="{{ route('roles.index') }}"><p>Liste des rôles</p></a></li>
                                        <li><a href="{{ route('roles.create') }}"><p>Créer un rôle</p></a></li>
                                    </ul>
                                </div>
                            </li>

                            {{-- Permissions --}}
                            <li class="nav-item {{ $isPermissions ? 'active' : '' }}">
                                <a data-bs-toggle="collapse" href="#menu-permissions" class="{{ $isPermissions ? '' : 'collapsed' }}" aria-expanded="{{ $isPermissions ? 'true' : 'false' }}">
                                    <i class="fas fa-key"></i>
                                    <p>Permissions</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse {{ $isPermissions ? 'show' : '' }}" id="menu-permissions">
                                    <ul class="nav nav-collapse">
                                        <li><a href="{{ route('permissions.index') }}"><p>Liste des permissions</p></a></li>
                                        <li><a href="{{ route('permissions.create') }}"><p>Créer une permission</p></a></li>
                                    </ul>
                                </div>
                            </li>

                        </ul>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>
