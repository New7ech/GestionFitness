<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu {{ $recu->receipt_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333333; font-size: 12px; line-height: 1.5; }
        .receipt-container { width: 100%; border: 1px solid #dddddd; }
        .header-strip { display: table; width: 100%; background: #2c3e50; color: #ffffff; padding: 12px 18px; }
        .logo, .receipt-id { display: table-cell; vertical-align: middle; }
        .logo { font-size: 16px; font-weight: bold; }
        .receipt-id { text-align: right; font-size: 12px; }
        .receipt-header { display: table; width: 100%; padding: 16px 18px; border-bottom: 1px solid #dddddd; }
        .title, .date { display: table-cell; vertical-align: middle; }
        .title { font-size: 22px; font-weight: bold; color: #2c3e50; }
        .date { text-align: right; color: #555555; }
        .section { padding: 16px 18px; border-bottom: 1px solid #dddddd; }
        .section h3 { font-size: 14px; color: #2c3e50; margin: 0 0 8px; }
        .details { width: 100%; border-collapse: collapse; }
        .details td { padding: 8px; border: 1px solid #dddddd; }
        .details .label { width: 35%; font-weight: bold; background: #f8f9fa; }
        .amount { font-size: 18px; font-weight: bold; color: #27ae60; }
        .footer { padding: 14px 18px; text-align: center; color: #555555; font-size: 10px; }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header-strip">
            <div class="logo">{{ config('app.name', 'Gestion Fitness') }}</div>
            <div class="receipt-id">REÇU N° {{ $recu->receipt_number }}</div>
        </div>

        <div class="receipt-header">
            <div class="title">Reçu de paiement</div>
            <div class="date">Émis le : {{ $recu->issued_at->format('d/m/Y H:i') }}</div>
        </div>

        <div class="section">
            <h3>Participante</h3>
            <table class="details">
                <tr>
                    <td class="label">Nom complet</td>
                    <td>{{ $recu->participante_full_name }}</td>
                </tr>
                <tr>
                    <td class="label">Challenge</td>
                    <td>{{ $recu->challenge_type_label }} - {{ $recu->challenge_duration_days }} jours</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>Paiement</h3>
            <table class="details">
                <tr>
                    <td class="label">Montant payé</td>
                    <td class="amount">{{ number_format((float) $recu->amount_paid, 2, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td class="label">Reste à payer</td>
                    <td>{{ number_format((float) $recu->amount_remaining, 2, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td class="label">Mode de paiement</td>
                    <td>{{ $recu->payment_mode }}</td>
                </tr>
                <tr>
                    <td class="label">Émis par</td>
                    <td>{{ $recu->issued_by_name ?: $recu->generatedBy?->name ?: 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Merci pour votre confiance.</p>
            <p><strong>{{ config('app.name', 'Gestion Fitness') }}</strong></p>
        </div>
    </div>
</body>
</html>
