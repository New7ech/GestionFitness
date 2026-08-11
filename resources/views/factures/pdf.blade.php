<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture N° {{ $facture->numero ?? $facture->id }}</title>
    {{-- Utilisation d'une police web standard pour une meilleure compatibilité PDF --}}
    <link href="https://fonts.googleapis.com/css?family=DejaVuSans&display=swap" rel="stylesheet">
    <style>
        /* Variables CSS pour les couleurs et espacements, facilitant la maintenance */
        :root {
            --primary-color: #2c3e50; /* Bleu nuit */
            --secondary-color: #3498db; /* Bleu clair */
            --text-color: #333333;
            --light-gray: #f8f9fa;
            --medium-gray: #dddddd; /* Gris plus visible pour les bordures */
            --dark-gray: #555555;
            --success-color: #27ae60; /* Vert */
            --danger-color: #c0392b; /* Rouge */
            --font-family: 'DejaVuSans', sans-serif; /* Police compatible PDF avec caractères spéciaux */
        }

        /* Réinitialisation et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            color: var(--text-color);
            font-size: 12px; /* Taille de police réduite pour PDF */
            background-color: #ffffff; /* Fond blanc pour l'impression */
            line-height: 1.5; /* Interligne amélioré */
        }

        /* Conteneur principal de la facture */
        .invoice-container {
            background-color: #ffffff;
            max-width: 800px; /* ou width: 100% pour s'adapter à la page A4 */
            margin: 0 auto; /* Centrage si max-width est utilisé */
            border: 1px solid var(--medium-gray);
            /* padding: 20px; Supprimé pour que le contenu touche les bords si nécessaire */
        }

        /* Bandeau supérieur */
        .header-strip {
            background-color: var(--primary-color);
            padding: 10px 20px;
            color: #ffffff;
            /* Utilisation de table pour une meilleure compatibilité avec les moteurs PDF */
            display: table;
            width: 100%;
        }
        .header-strip .logo, .header-strip .invoice-id {
            display: table-cell;
            vertical-align: middle;
        }
        .header-strip .logo {
            font-weight: bold;
            font-size: 16px;
            text-align: left;
        }
        .header-strip .invoice-id {
            font-size: 12px;
            text-align: right;
            background-color: rgba(255,255,255,0.1);
            padding: 3px 8px;
            border-radius: 10px;
        }

        /* En-tête de la facture (Titre "Facture" et date) */
        .invoice-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--medium-gray);
            display: table;
            width: 100%;
        }
        .invoice-header .title, .invoice-header .date-issued {
            display: table-cell;
            vertical-align: middle;
        }
        .invoice-header .title {
            font-size: 22px; /* Taille ajustée */
            color: var(--primary-color);
            font-weight: bold;
            text-align: left;
        }
        .invoice-header .date-issued {
            font-size: 12px;
            color: var(--dark-gray);
            text-align: right;
        }

        /* Informations de l'entreprise et du client */
        .info-section {
            padding: 15px 20px;
            display: table; /* Utilisation de table pour la disposition en colonnes */
            width: 100%;
            border-bottom: 1px solid var(--medium-gray);
        }
        .company-details, .client-details {
            display: table-cell;
            width: 50%; /* Chaque colonne prend 50% */
            vertical-align: top;
            padding-right: 10px; /* Espacement entre les colonnes */
        }
        .client-details {
            padding-right: 0;
            padding-left: 10px;
        }
        .info-section h3 {
            font-size: 14px;
            color: var(--primary-color);
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid var(--medium-gray);
        }
        .info-section p {
            margin-bottom: 3px; /* Espacement réduit entre les lignes d'info */
        }
        .info-section strong { /* Pour les labels comme "Nom:", "Adresse:" */
            font-weight: bold; /* DejaVu Sans a différents poids, s'assurer que 'bold' est chargé */
        }


        /* Métadonnées de la facture (Numéro, Statut, Mode de paiement) */
        .invoice-meta {
            padding: 10px 20px;
            background-color: var(--light-gray);
            border-bottom: 1px solid var(--medium-gray);
        }
        .invoice-meta p {
            margin-bottom: 5px;
            font-size: 12px;
        }
        .invoice-meta .status {
            padding: 3px 8px;
            border-radius: 4px; /* Coins moins arrondis pour un look pro */
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #ffffff; /* Texte blanc pour un meilleur contraste */
        }
        .invoice-meta .status.paid { background-color: var(--success-color); }
        .invoice-meta .status.pending { background-color: var(--danger-color); } /* Rouge pour impayé */
        .invoice-meta .status.default { background-color: var(--dark-gray); }


        /* Titre pour la section des articles */
        .section-title {
            text-align: center;
            color: var(--primary-color);
            margin: 15px 0 10px;
            font-size: 16px;
            font-weight: bold;
        }

        /* Tableau des articles */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th, .items-table td {
            border: 1px solid var(--medium-gray); /* Bordures visibles pour toutes les cellules */
            padding: 8px; /* Espacement interne des cellules */
            text-align: left;
        }
        .items-table thead {
            background-color: var(--primary-color);
            color: #ffffff;
        }
        .items-table th {
            font-weight: bold; /* Assurer que les en-têtes sont en gras */
        }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }


        /* Section des totaux */
        .totals-section {
            padding: 0 20px; /* Aligner avec le reste du contenu */
            margin-top: 10px;
            /* Utilisation de table pour aligner à droite */
        }
        .totals-table {
            width: 50%; /* Ou une largeur fixe si préféré */
            margin-left: auto; /* Aligner à droite */
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px;
            border: 1px solid var(--medium-gray);
        }
        .totals-table tr:last-child td {
            font-weight: bold;
            font-size: 14px;
            color: var(--primary-color);
        }
        .totals-table .label { text-align: right; font-weight: bold; }
        .totals-table .value { text-align: right; }


        /* Pied de page */
        .invoice-footer {
            text-align: center;
            margin: 20px;
            padding-top: 15px;
            font-size: 10px;
            color: var(--dark-gray);
            border-top: 1px solid var(--medium-gray);
        }
        .invoice-footer strong { color: var(--primary-color); }

        /* Classes utilitaires pour l'alignement du texte, si nécessaire ailleurs */
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

    </style>
</head>
<body>
    {{-- Conteneur principal de la facture --}}
    <div class="invoice-container">
        {{-- Bandeau supérieur avec logo et numéro de facture --}}
        <div class="header-strip">
            <div class="logo">{{ config('app.name', 'Entreprise XYZ') }}</div>
            <div class="invoice-id">FACTURE N° {{ $facture->numero ?? $facture->id }}</div>
        </div>

        {{-- En-tête de la facture (Titre "Facture" et date d'émission) --}}
        <div class="invoice-header">
            <div class="title">Facture</div>
            <div class="date-issued">Émise le : {{ $facture->date_facture ? \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') : 'N/A' }}</div>
        </div>

        {{-- Section des informations de l'entreprise et du client --}}
        <div class="info-section">
            <div class="company-details">
                <h3>Votre Entreprise</h3>
                {{-- TODO: Rendre ces informations dynamiques via la configuration ou une table de paramètres --}}
                <p><strong>Nom :</strong> {{ config('invoice.company_name', 'Entreprise XYZ') }}</p>
                <p><strong>Adresse :</strong> {{ config('invoice.company_address', '123 Rue de l\'Exemple, Ville') }}</p>
                <p><strong>Téléphone :</strong> {{ config('invoice.company_phone', '+226 XX XX XX XX') }}</p>
                <p><strong>Email :</strong> {{ config('invoice.company_email', 'contact@votredomaine.com') }}</p>
                {{-- <p><strong>N° IFU :</strong> {{ config('invoice.company_ifu', '000XXXXXX') }}</p> --}}
            </div>
            <div class="client-details">
                <h3>Client</h3>
                <p><strong>Nom :</strong> {{ $facture->client_nom ?? 'N/A' }} {{ $facture->client_prenom ?? '' }}</p>
                <p><strong>Adresse :</strong> {{ $facture->client_adresse ?? 'N/A' }}</p>
                <p><strong>Téléphone :</strong> {{ $facture->client_telephone ?? 'N/A' }}</p>
                <p><strong>Email :</strong> {{ $facture->client_email ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Métadonnées de la facture --}}
        <div class="invoice-meta">
            <p><strong>Numéro de Facture :</strong> {{ $facture->numero ?? $facture->id }}</p>
            <p>
                <strong>Statut du Paiement :</strong>
                @php
                    $statut = strtolower($facture->statut_paiement ?? 'impayé');
                    $statutClass = 'default';
                    if ($statut === 'payé') $statutClass = 'paid';
                    elseif ($statut === 'impayé') $statutClass = 'pending';
                @endphp
                <span class="status {{ $statutClass }}">
                    {{ ucfirst($facture->statut_paiement ?? 'N/A') }}
                </span>
            </p>
            @if($facture->mode_paiement)
                <p><strong>Mode de Paiement :</strong> {{ ucfirst($facture->mode_paiement) }}</p>
            @endif
            @if($facture->date_paiement && $statut === 'payé')
                <p><strong>Date de Paiement :</strong> {{ \Carbon\Carbon::parse($facture->date_paiement)->format('d/m/Y') }}</p>
            @endif
        </div>

        {{-- Titre pour la section des articles --}}
        <div class="section-title">Détails de la Facture</div>

        {{-- Tableau des articles --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th class="text-center">Quantité</th>
                    <th class="text-right">Prix Unitaire HT</th>
                    <th class="text-right">Montant HT</th>
                </tr>
            </thead>
            <tbody>
                {{-- Boucle sur les articles de la facture --}}
                {{-- La variable $details est attendue du contrôleur, contenant les articles avec leur qté et prix --}}
                @forelse($facture->articles as $article)
                    <tr>
                        <td>{{ $article->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $article->pivot->quantite }}</td>
                        <td class="text-right">{{ number_format($article->pivot->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                        <td class="text-right">{{ number_format($article->pivot->quantite * $article->pivot->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Aucun article sur cette facture.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Section des totaux --}}
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Montant HT Total :</td>
                    <td class="value">{{ number_format($facture->montant_ht, 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td class="label">TVA ({{ $facture->tva ?? 18 }}%) :</td>
                    <td class="value">{{ number_format($facture->montant_ttc - $facture->montant_ht, 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td class="label">Montant TTC Total :</td>
                    <td class="value">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</td>
                </tr>
            </table>
        </div>

        {{-- Pied de page de la facture --}}
        <div class="invoice-footer">
            <p>Merci de votre confiance. Paiement à effectuer dans les 30 jours suivant la réception de cette facture.</p>
            <p><strong>{{ config('app.name', 'Entreprise XYZ') }}</strong> — Votre partenaire de confiance.</p>
            {{-- TODO: Ajouter d'autres informations légales si nécessaire (N° RCCM, etc.) --}}
        </div>
    </div>
</body>
</html>
