// next-frontend/app/dashboard/property-management/components/FilterSidebar.jsx
'use client';
import { useEffect } from 'react';

export default function FilterSidebar({ open = false, onClose = () => { } }) {
  // lock scroll when open
  useEffect(() => {
    document.body.style.overflow = open ? 'hidden' : '';
    return () => { document.body.style.overflow = ''; };
  }, [open]);

  return (
    <>
      {/* backdrop */}
      <div
        className={`fixed inset-0 bg-black/40 z-40 transition-opacity ${open ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'}`}
        onClick={onClose}
      />

      {/* panel */}
      <aside
        className={`fixed right-0 top-0 h-full w-full sm:w-96 bg-white z-50 transform transition-transform ${open ? 'translate-x-0' : 'translate-x-full'}`}
      >
        <div className="p-5 border-b flex items-center justify-between">
          <h3 className="text-lg font-medium">Filters</h3>
          <button onClick={onClose} className="text-gray-500">Fermer</button>
        </div>

        <div className="p-5 space-y-6 overflow-auto h-[calc(100%-64px)]">
          <div>
            <label className="block text-sm text-gray-600 mb-2">Catégorie</label>
            <select className="w-full border rounded-md px-3 py-2 text-sm">
              <option value="">Sélectionner une catégorie</option>
              <option>Maison</option>
              <option>Terrains</option>
              <option>Commercial</option>

            </select>
          </div>

          <div>
            <label className="block text-sm text-gray-600 mb-2">Ville</label>
            <select className="w-full border rounded-md px-3 py-2 text-sm">
              <option value="">Sélectionner la ville</option>
              <option>Casablanca</option>
              <option>Rabat</option>
              <option>Marrakech</option>


            </select>
          </div>

          <div>
            <label className="block text-sm text-gray-600 mb-2">Emplacement</label>
            <select className="w-full border rounded-md px-3 py-2 text-sm">
              <option value="">Sélectionner l’emplacement</option>
            </select>
          </div>

          <div>
            <div className="flex items-center justify-between mb-2">
              <label className="text-sm text-gray-600">Fourchette de prix</label>
              <button className="text-sm text-green-600">Réinitialiser</button>

            </div>
            <div className="grid grid-cols-2 gap-3">
              <input placeholder="Min" className="border px-3 py-2 rounded-md text-sm" />
              <input placeholder="Max" className="border px-3 py-2 rounded-md text-sm" />
            </div>
            <div className="text-xs text-gray-400 mt-2">0 — 1 Arab</div>
          </div>

          <div>
            <div className="flex items-center justify-between mb-2">
              <label className="text-sm text-gray-600">Plage de superficie</label>
              <button className="text-sm text-green-600">Réinitialiser</button>

            </div>
            <div className="grid grid-cols-2 gap-3">
              <input placeholder="Min" className="border px-3 py-2 rounded-md text-sm" />
              <input placeholder="Max" className="border px-3 py-2 rounded-md text-sm" />
            </div>
            <div className="text-xs text-gray-400 mt-2">0 m² — 10 000 m²</div>

          </div>

          <div className="flex items-center justify-between pt-4">
            <button className="px-4 py-2 border rounded text-sm">Réinitialiser les filtres</button>
<button className="px-4 py-2 bg-green-600 text-white rounded text-sm">Rechercher</button>

          </div>
        </div>
      </aside>
    </>
  );
}
