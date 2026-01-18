"use client";
export default function PlatformSelection(){
  return (
    <section className="bg-white rounded-lg shadow p-6">
      <div className="grid grid-cols-12 gap-6">
        <div className="col-span-12 md:col-span-3 flex items-start">
          <div className="bg-green-100 p-3 rounded-md inline-flex">🔌</div>
          <div className="ml-3 hidden md:block"><p className="text-sm text-gray-600">Sélection de la plateforme</p></div>
        </div>
        <div className="col-span-12 md:col-span-9">
          <div className="border rounded p-4 bg-green-50">
            <img src="/zameen-badge.png" alt="hectare" className="h-8" />
          </div>
        </div>
      </div>
    </section>
  );
}
