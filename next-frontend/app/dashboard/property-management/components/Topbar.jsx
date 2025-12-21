// next-frontend/app/dashboard/property-management/components/Topbar.jsx
"use client";
import Link from "next/link";

export default function Topbar() {
  return (
    <div className="bg-white border-b border-gray-100">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
        <div className="flex items-center gap-4">
          <button className="md:hidden p-2 rounded-md hover:bg-gray-50">
            {/* hamburger */}
            <svg className="w-6 h-6 text-gray-600" viewBox="0 0 24 24" fill="none">
              <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round"/>
            </svg>
          </button>
          <div className="flex items-center gap-3">
            <div className="w-8 h-8 rounded-md bg-green-600 flex items-center justify-center text-white font-bold">Q</div>
            <div className="text-lg font-semibold text-gray-800">Profolio</div>
          </div>
        </div>

        <div className="flex items-center gap-3">
          <a className="text-sm text-gray-500" href="#">Go to zameen.com</a>
          <button className="px-3 py-1 border rounded-md text-sm text-gray-700">My Listings</button>
          <button className="px-3 py-1 bg-green-600 text-white rounded-md text-sm">Post Listing</button>
          <div className="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-700">U</div>
        </div>
      </div>
    </div>
  );
}
