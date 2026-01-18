// 'use client';
// import React from 'react';
// import { PlusCircle } from 'lucide-react';

// export default function RecentListings() {
//   return (
//     <div className="bg-white rounded-2xl border border-gray-200 shadow-sm mt-6">
//       {/* Header */}
//       <div className="flex justify-between items-center border-b border-gray-100 px-6 py-4">
//         <h2 className="text-base font-semibold text-gray-800">Recent Listings</h2>
//         <a
//           href="#"
//           className="text-green-600 text-sm font-medium hover:underline flex items-center gap-1"
//         >
//           View All Listings <span className="text-[16px]">↗</span>
//         </a>
//       </div>

//       {/* Empty State */}
//       <div className="flex flex-col items-center justify-center text-center py-12 px-6">
//         <div className="bg-green-50 rounded-full w-20 h-20 flex items-center justify-center mb-3">
//           <PlusCircle className="text-green-600 w-8 h-8" />
//         </div>
//         <h3 className="text-gray-800 font-semibold text-lg mb-1">No Active Listings</h3>
//         <p className="text-gray-500 text-sm mb-5">
//           Your active listings will appear here once added.
//         </p>
//         <button className="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-sm font-medium">
//           Post Listing
//         </button>
//       </div>

//       {/* Footer (Optional: For future pagination) */}
//       <div className="border-t border-gray-100 px-6 py-3 text-xs text-gray-400 text-right">
//         © 2025 — Powered by Zameen.com
//       </div>
//     </div>
//   );
// }





// next-frontend/app/dashboard/components/RecentListings.jsx
"use client";

import { useState } from "react";

export default function RecentListings() {
  const [listings] = useState([
    {
      id: 1,
      title: "Belle maison dans la vieille ville",
      price: 9000000,
      city: "Bahawalpur",
      area_text: "5 Marla",
      bedrooms: 4,
      bathrooms: 3,
      images: [{ url: "/placeholder-house.jpg" }],
      status: "Inactif",
    },

  ]);

  return (
    <div className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm mt-6">
      <div className="flex items-center justify-between">
        <h3 className="text-lg font-semibold text-gray-800">Vos annonces</h3>
        <div className="text-sm text-gray-500">Affichage des ajouts récents</div>

      </div>

      <div className="mt-4 overflow-x-auto">
        <table className="w-full text-sm">
          <thead>
            <tr className="text-left text-xs text-gray-500 border-b">
              <th className="py-3 px-2">#</th>
              <th className="py-3 px-2">Propriété</th>
              <th className="py-3 px-2">Emplacement</th>
              <th className="py-3 px-2">Prix</th>
              <th className="py-3 px-2">Chambres</th>
              <th className="py-3 px-2">Salles de bain</th>
              <th className="py-3 px-2">Statut</th>
              <th className="py-3 px-2">Actions</th>

            </tr>
          </thead>
          <tbody>
            {listings.map((l, idx) => (
              <tr key={l.id} className="border-b last:border-b-0 hover:bg-gray-50">
                <td className="py-3 px-2">{idx + 1}</td>
                <td className="py-3 px-2">
                  <div className="flex items-center gap-3">
                    <img src={l.images?.[0]?.url ?? "/placeholder-house.jpg"} alt="" className="w-20 h-12 object-cover rounded-md" />
                    <div>
                      <div className="font-medium text-gray-800">{l.title}</div>
                      <div className="text-xs text-gray-500">{l.area_text}</div>
                    </div>
                  </div>
                </td>
                <td className="py-3 px-2">{l.city}</td>
                <td className="py-3 px-2">PKR {new Intl.NumberFormat().format(l.price)}</td>
                <td className="py-3 px-2">{l.bedrooms ?? "-"}</td>
                <td className="py-3 px-2">{l.bathrooms ?? "-"}</td>
                <td className="py-3 px-2">
                  <span className="px-2 py-1 rounded-md text-xs bg-gray-100 text-gray-700">{l.status}</span>
                </td>
                <td className="py-3 px-2">
                  <div className="flex gap-2">
                    <button className="px-2 py-1 text-xs border rounded-md">Modifier</button>
                    <button className="px-2 py-1 text-xs border rounded-md">Voir</button>
                    <button className="px-2 py-1 text-xs border rounded-md">Supprimer</button>

                  </div>
                </td>
              </tr>
            ))}
            {listings.length === 0 && (
              <tr>
                <td colSpan="8" className="text-center py-6 text-gray-500">ucune annonce pour le moment</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

