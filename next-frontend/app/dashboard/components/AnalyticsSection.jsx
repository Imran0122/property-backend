export default function AnalyticsSection() {
  const items = [
    "Views",
    "Clicks",
    "Leads",
    "Calls",
    "WhatsApp",
    "SMS",
    "Emails",
  ];

  return (
    <section className="bg-white border rounded-xl p-6">
      <div className="flex justify-between mb-5">
        <h2 className="font-semibold">Analytics</h2>

        <div className="flex gap-2">
          <button className="bg-green-600 text-white px-3 py-1 rounded-md text-sm">
            All
          </button>
          <button className="border px-3 py-1 rounded-md text-sm">
            For Sale
          </button>
          <button className="border px-3 py-1 rounded-md text-sm">
            For Rent
          </button>
        </div>
      </div>

      <div className="grid grid-cols-7 gap-3">
        {items.map((item) => (
          <div key={item} className="border rounded-lg p-4 text-center">
            <p className="text-sm">{item}</p>
            <p className="font-semibold">0</p>
            <p className="text-xs text-gray-400">No Data</p>
          </div>
        ))}
      </div>

      <div className="text-center text-gray-500 mt-8">
        View In-Depth Insights
      </div>
    </section>
  );
}
