"use client";
import ListingsOverview from "./components/ListingsOverview";
import QuotaCredits from "./components/QuotaCredits";
import AnalyticsPanel from "./components/AnalyticsPanel";
import RecentListingsPanel from "./components/RecentListingsPanel";

export default function DashboardPage() {
  return (
    <div className="max-w-[1200px] mx-auto space-y-6">

      <div className="grid grid-cols-12 gap-6">
        <div className="col-span-7">
          <ListingsOverview />
        </div>
        <div className="col-span-5">
          <QuotaCredits />
        </div>
      </div>

      <AnalyticsPanel />
      <RecentListingsPanel />

    </div>
  );
}