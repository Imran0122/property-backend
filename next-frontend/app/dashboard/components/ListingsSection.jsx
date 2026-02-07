export default function ListingsSection() {
  const data = [
    { label: "Active", color: "bg-green-100" },
    { label: "For Sale", color: "bg-blue-100" },
    { label: "For Rent", color: "bg-indigo-100" },
    { label: "Super Hot", color: "bg-red-100" },
    { label: "Hot", color: "bg-orange-100" },
  ];

  return (
    <section className="bg-white border rounded-xl p-6">
      <div className="flex justify-between mb-6">
        <h2 className="font-semibold">Listings</h2>
        <span className="text-green-600 text-sm cursor-pointer">
          View all Hectare Listings
        </span>
      </div>

      <div className="grid grid-cols-5 gap-4">
        {data.map((item) => (
          <div key={item.label} className="flex gap-3 items-center">
            <div className={`w-10 h-10 rounded-full ${item.color}`} />
            <div>
              <p className="text-sm">{item.label}</p>
              <p className="font-semibold">0</p>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}
