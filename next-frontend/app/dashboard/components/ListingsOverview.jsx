"use client";
// import { Box, Home, Key, Flame, Zap } from "lucide-react";
// import { Card, CardContent } from "@/components/ui/card";
import { useDashboardStats } from "../../hooks/use-dashboard";  
import { Home, Key, Flame, Zap } from "lucide-react";
export default function ListingsOverview() {
  const { data: stats } = useDashboardStats();
  const listings = stats?.listings || { active: 0, forSale: 0, forRent: 0, superHot: 0, hot: 0 };

  const items = [
    { label: "For Sale", count: listings.forSale, icon: Home, color: "text-emerald-600", bg: "bg-emerald-50" },
    { label: "For Rent", count: listings.forRent, icon: Key, color: "text-blue-600", bg: "bg-blue-50" },
    { label: "Super Hot", count: listings.superHot, icon: Zap, color: "text-red-500", bg: "bg-red-50" },
    { label: "Hot", count: listings.hot, icon: Flame, color: "text-orange-500", bg: "bg-orange-50" },
  ];

  return (
<div className="rounded-xl border border-slate-200 shadow-sm overflow-hidden flex-1 bg-white">      <div className="px-6 py-4 flex items-center justify-end">
        <button className="text-primary text-sm font-semibold hover:underline">
          View all Zameen Listings
        </button>
      </div>
      <div className="px-6 pb-8 pt-2">
        <div className="grid grid-cols-2 gap-8">
          {items.map((item, i) => (
            <div key={item.label} className="flex items-center gap-4">
              <div className={`w-12 h-12 rounded-lg ${item.bg} flex items-center justify-center shrink-0`}>
                <item.icon className={`w-6 h-6 ${item.color}`} />
              </div>
              <div>
                <p className="text-sm font-medium text-slate-500">{item.label}</p>
                <h3 className="text-xl font-bold text-slate-900">{item.count}</h3>
              </div>
            </div>
          ))}
        </div>
    </div>
</div>
  );
}
