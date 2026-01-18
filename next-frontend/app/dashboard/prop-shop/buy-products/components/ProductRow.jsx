// File: next-frontend/app/dashboard/prop-shop/buy-products/components/ProductRow.jsx
"use client";
import React from "react";

export default function ProductRow({ product, onAdd }) {
  return (
    <div className="flex items-center justify-between border border-gray-100 rounded-md p-4 hover:shadow-sm transition">
      <div className="flex items-start gap-4">
        <div className="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center text-2xl">{product.icon}</div>
        <div>
          <div className="flex items-center gap-2">
            <h4 className="font-semibold text-gray-800">{product.title}</h4>
            {product.badge && (
              <span className="text-xs bg-green-50 text-green-700 rounded-full px-2 py-0.5">{product.badge}</span>
            )}
          </div>
          <p className="text-sm text-gray-500 mt-1 max-w-2xl">{product.subtitle}</p>
        </div>
      </div>

      <div className="flex items-center gap-4">
        <div className="text-gray-700 font-semibold">Rs {product.price.toLocaleString()}</div>
        <button
          onClick={onAdd}
          className="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition"
        >
          Ajouter au panier
        </button>
      </div>
    </div>
  );
}