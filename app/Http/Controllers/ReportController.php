<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Property;
use App\Models\Lead;
use App\Models\City;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;
use App\Exports\LeadsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Store a new report for a property
     */
    public function store(Request $request, Property $property)
    {
        $request->validate([
            'reason' => 'required|string',
            'message' => 'nullable|string|max:1000',
        ]);

        Report::create([
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Report submitted successfully!');
    }

    /**
     * Show list of reports (with pagination + filters + stats)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Report::class);

        $query = Report::with(['property', 'user', 'property.city', 'property.propertyType']);

        // ✅ Filters
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('city')) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('city_id', $request->city);
            });
        }
        if ($request->filled('property_type')) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('property_type_id', $request->property_type);
            });
        }

        $reports = $query->latest()->paginate(15);

        // ✅ Stats
        $totalProperties = Property::count();
        $totalLeads      = Lead::count();
        $newLeads        = Lead::where('status', 'new')->count();
        $closedLeads     = Lead::where('status', 'closed')->count();

        // ✅ Properties by type
        $propertiesByType = Property::selectRaw('property_type_id, COUNT(*) as count')
            ->groupBy('property_type_id')
            ->with('propertyType')
            ->get();

        // ✅ Leads per month
        $leadsPerMonth = Lead::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month');

        return view('reports.index', compact(
            'reports',
            'totalProperties',
            'totalLeads',
            'newLeads',
            'closedLeads',
            'propertiesByType',
            'leadsPerMonth'
        ));
    }

    /**
     * Update a report's status
     */
    public function update(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved',
        ]);

        $report->update(['status' => $request->status]);

        return back()->with('success', 'Report status updated!');
    }

    /**
     * Export Reports to Excel
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new ReportsExport($request->all()), 'reports.xlsx');
    }

    /**
     * Export Reports to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Report::with(['property', 'user']);

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('city')) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('city_id', $request->city);
            });
        }
        if ($request->filled('property_type')) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('property_type_id', $request->property_type);
            });
        }

        $reports = $query->latest()->get();

        $pdf = Pdf::loadView('reports.pdf', compact('reports'));
        return $pdf->download('reports.pdf');
    }

    /**
     * Export Leads to Excel
     */
    public function exportLeadsExcel(Request $request)
    {
        return Excel::download(new LeadsExport($request), 'leads_report.xlsx');
    }

    /**
     * Export Leads to PDF
     */
    public function exportLeadsPdf(Request $request)
    {
        $query = Lead::with('property', 'user');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('city')) {
            $query->whereHas('property.city', function($q) use ($request) {
                $q->where('id', $request->city);
            });
        }
        if ($request->filled('property_type')) {
            $query->whereHas('property.propertyType', function($q) use ($request) {
                $q->where('id', $request->property_type);
            });
        }

        $leads = $query->get();

        $pdf = Pdf::loadView('reports.leads-pdf', compact('leads'));
        return $pdf->download('leads_report.pdf');
    }
}
