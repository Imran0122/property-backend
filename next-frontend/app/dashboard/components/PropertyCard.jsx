'use client';
import React from "react";

export default function PropertyCard({ icon, label, value, bgColor, iconColor }) {
  return (
    <div
      className={`flex items-center justify-between p-4 rounded-xl shadow-sm border border-gray-200 ${bgColor}`}
    >
      <div className="flex items-center space-x-3">
        <div className={`p-2 rounded-full bg-white shadow-sm ${iconColor}`}>
          {icon}
        </div>
        <div>
          <p className="text-sm font-medium text-gray-500">{label}</p>
          <h3 className="text-xl font-semibold text-gray-900">{value}</h3>
        </div>
      </div>
    </div>
  );
}
