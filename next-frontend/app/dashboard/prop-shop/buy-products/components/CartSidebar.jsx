// File: next-frontend/app/dashboard/prop-shop/buy-products/components/CartSidebar.jsx
"use client";
import React from "react";

export default function CartSidebar({ cart = [], total = 0, onRemove, onChangeQty, onClear }) {
  return (
    <aside className="bg-white rounded-xl shadow-sm p-6">
      {cart.length === 0 ? (
        <div className="flex flex-col items-center justify-center h-56 w-full">
          <div className="text-6xl">🛒</div>
          <h4 className="mt-3 text-lg font-semibold">Aucun article</h4>
<p className="text-sm text-gray-500">Ajouté au panier</p>

        </div>
      ) : (
        <div className="w-full">
          <h4 className="text-lg font-semibold mb-4">Résumé de la commande</h4>

          <div className="space-y-3 max-h-72 overflow-auto">
            {cart.map((item) => (
              <div key={item.id} className="flex items-center justify-between bg-gray-50 rounded-md p-3">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-md bg-white flex items-center justify-center text-xl">{item.icon}</div>
                  <div>
                    <div className="text-sm font-medium">{item.title}</div>
                    <div className="text-xs text-gray-500">Rs {item.price.toLocaleString()}</div>
                  </div>
                </div>

                <div className="flex items-center gap-2">
                  <button className="px-2 py-1 border rounded-md" onClick={() => onChangeQty(item.id, item.qty - 1)}>-</button>
                  <div className="px-3 py-1 border rounded-md">{item.qty}</div>
                  <button className="px-2 py-1 border rounded-md" onClick={() => onChangeQty(item.id, item.qty + 1)}>+</button>
                  <button onClick={() => onRemove(item.id)} className="ml-2 text-red-600">🗑️</button>
                </div>
              </div>
            ))}
          </div>

          <div className="mt-4 border-t pt-4">
            <div className="flex items-center justify-between mb-2">
              <div className="text-sm text-gray-500">Sous-total</div>
              <div className="font-semibold">Rs {total.toLocaleString()}</div>
            </div>

            <button className="w-full bg-green-600 text-white py-3 rounded-md mb-2">Passer au paiement</button>
            <button onClick={onClear} className="w-full border py-2 rounded-md text-gray-700">Vider le panier</button>
          </div>
        </div>
      )}
    </aside>
  );
}
