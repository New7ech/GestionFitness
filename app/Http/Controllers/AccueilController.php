<?php

namespace App\Http\Controllers;

use App\Enums\ChallengeStatus;
use App\Enums\InscriptionStatus;
use App\Enums\PaymentStatus;
use App\Models\Challenge;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Participante;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class AccueilController extends Controller
{
    public function index(): View
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $nombreParticipantes = Participante::query()->count();
        $nombreParticipantesActives = Participante::query()->where('status', 'active')->count();
        $nombreChallengesEnCours = Challenge::query()->where('status', ChallengeStatus::EnCours)->count();
        $nombreChallengesActifs = Challenge::query()
            ->whereIn('status', [ChallengeStatus::Planifie, ChallengeStatus::EnCours])
            ->count();

        $revenusMoisCourant = (float) Paiement::query()
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $challengesImpayes = Inscription::query()
            ->whereIn('payment_status', [PaymentStatus::Impaye, PaymentStatus::PartiellementPaye])
            ->where('status', '!=', InscriptionStatus::Annulee->value)
            ->whereHas('challenge', function ($query) {
                $query->whereIn('status', [ChallengeStatus::Planifie, ChallengeStatus::EnCours, ChallengeStatus::Termine]);
            })
            ->count();

        $presencesMoisCourant = Presence::query()
            ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->count();

        $participantesRecentes = Participante::query()
            ->latest('registration_date')
            ->limit(5)
            ->get();

        $challengesRecents = Inscription::query()
            ->with(['participante', 'challenge.challengeType'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        $challengesImpayesList = Inscription::query()
            ->with(['participante', 'challenge.challengeType'])
            ->whereIn('payment_status', [PaymentStatus::Impaye, PaymentStatus::PartiellementPaye])
            ->where('status', '!=', InscriptionStatus::Annulee->value)
            ->whereHas('challenge', function ($query) {
                $query->whereIn('status', [ChallengeStatus::Planifie, ChallengeStatus::EnCours, ChallengeStatus::Termine]);
            })
            ->latest('inscription_date')
            ->limit(10)
            ->get();

        $paiementsJournaliers = $this->paiementsJournaliers7Jours();
        $challengesParType = $this->challengesParType();

        return view('accueil.index', compact(
            'nombreParticipantes',
            'nombreParticipantesActives',
            'nombreChallengesEnCours',
            'nombreChallengesActifs',
            'revenusMoisCourant',
            'challengesImpayes',
            'presencesMoisCourant',
            'participantesRecentes',
            'challengesRecents',
            'challengesImpayesList',
            'paiementsJournaliers',
            'challengesParType',
        ));
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, float>}
     */
    private function paiementsJournaliers7Jours(): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $labels[] = $day->translatedFormat('D d/m');
            $data[] = (float) Paiement::query()
                ->whereDate('payment_date', $day->toDateString())
                ->sum('amount');
        }

        return compact('labels', 'data');
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    private function challengesParType(): array
    {
        $rows = Challenge::query()
            ->join('challenge_types', 'challenges.challenge_type_id', '=', 'challenge_types.id')
            ->selectRaw('challenge_types.label as type_label, COUNT(*) as count')
            ->groupBy('challenge_types.label')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return [
            'labels' => $rows->pluck('type_label')->toArray(),
            'data' => $rows->pluck('count')->map(fn ($v) => (int) $v)->toArray(),
        ];
    }
}
