"use client";

import { useState, useEffect } from "react";
import TopFilters from "./components/TopFilters";
import Tabs from "./components/Tabs";
import ListingTable from "./components/ListingTable";
import SlideOverFilters from "./components/SlideOverFilters";

export default function PropertyManagementPage() {
  const [showFilters, setShowFilters] = useState(false);
  const [filters, setFilters] = useState({});
  const [activeTab, setActiveTab] = useState("Active");
  const [listings, setListings] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function load() {
      setLoading(true);
      try {
        const q = new URLSearchParams({ status: activeTab.toLowerCase(), ...filters }).toString();
        const res = await fetch(`/api/properties?${q}`);
        const json = await res.json();
        setListings(json.data ?? json ?? []);
      } catch (e) {
        setListings([
          {
  id: 53246261,
  title: "Belle maison dans la vieille ville",
  price: 9000000, // You can convert to MAD if needed
  city: "Casablanca",
  area_text: "5 Marla",
  bedrooms: 4,
  bathrooms: 3,
  images: [{ url: "/placeholder-house.jpg" }],
  status: "inactive",
},

        ]);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [activeTab, filters]);

  return (
    <div className="min-h-screen bg-gray-50 overflow-x-hidden">
      {/* ✅ Page ko screen me fit karne ke liye 100% width */}
      <div className="w-full max-w-[100%] mx-auto px-6 py-8">

        {/* ✅ Top Filters with proper alignment */}
        <div className="flex flex-wrap items-center justify-between gap-3">
          <TopFilters
            onShowMore={() => setShowFilters(true)}
            onSearch={(f) => setFilters(f)}
          />
        </div>

        {/* ✅ Listings Section only */}
        <div className="mt-8 space-y-6 w-full">
          <div className="bg-white rounded-xl shadow-sm p-5">
            <div className="flex items-center justify-between flex-wrap gap-2">
              <h3 className="text-lg font-semibold">Annonces</h3>
              <a
                href="#"
                className="text-green-600 text-sm font-medium hover:underline"
              >
                Voir toutes les annonces Hectare.ma
              </a>
            </div>

            <div className="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
              {["Actif", "À vendre", "À louer", "Hot"].map((label) => (
                <div key={label} className="bg-gray-50 p-4 rounded-lg text-center">
                  <div className="text-sm text-gray-500">{label}</div>
                  <div className="mt-2 text-2xl font-bold">0</div>
                </div>
              ))}
            </div>
          </div>

          {/* Tabs + Listing Table */}
          <div className="bg-white rounded-xl shadow-sm p-5 w-full overflow-x-auto">
            <Tabs active={activeTab} setActive={setActiveTab} />
            <div className="mt-4 w-full">
              <ListingTable listings={listings} loading={loading} />
            </div>
          </div>
        </div>
      </div>

      {/* ✅ Slide-over Filters */}
      <SlideOverFilters
        open={showFilters}
        onClose={() => setShowFilters(false)}
        onApply={(f) => {
          setFilters(f);
          setShowFilters(false);
        }}
      />
    </div>
  );
}
