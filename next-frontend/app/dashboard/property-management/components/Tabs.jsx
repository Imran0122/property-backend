// next-frontend/app/dashboard/property-management/components/Tabs.jsx
"use client";
import React from "react";

const tabList = ["Active","Pending","Rejected","Expired","Deleted","Downgraded","Inactive"];

export default function Tabs({ active, setActive }) {
  return (
    <div className="flex gap-2 flex-wrap">
      {tabList.map(tab => (
        <button
          key={tab}
          onClick={() => setActive(tab)}
          className={`px-3 py-1.5 rounded-full text-sm font-medium transition ${
            active === tab ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700'
          }`}
        >
          {tab} <span className="ml-1 text-xs text-gray-400">(0)</span>
        </button>
      ))}
      <div className="ml-auto text-sm text-green-600 cursor-pointer">Show More</div>
    </div>
  );
}
