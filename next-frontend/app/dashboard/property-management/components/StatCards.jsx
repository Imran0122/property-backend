"use client";
// StatCards.jsx
import React from "react";
import { Home, DollarSign, Flame, Star } from "lucide-react";

export default function StatCards() {
  return (
    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div className="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
        <div className="flex items-start justify-between">
          <h3 className="text-lg font-semibold">Listings</h3>
          <a className="text-green-600 text-sm hover:underline" href="#">View all Zameen Listings</a>
        </div>

        <div className="mt-6 grid grid-cols-3 gap-4">
          <div className="col-span-1 flex items-center gap-3">
            <div className="bg-green-50 p-3 rounded-lg">
              <Home className="text-green-600" />
            </div>
            <div>
              <div className="text-sm text-gray-500">Active</div>
              <div className="text-2xl font-bold">0</div>
            </div>
          </div>

          <div className="col-span-2 grid grid-cols-2 gap-3">
            <div className="bg-gray-50 rounded-md p-3">
              <div className="text-xs text-gray-400">For Sale</div>
              <div className="text-lg font-semibold">0</div>
            </div>
            <div className="bg-gray-50 rounded-md p-3">
              <div className="text-xs text-gray-400">For Rent</div>
              <div className="text-lg font-semibold">0</div>
            </div>

            <div className="bg-gray-50 rounded-md p-3">
              <div className="text-xs text-gray-400">Super Hot</div>
              <div className="text-lg font-semibold">0</div>
            </div>
            <div className="bg-gray-50 rounded-md p-3">
              <div className="text-xs text-gray-400">Hot</div>
              <div className="text-lg font-semibold">0</div>
            </div>
          </div>
        </div>
      </div>

      <div className="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
        <h3 className="text-lg font-semibold">Quota and Credits</h3>
        <div className="mt-4">
          <div className="flex justify-between items-center">
            <div>
              <div className="text-xs text-gray-500">Available Quota</div>
              <div className="text-2xl font-bold">0</div>
            </div>
            <div className="text-center">
              <div className="text-xs text-gray-500">Used</div>
              <div className="text-2xl font-bold">0</div>
            </div>
            <div className="text-center">
              <div className="text-xs text-gray-500">Total</div>
              <div className="text-2xl font-bold">0</div>
            </div>
          </div>
          <div className="h-3 bg-gray-100 rounded-full mt-4 overflow-hidden">
            <div style={{ width: "5%" }} className="h-full bg-green-200"></div>
          </div>
        </div>
      </div>
    </div>
  );
}
