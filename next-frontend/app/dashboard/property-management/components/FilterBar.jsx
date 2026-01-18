// next-frontend/app/dashboard/property-management/components/FilterBar.jsx
"use client";
import { useState } from "react";

export default function FilterBar({ openDrawer }) {
  const [q, setQ] = useState("");

  return (
    <div className="bg-white border border-gray-100 rounded-lg p-4 shadow-sm">
      <div className="flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
        <div className="flex gap-3 flex-1">
          <input
            className="flex-1 border border-gray-200 rounded-md px-3 py-2 text-sm"
            placeholder="Enter Listing ID"
            value={q}
            onChange={(e) => setQ(e.target.value)}
          />
          <select className="border border-gray-200 rounded-md px-3 py-2 text-sm">
            <option>Select Property Types</option>
          </select>
          <select className="border border-gray-200 rounded-md px-3 py-2 text-sm">
            <option>Select Purpose</option>
          </select>
          <input type="date" className="border border-gray-200 rounded-md px-3 py-2 text-sm" />
        </div>

        <div className="flex items-center gap-3">
          <button onClick={openDrawer} className="text-sm text-green-600">Afficher plus</button>
          <button className="bg-green-600 text-white text-sm px-4 py-2 rounded-md">Rechercher</button>

        </div>
      </div>
    </div>
  );
}
