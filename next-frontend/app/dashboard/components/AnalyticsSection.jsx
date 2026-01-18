// 'use client';
// import React, { useState } from "react";
// import { BarChart3, MousePointerClick, PhoneCall, MessageCircle, Mail, Eye } from "lucide-react";

// export default function AnalyticsSection() {
//   const [tab, setTab] = useState("views");
//   const [type, setType] = useState("all");
//   const [duration, setDuration] = useState("30");

//   const analyticsTabs = [
//     { id: "views", label: "Views", icon: <Eye size={16} /> },
//     { id: "clicks", label: "Clicks", icon: <MousePointerClick size={16} /> },
//     { id: "leads", label: "Leads", icon: <BarChart3 size={16} /> },
//     { id: "calls", label: "Calls", icon: <PhoneCall size={16} /> },
//     { id: "whatsapp", label: "WhatsApp", icon: <MessageCircle size={16} /> },
//     { id: "emails", label: "Emails", icon: <Mail size={16} /> },
//   ];

//   return (
//     <div className="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mt-6">
//       <h2 className="text-base font-semibold text-gray-800 mb-4">Analytics</h2>

//       {/* Filters */}
//       <div className="flex flex-wrap justify-between items-center border-b border-gray-200 pb-3 mb-4">
//         <div className="flex gap-2">
//           <button
//             onClick={() => setType("all")}
//             className={`text-sm font-medium px-3 py-1 rounded-md ${
//               type === "all" ? "bg-green-100 text-green-700" : "text-gray-600 hover:text-gray-800"
//             }`}
//           >
//             All
//           </button>
//           <button
//             onClick={() => setType("sale")}
//             className={`text-sm font-medium px-3 py-1 rounded-md ${
//               type === "sale" ? "bg-green-100 text-green-700" : "text-gray-600 hover:text-gray-800"
//             }`}
//           >
//             For Sale
//           </button>
//           <button
//             onClick={() => setType("rent")}
//             className={`text-sm font-medium px-3 py-1 rounded-md ${
//               type === "rent" ? "bg-green-100 text-green-700" : "text-gray-600 hover:text-gray-800"
//             }`}
//           >
//             For Rent
//           </button>
//         </div>

//         <select
//           value={duration}
//           onChange={(e) => setDuration(e.target.value)}
//           className="border border-gray-300 rounded-md px-3 py-1 text-sm text-gray-600 focus:outline-none"
//         >
//           <option value="30">Last 30 Days</option>
//           <option value="60">Last 60 Days</option>
//           <option value="90">Last 90 Days</option>
//         </select>
//       </div>

//       {/* Tabs */}
//       <div className="flex gap-6 mb-6 overflow-x-auto scrollbar-hide">
//         {analyticsTabs.map((item) => (
//           <button
//             key={item.id}
//             onClick={() => setTab(item.id)}
//             className={`flex items-center gap-2 pb-2 text-sm font-medium whitespace-nowrap ${
//               tab === item.id
//                 ? "text-green-600 border-b-2 border-green-600"
//                 : "text-gray-500 hover:text-gray-800"
//             }`}
//           >
//             {item.icon}
//             {item.label}
//             <span className="text-gray-400 text-xs ml-1">(0)</span>
//           </button>
//         ))}
//       </div>

//       {/* Empty Analytics State */}
//       <div className="text-center py-10">
//         <div className="mx-auto w-16 h-16 bg-green-50 text-green-600 flex items-center justify-center rounded-full mb-3">
//           <BarChart3 size={26} />
//         </div>
//         <h3 className="text-gray-700 font-semibold mb-1">View In-Depth Insights</h3>
//         <p className="text-gray-500 text-sm mb-4">
//           See the number of views, clicks and leads that your listing has received.
//         </p>
//         <button className="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-sm font-medium">
//           View Insights
//         </button>
//       </div>
//     </div>
//   );
// }






import React from "react";

export default function AnalyticsSection() {
 const items = ["Vues", "Clics", "Prospects", "Appels", "WhatsApp", "SMS", "E-mails"];

  return (
    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
      <div className="flex justify-between items-center mb-4">
        <h2 className="font-semibold text-gray-700">Analytique</h2>

        <div className="flex items-center gap-3 text-sm">
          <button className="text-green-600 font-medium border-b-2 border-green-600 pb-1">
            All
          </button>
         <button className="text-gray-500 hover:text-green-600">À Vendre</button>  
<button className="text-gray-500 hover:text-green-600">À Louer</button>  
<button className="text-gray-500 hover:text-green-600">Derniers 30 jours</button>  

        </div>
      </div>

      <div className="grid grid-cols-2 sm:grid-cols-7 gap-4 mb-6">
        {items.map((item, i) => (
          <div
            key={i}
            className="text-center border rounded-lg p-3 bg-gray-50 hover:bg-gray-100 transition"
          >
            <p className="font-semibold text-gray-800">0</p>
            <p className="text-xs text-gray-500">{item}</p>
            <p className="text-xs text-gray-400 mt-1">Pas de données</p>
          </div>
        ))}
      </div>

      <div className="flex flex-col items-center justify-center py-10 text-center">
        <div className="text-green-600 text-3xl mb-2">📊</div>
       <p className="font-medium text-gray-700">Voir les analyses détaillées</p>
<p className="text-sm text-gray-500 mt-1">
  Voyez le nombre de vues, de clics et de prospects que vos annonces ont reçus.
</p>
      </div>
    </div>
  );
}
