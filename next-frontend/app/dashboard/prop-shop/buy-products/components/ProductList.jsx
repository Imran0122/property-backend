// File: next-frontend/app/dashboard/prop-shop/buy-products/components/ProductList.jsx
"use client";
import React from "react";
import ProductRow from "./ProductRow";

export default function ProductList({ products = [], onAdd }) {
  return (
    <div className="space-y-4">
      {products.map((p) => (
        <ProductRow key={p.id} product={p} onAdd={() => onAdd(p)} />
      ))}
    </div>
  );
}