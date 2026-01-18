// 'use client';
// import React, { useState } from "react";
// import {
//   MousePointerClick,
//   Phone,
//   Smartphone,
//   MessageSquare,
//   Mail,
//   BarChart3,
// } from "lucide-react";
// import {
//   ResponsiveContainer,
//   LineChart,
//   Line,
//   CartesianGrid,
//   XAxis,
//   YAxis,
//   Tooltip,
// } from "recharts";

// export default function PerformanceSection() {
//   const [timeFilter, setTimeFilter] = useState("30days");

//   // Dummy chart data
//   const chartData = [
//     { name: "Mon", Views: 10, Clicks: 5, Leads: 2 },
//     { name: "Tue", Views: 25, Clicks: 8, Leads: 4 },
//     { name: "Wed", Views: 30, Clicks: 10, Leads: 5 },
//     { name: "Thu", Views: 15, Clicks: 7, Leads: 3 },
//     { name: "Fri", Views: 40, Clicks: 12, Leads: 6 },
//     { name: "Sat", Views: 28, Clicks: 9, Leads: 4 },
//     { name: "Sun", Views: 20, Clicks: 6, Leads: 2 },
//   ];

//   const metrics = [
//     { label: "Clicks", icon: <MousePointerClick className="w-5 h-5 text-orange-500" />, color: "bg-orange-50" },
//     { label: "Leads", icon: <Phone className="w-5 h-5 text-blue-500" />, color: "bg-blue-50" },
//     { label: "Calls", icon: <Smartphone className="w-5 h-5 text-red-500" />, color: "bg-red-50" },
//     { label: "WhatsApp", icon: <MessageSquare className="w-5 h-5 text-green-500" />, color: "bg-green-50" },
//     { label: "SMS", icon: <Mail className="w-5 h-5 text-purple-500" />, color: "bg-purple-50" },
//     { label: "Emails", icon: <BarChart3 className="w-5 h-5 text-indigo-500" />, color: "bg-indigo-50" },
//   ];

//   return (
//     <div className="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
//       {/* ---------- LEFT SECTION: Performance Overview + Chart ---------- */}
//       <div className="col-span-2 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
//         {/* Header */}
//         <div className="flex justify-between items-center mb-5">
//           <h2 className="text-base font-semibold text-gray-800">
//             Performance Overview
//           </h2>
//           <select
//             value={timeFilter}
//             onChange={(e) => setTimeFilter(e.target.value)}
//             className="border border-gray-300 rounded-md text-sm px-3 py-1.5 text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-600"
//           >
//             <option value="7days">Last 7 Days</option>
//             <option value="30days">Last 30 Days</option>
//             <option value="90days">Last 90 Days</option>
//           </select>
//         </div>

//         {/* Metrics Grid */}
//         <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4 mb-6">
//           {metrics.map((m) => (
//             <div
//               key={m.label}
//               className={`flex flex-col items-center justify-center rounded-lg py-4 border border-gray-100 hover:shadow-sm transition ${m.color}`}
//             >
//               <div className="flex items-center justify-center w-8 h-8 mb-2 bg-white rounded-full shadow-sm">
//                 {m.icon}
//               </div>
//               <p className="text-xs text-gray-500">{m.label}</p>
//               <p className="text-lg font-semibold text-gray-800 mt-1">0</p>
//               <p className="text-xs text-gray-400">No Data</p>
//             </div>
//           ))}
//         </div>

//         {/* Chart Section */}
//         <div className="w-full h-64">
//           <ResponsiveContainer width="100%" height="100%">
//             <LineChart data={chartData} margin={{ top: 10, right: 30, left: 0, bottom: 0 }}>
//               <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
//               <XAxis dataKey="name" tick={{ fontSize: 12 }} stroke="#9ca3af" />
//               <YAxis tick={{ fontSize: 12 }} stroke="#9ca3af" />
//               <Tooltip
//                 contentStyle={{
//                   borderRadius: "10px",
//                   borderColor: "#e5e7eb",
//                 }}
//               />
//               <Line type="monotone" dataKey="Views" stroke="#22c55e" strokeWidth={2} />
//               <Line type="monotone" dataKey="Clicks" stroke="#f59e0b" strokeWidth={2} />
//               <Line type="monotone" dataKey="Leads" stroke="#3b82f6" strokeWidth={2} />
//             </LineChart>
//           </ResponsiveContainer>
//         </div>

//         {/* Empty State */}
//         <div className="mt-6 bg-gray-50 border border-gray-200 rounded-xl py-10 text-center">
//           <h3 className="text-gray-800 font-semibold text-lg">
//             No Active Listings Found
//           </h3>
//           <p className="text-gray-500 text-sm mt-1">
//             Start by adding your first property listing to get insights and leads.
//           </p>
//           <button className="mt-4 bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-sm font-medium transition-all">
//             Add Property
//           </button>
//         </div>
//       </div>

//       {/* ---------- RIGHT SECTION: Insights + Property Shop ---------- */}
//       <div className="flex flex-col gap-6">
//         {/* Insights Card */}
//         <div className="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
//           <h2 className="text-base font-semibold text-gray-800 mb-2">
//             Insights
//           </h2>
//           <p className="text-sm text-gray-500 mb-4 leading-relaxed">
//             Get valuable insights into your listing performance and improve
//             visibility.
//           </p>
//           <button className="text-green-600 text-sm font-medium hover:underline">
//             View Insights →
//           </button>
//         </div>

//         {/* Property Shop */}
//         <div className="bg-gradient-to-r from-green-600 to-green-500 text-white rounded-2xl p-5 shadow-md">
//           <h2 className="text-base font-semibold mb-2">Property Shop</h2>
//           <p className="text-sm opacity-90 mb-4 leading-relaxed">
//             List more properties and reach thousands of potential buyers instantly.
//           </p>
//           <button className="bg-white text-green-700 px-5 py-2 rounded-md text-sm font-semibold hover:bg-gray-100 transition-all">
//             Explore Shop
//           </button>
//         </div>
//       </div>
//     </div>
//   );
// }









'use client';
import React from 'react';
import { ArrowUpRight } from 'lucide-react';

export default function PerformanceSection() {
  return (
    <div className="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
      {/* Header */}
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-lg font-semibold text-gray-800">Aperçu des performances</h2>
        <select className="border border-gray-300 text-gray-600 text-sm rounded-md px-3 py-1 focus:outline-none">
  <option>30 derniers jours</option>
<option>60 derniers jours</option>
<option>90 derniers jours</option>
        </select>
      </div>

      {/* Metrics */}
      <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
        {[
          { label: "Clicks", value: "1,245", change: "+12%", color: "text-orange-500", icon: "⚡" },
          { label: "Leads", value: "540", change: "+8%", color: "text-blue-500", icon: "📞" },
          { label: "Calls", value: "86", change: "-5%", color: "text-red-500", icon: "📱" },
          { label: "WhatsApp", value: "324", change: "+15%", color: "text-green-500", icon: "💬" },
          { label: "SMS", value: "203", change: "+6%", color: "text-purple-500", icon: "✉️" },
          { label: "Emails", value: "127", change: "-3%", color: "text-indigo-500", icon: "📧" },
        ].map((metric) => (
          <div
            key={metric.label}
            className="flex flex-col items-center justify-center bg-gray-50 border border-gray-100 rounded-lg py-4 hover:shadow transition-shadow duration-200"
          >
            <span className={`${metric.color} text-2xl mb-1`}>{metric.icon}</span>
            <p className="text-xs text-gray-500">{metric.label}</p>
            <p className="text-lg font-semibold text-gray-800 mt-1">{metric.value}</p>
            <div className="flex items-center gap-1 text-xs text-gray-500">
              <ArrowUpRight size={12} className={`${metric.change.includes('+') ? 'text-green-500' : 'text-red-500'}`} />
              <span>{metric.change}</span>
            </div>
          </div>
        ))}
      </div>

      {/* Empty State */}
      <div className="mt-8 bg-gray-50 border border-gray-200 rounded-lg py-10 text-center">
        <h3 className="text-gray-800 font-semibold text-lg">Aucune annonce active trouvée</h3>
        <p className="text-gray-500 text-sm mt-1">
          Commencez par ajouter votre première annonce immobilière pour obtenir des analyses et des prospects.
        </p>
        <button className="mt-4 bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-sm font-medium">
          Ajouter une annonce
        </button>
      </div>
    </div>
  );
}
