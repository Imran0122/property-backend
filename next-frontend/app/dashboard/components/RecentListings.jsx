export default function RecentListings() {
  return (
    <section className="bg-white border rounded-xl p-10 text-center">
      <h3 className="font-semibold mb-2">No Active Listings</h3>
      <p className="text-sm text-gray-500 mb-4">
        Your active listings will appear here
      </p>
      <button className="bg-green-600 text-white px-5 py-2 rounded-md">
        Post Listing
      </button>
    </section>
  );
}
