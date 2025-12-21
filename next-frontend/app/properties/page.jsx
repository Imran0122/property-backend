import React from "react";
import Sidebar from "./components/Sidebar";
import Topbar from "./components/Topbar";
import ListingsSection from "./components/ListingsSection";
import QuotaSection from "./components/QuotaSection";
import AnalyticsSection from "./components/AnalyticsSection";
import RecentListings from "./components/RecentListings";
import DashboardFooter from "./components/DashboardFooter";

export default function DashboardPage() {
  return (
    <div className="flex min-h-screen bg-[#F9FAFB] text-gray-800">
      {/* Sidebar */}
      <Sidebar />

      {/* Main Content */}
      <div className="flex-1 flex flex-col">
        {/* Topbar */}
        <Topbar />

        {/* Main Dashboard Area */}
        <main className="p-4 md:p-6 space-y-6 max-w-[1400px] mx-auto w-full">
          {/* Listings + Quota Section */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <ListingsSection />
            <QuotaSection />
          </div>

          {/* Analytics */}
          <AnalyticsSection />

          {/* Recent Listings */}
          <RecentListings />

          {/* Footer */}
          <DashboardFooter />
        </main>
      </div>
    </div>
  );
}
