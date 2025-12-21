"use client";
import { useEffect, useState } from "react";
import PropertyCard from "./PropertyCard";

export default function PropertyList({ initialQuery = {} }) {
  const [properties, setProperties] = useState([]);
  const [loading, setLoading] = useState(true);
  const [pageMeta, setPageMeta] = useState(null);

  async function load(query = {}) {
    setLoading(true);
    const params = new URLSearchParams({ per_page: 9, ...query }).toString();
    const res = await fetch(`${process.env.NEXT_PUBLIC_BACKEND_URL || 'http://127.0.0.1:8000'}/api/properties?${params}`);
    const data = await res.json();
    setProperties(data.data || data);
    setPageMeta({
      current_page: data.current_page,
      last_page: data.last_page,
      total: data.total
    });
    setLoading(false);
  }

  useEffect(() => {
    load(initialQuery);
  }, [JSON.stringify(initialQuery)]);

  if (loading) return <div className="text-center p-6">Loading properties...</div>;

  return (
    <div>
      <div className="grid md:grid-cols-3 gap-6">
        {properties.map((p) => <PropertyCard key={p.id} property={p} />)}
      </div>
      <div className="mt-6 text-center text-gray-600">
        {pageMeta && `Showing page ${pageMeta.current_page} of ${pageMeta.last_page} — ${pageMeta.total} listings`}
      </div>
    </div>
  );
}
