// next-frontend/app/dashboard/property-management/components/FilterDrawer.jsx
"use client";

import { useEffect } from "react";

export default function FilterDrawer({ open = false, onClose = () => { } }) {

  useEffect(() => {
    if (open) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
    return () => { document.body.style.overflow = ""; }
  }, [open]);

  return (
    <>
      {/* backdrop */}
      <div
        className={`fixed inset-0 bg-black/40 transition-opacity ${open ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'}`}
        onClick={onClose}
      />

      <aside className={`fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-xl transform transition-transform ${open ? 'translate-x-0' : 'translate-x-full'}`}>
        <div className="p-5 border-b flex items-center justify-between">
          <h3 className="text-lg font-semibold">Filtres</h3>
          <button onClick={onClose} className="text-gray-500">✕</button>
        </div>

        <div className="p-5 overflow-y-auto h-[calc(100%-64px)]">
          <div className="space-y-5">
            <div>
              <label className="text-sm text-gray-600">Catégorie</label>
              <select className="w-full mt-2 border border-gray-200 rounded-md px-3 py-2 text-sm">
                <option>Sélectionner une catégorie</option>
              </select>
            </div>

            <div>
              <label className="text-sm text-gray-600">Ville</label>
              <select className="w-full mt-2 border border-gray-200 rounded-md px-3 py-2 text-sm">
                <option>Sélectionner la ville</option>
              </select>
            </div>

            <div>
              <label className="text-sm text-gray-600">Fourchette de prix</label>
              <div className="flex gap-2 mt-2">
                <input className="flex-1 border border-gray-200 rounded-md px-3 py-2 text-sm" placeholder="Min" />
                <input className="flex-1 border border-gray-200 rounded-md px-3 py-2 text-sm" placeholder="Max" />
              </div>
            </div>

            <div>
              <label className="text-sm text-gray-600">Plage de superficie</label>
              <div className="flex gap-2 mt-2">
                <input className="flex-1 border border-gray-200 rounded-md px-3 py-2 text-sm" placeholder="Min" />
                <input className="flex-1 border border-gray-200 rounded-md px-3 py-2 text-sm" placeholder="Max" />
              </div>
            </div>

            <div className="flex items-center justify-between">
              <button className="border border-gray-200 px-4 py-2 rounded-md text-sm">Réinitialiser les filtres</button>
              <button className="bg-green-600 text-white px-4 py-2 rounded-md text-sm">Rechercher</button>

            </div>
          </div>
        </div>
      </aside>
    </>
  );
}
