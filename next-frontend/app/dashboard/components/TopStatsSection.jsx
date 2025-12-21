// 'use client';
// import React from 'react';
// import { Home, Flame, Zap, DollarSign, RefreshCw } from 'lucide-react';

// const TopStatsSection = () => {
//   return (
//     <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
//       {/* ---------------- LISTINGS BOX ---------------- */}
//       <div className="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 p-6">
//         {/* Header */}
//         <div className="flex justify-between items-center mb-9">
//           <h3 className="text-[1px] font-semibold text-gray-900">Listings</h3>
//           <a
//             href="#"
//             className="text-green-600 hover:text-green-700 text-sm font-medium hover:underline"
//           >
//             View all Zameen Listings
//           </a>
//         </div>

//         {/* Content */}
//         <div className="flex flex-wrap items-start">
//           {/* Active Section (Left) */}
//           <div className="flex items-center space-x- pr- border-r border-gray-100 flex-shrink-0">
//             <div className="bg-green-50 p-3 rounded-lg flex items-center justify-center">
//               <Home className="text-green-600 w-5 h-5" />
//             </div>
//             <div>
//               <p className="text-sm text-gray-600">Active</p>
//               <p className="text-2xl font-semibold text-gray-900 leading-tight">0</p>
//             </div>
//           </div>

//           {/* Other Stats */}
//           <div className="grid grid-cols-2 sm:grid-cols-2 gap-x-10 gap-y-3 pl-6 flex-1">
//             {[
//               { icon: DollarSign, color: 'text-green-600', bg: 'bg-green-50', label: 'For Sale' },
//               { icon: RefreshCw, color: 'text-blue-600', bg: 'bg-blue-50', label: 'For Rent' },
//               { icon: Flame, color: 'text-red-500', bg: 'bg-red-50', label: 'Super Hot' },
//               { icon: Zap, color: 'text-yellow-500', bg: 'bg-yellow-50', label: 'Hot' },
//             ].map(({ icon: Icon, color, bg, label }, i) => (
//               <div key={i} className="flex items-center space-x-2">
//                 <div className={`${bg} p-2 rounded-md flex items-center justify-center`}>
//                   <Icon className={`${color} w-4 h-4`} />
//                 </div>
//                 <div>
//                   <p className="text-[13px] text-gray-700">{label}</p>
//                   <p className="font-bold text-gray-900 text-[13px] leading-tight">0</p>
//                 </div>
//               </div>
//             ))}
//           </div>
//         </div>
//       </div>

//       {/* ---------------- QUOTA & CREDITS BOX ---------------- */}
//       <div className="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 p-6">
//         <h3 className="text-[15px] font-semibold text-gray-900 mb-5">
//           Quota and Credits
//         </h3>

//         {/* Scrollable Tabs */}
//         <div className="flex overflow-x-auto space-x-6 border-b border-gray-100 mb-6 scrollbar-hide">
//           {[
//             'Listing Quota (0)',
//             'Refresh Credits (0)',
//             'Hot Credits (0)',
//             'Super Hot Credits (0)',
//           ].map((tab, i) => (
//             <button
//               key={i}
//               className={`whitespace-nowrap pb-2 text-[12px] font-medium transition-colors ${
//                 i === 0
//                   ? 'text-green-600 border-b-2 border-green-600'
//                   : 'text-gray-500 hover:text-gray-700'
//               }`}
//             >
//               {tab}
//             </button>
//           ))}
//         </div>

//         {/* Quota Stats */}
//         <div className="grid grid-cols-3 text-center mb-5">
//           {['Available Quota', 'Used', 'Total'].map((label, i) => (
//             <div key={i}>
//               <p className="text-[13px] text-gray-600">{label}</p>
//               <p className="text-lg font-semibold text-gray-900 mt-1">0</p>
//             </div>
//           ))}
//         </div>

//         {/* Progress Bar */}
//         <div className="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
//           <div
//             className="bg-green-500 h-2 rounded-full transition-all duration-300"
//             style={{ width: '0%' }}
//           />
//         </div>
//       </div>
//     </div>
//   );
// };

// export default TopStatsSection;













"use client";

import { Home, DollarSign, RefreshCw, Flame, Bolt } from "lucide-react";

export default function TopStatsSection() {
  return (
    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {/* Listings Box */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div className="flex items-center justify-between mb-5">
          <h3 className="text-[15px] font-semibold text-gray-800">Listings</h3>
          <a href="#" className="text-green-600 hover:text-green-700 text-sm font-medium">View all Zameen Listings</a>
        </div>

        <div className="flex items-center">
          {/* Left - Active */}
          <div className="flex items-center gap-3 pr-6 border-r border-gray-100">
            <div className="bg-green-50 p-3 rounded-lg">
              <Home size={18} className="text-green-600" />
            </div>
            <div>
              <p className="text-sm text-gray-600">Active</p>
              <p className="text-2xl font-bold text-gray-900">0</p>
            </div>
          </div>

          {/* Right small stats */}
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-6 pl-6 flex-1">
            <div>
              <div className="flex items-center gap-2 mb-1">
                <div className="bg-green-50 p-2 rounded-md">
                  <DollarSign size={14} className="text-green-600" />
                </div>
                <span className="text-sm text-gray-700">For Sale</span>
              </div>
              <p className="text-base font-bold text-gray-900">0</p>
            </div>

            <div>
              <div className="flex items-center gap-2 mb-1">
                <div className="bg-blue-50 p-2 rounded-md">
                  <RefreshCw size={14} className="text-blue-600" />
                </div>
                <span className="text-sm text-gray-700">For Rent</span>
              </div>
              <p className="text-base font-bold text-gray-900">0</p>
            </div>

            <div>
              <div className="flex items-center gap-2 mb-1">
                <div className="bg-red-50 p-2 rounded-md">
                  <Flame size={14} className="text-red-500" />
                </div>
                <span className="text-sm text-gray-700">Super Hot</span>
              </div>
              <p className="text-base font-bold text-gray-900">0</p>
            </div>

            <div>
              <div className="flex items-center gap-2 mb-1">
                <div className="bg-yellow-50 p-2 rounded-md">
                  <Bolt size={14} className="text-yellow-500" />
                </div>
                <span className="text-sm text-gray-700">Hot</span>
              </div>
              <p className="text-base font-bold text-gray-900">0</p>
            </div>
          </div>
        </div>
      </div>

      {/* Quota & Credits Box */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 className="text-[15px] font-semibold text-gray-800 mb-4">Quota and Credits</h3>

        <div className="flex items-center gap-6 border-b border-gray-100 pb-4 mb-4">
          <button className="text-sm font-medium text-green-600 border-b-2 border-green-600 pb-2">Listing Quota (0)</button>
          <button className="text-sm text-gray-500">Refresh Credits (0)</button>
          <button className="text-sm text-gray-500">Hot Credits (0)</button>
          <button className="text-sm text-gray-500">Super Hot Credits (0)</button>
        </div>

        <div className="grid grid-cols-3 gap-4 text-center mb-4">
          <div>
            <p className="text-xs text-gray-500">Available Quota</p>
            <p className="font-bold text-gray-900">0</p>
          </div>
          <div>
            <p className="text-xs text-gray-500">Used</p>
            <p className="font-bold text-gray-900">0</p>
          </div>
          <div>
            <p className="text-xs text-gray-500">Total</p>
            <p className="font-bold text-gray-900">0</p>
          </div>
        </div>

        <div className="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
          <div className="bg-green-500 h-2 w-1/4 rounded-full transition-all" />
        </div>
      </div>
    </div>
  );
}
