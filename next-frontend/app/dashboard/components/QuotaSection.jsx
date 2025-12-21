// import React from "react";

// const QuotaSection = () => {
//   const quotaData = [
//     { label: "Available Quota", value: 0 },
//     { label: "Used", value: 0 },
//     { label: "Total", value: 0 },
//   ];

//   const tabs = ["Listing Quota (0)", "Refresh Credits (0)", "Hot Credits (0)", "Super Hot Credits (0)"];

//   return (
//     <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
//       <div className="flex flex-col md:flex-row md:items-center justify-between mb-4">
//         <h2 className="font-semibold text-gray-800 mb-2 md:mb-0">Quota and Credits</h2>
//         <div className="flex flex-wrap gap-2">
//           {tabs.map((tab, index) => (
//             <button
//               key={index}
//               className={`text-xs px-3 py-1 rounded-full border ${
//                 index === 0
//                   ? "bg-green-100 text-green-700 border-green-200"
//                   : "text-gray-500 border-gray-200 hover:text-green-600"
//               }`}
//             >
//               {tab}
//             </button>
//           ))}
//         </div>
//       </div>

//       <div className="grid grid-cols-3 gap-4 mt-4">
//         {quotaData.map((q, i) => (
//           <div key={i} className="bg-gray-50 text-center p-3 rounded-xl border">
//             <h3 className="text-gray-600 text-sm">{q.label}</h3>
//             <p className="font-semibold text-lg">{q.value}</p>
//           </div>
//         ))}
//       </div>
//     </div>
//   );
// };

// export default QuotaSection;














// next-frontend/app/dashboard/components/QuotaSection.jsx
"use client";

export default function QuotaSection() {
  // This file is intentionally small because TopStats already contains quota.
  // Keep for modularity / future dynamic data.
  return null;
}
