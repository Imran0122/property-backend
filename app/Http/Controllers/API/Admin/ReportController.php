<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    /**
     * Download properties report
     * GET /api/admin/report/properties
     * Optional filters: status, city_id, property_type_id
     */
    public function propertiesReport(Request $request)
    {
        $query = Property::with(['user', 'city', 'propertyType']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->has('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }

        $properties = $query->orderBy('created_at', 'desc')->get();

        // CSV Header
        $csvHeader = [
            'Property ID',
            'Title',
            'Owner',
            'City',
            'Type',
            'Price',
            'Status',
            'Created At'
        ];

        // Open output stream
        $callback = function() use ($properties, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($properties as $property) {
                fputcsv($file, [
                    $property->id,
                    $property->title,
                    $property->user->name ?? '',
                    $property->city->name ?? '',
                    $property->propertyType->name ?? '',
                    $property->price,
                    $property->status,
                    $property->created_at
                ]);
            }

            fclose($file);
        };

        $filename = 'properties_report_' . now()->format('Y_m_d_H_i_s') . '.csv';

        return Response::stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ]);
    }
}