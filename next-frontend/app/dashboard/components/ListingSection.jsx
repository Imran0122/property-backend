import React from "react";
import { Home, Flame, Star, Tag, Building2 } from "lucide-react";

const ListingsSection = () => {
  const listings = [
    { label: "Active", icon: <Home className="text-green-600" />, count: 0 },
    { label: "For Sale", icon: <Tag className="text-yellow-600" />, count: 0 },
    { label: "For Rent", icon: <Building2 className="text-blue-600" />, count: 0 },
    { label: "Super Hot", icon: <Flame className="text-orange-600" />, count: 0 },
    { label: "Hot", icon: <Star className="text-red-600" />, count: 0 },
  ];

  return (
    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
      <div className="flex items-center justify-between mb-4">
        <h2 className="font-semibold text-gray-800">Listings</h2>
        <a href="#" className="text-green-600 text-sm font-medium hover:underline">
          View all Zameen Listings
        </a>
      </div>
      <div className="flex flex-wrap gap-4">
        {listings.map((item, index) => (
          <div
            key={index}
            className="flex flex-col items-center justify-center flex-1 min-w-[100px] bg-gray-50 p-4 rounded-xl border hover:shadow transition-all"
          >
            <div className="text-2xl mb-2">{item.icon}</div>
            <p className="text-gray-500 text-sm">{item.label}</p>
            <span className="font-semibold text-lg">{item.count}</span>
          </div>
        ))}
      </div>
    </div>
  );
};

export default ListingsSection;
