// import { ArrowRight, Plus, FolderOpen, Sparkles } from "lucide-react";
// // import { Card } from "@/components/ui/card";
// // import { Button } from "@/components/ui/button";

// export default function RecentListingsPanel()  {
//   return (
//     <div className="rounded-2xl border-slate-200 shadow-sm overflow-hidden hover:shadow-lg transition-all duration-500 group">
//       <div className="p-6 sm:px-8 sm:py-6 border-b border-slate-100 flex items-center justify-between bg-white/50 backdrop-blur-sm">
//         <div className="flex items-center gap-3">
//           <div className="w-2 h-8 bg-primary rounded-full shadow-[0_0_12px_rgba(0,166,81,0.4)]" />
//           <h2 className="text-2xl font-black text-slate-900 tracking-tight">Recent Listings</h2>
//         </div>
//         <a 
//           href="#" 
//           className="text-sm font-bold text-primary hover:text-primary/80 flex items-center gap-2 group/link px-4 py-2 rounded-xl hover:bg-primary/5 transition-all uppercase tracking-wider"
//         >
//           View All Listings
//           <ArrowRight className="w-4 h-4 group-hover/link:translate-x-1.5 transition-transform" />
//         </a>
//       </div>
      
//       <div className="p-16 sm:p-24 flex flex-col items-center justify-center bg-slate-50/30 relative overflow-hidden">
//         {/* Background Decorative Elements */}
//         <div className="absolute top-0 left-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
//         <div className="absolute bottom-0 right-0 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl translate-x-1/3 translate-y-1/3" />

//         <div className="w-40 h-40 mb-10 relative flex items-center justify-center">
//           <div className="absolute inset-0 bg-primary/5 rounded-[2.5rem] rotate-6 group-hover:rotate-12 transition-transform duration-500" />
//           <div className="absolute inset-0 bg-primary/10 rounded-[2.5rem] -rotate-6 group-hover:-rotate-12 transition-transform duration-500" />
//           <div className="relative w-28 h-28 bg-white border-2 border-primary/20 rounded-[2rem] flex items-center justify-center shadow-2xl transform group-hover:scale-110 transition-transform duration-500">
//             <FolderOpen className="w-12 h-12 text-primary shadow-sm" />
//             <div className="absolute -top-3 -right-3 w-10 h-10 bg-primary rounded-2xl flex items-center justify-center shadow-lg shadow-primary/30 border-4 border-white animate-bounce">
//               <Sparkles className="w-5 h-5 text-white" />
//             </div>
//           </div>
//         </div>

//         <h3 className="text-3xl font-black text-slate-900 mb-4 tracking-tight text-center">Your Portfolio is Empty</h3>
//         <p className="text-slate-500 text-center max-w-md mb-10 font-medium leading-relaxed text-lg">
//           Don't miss out on potential leads! Start listing your properties today and reach millions of buyers across Pakistan.
//         </p>
        
//         <button 
//           className="bg-primary hover:bg-primary/90 text-white font-black rounded-2xl shadow-xl shadow-primary/30 hover-elevate px-10 py-8 h-auto text-xl uppercase tracking-widest transform active:scale-95 transition-all group/btn border-b-4 border-emerald-700"
//           onClick={() => {}}
//         >
//           <Plus className="w-6 h-6 mr-3 group-hover/btn:rotate-90 transition-transform" />
//           Post New Listing
//         </button>
//       </div>
//     </div>
//   );
// }




"use client";

import { ArrowRight, Plus, FolderOpen, Sparkles } from "lucide-react";

export default function RecentListingsPanel() {
  return (
    <div className="rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-lg transition-all duration-500 group bg-white">
      
      {/* Header */}
      <div className="p-6 sm:px-8 sm:py-6 border-b border-slate-100 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <div className="w-2 h-8 bg-emerald-600 rounded-full shadow-[0_0_12px_rgba(16,185,129,0.4)]" />
          <h2 className="text-2xl font-black text-slate-900 tracking-tight">
            Recent Listings
          </h2>
        </div>

        <a
          href="#"
          className="text-sm font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-2 px-4 py-2 rounded-xl hover:bg-emerald-50 transition-all uppercase tracking-wider"
        >
          View All Listings
          <ArrowRight className="w-4 h-4 transition-transform group-hover:translate-x-1.5" />
        </a>
      </div>

      {/* Content */}
      <div className="p-16 sm:p-24 flex flex-col items-center justify-center bg-slate-50/30 relative overflow-hidden">

        {/* Background blur shapes */}
        <div className="absolute top-0 left-0 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
        <div className="absolute bottom-0 right-0 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl translate-x-1/3 translate-y-1/3" />

        <div className="w-40 h-40 mb-10 relative flex items-center justify-center">
          <div className="absolute inset-0 bg-emerald-500/5 rounded-[2.5rem] rotate-6 group-hover:rotate-12 transition-transform duration-500" />
          <div className="absolute inset-0 bg-emerald-500/10 rounded-[2.5rem] -rotate-6 group-hover:-rotate-12 transition-transform duration-500" />

          <div className="relative w-28 h-28 bg-white border-2 border-emerald-200 rounded-[2rem] flex items-center justify-center shadow-2xl transform group-hover:scale-110 transition-transform duration-500">
            <FolderOpen className="w-12 h-12 text-emerald-600" />

            <div className="absolute -top-3 -right-3 w-10 h-10 bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-600/30 border-4 border-white">
              <Sparkles className="w-5 h-5 text-white" />
            </div>
          </div>
        </div>

        <h3 className="text-3xl font-black text-slate-900 mb-4 tracking-tight text-center">
          Your Portfolio is Empty
        </h3>

        <p className="text-slate-500 text-center max-w-md mb-10 font-medium leading-relaxed text-lg">
          Don't miss out on potential leads! Start listing your properties today
          and reach millions of buyers across Pakistan.
        </p>

        <button
          className="flex items-center justify-center bg-green-600 hover:bg-emerald-700 text-white font-black rounded-2xl shadow-xl shadow-emerald-600/30 px-10 py-5 text-xl uppercase tracking-widest transform active:scale-95 transition-all border-b-4 border-emerald-800"
        >
          <Plus className="w-6 h-6 mr-3 transition-transform group-hover:rotate-90" />
          Post New Listing
        </button>
      </div>
    </div>
  );
}