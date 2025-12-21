// next-frontend/app/dashboard/property-management/components/SlideOverFilters.jsx
"use client";

import { useEffect, useState } from "react";
import { X } from "lucide-react";
import clsx from "clsx";

export default function SlideOverFilters({ open = false, onClose = () => {}, onApply = () => {} }) {
  const [local, setLocal] = useState({
    category: "",
    city: "",
    location: "",
    minPrice: "",
    maxPrice: "",
  });

  // Reset local state when panel opens (optional)
  useEffect(() => {
    if (open) {
      setLocal({ category: "", city: "", location: "", minPrice: "", maxPrice: "" });
      // disable body scroll while open
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
    return () => { document.body.style.overflow = ""; };
  }, [open]);

  function apply() {
    onApply(local);
  }

  if (!open) return null;

  return (
    <div className="fixed inset-0 z-50 flex">
      {/* Backdrop - semi transparent, only visible when open */}
      <div
        className="fixed inset-0 bg-black/30 transition-opacity"
        onClick={onClose}
        aria-hidden="true"
      />

      {/* Panel */}
      <aside className={clsx("ml-auto w-full sm:w-[420px] bg-white h-full shadow-xl transform transition-transform", { "translate-x-0": open })}>
        <div className="p-4 border-b flex items-center justify-between">
          <h3 className="text-lg font-semibold">Advanced Filters</h3>
          <button onClick={onClose} className="p-2 rounded-md hover:bg-gray-100">
            <X />
          </button>
        </div>

        <div className="p-6 space-y-6 overflow-y-auto h-[calc(100vh-72px)]">
          <div>
            <label className="block text-sm text-gray-600 mb-2">Category</label>
            <select value={local.category} onChange={(e)=>setLocal({...local, category:e.target.value})} className="w-full border rounded-md px-3 py-2 text-sm">
              <option value="">Select Category</option>
            </select>
          </div>

          <div>
            <label className="block text-sm text-gray-600 mb-2">City</label>
            <select value={local.city} onChange={(e)=>setLocal({...local, city:e.target.value})} className="w-full border rounded-md px-3 py-2 text-sm">
              <option value="">Select City</option>
            </select>
          </div>

          <div>
            <label className="block text-sm text-gray-600 mb-2">Location</label>
            <select value={local.location} onChange={(e)=>setLocal({...local, location:e.target.value})} className="w-full border rounded-md px-3 py-2 text-sm">
              <option value="">Select Location</option>
            </select>
          </div>

          <div>
            <label className="block text-sm text-gray-600 mb-2">Price Range</label>
            <div className="grid grid-cols-2 gap-3">
              <input value={local.minPrice} onChange={(e)=>setLocal({...local, minPrice:e.target.value})} placeholder="Min" className="border rounded-md px-3 py-2 text-sm" />
              <input value={local.maxPrice} onChange={(e)=>setLocal({...local, maxPrice:e.target.value})} placeholder="Max" className="border rounded-md px-3 py-2 text-sm" />
            </div>
          </div>

          <div className="flex items-center justify-between mt-6">
            <button onClick={() => setLocal({ category: "", city: "", location: "", minPrice: "", maxPrice: "" })} className="px-4 py-2 border rounded-md">Reset Filters</button>
            <div className="flex items-center gap-2">
              <button onClick={apply} className="px-4 py-2 bg-green-600 text-white rounded-md">Search</button>
            </div>
          </div>
        </div>
      </aside>
    </div>
  );
}
