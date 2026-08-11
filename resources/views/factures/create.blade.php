@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', 'Créer une Nouvelle Facture')

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tête de la page avec titre et fil d'Ariane --}}
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Factures</h3>
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
            <a href="{{ route('factures.index') }}">Factures</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Créer une Facture</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour le formulaire de création de facture --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour le formulaire --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Formulaire de Création de Facture</div>
                <div class="card-category">Remplissez les informations ci-dessous pour générer une nouvelle facture.</div>
            </div>
            <div class="card-body">
                {{-- Affichage des erreurs de validation générales --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Erreur !</strong> Veuillez corriger les erreurs ci-dessous.
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Formulaire de création de facture --}}
                <form action="{{ route('factures.store') }}" method="POST" id="factureForm" class="needs-validation" novalidate>
                    @csrf

                    {{-- Section Informations Client --}}
                    <h4 class="mt-3 mb-3">Informations du Client</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_nom">Nom du client <span class="text-danger">*</span></label>
                                <input type="text" name="client_nom" id="client_nom" class="form-control @error('client_nom') is-invalid @enderror"
                                       required value="{{ old('client_nom') }}" placeholder="Nom">
                                @error('client_nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Le nom du client est requis.</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_prenom">Prénom du client</label>
                                <input type="text" name="client_prenom" id="client_prenom" class="form-control @error('client_prenom') is-invalid @enderror"
                                       value="{{ old('client_prenom') }}" placeholder="Prénom">
                                @error('client_prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_telephone">Téléphone</label>
                                <input type="tel" name="client_telephone" id="client_telephone" class="form-control @error('client_telephone') is-invalid @enderror"
                                       value="{{ old('client_telephone') }}" placeholder="Numéro de téléphone">
                                @error('client_telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="client_adresse">Adresse</label>
                                <input type="text" name="client_adresse" id="client_adresse" class="form-control @error('client_adresse') is-invalid @enderror"
                                       value="{{ old('client_adresse') }}" placeholder="Adresse complète">
                                @error('client_adresse') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_email">Email</label>
                                <input type="email" name="client_email" id="client_email" class="form-control @error('client_email') is-invalid @enderror"
                                       value="{{ old('client_email') }}" placeholder="adresse@email.com">
                                @error('client_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                 <div class="invalid-feedback">Veuillez fournir une adresse email valide.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Section Articles de la Facture --}}
                    <h4 class="mt-4 mb-3">Articles de la Facture</h4>
                    <div id="articles-container">
                        {{-- La première ligne d'article est générée par défaut --}}
                        <div class="row mb-3 article-row align-items-center">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="form-label">Article <span class="text-danger">*</span></label>
                                    <select name="articles[0][article_id]" class="form-select article-select" required>
                                        <option value="" data-prix="0" data-stock="0">-- Sélectionner un article --</option>
                                        @foreach($articles as $article)
                                            <option value="{{ $article->id }}" data-prix="{{ $article->prix }}" data-stock="{{ $article->quantite }}">
                                                {{ $article->name }} (Stock: {{ $article->quantite }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Veuillez sélectionner un article.</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Prix Unitaire</label>
                                    <input type="text" class="form-control article-prix" readonly value="0.00 FCFA">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Quantité <span class="text-danger">*</span></label>
                                    <input type="number" name="articles[0][quantity]" class="form-control quantity-input" min="1" required value="1">
                                    <div class="invalid-feedback">La quantité est requise (min 1).</div>
                                    <small class="form-text text-muted stock-info"></small>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                {{-- Le premier bouton de suppression est désactivé par défaut --}}
                                <button type="button" class="btn btn-danger btn-sm remove-article" disabled title="Supprimer cet article">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Bouton pour ajouter plus d'articles --}}
                    <div class="mb-3">
                        <button type="button" class="btn btn-success btn-sm" id="addArticleBtn">
                            <i class="fas fa-plus"></i> Ajouter une ligne d'article
                        </button>
                    </div>

                    <hr>
                    {{-- Section Récapitulatif --}}
                    <h4 class="mt-4 mb-3">Récapitulatif de la Facture</h4>
                    <div class="row p-3 rounded mb-4" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                        <div class="col-md-4">
                            <p class="mb-1">Montant HT Total:</p>
                            <h5 class="fw-bold"><span id="montantHT">0.00</span> FCFA</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1">TVA (18%):</p>
                            <h5 class="fw-bold"><span id="montantTVA">0.00</span> FCFA</h5>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1">Montant TTC Total:</p>
                            <h5 class="fw-bold text-primary"><span id="montantTTC">0.00</span> FCFA</h5>
                        </div>
                    </div>
                     {{-- Champs cachés pour stocker les totaux calculés --}}
                    <input type="hidden" name="montant_ht" id="inputMontantHT" value="0">
                    <input type="hidden" name="montant_ttc" id="inputMontantTTC" value="0">
                    <input type="hidden" name="tva" id="inputTVA" value="18">


                    {{-- Section Paiement --}}
                    <h4 class="mt-4 mb-3">Informations de Paiement</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mode_paiement">Mode de paiement</label>
                                <select name="mode_paiement" id="mode_paiement" class="form-select @error('mode_paiement') is-invalid @enderror">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="carte" {{ old('mode_paiement') == 'carte' ? 'selected' : '' }}>Carte Bancaire</option>
                                    <option value="chèque" {{ old('mode_paiement') == 'chèque' ? 'selected' : '' }}>Chèque</option>
                                    <option value="espèces" {{ old('mode_paiement') == 'espèces' ? 'selected' : '' }}>Espèces</option>
                                    <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement Bancaire</option>
                                </select>
                                @error('mode_paiement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="statut_paiement">Statut du paiement <span class="text-danger">*</span></label>
                                <select name="statut_paiement" id="statut_paiement" class="form-select @error('statut_paiement') is-invalid @enderror" required>
                                    <option value="impayé" {{ old('statut_paiement', 'impayé') == 'impayé' ? 'selected' : '' }}>Impayé</option>
                                    <option value="payé" {{ old('statut_paiement') == 'payé' ? 'selected' : '' }}>Payé</option>
                                </select>
                                @error('statut_paiement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Le statut du paiement est requis.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions du formulaire --}}
                    <div class="card-action text-end mt-4">
                        <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                            <i class="fas fa-save"></i> Enregistrer la Facture
                        </button>
                        <a href="{{ route('factures.index') }}" class="btn btn-danger">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

{{-- Section pour les scripts JavaScript spécifiques à cette page --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let articleIndex = {{ old('articles') ? count(old('articles')) : 1 }}; // Gère les anciennes entrées en cas d'erreur de validation
    const articlesContainer = document.getElementById('articles-container');
    const addArticleButton = document.getElementById('addArticleBtn');
    const submitFactureButton = document.getElementById('submitBtn');
    const factureForm = document.getElementById('factureForm');

    // Taux de TVA (par exemple, 18%)
    const TAUX_TVA = 0.18;

    // Fonction pour mettre à jour et afficher les totaux
    function updateTotals() {
        let totalHT = 0;
        let formIsValid = true; // Indicateur pour la validité du formulaire

        document.querySelectorAll('.article-row').forEach(row => {
            const articleSelect = row.querySelector('.article-select');
            const quantityInput = row.querySelector('.quantity-input');
            const stockInfo = row.querySelector('.stock-info');
            const prixArticle = parseFloat(articleSelect.selectedOptions[0]?.dataset.prix || 0);
            const stockDisponible = parseInt(articleSelect.selectedOptions[0]?.dataset.stock || 0);
            const quantiteDemandee = parseInt(quantityInput.value || 0);

            // Met à jour le champ prix unitaire affiché
            const prixUnitaireField = row.querySelector('.article-prix');
            if(prixUnitaireField) {
                prixUnitaireField.value = prixArticle > 0 ? prixArticle.toFixed(2) + ' FCFA' : '0.00 FCFA';
            }

            // Validation du stock
            if (quantiteDemandee > 0 && articleSelect.value) {
                 if (quantiteDemandee > stockDisponible) {
                    quantityInput.classList.add('is-invalid');
                    stockInfo.textContent = `Stock insuffisant (${stockDisponible} disponible)!`;
                    stockInfo.style.color = 'red';
                    formIsValid = false; // Le formulaire n'est pas valide
                } else {
                    quantityInput.classList.remove('is-invalid');
                    stockInfo.textContent = `Stock: ${stockDisponible}`;
                    stockInfo.style.color = 'green';
                }
            } else if (quantiteDemandee <= 0 && articleSelect.value) {
                 quantityInput.classList.add('is-invalid'); // Quantité doit être > 0 si article sélectionné
                 stockInfo.textContent = `Quantité requise.`;
                 stockInfo.style.color = 'red';
                 formIsValid = false;
            }
             else {
                quantityInput.classList.remove('is-invalid');
                stockInfo.textContent = stockDisponible > 0 ? `Stock: ${stockDisponible}` : (articleSelect.value ? 'Stock: 0' : '');
                stockInfo.style.color = stockDisponible > 0 ? 'green' : 'red';
            }


            if (prixArticle > 0 && quantiteDemandee > 0 && quantiteDemandee <= stockDisponible) {
                totalHT += prixArticle * quantiteDemandee;
            }
        });

        const montantTVA = totalHT * TAUX_TVA;
        const montantTTC = totalHT + montantTVA;

        document.getElementById('montantHT').textContent = totalHT.toFixed(2);
        document.getElementById('montantTVA').textContent = montantTVA.toFixed(2);
        document.getElementById('montantTTC').textContent = montantTTC.toFixed(2);

        // Mettre à jour les champs cachés
        document.getElementById('inputMontantHT').value = totalHT.toFixed(2);
        document.getElementById('inputMontantTTC').value = montantTTC.toFixed(2);


        // Activer ou désactiver le bouton de soumission
        // Le bouton est désactivé si aucun article valide n'est ajouté OU si le formulaire a des erreurs
        const hasArticles = totalHT > 0;
        submitFactureButton.disabled = !hasArticles || !formIsValid;
    }

    // Fonction pour ajouter une nouvelle ligne d'article au formulaire
    function addArticleRow() {
        const newRow = document.createElement('div');
        newRow.className = 'row mb-3 article-row align-items-center';
        // Contenu HTML de la nouvelle ligne d'article
        newRow.innerHTML = `
            <div class="col-md-5">
                <div class="form-group">
                    <label class="form-label visually-hidden">Article <span class="text-danger">*</span></label>
                    <select name="articles[${articleIndex}][article_id]" class="form-select article-select" required>
                        <option value="" data-prix="0" data-stock="0">-- Sélectionner un article --</option>
                        @foreach($articles as $article)
                            <option value="{{ $article->id }}" data-prix="{{ $article->prix }}" data-stock="{{ $article->quantite }}">
                                {{ $article->name }} (Stock: {{ $article->quantite }})
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">Veuillez sélectionner un article.</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label visually-hidden">Prix Unitaire</label>
                     <input type="text" class="form-control article-prix" readonly value="0.00 FCFA">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label visually-hidden">Quantité <span class="text-danger">*</span></label>
                    <input type="number" name="articles[${articleIndex}][quantity]" class="form-control quantity-input" min="1" required value="1">
                    <div class="invalid-feedback">La quantité est requise (min 1).</div>
                    <small class="form-text text-muted stock-info"></small>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-article" title="Supprimer cet article">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        articlesContainer.appendChild(newRow);
        articleIndex++; // Incrémenter l'index pour la prochaine ligne
        attachEventListenersToRow(newRow); // Attacher les écouteurs d'événements à la nouvelle ligne
        updateRemoveButtonsState(); // Mettre à jour l'état des boutons de suppression
        updateTotals(); // Recalculer les totaux
    }

    // Fonction pour supprimer une ligne d'article
    function removeArticleRow(event) {
        // S'assurer qu'il y a plus d'une ligne avant de supprimer
        if (articlesContainer.querySelectorAll('.article-row').length > 1) {
            event.target.closest('.article-row').remove();
            updateRemoveButtonsState(); // Mettre à jour l'état des boutons
            updateTotals(); // Recalculer les totaux
        }
    }

    // Mettre à jour l'état (activé/désactivé) des boutons de suppression
    function updateRemoveButtonsState() {
        const rows = articlesContainer.querySelectorAll('.article-row');
        rows.forEach((row, idx) => {
            const removeButton = row.querySelector('.remove-article');
            if (removeButton) {
                removeButton.disabled = rows.length === 1; // Désactiver si c'est la seule ligne
            }
        });
    }

    // Attacher les écouteurs d'événements aux éléments d'une ligne d'article
    function attachEventListenersToRow(row) {
        const articleSelect = row.querySelector('.article-select');
        const quantityInput = row.querySelector('.quantity-input');
        const removeButton = row.querySelector('.remove-article');

        articleSelect.addEventListener('change', updateTotals);
        quantityInput.addEventListener('input', updateTotals);
        if(removeButton) {
            removeButton.addEventListener('click', removeArticleRow);
        }
    }

    // Attacher les écouteurs aux lignes existantes au chargement de la page
    articlesContainer.querySelectorAll('.article-row').forEach(row => {
        attachEventListenersToRow(row);
    });

    // Écouteur pour le bouton "Ajouter un article"
    addArticleButton.addEventListener('click', addArticleRow);

    // Initialiser l'état des boutons et les totaux au chargement
    updateRemoveButtonsState();
    updateTotals();


    // Gestion de la soumission du formulaire pour la validation Bootstrap
    // Le script global dans app.blade.php s'en charge déjà si 'needs-validation' et 'novalidate' sont sur le form.
    // Cependant, nous devons nous assurer que notre propre logique de validation (stock) est vérifiée avant soumission.
    factureForm.addEventListener('submit', function (event) {
        // Appel explicite pour revérifier avant soumission, au cas où.
        updateTotals();

        // Le bouton submitBtn.disabled est déjà géré par updateTotals()
        // Si le bouton est activé, cela signifie que nos validations JS personnalisées sont passées.
        // La validation Bootstrap standard prendra alors le relais pour les champs `required`, `type=email`, etc.
        if (submitFactureButton.disabled) {
            event.preventDefault(); // Empêche la soumission si notre logique JS trouve des erreurs
            event.stopPropagation();
            // On peut ajouter un message d'alerte plus global si besoin
            Swal.fire({
                title: 'Erreur de validation',
                text: 'Veuillez corriger les erreurs dans le formulaire avant de soumettre. Vérifiez notamment les quantités et le stock disponible.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
        // Laisser la validation Bootstrap faire son travail si nos conditions sont remplies
        if (!factureForm.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        factureForm.classList.add('was-validated');

    }, false);


});
</script>
@endpush
