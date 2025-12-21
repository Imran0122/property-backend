// 'use client';
// import { BarChart3, Home, Flame, Phone, Mail, MessageSquare, Eye } from 'lucide-react';

// export default function DashboardPage() {
//   return (
//     <div className="p-6 bg-gray-50 min-h-screen">
//       {/* Top Row */}
//       <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
//         {/* Listings Overview */}
//         <div className="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 col-span-2">
//           <div className="flex justify-between items-center mb-5">
//             <h2 className="text-gray-800 font-semibold">Listings</h2>
//             <a href="#" className="text-green-600 text-sm font-medium hover:underline">
//               View all Zameen Listings
//             </a>
//           </div>
//           <div className="grid grid-cols-5 gap-4 text-center">
//             {[
//               { label: 'Active', value: 0, icon: <Home className="text-green-600" /> },
//               { label: 'For Sale', value: 0, icon: <span className="text-green-500">$</span> },
//               { label: 'For Rent', value: 0, icon: <span className="text-blue-500">🏠</span> },
//               { label: 'Super Hot', value: 0, icon: <Flame className="text-red-500" /> },
//               { label: 'Hot', value: 0, icon: <Flame className="text-orange-400" /> },
//             ].map((item, i) => (
//               <div
//                 key={i}
//                 className="flex flex-col items-center justify-center border border-gray-100 rounded-xl py-3 hover:bg-gray-50 transition"
//               >
//                 <div className="mb-1">{item.icon}</div>
//                 <p className="text-gray-800 text-sm font-medium">{item.label}</p>
//                 <p className="text-gray-500 text-xs">{item.value}</p>
//               </div>
//             ))}
//           </div>
//         </div>

//         {/* Quota & Credits */}
//         <div className="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
//           <h2 className="text-gray-800 font-semibold mb-3">Quota and Credits</h2>
//           <p className="text-sm text-gray-600 mb-4">Listing Quota (0)</p>
//           <div className="flex justify-between text-sm text-gray-700">
//             <div>
//               <p className="text-gray-500">Available Quota</p>
//               <p className="font-semibold">0</p>
//             </div>
//             <div>
//               <p className="text-gray-500">Used</p>
//               <p className="font-semibold">0</p>
//             </div>
//             <div>
//               <p className="text-gray-500">Total</p>
//               <p className="font-semibold">0</p>
//             </div>
//           </div>
//         </div>
//       </div>

//       {/* Performance Overview */}
//       <div className="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
//         <div className="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 col-span-2">
//           <div className="flex justify-between items-center mb-4">
//             <h2 className="text-gray-800 font-semibold">Performance Overview</h2>
//             <select className="border border-gray-200 rounded-md text-sm text-gray-600 px-2 py-1">
//               <option>Last 30 Days</option>
//               <option>Last 7 Days</option>
//             </select>
//           </div>

//           {/* Analytics Cards */}
//           <div className="grid grid-cols-6 gap-3 mb-6">
//             {[
//               { label: 'Clicks', icon: <Eye className="text-orange-400" /> },
//               { label: 'Leads', icon: <MessageSquare className="text-blue-400" /> },
//               { label: 'Calls', icon: <Phone className="text-green-500" /> },
//               { label: 'WhatsApp', icon: <MessageSquare className="text-green-600" /> },
//               { label: 'SMS', icon: <MessageSquare className="text-gray-400" /> },
//               { label: 'Emails', icon: <Mail className="text-purple-400" /> },
//             ].map((metric, i) => (
//               <div
//                 key={i}
//                 className="bg-gray-50 border border-gray-100 rounded-xl p-3 flex flex-col items-center justify-center"
//               >
//                 {metric.icon}
//                 <p className="text-xs text-gray-500 mt-1">{metric.label}</p>
//                 <p className="font-semibold text-sm text-gray-800">0</p>
//                 <p className="text-[11px] text-gray-400">No Data</p>
//               </div>
//             ))}
//           </div>

//           {/* Chart Placeholder */}
//           <div className="h-48 flex items-center justify-center border border-dashed border-gray-200 rounded-xl text-gray-400 text-sm">
//             Chart Component Here
//           </div>

//           {/* No Active Listings */}
//           <div className="mt-6 border-t border-gray-100 pt-6 text-center">
//             <p className="text-gray-800 font-semibold mb-1">No Active Listings Found</p>
//             <p className="text-gray-500 text-sm mb-4">
//               Start by adding your first property listing to get insights and leads.
//             </p>
//             <button className="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-sm font-medium">
//               Add Property
//             </button>
//           </div>
//         </div>

//         {/* Right Side Cards */}
//         <div className="flex flex-col gap-4">
//           {/* Insights */}
//           <div className="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
//             <h3 className="text-gray-800 font-semibold mb-1">Insights</h3>
//             <p className="text-gray-500 text-sm mb-3">
//               Get valuable insights into your listing performance and improve visibility.
//             </p>
//             <a href="#" className="text-green-600 text-sm font-medium hover:underline">
//               View Insights →
//             </a>
//           </div>

//           {/* Property Shop */}
//           <div className="bg-green-600 text-white rounded-2xl shadow-sm p-5">
//             <h3 className="font-semibold mb-1">Property Shop</h3>
//             <p className="text-sm text-green-50 mb-3">
//               List more properties and reach thousands of potential buyers instantly.
//             </p>
//             <button className="bg-white text-green-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-100">
//               Explore Shop
//             </button>
//           </div>
//         </div>
//       </div>
//     </div>
//   );
// }
















"use client";

import TopStatsSection from "./components/TopStatsSection";
import PerformanceSection from "./components/PerformanceSection";
import ListingTable from "./components/ListingTable";

export default function DashboardPage() {
  return (
    <div className="p-6 bg-gray-50 min-h-screen">
      {/* Top box area: Listings + Quota */}
      <TopStatsSection />

      {/* Performance + Right-side cards */}
      <PerformanceSection />

      {/* Listings table - full width */}
      <div className="mt-6">
        <ListingTable />
      </div>
    </div>
  );
}

