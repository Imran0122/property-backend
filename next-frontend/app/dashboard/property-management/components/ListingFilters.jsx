// next-frontend/app/dashboard/property-management/components/ListingFilters.jsx
'use client';
import { useState } from 'react';
import FilterSidebar from './FilterSidebar';

export default function ListingFilters() {
  const [drawerOpen, setDrawerOpen] = useState(false);
  const [filters, setFilters] = useState({
    listingId: '',
    propertyType: '',
    purpose: '',
    dateRange: '',
  });

  return (
    <>
      <div className="bg-white rounded-md shadow-sm p-4 border border-gray-100">
        <div className="flex flex-col lg:flex-row gap-3 items-center">
          <input
            value={filters.listingId}
            onChange={(e) => setFilters({ ...filters, listingId: e.target.value })}
            placeholder="Enter Listing ID"
            className="px-3 py-2 border rounded-md w-full lg:w-72 text-sm"
          />

          <select
            value={filters.propertyType}
            onChange={(e) => setFilters({ ...filters, propertyType: e.target.value })}
            className="px-3 py-2 border rounded-md w-full lg:w-64 text-sm"
          >
            <option value="">Sélectionner le type de propriété</option>
            <option>Maison</option>
            <option>Terrains</option>
            <option>Commercial</option>

          </select>

          <select
            value={filters.purpose}
            onChange={(e) => setFilters({ ...filters, purpose: e.target.value })}
            className="px-3 py-2 border rounded-md w-full lg:w-56 text-sm"
          >
            <option value="">Sélectionner l’objectif</option>
            <option>Vente</option>
            <option>Location</option>

          </select>

          <input
            value={filters.dateRange}
            onChange={(e) => setFilters({ ...filters, dateRange: e.target.value })}
            placeholder="Select Date Range"
            className="px-3 py-2 border rounded-md w-full lg:w-52 text-sm"
          />

          <button
            onClick={() => setDrawerOpen(true)}
            className="text-sm text-green-600 hover:underline hidden sm:inline"
          >
            Afficher plus
          </button>

          <div className="ml-auto flex items-center gap-2">
            <button
              onClick={() => setFilters({ listingId: '', propertyType: '', purpose: '', dateRange: '' })}
              className="px-3 py-2 text-sm rounded-md border bg-gray-50"
            >
              Effacer les filtres
            </button>
            <button className="px-4 py-2 text-sm rounded-md bg-green-600 text-white">Rechercher</button>
          </div>
        </div>
      </div>

      <FilterSidebar open={drawerOpen} onClose={() => setDrawerOpen(false)} />
    </>
  );
}
