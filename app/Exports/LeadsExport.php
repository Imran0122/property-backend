<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadsExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Lead::with('property', 'user');

        if ($this->request->filled('from')) {
            $query->whereDate('created_at', '>=', $this->request->from);
        }
        if ($this->request->filled('to')) {
            $query->whereDate('created_at', '<=', $this->request->to);
        }
        if ($this->request->filled('city')) {
            $query->whereHas('property.city', function($q) {
                $q->where('id', $this->request->city);
            });
        }
        if ($this->request->filled('property_type')) {
            $query->whereHas('property.propertyType', function($q) {
                $q->where('id', $this->request->property_type);
            });
        }

        return $query->get()->map(function($lead) {
            return [
                'Name'        => $lead->name,
                'Email'       => $lead->user ? $lead->user->email : 'N/A',
                'Message'     => $lead->message,
                'Property'    => $lead->property->title,
                'Status'      => ucfirst($lead->status),
                'Created At'  => $lead->created_at->format('d-m-Y'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Message', 'Property', 'Status', 'Created At'];
    }
}
