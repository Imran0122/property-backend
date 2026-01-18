"use client";
import React from "react";
import { FaSearch } from "react-icons/fa";

const OrderHistory = () => {
  return (
    <div className="p-6 bg-gray-50 min-h-screen flex flex-col">
      {/* Header */}
      <div className="flex items-center justify-between mb-4">
        <h1 className="text-lg font-semibold text-gray-800">Mes commandes</h1>
      </div>

      {/* Main Content */}
      <div className="flex-1 bg-white rounded-2xl shadow-sm overflow-x-auto">
        <div className="flex flex-col items-center justify-center h-[400px] text-center">
          <div className="bg-green-50 p-4 rounded-full mb-3">
            <FaSearch className="text-green-600 text-3xl opacity-70" />
          </div>
          <h2 className="text-gray-700 font-semibold text-base">
            Aucun enregistrement trouvé
          </h2>
        </div>
      </div>

      {/* Footer */}
      <footer className="text-center text-gray-400 text-sm mt-8">
        © 2025 – Propulsé par Hectare.ma
      </footer>
    </div>
  );
};

export default OrderHistory;
