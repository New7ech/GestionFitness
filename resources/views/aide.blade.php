@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Aide et Support')

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-header">
<div class="page-header">
    <h3 class="fw-bold mb-3">Centre d'Aide</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('accueil') }}">
                <i class="icon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Aide</a>
        </li>
    </ul>
</div>

{{-- Contenu principal de la page d'aide --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Bienvenue au Centre d'Aide</h4>
                <div class="card-category">
                    Trouvez ici des réponses à vos questions et des guides pour utiliser l'application.
                </div>
            </div>
            <div class="card-body">
                {{-- Section: Questions Fréquemment Posées (FAQ) --}}
                <section id="faq" class="mb-4">
                    <h5 class="fw-bold text-primary mb-3">Questions Fréquemment Posées (FAQ)</h5>
                    <div class="accordion accordion-bordered" id="accordionFAQ">
                        {{-- Exemple de question 1 --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    Comment ajouter un nouvel article ?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                 data-bs-parent="#accordionFAQ">
                                <div class="accordion-body">
                                    <p>Pour ajouter un nouvel article :</p>
                                    <ol>
                                        <li>Rendez-vous dans la section "Articles" via le menu latéral.</li>
                                        <li>Cliquez sur le bouton "Ajouter un Article".</li>
                                        <li>Remplissez le formulaire avec les informations de l'article (nom, description, prix, quantité, catégorie, etc.).</li>
                                        <li>Cliquez sur "Enregistrer l'Article".</li>
                                    </ol>
                                    <p>Assurez-vous que les catégories, fournisseurs et emplacements existent déjà si vous souhaitez les lier.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Exemple de question 2 --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Comment générer une facture ?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                 data-bs-parent="#accordionFAQ">
                                <div class="accordion-body">
                                    <p>La génération de facture se fait en plusieurs étapes :</p>
                                    <ul>
                                        <li>Allez dans la section "Factures".</li>
                                        <li>Cliquez sur "Créer une Facture".</li>
                                        <li>Renseignez les informations du client.</li>
                                        <li>Ajoutez les articles souhaités à la facture en spécifiant les quantités. Le système vérifiera le stock disponible.</li>
                                        <li>Vérifiez le récapitulatif (Montant HT, TVA, TTC).</li>
                                        <li>Sélectionnez le mode et le statut de paiement.</li>
                                        <li>Cliquez sur "Enregistrer la Facture". Vous pourrez ensuite la visualiser et la télécharger en PDF.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                         {{-- Exemple de question 3 --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Que faire si j'oublie mon mot de passe ?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                 data-bs-parent="#accordionFAQ">
                                <div class="accordion-body">
                                    Si vous avez oublié votre mot de passe, vous pouvez utiliser la fonctionnalité "Mot de passe oublié ?" présente sur la page de connexion. Un email vous sera envoyé avec les instructions pour réinitialiser votre mot de passe. Si vous ne recevez pas l'email, vérifiez votre dossier de courrier indésirable (spam) ou contactez un administrateur.
                                </div>
                            </div>
                        </div>
                        {{-- Ajoutez d'autres questions ici --}}
                    </div>
                </section>

                {{-- Section: Contacter le Support --}}
                <section id="support" class="mt-5">
                    <h5 class="fw-bold text-primary mb-3">Contacter le Support</h5>
                    <p>Si vous ne trouvez pas de réponse à votre question dans la FAQ ou si vous rencontrez un problème technique, n'hésitez pas à contacter notre équipe de support :</p>
                    <ul>
                        <li><strong>Email :</strong> <a href="mailto:support@example.com">support@example.com</a> (remplacez par votre email de support)</li>
                        <li><strong>Téléphone :</strong> +22X XX XX XX XX (remplacez par votre numéro de support)</li>
                        <li><strong>Horaires :</strong> Du Lundi au Vendredi, de 08h00 à 18h00.</li>
                    </ul>
                    <p class="mt-3">Veuillez fournir autant de détails que possible sur votre problème (captures d'écran, étapes pour reproduire l'erreur, etc.) afin que nous puissions vous assister plus efficacement.</p>
                </section>
            </div>
            <div class="card-footer text-center">
                <p class="text-muted mb-0">Nous espérons que ce centre d'aide vous sera utile !</p>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('styles')
<style>
    /* Styles optionnels pour améliorer la lisibilité de l'accordéon */
    .accordion-button:not(.collapsed) {
        color: var(--kai-primary-color); /* Ou une autre couleur de votre thème */
        background-color: var(--kai-primary-color-lightest); /* Une couleur de fond légère pour le bouton actif */
    }
    .accordion-button:focus {
        box-shadow: none; /* Éviter le box-shadow par défaut de Bootstrap sur le focus */
    }
    .accordion-item {
        border: 1px solid #eee; /* Bordure plus légère pour les items */
    }
    .accordion-item:first-of-type .accordion-button {
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
    }
     .accordion-item:last-of-type .accordion-button.collapsed {
        border-bottom-right-radius: .25rem;
        border-bottom-left-radius: .25rem;
    }
    .accordion-body ul, .accordion-body ol {
        padding-left: 1.5rem; /* Indentation pour les listes dans l'accordéon */
    }
</style>
@endpush
