"use client";
import React from "react";

export default function PropertyCard({ property }) {
  return (
    <div style={{ border: "1px solid #ddd", borderRadius: "10px", padding: "15px" }}>
      <img
        src={property.image || "/placeholder.jpg"}
        alt={property.title}
        style={{ width: "100%", height: "200px", objectFit: "cover", borderRadius: "8px" }}
      />
      <h3 style={{ marginTop: "10px" }}>{property.title}</h3>
      <p>{property.location}</p>
      <strong>${property.price}</strong>
    </div>
  );
}
