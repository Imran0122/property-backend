// "use client";
// import { useState } from "react";
// // import { Card, CardContent } from "@/components/ui/card";
// import { useDashboardStats } from "../../hooks/use-dashboard";
// // import { cn } from "@/lib/utils";
// import { MoreHorizontal } from "lucide-react";

// export default function QuotaCredits() {
//   const { data: stats } = useDashboardStats();
//   const [activeTab, setActiveTab] = useState("quota");

//   const credits = stats?.credits || { available: 0, used: 0, total: 0 };

//   const tabs = [
//     { id: "quota", label: "Listing Quota", count: 0 },
//     { id: "refresh", label: "Refresh Credits", count: 0 },
//     { id: "hot", label: "Hot Credits", count: 0 },
//     { id: "superhot", label: "Super Hot Credits", count: 0 },
//   ];

//   return (
//     <div className="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
//       <div className="p-6 pb-0">
//         <h2 className="text-lg font-bold text-slate-800 mb-4">Quota and Credits</h2>
//         <div className="flex border-b border-slate-100 overflow-x-auto no-scrollbar gap-6">
//           {tabs.map((tab) => (
//             <button
//               key={tab.id}
//               onClick={() => setActiveTab(tab.id)}
//               className={`pb-3 text-sm font-semibold whitespace-nowrap relative transition-all outline-none ${activeTab === tab.id
//                   ? "text-green-600"
//                   : "text-slate-500 hover:text-slate-700"
//                 }`}
//             >
//               {tab.label} ({tab.count})
//               {activeTab === tab.id && (
//                 <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-t-full" />
//               )}
//             </button>
//           ))}
//           <button className="pb-3 text-slate-400">
//             <MoreHorizontal className="w-5 h-5" />
//           </button>
//         </div>
//       </div>

//       <div className="p-6 pt-8">
//         <div className="grid grid-cols-4 gap-4">
//           <div>
//             <p className="text-xs font-medium text-slate-500 mb-1">Available Quota</p>
//             <h3 className="text-2xl font-bold text-slate-900 leading-none">0</h3>
//           </div>
//           <div>
//             <p className="text-xs font-medium text-slate-500 mb-1">Used</p>
//             <h3 className="text-2xl font-bold text-slate-900 leading-none">0</h3>
//           </div>
//           <div>
//             <p className="text-xs font-medium text-slate-500 mb-1">Total</p>
//             <h3 className="text-2xl font-bold text-slate-900 leading-none">0</h3>
//           </div>
//           <div>
//             <p className="text-xs font-medium text-slate-500 mb-1 text-right">Current Plan</p>
//             <h3 className="text-xl font-bold text-slate-400 leading-none text-right">-</h3>
//           </div>
//         </div>
//         <div className="mt-6 w-full h-1 bg-slate-100 rounded-full overflow-hidden">
//           <div className="w-0 h-full bg-primary" />
//         </div>
//       </div>
//     </div>
//   );
// }






"use client";
import { useState } from "react";
import { useDashboardStats } from "../../hooks/use-dashboard";
import { MoreHorizontal } from "lucide-react";

export default function QuotaCredits() {
  const { data: stats } = useDashboardStats();
  const [activeTab, setActiveTab] = useState("quota");

  const credits = stats?.credits || { available: 0, used: 0, total: 0 };

  const tabs = [
    { id: "quota", label: "Listing Quota", count: 0 },
    { id: "refresh", label: "Refresh Credits", count: 0 },
    { id: "hot", label: "Hot Credits", count: 0 },
    { id: "superhot", label: "Super Hot Credits", count: 0 },
  ];

  return (
    <div className="rounded-xl border border-slate-200 shadow-sm overflow-hidden flex-1 flex flex-col bg-white">
      
      {/* Header */}
      <div className="p-6 pb-0">
        <h2 className="text-lg font-bold text-slate-800 mb-4">
          Quota and Credits
        </h2>

        <div className="flex border-b border-slate-100 overflow-x-auto gap-6">
          {tabs.map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`pb-3 text-sm font-semibold whitespace-nowrap relative transition-all ${
                activeTab === tab.id
                  ? "text-emerald-600"
                  : "text-slate-500 hover:text-slate-700"
              }`}
            >
              {tab.label} ({tab.count})

              {activeTab === tab.id && (
                <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600 rounded-t-full" />
              )}
            </button>
          ))}

          <button className="pb-3 text-slate-400">
            <MoreHorizontal className="w-5 h-5" />
          </button>
        </div>
      </div>

      {/* Content */}
      <div className="p-6 pt-8">
        <div className="grid grid-cols-4 gap-4">
          <div>
            <p className="text-xs font-medium text-slate-500 mb-1">
              Available Quota
            </p>
            <h3 className="text-2xl font-bold text-slate-900 leading-none">
              {credits.available}
            </h3>
          </div>

          <div>
            <p className="text-xs font-medium text-slate-500 mb-1">Used</p>
            <h3 className="text-2xl font-bold text-slate-900 leading-none">
              {credits.used}
            </h3>
          </div>

          <div>
            <p className="text-xs font-medium text-slate-500 mb-1">Total</p>
            <h3 className="text-2xl font-bold text-slate-900 leading-none">
              {credits.total}
            </h3>
          </div>

          <div className="text-right">
            <p className="text-xs font-medium text-slate-500 mb-1">
              Current Plan
            </p>
            <h3 className="text-xl font-bold text-slate-400 leading-none">
              -
            </h3>
          </div>
        </div>

        <div className="mt-6 w-full h-1 bg-slate-100 rounded-full overflow-hidden">
          <div className="h-full bg-emerald-600" style={{ width: "0%" }} />
        </div>
      </div>
    </div>
  );
}