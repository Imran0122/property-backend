<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AreaUnit;
use App\Models\City;
use App\Models\ConstructionMode;
use App\Models\ConstructionRate;
use App\Models\ConstructionType;
use Illuminate\Http\Request;

class ConstructionCalculatorController extends Controller
{
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'area_size' => 'required|numeric|min:1',
            'unit_id' => 'required|exists:area_units,id',
            'construction_type_id' => 'required|exists:construction_types,id',
            'construction_mode_id' => 'required|exists:construction_modes,id',
            'covered_area' => 'nullable|numeric|min:0',
        ]);

        $estimate = $this->buildEstimate($validated);

        return response()->json([
            'status' => true,
            'data' => $estimate,
        ]);
    }

    public function details(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'area_size' => 'required|numeric|min:1',
            'unit_id' => 'required|exists:area_units,id',
            'construction_type_id' => 'required|exists:construction_types,id',
            'construction_mode_id' => 'required|exists:construction_modes,id',
            'covered_area' => 'nullable|numeric|min:0',
        ]);

        $estimate = $this->buildEstimate($validated);

        return response()->json([
            'status' => true,
            'data' => [
                'summary' => $estimate,
                'tabs' => $this->buildTabs($estimate),
                'faq' => $this->faqData(),
            ],
        ]);
    }

    private function buildEstimate(array $validated): array
    {
        $city = City::findOrFail($validated['city_id']);
        $unit = AreaUnit::findOrFail($validated['unit_id']);
        $type = ConstructionType::findOrFail($validated['construction_type_id']);
        $mode = ConstructionMode::findOrFail($validated['construction_mode_id']);

        $sqft = $validated['area_size'] * $unit->conversion_to_sqft;

        $rate = ConstructionRate::where('city_id', $city->id)
            ->where('construction_type_id', $type->id)
            ->where('construction_mode_id', $mode->id)
            ->first();

        if (!$rate) {
            abort(response()->json([
                'status' => false,
                'message' => 'Rate not found for selected combination'
            ], 404));
        }

        $totalCost = round($sqft * $rate->rate_per_sqft, 2);

        $coveredArea = isset($validated['covered_area']) && $validated['covered_area'] > 0
            ? (float) $validated['covered_area']
            : round($sqft);

        return [
            'title' => "{$validated['area_size']} {$unit->name} House Construction Cost in {$city->name}",
            'city' => [
                'id' => $city->id,
                'name' => $city->name,
            ],
            'unit' => [
                'id' => $unit->id,
                'name' => $unit->name,
                'slug' => $unit->slug,
            ],
            'construction_type' => [
                'id' => $type->id,
                'name' => $type->name,
                'slug' => $type->slug,
            ],
            'construction_mode' => [
                'id' => $mode->id,
                'name' => $mode->name,
                'slug' => $mode->slug,
            ],
            'area_size' => (float) $validated['area_size'],
            'covered_area' => $coveredArea,
            'converted_sqft' => round($sqft, 2),
            'rate_per_sqft' => round($rate->rate_per_sqft, 2),
            'total_cost' => $totalCost,
            'updated_at' => now()->format('jS F, Y'),
        ];
    }

    private function buildTabs(array $estimate): array
    {
        $total = $estimate['total_cost'];

        $complete = [
            'total_cost' => round($total, 2),
            'grey_structure_cost' => round($total * 0.50, 2),
            'finishing_cost' => round($total * 0.41, 2),
            'labour_cost' => round($total * 0.09, 2),
            'price_per_sqft' => round($estimate['rate_per_sqft'], 2),
            'chart' => [
                ['label' => 'Travaux de plomberie', 'value' => round($total * 0.18, 2)],
                ['label' => 'Travaux électriques', 'value' => round($total * 0.15, 2)],
                ['label' => 'Travaux de bois, métal et carrelage', 'value' => round($total * 0.26, 2)],
                ['label' => 'Accessoires et appareils', 'value' => round($total * 0.08, 2)],
                ['label' => 'Fondation & structure', 'value' => round($total * 0.33, 2)],
            ],
            'sections' => [
                ['title' => 'Travaux de plomberie', 'description' => 'Comprend les systèmes de tuyauterie et d’évacuation.', 'total_cost' => round($total * 0.18, 2)],
                ['title' => 'Travaux électriques', 'description' => 'Comprend le câblage et les installations électriques.', 'total_cost' => round($total * 0.15, 2)],
                ['title' => 'Travaux de bois, métal et carrelage', 'description' => 'Comprend portes, fenêtres, métal et carrelage.', 'total_cost' => round($total * 0.26, 2)],
                ['title' => 'Accessoires et appareils', 'description' => 'Comprend les finitions et accessoires divers.', 'total_cost' => round($total * 0.08, 2)],
                ['title' => 'Fondation & structure', 'description' => 'Comprend la base structurelle du bâtiment.', 'total_cost' => round($total * 0.33, 2)],
            ],
        ];

        $grey = [
            'total_cost' => round($total * 0.72, 2),
            'grey_structure_cost' => round($total * 0.72, 2),
            'finishing_cost' => 0,
            'labour_cost' => round($total * 0.10, 2),
            'price_per_sqft' => round($estimate['rate_per_sqft'] * 0.72, 2),
            'chart' => [
                ['label' => 'Excavation', 'value' => round($total * 0.10, 2)],
                ['label' => 'Travaux en béton', 'value' => round($total * 0.25, 2)],
                ['label' => 'Maçonnerie', 'value' => round($total * 0.18, 2)],
                ['label' => 'Toiture', 'value' => round($total * 0.08, 2)],
                ['label' => 'Fondation & structure', 'value' => round($total * 0.11, 2)],
            ],
            'sections' => [
                ['title' => 'Excavation', 'description' => 'Travaux d’excavation et nivellement.', 'total_cost' => round($total * 0.10, 2)],
                ['title' => 'Travaux en béton', 'description' => 'Béton, coffrage et structure principale.', 'total_cost' => round($total * 0.25, 2)],
                ['title' => 'Maçonnerie', 'description' => 'Briques, murs et éléments de maçonnerie.', 'total_cost' => round($total * 0.18, 2)],
                ['title' => 'Toiture', 'description' => 'Travaux de dalle et toiture.', 'total_cost' => round($total * 0.08, 2)],
                ['title' => 'Fondation & structure', 'description' => 'Fondations et structure porteuse.', 'total_cost' => round($total * 0.11, 2)],
            ],
        ];

        $finishing = [
            'total_cost' => round($total * 0.28, 2),
            'grey_structure_cost' => 0,
            'finishing_cost' => round($total * 0.28, 2),
            'labour_cost' => round($total * 0.07, 2),
            'price_per_sqft' => round($estimate['rate_per_sqft'] * 0.28, 2),
            'chart' => [
                ['label' => 'Peinture', 'value' => round($total * 0.06, 2)],
                ['label' => 'Revêtements de sol', 'value' => round($total * 0.08, 2)],
                ['label' => 'Cuisine & salle de bain', 'value' => round($total * 0.07, 2)],
                ['label' => 'Portes & fenêtres', 'value' => round($total * 0.04, 2)],
                ['label' => 'Finitions diverses', 'value' => round($total * 0.03, 2)],
            ],
            'sections' => [
                ['title' => 'Peinture', 'description' => 'Travaux de peinture intérieure et extérieure.', 'total_cost' => round($total * 0.06, 2)],
                ['title' => 'Revêtements de sol', 'description' => 'Carrelage, marbre et autres sols.', 'total_cost' => round($total * 0.08, 2)],
                ['title' => 'Cuisine & salle de bain', 'description' => 'Équipements et finitions des espaces humides.', 'total_cost' => round($total * 0.07, 2)],
                ['title' => 'Portes & fenêtres', 'description' => 'Bois, aluminium et ouvertures.', 'total_cost' => round($total * 0.04, 2)],
                ['title' => 'Finitions diverses', 'description' => 'Divers travaux de finition.', 'total_cost' => round($total * 0.03, 2)],
            ],
        ];

        return [
            'Complete' => $complete,
            'Grey Structure' => $grey,
            'Finishing' => $finishing,
        ];
    }

    private function faqData(): array
    {
        return [
            [
                'question' => "Quel est le coût de construction d'une maison ?",
                'answer' => "Le coût dépend de la ville, de la surface, du type de construction et du mode de construction sélectionnés."
            ],
            [
                'question' => "Quel est le coût de construction par m² ?",
                'answer' => "Le prix par m² varie selon le tarif par pied carré enregistré pour la combinaison sélectionnée."
            ],
            [
                'question' => "Qu'est-ce qui est inclus dans la structure brute ?",
                'answer' => "La structure brute comprend les fondations, la structure, la maçonnerie et les travaux de base nécessaires à la construction."
            ],
        ];
    }
}