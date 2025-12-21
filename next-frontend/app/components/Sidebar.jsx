export default function Sidebar() {
  return (
    <aside className="w-full lg:w-1/4 bg-white p-4 shadow-md rounded-lg h-fit">
      <h3 className="font-semibold text-lg mb-4">Sponsored Projects</h3>
      <div className="space-y-3">
        <div className="border rounded-md p-2">
          <img src="/property.jpg" alt="Ad" className="w-full h-32 object-cover rounded-md" />
          <h4 className="text-sm mt-2 font-medium">DHA Lahore Phase 6</h4>
          <p className="text-xs text-gray-500">By ABC Developers</p
          >
        </div>
        <div className="border rounded-md p-2">
          <img src="/property.jpg" alt="Ad" className="w-full h-32 object-cover rounded-md" />
          <h4 className="text-sm mt-2 font-medium">Bahria Town Karachi</h4>
          <p classing="text-xs text-gray-500">By Bahria Builders</p>
        </div>
      </div>
    </aside>
  );
}
