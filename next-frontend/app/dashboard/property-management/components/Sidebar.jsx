// next-frontend/app/dashboard/property-management/components/Sidebar.jsx
"use client";
import Link from "next/link";

export default function Sidebar() {
  return (
    <div className="p-6">
      <div className="flex items-center gap-3 mb-8">
        <div className="w-9 h-9 rounded-md bg-green-600 flex items-center justify-center text-white font-bold">Q</div>
        <div>
          <div className="font-bold text-gray-800">Profolio</div>
          <div className="text-xs text-gray-400">Agent</div>
        </div>
      </div>

      <nav className="space-y-1 text-sm">
        <Link href="#" className="block rounded-xl px-4 py-2 bg-green-50 text-green-700 font-semibold">Dashboard</Link>

        <Link href="#" className="block px-4 py-2 rounded-md text-gray-700 hover:bg-gray-50">Post Listing</Link>

        <div>
          <button className="w-full text-left px-4 py-2 rounded-md text-gray-700 hover:bg-gray-50 flex items-center justify-between">
            <span>Property Management</span>
            <span className="text-gray-400">▾</span>
          </button>
          <div className="pl-6 mt-1 space-y-1">
            <Link href="#" className="block text-sm text-gray-600">All Listings</Link>
          </div>
        </div>

        <Link href="#" className="block px-4 py-2 rounded-md text-gray-700 hover:bg-gray-50">Inbox</Link>
        <Link href="#" className="block px-4 py-2 rounded-md text-gray-700 hover:bg-gray-50">Settings</Link>
        <div className="mt-4">
          <button className="w-full bg-green-600 text-white rounded-md py-2">Chat</button>
        </div>
      </nav>
    </div>
  );
}
