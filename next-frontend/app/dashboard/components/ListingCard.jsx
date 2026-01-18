"use client";
import { useState } from "react";
import { apiFetch } from "../../utils/api";

export default function ListingCard({ property }) {
  const [deleting, setDeleting] = useState(false);

  async function handleDelete() {
    if (!confirm("Delete this listing?")) return;
    setDeleting(true);
    try {
      await apiFetch(`/api/properties/${property.id}`, { method: "DELETE" });
      // optionally refresh parent
    } catch (e) {
      alert("Delete failed");
    } finally {
      setDeleting(false);
    }
  }

  return (
    <div className="card overflow-hidden">
      <div className="h-44 bg-gray-100">
        <img src={property.images?.[0]?.url ?? "/property.jpg"} alt="" className="w-full h-full object-cover" />
      </div>

      <div className="p-4">
        <div className="flex justify-between items-start">
          <div>
            <h4 className="font-semibold text-gray-800">{property.title ?? "Untitled"}</h4>
            <div className="text-sm text-gray-500">{property.city?.name ?? property.location}</div>
          </div>

          <div className="text-[var(--brand-green)] font-bold">MAD {Number(property.price || 0).toLocaleString()}</div>
        </div>

        <div className="mt-3 flex items-center justify-between text-sm">
          <div className="text-gray-500">Status: <span className="ml-1">{property.status}</span></div>
          <div className="flex items-center gap-3">
            <a href={`/properties/${property.id}`} className="text-blue-600">View</a>
            <a href={`/dashboard/edit/${property.id}`} className="text-yellow-600">Edit</a>
            <button onClick={handleDelete} disabled={deleting} className="text-red-600">{deleting ? "Deleting..." : "Delete"}</button>
          </div>
        </div>
      </div>
    </div>
  );
}
