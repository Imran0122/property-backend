// "use client";

// import { useState } from "react";
// import Sidebar from "./components/Sidebar";
// import Topbar from "./components/Topbar";

// export default function DashboardLayout({ children }) {
//   const [sidebarOpen, setSidebarOpen] = useState(false);

//   return (
//     <div className="flex h-screen overflow-hidden bg-gray-50">
//       {/* Sidebar (Sticky on Desktop, Slide-in on Mobile) */}
//       <aside
//         className={`fixed top-0 left-0 z-40 h-full bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out
//         ${sidebarOpen ? "translate-x-0" : "-translate-x-full"} 
//         lg:translate-x-0 lg:static lg:block w-64`}
//       >
//         <Sidebar setSidebarOpen={setSidebarOpen} />
//       </aside>

//       {/* Overlay when sidebar is open (mobile only) */}
//       {sidebarOpen && (
//         <div
//           className="fixed inset-0 bg-black bg-opacity-40 z-30 lg:hidden"
//           onClick={() => setSidebarOpen(false)}
//         ></div>
//       )}

//       {/* Main Section */}
//       <div className="flex flex-col flex-1 min-w-0 h-full overflow-hidden">
//         {/* Sticky Topbar */}
//         <div className="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
//           <Topbar setSidebarOpen={setSidebarOpen} />
//         </div>

//         {/* Scrollable Content */}
//         <main className="flex-1 overflow-y-auto px-4 sm:px-6 py-6">
//           {children}
//         </main>
//       </div>
//     </div>
//   );
// }

















"use client";

import { useState } from "react";
import Sidebar from "./components/Sidebar";
import Topbar from "./components/Topbar";
import { MessageSquareText } from "lucide-react";

export default function DashboardLayout({ children }) {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div className="flex min-h-screen bg-gray-50">
      {/* Sidebar - fixed on desktop, slide-in on mobile */}
      <Sidebar sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />

      {/* Mobile overlay */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 z-40 bg-black/30 lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* Main area */}
      <div className="flex-1 flex flex-col">
        {/* Sticky topbar */}
        <div className="sticky top-0 z-50">
          <Topbar setSidebarOpen={setSidebarOpen} />
        </div>

        <main className="p-6 flex-1 overflow-y-auto">{children}</main>
      </div>
    </div>
  );
}

export function Fab() {
  return (
    <button
      className="fixed bottom-6 left-6 md:left-24 bg-primary hover:bg-primary/90 text-white font-semibold px-6 py-3.5 rounded-full shadow-lg shadow-primary/30 flex items-center gap-2 hover:-translate-y-1 transition-all duration-300 z-50 focus:outline-none focus:ring-4 focus:ring-primary/20"
      onClick={() => {}}
    >
      <MessageSquareText className="w-5 h-5" />
      Chat
    </button>
  );
}
