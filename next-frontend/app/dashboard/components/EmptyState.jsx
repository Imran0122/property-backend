// // app/dashboard/components/EmptyState.jsx (Revised for 100% match)
// import React from 'react';

// const EmptyState = () => {
//   return (
//     <div className="bg-white p-8 rounded-lg shadow-sm border border-gray-200 text-center flex flex-col items-center justify-center min-h-[300px]"> {/* rounded-lg, border-gray-200 */}
//       <div className="mb-4">
//         <span className="text-gray-400 text-8xl leading-none opacity-60">📝</span> {/* text-8xl, leading-none, opacity-60 */}
//       </div>
//       <h2 className="text-xl font-semibold mb-2 text-gray-800">No Active Listings</h2>
//       <p className="text-gray-500 text-sm mb-6 max-w-sm mx-auto">Your active listings will appear here</p>
//       <button className="bg-green-600 text-white px-6 py-3 rounded-md flex items-center justify-center hover:bg-green-700 transition-colors shadow-md font-medium"> {/* rounded-md, font-medium */}
//         <span className="mr-2 text-lg leading-none">➕</span> Post Listing
//       </button>
//     </div>
//   );
// };

// export default EmptyState;




// next-frontend/app/dashboard/components/EmptyState.jsx
"use client";
import React from "react";

export default function EmptyState() {
  return (
    <div className="bg-white rounded-xl border border-gray-200 p-8 shadow-sm text-center">
      <div className="mb-4">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
          <path d="M3 5h18v14H3z" stroke="#D1D5DB" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round"/>
        </svg>
      </div>
      <h3 className="text-lg font-semibold text-gray-800">No Active Listings</h3>
      <p className="text-sm text-gray-500 mt-1">Your active listings will appear here</p>
      <button className="mt-4 bg-green-600 text-white px-4 py-2 rounded-md">Post Listing</button>
    </div>
  );
}
