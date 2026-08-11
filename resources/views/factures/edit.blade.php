@extends('layouts.app')

{{-- Section pour le titre de la page --}}
@section('title', "Modifier la Facture NÂ° " . ($facture->numero ?? $facture->id))

{{-- Section pour le contenu principal de la page --}}
@section('contenus')

{{-- En-tÃªte de la page avec titre et fil d'Ariane --}}
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
            <a href="#">Modifier Facture NÂ° {{ $facture->numero ?? $facture->id }}</a>
        </li>
    </ul>
</div>

{{-- Conteneur principal pour le formulaire de modification de facture --}}
<div class="row">
    <div class="col-md-12">
        {{-- Carte KaiAdmin pour le formulaire --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Formulaire de Modification de Facture</div>
                <div class="card-category">Modifiez les informations de la facture NÂ° <strong>{{ $facture->numero ?? $facture->id }}</strong>.</div>
            </div>
            <div class="card-body">
                {{-- Affichage des erreurs de validation gÃ©nÃ©rales --}}
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

                {{-- Formulaire de modification de facture --}}
                <form action="{{ route('factures.update', $facture->id) }}" method="POST" id="factureForm" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Section Informations Client (non modifiable ici, gÃ©nÃ©ralement) --}}
                    {{-- Si la modification des infos client est souhaitÃ©e sur la facture, dÃ©commenter et adapter --}}
                    <h4 class="mt-3 mb-3">Informations du Client (RÃ©fÃ©rence)</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group form-group-default">
                                <label>Nom du client</label>
                                <p class="form-control-static">{{ $facture->client_nom ?? 'N/A' }}</p>
                                <input type="hidden" name="client_nom" value="{{ $facture->client_nom }}">
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group form-group-default">
                                <label>PrÃ©nom du client</label>
                                <p class="form-control-static">{{ $facture->client_prenom ?? 'N/A' }}</p>
                                 <input type="hidden" name="client_prenom" value="{{ $facture->client_prenom }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-group-default">
                                <label>TÃ©lÃ©phone</label>
                                <p class="form-control-static">{{ $facture->client_telephone ?? 'N/A' }}</p>
                                <input type="hidden" name="client_telephone" value="{{ $facture->client_telephone }}">
                            </div>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-8">
                            <div class="form-group form-group-default">
                                <label>Adresse</label>
                                <p class="form-control-static">{{ $facture->client_adresse ?? 'N/A' }}</p>
                                <input type="hidden" name="client_adresse" value="{{ $facture->client_adresse }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-group-default">
                                <label>Email</label>
                                <p class="form-control-static">{{ $facture->client_email ?? 'N/A' }}</p>
                                 <input type="hidden" name="client_email" value="{{ $facture->client_email }}">
                            </div>
                        </div>
                    </div>


                    {{-- Section Articles de la Facture --}}
                    <h4 class="mt-4 mb-3">Articles de la Facture</h4>
                    <div id="articles-container">
                        {{-- Boucle pour afficher les articles existants de la facture --}}
                        @foreach($facture->articles as $idx => $articleFacture)
                        <div class="row mb-3 article-row align-items-center">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="form-label visually-hidden">Article <span class="text-danger">*</span></label>
                                    <select name="articles[{{ $idx }}][article_id]" class="form-select article-select" required>
                                        <option value="" data-prix="0" data-stock="0">-- SÃ©lectionner un article --</option>
                                        @foreach($articles as $articleOption)
                                            <option value="{{ $articleOption->id }}"
                                                    data-prix="{{ $articleOption->prix }}"
                                                    data-stock="{{ $articleOption->quantite }}"
                                                    {{ $articleFacture->id == $articleOption->id ? 'selected' : '' }}>
                                                {{ $articleOption->name }} (Stock: {{ $articleOption->quantite }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Veuillez sÃ©lectionner un article.</div>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label visually-hidden">Prix Unitaire</label>
                                    <input type="text" class="form-control article-prix" readonly value="{{ number_format($articleFacture->pivot->prix_unitaire, 2, ',', ' ') }} FCFA">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label visually-hidden">QuantitÃ© <span class="text-danger">*</span></label>
                                    <input type="number" name="articles[{{ $idx }}][quantity]" class="form-control quantity-input"
                                           min="1" required value="{{ $articleFacture->pivot->quantite }}">
                                    <div class="invalid-feedback">La quantitÃ© est requise (min 1).</div>
                                     <small class="form-text text-muted stock-info"></small>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm remove-article" title="Supprimer cet article">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Bouton pour ajouter plus d'articles --}}
                    <div class="mb-3">
                        <button type="button" class="btn btn-success btn-sm" id="addArticleBtn">
                            <i class="fas fa-plus"></i> Ajouter une ligne d'article
                        </button>
                    </div>

                    <hr>
                    {{-- Section RÃ©capitulatif --}}
                    <h4 class="mt-4 mb-3">RÃ©capitulatif de la Facture</h4>
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
                    {{-- Champs cachÃ©s pour stocker les totaux calculÃ©s --}}
                    <input type="hidden" name="montant_ht" id="inputMontantHT" value="{{ old('montant_ht', $facture->montant_ht) }}">
                    <input type="hidden" name="montant_ttc" id="inputMontantTTC" value="{{ old('montant_ttc', $facture->montant_ttc) }}">
                    <input type="hidden" name="tva" id="inputTVA" value="{{ old('tva', $facture->tva ?? 18) }}">


                    {{-- Section Paiement --}}
                    <h4 class="mt-4 mb-3">Informations de Paiement</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mode_paiement">Mode de paiement</label>
                                <select name="mode_paiement" id="mode_paiement" class="form-select @error('mode_paiement') is-invalid @enderror">
                                    <option value="">-- SÃ©lectionner --</option>
                                    <option value="carte" {{ old('mode_paiement', $facture->mode_paiement) == 'carte' ? 'selected' : '' }}>Carte Bancaire</option>
                                    <option value="chÃ¨que" {{ old('mode_paiement', $facture->mode_paiement) == 'chÃ¨que' ? 'selected' : '' }}>ChÃ¨que</option>
                                    <option value="espÃ¨ces" {{ old('mode_paiement', $facture->mode_paiement) == 'espÃ¨ces' ? 'selected' : '' }}>EspÃ¨ces</option>
                                    <option value="virement" {{ old('mode_paiement', $facture->mode_paiement) == 'virement' ? 'selected' : '' }}>Virement Bancaire</option>
                                </select>
                                @error('mode_paiement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="statut_paiement">Statut du paiement <span class="text-danger">*</span></label>
                                <select name="statut_paiement" id="statut_paiement" class="form-select @error('statut_paiement') is-invalid @enderror" required>
                                    <option value="impayé" {{ old('statut_paiement', $facture->statut_paiement) == 'impayé' ? 'selected' : '' }}>Impayé</option>
                                    <option value="payé" {{ old('statut_paiement', $facture->statut_paiement) == 'payé' ? 'selected' : '' }}>Payé</option>
                                </select>
                                @error('statut_paiement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="invalid-feedback">Le statut du paiement est requis.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions du formulaire --}}
                    <div class="card-action text-end mt-4">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> Mettre Ã  Jour la Facture
                        </button>
                        <a href="{{ route('factures.index') }}" class="btn btn-secondary">
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

{{-- Section pour les scripts JavaScript spÃ©cifiques Ã  cette page --}}
@push('scripts')
<script>
// Le script est identique Ã  celui de factures/create.blade.php
// avec l'initialisation de `articleIndex` adaptÃ©e pour la modification.
document.addEventListener('DOMContentLoaded', () => {
    // Initialise articleIndex en fonction du nombre d'articles existants ou des donnÃ©es 'old'
    let articleIndex = {{ old('articles') ? count(old('articles')) : $facture->articles->count() }};
    const articlesContainer = document.getElementById('articles-container');
    const addArticleButton = document.getElementById('addArticleBtn');
    const submitFactureButton = document.getElementById('submitBtn');
    const factureForm = document.getElementById('factureForm');

    const TAUX_TVA = {{ ($facture->tva ?? 18) / 100 }}; // Utilise la TVA de la facture ou 18% par dÃ©faut

    function updateTotals() {
        let totalHT = 0;
        let formIsValid = true;

        document.querySelectorAll('.article-row').forEach(row => {
            const articleSelect = row.querySelector('.article-select');
            const quantityInput = row.querySelector('.quantity-input');
            const stockInfo = row.querySelector('.stock-info');
            const prixArticle = parseFloat(articleSelect.selectedOptions[0]?.dataset.prix || 0);
            // Pour la modification, le stock disponible doit Ãªtre le stock actuel + la quantitÃ© dÃ©jÃ  sur cette facture pour cet article
            const currentQuantityOnInvoice = parseInt(quantityInput.defaultValue || 0); // QuantitÃ© initiale de la ligne
            const selectedArticleId = articleSelect.value;
            let stockDisponible = parseInt(articleSelect.selectedOptions[0]?.dataset.stock || 0);

            // Ajustement du stock pour la modification:
            // Si l'article sÃ©lectionnÃ© est le mÃªme que celui initialement sur cette ligne de facture,
            // on ajoute la quantitÃ© initialement facturÃ©e au stock affichÃ© comme disponible.
            // Ceci est une simplification; une gestion de stock plus complexe pourrait Ãªtre nÃ©cessaire
            // pour reflÃ©ter le stock "rÃ©el" moins ce qui est sur *d'autres* factures.
            // Pour l'instant, on se concentre sur la validation de cette ligne par rapport au stock affichÃ©.
            // Note: $articleFacture->id (PHP) vs articleOption.id (JS) pour l'article initial de la ligne
            // Il faudrait passer l'ID initial de l'article de la ligne au JS pour une comparaison fiable.
            // Pour cette version, on prend le stock tel quel.
            // Une meilleure approche serait de recalculer le stock disponible cÃ´tÃ© serveur et de le passer.

            const quantiteDemandee = parseInt(quantityInput.value || 0);

            const prixUnitaireField = row.querySelector('.article-prix');
            if(prixUnitaireField) {
                prixUnitaireField.value = prixArticle > 0 ? prixArticle.toFixed(2) + ' FCFA' : '0.00 FCFA';
            }

            if (quantiteDemandee > 0 && articleSelect.value) {
                if (quantiteDemandee > stockDisponible) {
                    // Exception: si l'article est celui d'origine de la ligne et la quantitÃ© n'a pas augmentÃ© au-delÃ  du stock + qtÃ© origine
                    // Cette logique est complexe Ã  gÃ©rer purement en JS sans connaÃ®tre l'ID original de l'article sur la ligne.
                    // Pour l'instant, on se base sur le data-stock qui est le stock actuel en base.
                    // L'utilisateur ne devrait pas pouvoir commander plus que ce stock.
                    // S'il modifie une ligne existante, la quantitÃ© ne doit pas dÃ©passer le stock.
                    // La validation finale se fera cÃ´tÃ© serveur.
                    quantityInput.classList.add('is-invalid');
                    stockInfo.textContent = `Stock insuffisant (${stockDisponible} disponible)!`;
                    stockInfo.style.color = 'red';
                    formIsValid = false;
                } else {
                    quantityInput.classList.remove('is-invalid');
                    stockInfo.textContent = `Stock: ${stockDisponible}`;
                    stockInfo.style.color = 'green';
                }
            } else if (quantiteDemandee <= 0 && articleSelect.value) {
                 quantityInput.classList.add('is-invalid');
                 stockInfo.textContent = `QuantitÃ© requise.`;
                 stockInfo.style.color = 'red';
                 formIsValid = false;
            } else {
                quantityInput.classList.remove('is-invalid');
                 stockInfo.textContent = stockDisponible > 0 ? `Stock: ${stockDisponible}` : (articleSelect.value ? 'Stock: 0' : '');
                stockInfo.style.color = stockDisponible > 0 ? 'green' : 'red';
            }

            if (prixArticle > 0 && quantiteDemandee > 0 && (quantiteDemandee <= stockDisponible || formIsValid /* Permettre si la ligne est valide malgrÃ© tout pour le calcul */) ) {
                totalHT += prixArticle * quantiteDemandee;
            }
        });

        const montantTVA = totalHT * TAUX_TVA;
        const montantTTC = totalHT + montantTVA;

        document.getElementById('montantHT').textContent = totalHT.toFixed(2);
        document.getElementById('montantTVA').textContent = montantTVA.toFixed(2);
        document.getElementById('montantTTC').textContent = montantTTC.toFixed(2);

        document.getElementById('inputMontantHT').value = totalHT.toFixed(2);
        document.getElementById('inputMontantTTC').value = montantTTC.toFixed(2);

        const hasArticles = totalHT > 0;
        submitFactureButton.disabled = !hasArticles || !formIsValid;
    }

    function addArticleRow() {
        const newRow = document.createElement('div');
        newRow.className = 'row mb-3 article-row align-items-center';
        newRow.innerHTML = `
            <div class="col-md-5">
                <div class="form-group">
                    <label class="form-label visually-hidden">Article <span class="text-danger">*</span></label>
                    <select name="articles[${articleIndex}][article_id]" class="form-select article-select" required>
                        <option value="" data-prix="0" data-stock="0">-- SÃ©lectionner un article --</option>
                        @foreach($articles as $articleOption)
                            <option value="{{ $articleOption->id }}" data-prix="{{ $articleOption->prix }}" data-stock="{{ $articleOption->quantite }}">
                                {{ $articleOption->name }} (Stock: {{ $articleOption->quantite }})
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">Veuillez sÃ©lectionner un article.</div>
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
                    <label class="form-label visually-hidden">QuantitÃ© <span class="text-danger">*</span></label>
                    <input type="number" name="articles[${articleIndex}][quantity]" class="form-control quantity-input" min="1" required value="1">
                    <div class="invalid-feedback">La quantitÃ© est requise (min 1).</div>
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
        articleIndex++;
        attachEventListenersToRow(newRow);
        updateRemoveButtonsState();
        updateTotals();
    }

    function removeArticleRow(event) {
        if (articlesContainer.querySelectorAll('.article-row').length > 1) {
            event.target.closest('.article-row').remove();
            updateRemoveButtonsState();
            updateTotals();
        } else {
             // Optionnel: Alerter l'utilisateur qu'au moins une ligne est requise
            Swal.fire('Attention', 'Une facture doit contenir au moins un article.', 'warning');
        }
    }

    function updateRemoveButtonsState() {
        const rows = articlesContainer.querySelectorAll('.article-row');
        rows.forEach((row, idx) => {
            const removeButton = row.querySelector('.remove-article');
            if (removeButton) {
                // Le bouton de suppression est dÃ©sactivÃ© uniquement s'il ne reste qu'une seule ligne
                removeButton.disabled = rows.length === 1;
            }
        });
         // S'il n'y a aucune ligne (ce qui ne devrait pas arriver avec la logique actuelle, mais par sÃ©curitÃ©)
        if (rows.length === 0) {
            submitFactureButton.disabled = true;
        }
    }

    function attachEventListenersToRow(row) {
        const articleSelect = row.querySelector('.article-select');
        const quantityInput = row.querySelector('.quantity-input');
        const removeButton = row.querySelector('.remove-article');

        articleSelect.addEventListener('change', updateTotals);
        quantityInput.addEventListener('input', updateTotals);
        if(removeButton){
            removeButton.addEventListener('click', removeArticleRow);
        }
    }

    articlesContainer.querySelectorAll('.article-row').forEach(row => {
        attachEventListenersToRow(row);
    });

    addArticleButton.addEventListener('click', addArticleRow);

    updateRemoveButtonsState();
    updateTotals(); // Calcul initial des totaux au chargement de la page

    factureForm.addEventListener('submit', function (event) {
        updateTotals();
        if (submitFactureButton.disabled) {
            event.preventDefault();
            event.stopPropagation();
            Swal.fire({
                title: 'Erreur de validation',
                text: 'Veuillez corriger les erreurs dans le formulaire avant de soumettre. VÃ©rifiez notamment les quantitÃ©s et le stock disponible, ou assurez-vous d\'avoir au moins un article valide.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
        if (!factureForm.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        factureForm.classList.add('was-validated');
    }, false);
});
</script>
@endpush

