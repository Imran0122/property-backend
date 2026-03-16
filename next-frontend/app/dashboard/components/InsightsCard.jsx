// // app/dashboard/components/InsightsCard.jsx
// import React from 'react';

// const InsightsCard = () => {
//   return (
//     <div className="bg-white p-8 rounded-xl shadow-sm border border-gray-100 text-center mb-8 flex flex-col items-center justify-center"> {/* Adjusted padding, rounded, shadow, border, flex for centering */}
//       <div className="mb-4">
//         <span className="text-green-500 text-7xl">📈</span> {/* Larger icon */}
//       </div>
//       <h2 className="text-xl font-semibold mb-2 text-gray-800">View In-Depth Insights</h2> {/* Font weight and color */}
//       <p className="text-gray-500 text-sm max-w-md mx-auto">See the number of views, clicks and leads that your listing has received.</p> {/* text-sm and max-width */}
//     </div>
//   );
// };

// export default InsightsCard;










// next-frontend/app/dashboard/components/InsightsCard.jsx
"use client";
import React from "react";

export default function InsightsCard() {
  return (
    <div className="bg-white rounded-xl border border-gray-200 p-4 shadow-sm max-w-7xl mx-auto">
      <h4 className="text-base font-semibold text-gray-800">Aperçus</h4>
      <p className="text-sm text-gray-500 mt-2">
        Obtenez des informations précieuses sur les performances de vos annonces et améliorez leur visibilité.
      </p>
      <a className="mt-3 inline-block text-green-600 font-medium" href="#">Voir les aperçus →</a>

    </div>
  );
}
