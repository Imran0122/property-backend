// next-frontend/app/dashboard/property-management/components/PropertyCard.jsx
"use client";

export default function PropertyCard({ property }) {
  return (
    <div className="flex gap-4 items-start bg-white border border-gray-100 rounded-md p-3">
      <div className="w-32 h-20 bg-gray-100 rounded-md overflow-hidden flex-shrink-0">
        <img src={property.img || "/property.jpg"} alt={property.title} className="w-full h-full object-cover" />
      </div>

      <div className="flex-1">
        <div className="flex items-start justify-between">
          <div>
            <div className="text-sm text-green-600 font-semibold">Rs {Number(property.price).toLocaleString()}</div>
            <div className="text-sm font-medium text-gray-800">{property.title}</div>
            <div className="text-xs text-gray-500 mt-1">{property.city} • {property.area}</div>
            <div className="mt-2 text-xs text-gray-500 flex items-center gap-3">
              <span>🏠 {property.beds}</span>
              <span>🛁 {property.baths}</span>
            </div>
            <div className="text-xs text-gray-400 mt-1">ID {property.id}</div>
          </div>

          <div className="text-right">
            <div className="text-xs text-gray-500">Vues -</div>
            <div className="mt-3">
              <span className="px-3 py-1 rounded-full bg-gray-100 text-sm text-gray-700">Non publié</span>
            </div>
            <div className="mt-3">
              <button className="px-3 py-1 bg-white border rounded-md text-sm">Publier maintenant</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
