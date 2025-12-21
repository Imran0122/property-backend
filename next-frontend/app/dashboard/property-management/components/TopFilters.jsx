// next-frontend/app/dashboard/property-management/components/TopFilters.jsx
"use client";
import { useState } from "react";
import { Search, Sliders } from "lucide-react";

export default function TopFilters({ onShowMore = () => {}, onSearch = () => {} }) {
  const [listingId, setListingId] = useState("");
  const [propertyType, setPropertyType] = useState("");
  const [purpose, setPurpose] = useState("");
  const [dateRange, setDateRange] = useState("");

  function submit(e) {
    e?.preventDefault();
    onSearch({ listingId, propertyType, purpose, dateRange });
  }

  return (
    // NOTE: removed outer border and used a simple shadow / rounded container
    <form onSubmit={submit} className="bg-white rounded-xl p-4 shadow-sm">
      <div className="flex flex-col md:flex-row md:items-center gap-3">
        {/* Left controls: ID, Type, Purpose */}
        <div className="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
          <input
            name="listingId"
            value={listingId}
            onChange={(e) => setListingId(e.target.value)}
            placeholder="Enter Listing ID"
            className="border rounded-md px-3 py-2 text-sm w-full sm:w-48"
          />

          <select
            value={propertyType}
            onChange={(e) => setPropertyType(e.target.value)}
            className="border rounded-md px-3 py-2 text-sm w-full sm:w-48"
          >
            <option value="">Select Property Types</option>
            <option>House</option>
            <option>Plot</option>
            <option>Commercial</option>
          </select>

          <select
            value={purpose}
            onChange={(e) => setPurpose(e.target.value)}
            className="border rounded-md px-3 py-2 text-sm w-full sm:w-48"
          >
            <option value="">Select Purpose</option>
            <option>Sell</option>
            <option>Rent</option>
          </select>
        </div>

        {/* Right controls: Date range, Show More, Search */}
        <div className="flex items-center gap-3 ml-auto">
          <input
            value={dateRange}
            onChange={(e) => setDateRange(e.target.value)}
            placeholder="Select Date Range"
            className="border rounded-md px-3 py-2 text-sm w-48"
          />

          <button
            type="button"
            onClick={onShowMore}
            className="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900"
            aria-label="Open advanced filters"
          >
            <Sliders size={16} /> Show More
          </button>

          <button
            type="submit"
            className="bg-green-600 text-white px-4 py-2 rounded-md flex items-center gap-2"
          >
            <Search size={16} /> Search
          </button>
        </div>
      </div>
    </form>
  );
}
