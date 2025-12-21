"use client";
import PropertyCard from "@/app/components/PropertyCard";

export default function PropertyGrid({ properties }) {
  if (!properties?.length) {
    return <p>No properties found.</p>;
  }

  return (
    <div
      style={{
        display: "grid",
        gridTemplateColumns: "repeat(auto-fit, minmax(250px, 1fr))",
        gap: "20px",
      }}
    >
      {properties.map((property) => (
        <PropertyCard key={property.id} property={property} />
      ))}
    </div>
  );
}
