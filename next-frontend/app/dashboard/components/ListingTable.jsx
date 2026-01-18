"use client";

export default function ListingTable({ listings = [], loading = false }) {
  // fallback sample row when no listings
  const sample = [
    {
      id: 1,
      title: "Belle maison dans la vieille ville",
      city: "Casablanca", // replacing Lahore with a major Moroccan city
      price: 9000000,     // you can keep SAR or MAD depending on currency
      area: "5 Marla",    // optionally convert to m² if needed
      status: "Inactif",  // French for Inactive
    }
  ];

  const rows = listings.length ? listings : sample;

  return (
    <div className="overflow-x-auto">
      <table className="w-full table-auto bg-white border border-gray-200 rounded-lg">
        <thead>
          <tr className="text-left text-sm text-gray-500">
            <th className="px-4 py-3">#</th>
            <th className="px-4 py-3">Titre</th>
            <th className="px-4 py-3">Ville</th>
            <th className="px-4 py-3">Superficie</th>
            <th className="px-4 py-3">Prix</th>
            <th className="px-4 py-3">Statut</th>
            <th className="px-4 py-3">Actions</th>

          </tr>
        </thead>
        <tbody className="text-sm text-gray-700">
          {rows.map((r, i) => (
            <tr key={r.id} className="border-t border-gray-100 hover:bg-gray-50">
              <td className="px-4 py-3">{i + 1}</td>
              <td className="px-4 py-3">{r.title}</td>
              <td className="px-4 py-3">{r.city}</td>
              <td className="px-4 py-3">{r.area}</td>
              <td className="px-4 py-3">PKR {new Intl.NumberFormat().format(r.price)}</td>
              <td className="px-4 py-3">{r.status}</td>
              <td className="px-4 py-3">
                <div className="flex gap-2">
                  <button className="text-sm px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700">Modifier</button>
                  <button className="text-sm px-3 py-1 rounded border hover:bg-gray-50">Supprimer</button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
