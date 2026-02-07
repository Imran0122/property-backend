import ListingsSection from "./components/ListingsSection";
import QuotaSection from "./components/QuotaSection";
import AnalyticsSection from "./components/AnalyticsSection";
import RecentListings from "./components/RecentListings";

export default function DashboardPage() {
  return (
    <div className="max-w-[1200px] mx-auto space-y-6">

      <div className="grid grid-cols-12 gap-6">
        <div className="col-span-7">
          <ListingsSection />
        </div>
        <div className="col-span-5">
          <QuotaSection />
        </div>
      </div>

      <AnalyticsSection />
      <RecentListings />

    </div>
  );
}
