// next-frontend/app/dashboard/property-management/components/ListingRow.jsx
"use client";
import React from "react";

export default function ListingRow({ item }) {
  return (
    <tr className="border-b last:border-b-0">
      <td className="px-4 py-5 align-top w-1/2">
        <div className="flex gap-4">
          <div className="w-28 h-20 bg-gray-100 rounded-md overflow-hidden flex items-center justify-center">
            <img src={item.images?.[0]?.url ?? "/placeholder-house.jpg"} alt="" className="object-cover w-full h-full" />
          </div>
          <div>
            <div className="text-green-700 font-semibold">Rs {Number(item.price).toLocaleString()}</div>
            <div className="text-sm font-bold text-gray-800">{item.title ?? "Property title"}</div>
            <div className="text-xs text-gray-500 mt-1">{item.city} • {item.area_text}</div>
            <div className="text-xs text-gray-400 mt-1">ID {item.id}</div>
          </div>
        </div>
      </td>

      <td className="px-4 py-5 align-top">
        <div className="text-xs text-gray-500">Platform</div>
        <div className="text-sm">—</div>
      </td>

      <td className="px-4 py-5 align-top">
        <div className="text-xs text-gray-500">Views -</div>
        <div className="text-xs text-gray-500">Clicks -</div>
      </td>

      <td className="px-4 py-5 align-top">
        <div className="text-xs text-gray-500">—</div>
      </td>

      <td className="px-4 py-5 align-top">
        <span className="px-3 py-1 rounded-full bg-gray-100 text-sm">Not Published</span>
      </td>

      <td className="px-4 py-5 align-top">
        <div className="flex items-center gap-2">
          <button className="px-3 py-1 border rounded-md">Publish Now</button>
        </div>
      </td>
    </tr>
  );
}
