"use client";

import Link from "next/link";
import { Menu, Search, PlusCircle } from "lucide-react";
import { useState } from "react";

export default function Topbar({ setSidebarOpen }) {
  const [isSearchOpen, setIsSearchOpen] = useState(false);

  return (
    <header className="w-full bg-white border-b border-gray-200 px- sm:px- py-4 flex items-center justify-between sticky top-0 z-50">
      {/* Left Section */}
      <div className="flex items-center gap-3">
        {/* Hamburger Menu (mobile only) */}
        <button
          onClick={() => setSidebarOpen(true)}
          className="lg:hidden p-2 rounded-md hover:bg-gray-100"
        >
          <Menu size={22} className="text-gray-700" />
        </button>

        {/* Logo */}
        <div className="flex items-center gap-2">
          <img src="/logo.svg" alt="Profolio" className="w-7 h-7" />
          <span className="text-lg font-semibold text-gray-800">Portefeuille</span>
        </div>
      </div>

      {/* Right Section */}
      <div className="flex items-center gap-3 sm:gap-4">
        {/* Desktop Links */}
        <div className="hidden lg:flex items-center gap-4">
          <Link
            href="https://hectare.ma"
            target="_blank"
            className="text-sm text-gray-600 hover:text-green-600 flex items-center gap-1"
          >
            <span>Aller sur Hectare.ma</span>
          </Link>

          <Link
            href="#"
            className="text-sm text-gray-700 border border-gray-200 px-3 py-1.5 rounded-md hover:text-green-600 font-medium flex items-center gap-2"
          >
            <img src="/icons/listing.svg" alt="" className="w-4 h-4" />
            Mes annonces
          </Link>

          <Link
            href="#"
            className="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-md text-sm font-medium transition"
          >
            <PlusCircle size={16} />
            Publier une annonce
          </Link>
        </div>

        {/* Search Button (mobile & desktop) */}
        <button
          onClick={() => setIsSearchOpen(!isSearchOpen)}
          className="p-2 rounded-md hover:bg-gray-100"
        >
          <Search size={18} className="text-gray-600" />
        </button>

        {/* User Section */}
        <div className="flex items-center gap-2 border border-gray-200 rounded-md px-2 py-1">
          <span className="hidden sm:block text-sm text-gray-700">itec skill</span>
          <img
            src="/icons/user.svg"
            alt="user"
            className="w-5 h-5 text-gray-700"
          />
        </div>
      </div>
    </header>
  );
}
