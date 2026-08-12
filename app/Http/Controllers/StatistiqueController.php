<?php

namespace App\Http\Controllers;

use App\Enums\ChallengeStatus;
use App\Enums\PaymentStatus;
use App\Models\Challenge;
use App\Models\ChallengeType;
use App\Models\Mesure;
use App\Models\Paiement;
use App\Models\Participante;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index(): View
    {
        $since30Days = Carbon::now()->subDays(30);

        $totalParticipantesActives = Participante::query()->where('status', 'active')->count();
        $totalRevenus30Jours       = (float) Paiement::query()
            ->where('payment_date', '>=', $since30Days)
            ->sum('amount');

        $challengeTypes = ChallengeType::query()
            ->withCount('challenges')
            ->orderByDesc('challenges_count')
            ->get();

        $challengesParTypeLabels = $challengeTypes->pluck('label')->toArray();
        $challengesParTypeData   = $challengeTypes->pluck('challenges_count')->toArray();

        $paiementsTrendRaw = Paiement::query()
            ->select(DB::raw('DATE(payment_date) as date'), DB::raw('SUM(amount) as total'))
            ->where('payment_date', '>=', $since30Days)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $paiementsTrendLabels = [];
        $paiementsTrendData   = [];
        $period               = Carbon::now()->subDays(29);

        for ($i = 0; $i < 30; $i++) {
            $dateStr                = $period->format('Y-m-d');
            $paiementsTrendLabels[] = $period->format('d/m');
            $row                    = $paiementsTrendRaw->firstWhere('date', $dateStr);
            $paiementsTrendData[]   = $row ? (float) $row->total : 0.0;
            $period->addDay();
        }

        $topChallengeTypesRaw = Challenge::query()
            ->select('challenge_types.label', DB::raw('COUNT(*) as total'))
            ->join('challenge_types', 'challenges.challenge_type_id', '=', 'challenge_types.id')
            ->where('challenges.created_at', '>=', $since30Days)
            ->groupBy('challenge_types.label')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topChallengeTypesLabels = $topChallengeTypesRaw->pluck('label')->toArray();
        $topChallengeTypesData   = $topChallengeTypesRaw->pluck('total')->map(fn ($v) => (int) $v)->toArray();

        $challengesImpayes = Challenge::query()
            ->with(['participante', 'challengeType'])
            ->whereIn('payment_status', [PaymentStatus::Impaye, PaymentStatus::PartiellementPaye])
            ->whereIn('status', [ChallengeStatus::Planifie, ChallengeStatus::EnCours, ChallengeStatus::Termine])
            ->orderBy('start_date')
            ->get();

        $totalPresences30Jours = Presence::query()
            ->where('attendance_date', '>=', $since30Days)
            ->count();

        $totalMesures30Jours = Mesure::query()
            ->where('measured_at', '>=', $since30Days)
            ->count();

        return view('statistiques.index', compact(
            'totalParticipantesActives',
            'totalRevenus30Jours',
            'challengesParTypeLabels',
            'challengesParTypeData',
            'paiementsTrendLabels',
            'paiementsTrendData',
            'topChallengeTypesLabels',
            'topChallengeTypesData',
            'challengesImpayes',
            'totalPresences30Jours',
            'totalMesures30Jours',
        ));
    }
}
