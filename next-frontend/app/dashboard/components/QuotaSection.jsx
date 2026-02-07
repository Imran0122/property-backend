export default function QuotaSection() {
  return (
    <section className="bg-white border rounded-xl p-6">
      <h2 className="font-semibold mb-4">Quota and Credits</h2>

      <div className="flex gap-5 text-sm mb-5">
        <span className="text-green-600 font-medium">
          Listing Quota (0)
        </span>
        <span className="text-gray-500">Refresh Credits (0)</span>
        <span className="text-gray-500">Hot Credits (0)</span>
        <span className="text-gray-500">Super Hot Credits (0)</span>
      </div>

      <div className="grid grid-cols-4 text-center">
        {["Available", "Used", "Total", "Current Plan"].map((item) => (
          <div key={item}>
            <p className="text-gray-500 text-sm">{item}</p>
            <p className="font-semibold">0</p>
          </div>
        ))}
      </div>
    </section>
  );
}
