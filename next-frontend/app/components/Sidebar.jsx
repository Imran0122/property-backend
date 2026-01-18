export default function Sidebar() {
  return (
    <aside className="w-full lg:w-1/4 bg-white p-4 shadow-md rounded-lg h-fit">
      <h3 className="font-semibold text-lg mb-4">Sponsored Projects</h3>
      <div className="space-y-3">
        <div className="border rounded-md p-2">
          <img src="/property.jpg" alt="Ad" className="w-full h-32 object-cover rounded-md" />
          <h4 className="text-sm mt-2 font-medium">Quartier Anfa, Casablanca</h4>
          <p className="text-xs text-gray-500">Par ABC Développeurs</p
          >
        </div>
        <div className="border rounded-md p-2">
          <img src="/property.jpg" alt="Ad" className="w-full h-32 object-cover rounded-md" />
          <h4 className="text-sm mt-2 font-medium">Bahria Town Casablanca</h4>
          <p className="text-xs text-gray-500">Par Bahria Constructeurs</p>
        </div>
      </div>
    </aside>
  );
}
