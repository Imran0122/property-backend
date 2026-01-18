// next-frontend/app/dashboard/property-management/components/ListingTable.jsx
"use client";
import React from "react";
import ListingRow from "./ListingRow";

export default function ListingTable({ listings = [], loading = false }) {
  return (
    <div className="mt-4 w-full">
      <div className="w-full overflow-x-auto">
        <table className="min-w-full w-full bg-white">
          <thead className="text-sm text-gray-500 border-b">
            <tr>
              <th className="px-4 py-3 text-left">Propriété</th>
              <th className="px-4 py-3 text-left">Plateforme</th>
              <th className="px-4 py-3 text-left">Statistiques</th>
              <th className="px-4 py-3 text-left">Publié le</th>
              <th className="px-4 py-3 text-left">Statut</th>
              <th className="px-4 py-3 text-left">Actions</th>

            </tr>
          </thead>

          <tbody className="text-sm text-gray-700">
            {loading ? (
              <tr><td colSpan="6" className="p-6 text-center">Chargement...</td></tr>
            ) : listings.length === 0 ? (
              <tr><td colSpan="6" className="p-6 text-center">Aucune annonce trouvée</td></tr>
            ) : (
              listings.map(item => <ListingRow key={item.id} item={item} />)
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
