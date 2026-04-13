<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class HomeLoanController extends Controller
{
    public function meta()
    {
        $banks = Bank::where('is_active', true)
            ->select('id', 'name', 'logo', 'interest_rate')
            ->get()
            ->map(function ($bank) {
                return [
                    'id' => $bank->id,
                    'name' => $bank->name,
                    'interest_rate' => $bank->interest_rate,
                    'logo' => $bank->logo,
                    'logo_url' => $this->resolveMediaUrl($bank->logo),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'defaults' => [
                    'property_price' => 15000000,
                    'down_payment_percentage' => 30,
                    'loan_years' => 20,
                    'interest_rate' => 12,
                ],
                'limits' => [
                    'property_price_min' => 1000000,
                    'property_price_max' => 50000000,
                    'property_price_step' => 100000,
                    'down_payment_min' => 10,
                    'down_payment_max' => 50,
                    'loan_years_min' => 1,
                    'loan_years_max' => 30,
                ],
                'banks' => $banks,
                'products' => collect($this->products())->map(function ($product) {
                    return [
                        'slug' => $product['slug'],
                        'title' => $product['title'],
                        'loan_type' => $product['loan_type'],
                    ];
                })->values(),
                'about' => "Tout le monde rêve de posséder sa propre maison à un moment de sa vie. Cependant, cela n’est pas facilement réalisable car le processus demande beaucoup de dévouement, de réflexion et, bien sûr, d’argent. Pour faciliter cette démarche, Hectare présente son calculateur de prêt immobilier. Utilisez cet outil pour évaluer les meilleures options de financement immobilier au Maroc. Votre éligibilité dépend de plusieurs facteurs comme votre salaire et votre nationalité. Pesez soigneusement vos options pour trouver la banque qui vous convient le mieux.",
                'faqs' => [
                    [
                        'question' => "Qu'est-ce que le KIBOR ?",
                        'answer' => "Les activités transactionnelles interbancaires des principales banques sur le marché marocain utilisent le taux interbancaire comme taux d'intérêt de référence pour les échanges de dépôts à terme."
                    ],
                    [
                        'question' => "Comment est calculé l'intérêt composé pour les prêts immobiliers ?",
                        'answer' => "Les mensualités sont calculées sur la base du montant emprunté, du taux d'intérêt annuel et de la durée du prêt en mois."
                    ],
                    [
                        'question' => "Quels sont les critères d'éligibilité de base pour les prêts immobiliers ?",
                        'answer' => "Le demandeur doit satisfaire aux critères de la banque, fournir les documents requis et avoir la capacité de remboursement nécessaire."
                    ],
                ],
            ]
        ]);
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'property_price' => 'required|numeric|min:1',
            'down_payment_percentage' => 'required|numeric|min:0|max:100',
            'loan_years' => 'required|integer|min:1|max:35',
            'interest_rate' => 'required|numeric|min:0.1|max:50',
            'loan_type' => 'nullable|string',
            'employment_status' => 'nullable|string',
        ]);

        $propertyPrice = (float) $validated['property_price'];
        $downPaymentPercent = (float) $validated['down_payment_percentage'];
        $loanYears = (int) $validated['loan_years'];
        $baseInterestRate = (float) $validated['interest_rate'];

        $loanTypeFilter = mb_strtolower(trim((string) $request->get('loan_type', 'all')));
        $employmentFilter = mb_strtolower(trim((string) $request->get('employment_status', 'tous')));

        $products = collect($this->products());

        if (!in_array($loanTypeFilter, ['all', 'tous', ''])) {
            $products = $products->filter(function ($product) use ($loanTypeFilter) {
                return mb_strtolower($product['loan_type']) === $loanTypeFilter;
            });
        }

        if (!in_array($employmentFilter, ['all', 'tous', ''])) {
            $products = $products->filter(function ($product) use ($employmentFilter) {
                $statuses = collect($product['employment_statuses'])
                    ->map(fn($item) => mb_strtolower($item))
                    ->values()
                    ->all();

                return in_array($employmentFilter, $statuses);
            });
        }

        $options = $products->values()->map(function ($product) use ($propertyPrice, $downPaymentPercent, $loanYears, $baseInterestRate) {
            return $this->transformProduct(
                $product,
                $propertyPrice,
                $downPaymentPercent,
                $loanYears,
                $baseInterestRate,
                false
            );
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'property_price' => round($propertyPrice),
                'down_payment_percentage' => $downPaymentPercent,
                'down_payment_amount' => round(($propertyPrice * $downPaymentPercent) / 100),
                'loan_years' => $loanYears,
                'interest_rate' => $baseInterestRate,
                'options' => $options,
            ]
        ]);
    }

    public function show(Request $request, string $slug)
    {
        $inputs = [
            'property_price' => (float) $request->get('property_price', 15000000),
            'down_payment_percentage' => (float) $request->get('down_payment_percentage', 30),
            'loan_years' => (int) $request->get('loan_years', 20),
            'interest_rate' => (float) $request->get('interest_rate', 12),
        ];

        validator($inputs, [
            'property_price' => 'required|numeric|min:1',
            'down_payment_percentage' => 'required|numeric|min:0|max:100',
            'loan_years' => 'required|integer|min:1|max:35',
            'interest_rate' => 'required|numeric|min:0.1|max:50',
        ])->validate();

        $product = collect($this->products())->firstWhere('slug', $slug);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Loan product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformProduct(
                $product,
                $inputs['property_price'],
                $inputs['down_payment_percentage'],
                $inputs['loan_years'],
                $inputs['interest_rate'],
                true
            )
        ]);
    }

    private function transformProduct(array $product, float $propertyPrice, float $downPaymentPercent, int $loanYears, float $baseInterestRate, bool $includeDetail = false): array
    {
        $effectiveInterestRate = $product['rate_mode'] === 'fixed'
            ? (float) $product['fixed_rate']
            : (float) ($baseInterestRate + $product['margin']);

        $calculation = $this->calculateLoan(
            $propertyPrice,
            $downPaymentPercent,
            $loanYears,
            $effectiveInterestRate
        );

        $primaryBank = Bank::where('is_active', true)->first();

        $payload = [
            'slug' => $product['slug'],
            'product' => $product['title'],
            'bank_name' => $primaryBank?->name ?? $product['bank_name'],
            'bank_logo' => $primaryBank ? $this->resolveMediaUrl($primaryBank->logo) : null,
            'monthly_installments' => $calculation['monthly_installment'],
            'initial_deposit' => $calculation['down_payment_amount'],
            'interest' => $product['interest_label'],
            'effective_interest_rate' => round($effectiveInterestRate, 2),
            'loan_type' => $product['loan_type'],
            'property_price' => $calculation['property_price'],
            'down_payment_percentage' => $calculation['down_payment_percentage'],
            'down_payment_amount' => $calculation['down_payment_amount'],
            'loan_years' => $calculation['loan_years'],
            'loan_months' => $calculation['loan_months'],
            'loan_amount' => $calculation['loan_amount'],
            'total_payment' => $calculation['total_payment'],
            'total_interest' => $calculation['total_interest'],
        ];

        if ($includeDetail) {
            $payload['employment_statuses'] = $product['employment_statuses'];
            $payload['features'] = $product['features'];
            $payload['disclaimer'] = $product['disclaimer'];
            $payload['overview'] = $product['overview'];
            $payload['eligibility'] = $product['eligibility'];
        }

        return $payload;
    }

    private function calculateLoan(float $propertyPrice, float $downPaymentPercent, int $loanYears, float $interestRate): array
    {
        $downPaymentAmount = ($propertyPrice * $downPaymentPercent) / 100;
        $loanAmount = $propertyPrice - $downPaymentAmount;
        $monthlyRate = $interestRate / 100 / 12;
        $months = $loanYears * 12;

        if ($months <= 0) {
            $months = 1;
        }

        if ($monthlyRate == 0) {
            $monthlyInstallment = $loanAmount / $months;
        } else {
            $monthlyInstallment = ($loanAmount * $monthlyRate * pow(1 + $monthlyRate, $months)) /
                (pow(1 + $monthlyRate, $months) - 1);
        }

        $totalPayment = $monthlyInstallment * $months;
        $totalInterest = $totalPayment - $loanAmount;

        return [
            'property_price' => round($propertyPrice),
            'down_payment_percentage' => $downPaymentPercent,
            'down_payment_amount' => round($downPaymentAmount),
            'loan_years' => $loanYears,
            'loan_months' => $months,
            'loan_amount' => round($loanAmount),
            'monthly_installment' => round($monthlyInstallment),
            'total_payment' => round($totalPayment),
            'total_interest' => round($totalInterest),
        ];
    }

    private function products(): array
    {
        return [
            [
                'slug' => 'hectare-housing-program',
                'title' => 'Programme de logement Hectare',
                'bank_name' => 'HBFC',
                'rate_mode' => 'margin',
                'margin' => 3,
                'interest_label' => 'Taux interbancaire + 3 %',
                'loan_type' => 'Conventionnel',
                'employment_statuses' => ['Tous', 'Salarié', 'Indépendant'],
                'features' => [
                    'Plan de remboursement abordable et facile',
                    'Aide à la construction sur un terrain que vous possédez déjà',
                    'Permet d’agrandir ou d’étendre votre logement existant',
                    'Permet l’achat ainsi que la construction sur le terrain',
                    'Achat de maison / appartement',
                    'Taux d’intérêt hybrides',
                    'Assurance du bien immobilier',
                    'Proposé dans tout le pays',
                ],
                'disclaimer' => 'Il est important de mentionner ici que la banque peut demander des documents supplémentaires.',
                'overview' => 'House Building Finance Company Limited (HBFC) est l’une des principales institutions de financement immobilier. Ce programme vise à fournir des solutions de financement simples et accessibles pour les acheteurs de logements au Maroc.',
                'eligibility' => [
                    'Tous les ressortissants marocains titulaires d’une carte d’identité nationale valide',
                    'Primo-accédant à la propriété',
                ],
            ],
            [
                'slug' => 'hectare-special-mortgage-loan',
                'title' => 'Prêt immobilier Hectare Spécial',
                'bank_name' => 'HBFC',
                'rate_mode' => 'fixed',
                'fixed_rate' => 4,
                'interest_label' => '4 %',
                'loan_type' => 'Conventionnel',
                'employment_statuses' => ['Tous', 'Salarié'],
                'features' => [
                    'Faible taux fixe',
                    'Mensualités prévisibles',
                    'Financement adapté aux primo-accédants',
                    'Procédure simplifiée',
                    'Achat de maison / appartement',
                    'Financement du marché secondaire',
                    'Assurance du bien immobilier',
                    'Proposé dans tout le pays',
                ],
                'disclaimer' => 'La banque peut demander des documents supplémentaires selon le profil du demandeur.',
                'overview' => 'Le prêt immobilier Hectare Spécial est conçu pour les clients qui souhaitent une mensualité plus stable grâce à un taux fixe compétitif.',
                'eligibility' => [
                    'Carte d’identité nationale valide',
                    'Capacité de remboursement suffisante',
                ],
            ],
            [
                'slug' => 'hectare-preferential-rate',
                'title' => 'Programme Maison – Taux préférentiel',
                'bank_name' => 'HBFC',
                'rate_mode' => 'margin',
                'margin' => 4,
                'interest_label' => 'Taux interbancaire + 4 %',
                'loan_type' => 'Islamique',
                'employment_statuses' => ['Tous', 'Salarié', 'Indépendant'],
                'features' => [
                    'Plan de remboursement abordable et facile',
                    'Aide à la construction sur un terrain que vous possédez déjà',
                    'Permet d’agrandir ou d’étendre votre logement existant',
                    'Permet l’achat ainsi que la construction sur le terrain',
                    'Profitez d’un taux locatif de 5 % pendant les cinq premières années',
                    'Achat de maison / appartement',
                    'Taux d’intérêt hybrides',
                    'Financement jusqu’à 1 million MAD',
                    'Assurance du bien immobilier',
                    'Proposé dans tout le pays',
                    'Achat d’un bien résidentiel complet',
                    'Financement allant jusqu’à 80 % de la valeur du bien',
                ],
                'disclaimer' => 'Il est important de mentionner ici que la banque peut demander des documents supplémentaires.',
                'overview' => 'House Building Finance Company Limited (HBFC), créée en 1952, est l’une des principales institutions de financement immobilier. Ce produit à taux préférentiel vise à améliorer l’accessibilité du financement pour les ménages.',
                'eligibility' => [
                    'Tous les ressortissants marocains titulaires d’une carte d’identité nationale valide',
                    'Primo-accédant à la propriété',
                ],
            ],
        ];
    }

    private function resolveMediaUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return url($path);
        }

        if (str_starts_with($path, 'storage/')) {
            return url('/' . $path);
        }

        return url('/storage/' . ltrim($path, '/'));
    }
}