"use client";
import { useState } from "react";
import { Ruler, DollarSign } from "lucide-react";

export default function PriceArea() {
  const [unit, setUnit] = useState("Marla");

  return (
    <div className="bg-white border border-gray-200 rounded-xl shadow-sm p-6 font-[Inter] text-gray-800 mt-6">
      {/* Title */}
      <div className="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
        <div className="w-9 h-9 flex items-center justify-center bg-green-100 rounded-md">
          <DollarSign className="text-green-600" size={20} />
        </div>
        <div>
          <h2 className="text-[15px] font-semibold text-gray-900">
            Prix et Surface
          </h2>
          <p className="text-xs text-gray-500">
            Spécifiez le prix de la propriété et la superficie du terrain.
          </p>
        </div>
      </div>

      {/* Price & Area Inputs */}
      <div className="grid md:grid-cols-2 gap-6">
        {/* Price */}
        <div>
          <label className="block text-[13px] font-medium text-gray-900 mb-1">
            Prix
          </label>
          <div className="relative">
            <DollarSign
              size={16}
              className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
            />
            <input
              type="number"
              placeholder="Enter price"
              className="w-full border border-gray-300 rounded-md text-sm pl-9 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
            />
          </div>
        </div>

        {/* Land Area */}
        <div>
          <label className="block text-[13px] font-medium text-gray-900 mb-1">
            Superficie du terrain
          </label>
          <div className="flex gap-3">
            <div className="relative flex-1">
              <Ruler
                size={16}
                className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
              />
              <input
                type="number"
                placeholder="Enter land area"
                className="w-full border border-gray-300 rounded-md text-sm pl-9 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
              />
            </div>
            <select
              value={unit}
              onChange={(e) => setUnit(e.target.value)}
              className="w-[120px] border border-gray-300 rounded-md text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
              <option>250 m²</option>
              <option>500 m²</option>
              <option>Sq. Ft</option>
              <option>Sq. Yd</option>
              <option>1 ha</option>
            </select>
          </div>
        </div>
      </div>

      {/* Estimated Hint */}
      <p className="text-xs text-gray-500 mt-3">
       Remarque : Le prix et la superficie du terrain aident les acheteurs à estimer la valeur avec précision.
      </p>
    </div>
  );
}
