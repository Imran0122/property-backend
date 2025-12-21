"use client";

import { useState } from "react";
import Link from "next/link";
import {
  LayoutDashboard,
  PlusCircle,
  Building2,
  Inbox,
  Settings,
  ShoppingBag,
  ChevronDown,
  ChevronUp,
  X,
} from "lucide-react";

export default function Sidebar({ sidebarOpen, setSidebarOpen }) {
  const [openPropMgmt, setOpenPropMgmt] = useState(false);
  const [openPropShop, setOpenPropShop] = useState(false);
  const [active, setActive] = useState("Dashboard");

  const linkClass = (name) =>
    `flex items-center gap-3 px-5 py-2.5 rounded-lg text-[15px] font-medium whitespace-nowrap transition-all duration-150 ${
      active === name
        ? "bg-green-100 text-green-700"
        : "text-gray-700 hover:bg-gray-100"
    }`;

  return (
    <aside
      className={`fixed lg:sticky top-0 left-0 z-50 h-screen bg-white border-r border-gray-200 flex flex-col p-4 transform transition-transform duration-300 ease-in-out
      ${sidebarOpen ? "translate-x-0" : "-translate-x-full lg:translate-x-0"}
      w-64`}
    >
      {/* Logo Header */}
      <div className="flex items-center justify-between mb-10 px-2">
        <div className="flex items-center gap-2">
          <div className="w-6 h-6 bg-green-600 rounded"></div>
          <span className="text-lg font-semibold tracking-tight">Profolio</span>
        </div>
        <button
          className="lg:hidden p-1 rounded hover:bg-gray-100"
          onClick={() => setSidebarOpen(false)}
        >
          <X size={18} />
        </button>
      </div>

      {/* Navigation */}
      <nav className="flex flex-col gap-2.5 text-sm overflow-y-auto">
        <Link
          href="#"
          onClick={() => setActive("Dashboard")}
          className={linkClass("Dashboard")}
        >
          <LayoutDashboard size={16} />
          Dashboard
        </Link>

        <Link
          href="#"
          onClick={() => setActive("Post Listing")}
          className={linkClass("Post Listing")}
        >
          <PlusCircle size={16} />
          Post Listing
        </Link>

        {/* Property Management */}
        <div className="mt-1">
          <button
            onClick={() => setOpenPropMgmt(!openPropMgmt)}
            className={`w-full flex items-center justify-between px-5 py-2.5 rounded-lg text-[15px] font-medium whitespace-nowrap ${
              openPropMgmt
                ? "text-green-700 bg-gray-50"
                : "text-gray-700 hover:bg-gray-100"
            }`}
          >
            <span className="flex items-center gap-3">
              <Building2 size={16} />
              Property Management
            </span>
            {openPropMgmt ? <ChevronUp size={14} /> : <ChevronDown size={14} />}
          </button>

          {openPropMgmt && (
            <div className="ml-10 mt-1 flex flex-col gap-1 text-gray-600">
              <Link href="#" className="hover:text-green-600 text-sm">
                All Listings
              </Link>
            </div>
          )}
        </div>

        <Link
          href="#"
          onClick={() => setActive("Inbox")}
          className={linkClass("Inbox")}
        >
          <Inbox size={16} />
          Inbox
        </Link>

        <Link
          href="#"
          onClick={() => setActive("Settings")}
          className={linkClass("Settings")}
        >
          <Settings size={16} />
          Settings
        </Link>

        {/* Prop Shop */}
        <div>
          <button
            onClick={() => setOpenPropShop(!openPropShop)}
            className={`w-full flex items-center justify-between px-5 py-2.5 rounded-lg text-[15px] font-medium whitespace-nowrap ${
              openPropShop
                ? "text-green-700 bg-gray-50"
                : "text-gray-700 hover:bg-gray-100"
            }`}
          >
            <span className="flex items-center gap-3">
              <ShoppingBag size={16} />
              Prop Shop
            </span>
            {openPropShop ? <ChevronUp size={14} /> : <ChevronDown size={14} />}
          </button>

          {openPropShop && (
            <div className="ml-10 mt-1 flex flex-col gap-1 text-gray-600">
              <Link href="#" className="hover:text-green-600 text-sm">
                Buy Products
              </Link>
              <Link href="#" className="hover:text-green-600 text-sm">
                Order History
              </Link>
            </div>
          )}
        </div>
      </nav>

      {/* Chat Button */}
      <div className="mt-auto pt-4">
        <button className="bg-green-600 text-white w-full py-2 rounded-full font-medium flex items-center justify-center gap-2 hover:bg-green-700 transition">
          💬 Chat
        </button>
      </div>
    </aside>
  );
}
