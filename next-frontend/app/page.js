"use client";

import React from "react";
import { Home, Key, Flame, Zap } from "lucide-react";

import Sidebar from "./dashboard/components/Sidebar";
import Topbar from "./dashboard/components/Topbar";
// import PropertyCard from "./components/PropertyCard";
import QuotaCard from "./dashboard/components/QuotaCard";
import InsightsEmpty from "./dashboard/components/InsightsEmpty";
import ListingsEmpty from "./dashboard/components/ListingsEmpty";
import PropertyCard from './dashboard/components/PropertyCard';

export default function Dashboard() {
  return (
    <div className="min-h-screen bg-gray-50 flex">
      <Sidebar />

      <div className="flex-1 ml-64">
        <Topbar />

        <main className="pt-20 px-6">
          {/* Header Section */}
          <div className="mb-6">
            <div className="flex justify-between items-center mb-6">
              <h1 className="text-2xl font-bold text-gray-900">Tableau de bord</h1>
              <a
                href="#"
                className="text-sm text-green-600 hover:text-green-700 flex items-center gap-1"
              >
                Voir toutes les annonces Hectare
                <svg
                  width="16"
                  height="16"
                  viewBox="0 0 16 16"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M6 4l4 4-4 4"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  />
                </svg>
              </a>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
              <PropertyCard
                icon={<Home size={20} />}
                label="For Sale"
                value={0}
                bgColor="bg-green-50"
                iconColor="text-green-600"
              />
              <PropertyCard
                icon={<Key size={20} />}
                label="For Rent"
                value={0}
                bgColor="bg-blue-50"
                iconColor="text-blue-600"
              />
              <PropertyCard
                icon={<Flame size={20} />}
                label="Super Hot"
                value={0}
                bgColor="bg-red-50"
                iconColor="text-red-600"
              />
              <PropertyCard
                icon={<Zap size={20} />}
                label="Hot"
                value={0}
                bgColor="bg-orange-50"
                iconColor="text-orange-600"
              />
            </div>
          </div>

          {/* Quota Card */}
          <div className="mb-6">
            <QuotaCard />
          </div>

          {/* Insights Card */}
          <div className="mb-6">
            <InsightsEmpty />
          </div>

          {/* Listings Empty State */}
          <div className="mb-6">
            <ListingsEmpty />
          </div>

          {/* Footer */}
          <div className="text-center py-6 text-sm text-gray-500">
            © 2025 - Propulsé par Hectare.ma
          </div>
        </main>
      </div>
    </div>
  );
}
