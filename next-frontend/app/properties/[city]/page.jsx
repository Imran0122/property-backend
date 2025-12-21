
import PropertyCard from "@/components/PropertyCard";
import { properties } from "@/utils/dummyData";

export default function CityPropertiesPage({ params }) {
  const { city } = params;
  const filtered = properties.filter(
    (p) => p.city.toLowerCase() === city.toLowerCase()
  );

  return (
    <div className="container mx-auto px-6 py-10">
      <h1 className="text-3xl font-bold mb-6 text-gray-800 capitalize">
        Properties in {city}
      </h1>
      {filtered.length > 0 ? (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {filtered.map((p) => (
            <PropertyCard key={p.id} property={p} />
          ))}
        </div>
      ) : (
        <p className="text-gray-500 text-lg">No properties found in this city.</p>
      )}
    </div>
  );
}
