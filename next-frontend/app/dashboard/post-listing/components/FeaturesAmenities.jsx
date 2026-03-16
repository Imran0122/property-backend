"use client";

export default function FeaturesAmenities({ formData, handleChange }) {
  const bedrooms = ["Studio", 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, "10+"];
  const bathrooms = [1, 2, 3, 4, 5, 6, "6+"];

  return (
    <section className="bg-white rounded-lg shadow p-6">
      <div className="grid grid-cols-12 gap-6">
        <div className="col-span-12 md:col-span-3 flex items-start">
          <div className="bg-green-100 p-3 rounded-md inline-flex">🏷️</div>
          <div className="ml-3 hidden md:block">
            <p className="text-sm text-gray-600">Caractéristiques et équipements</p>
          </div>
        </div>
        <div className="col-span-12 md:col-span-9">
          <div className="mb-4">
            <p className="text-sm text-gray-700 mb-2">Chambres</p>
            <div className="flex flex-wrap gap-2">
              {bedrooms.map((b, i) => (
                <button
                  key={i}
                  onClick={() => handleChange("bedrooms", b)}
                  className={`px-3 py-1 rounded-full border ${
                    formData.bedrooms === b
                      ? "bg-green-50 border-green-300 text-green-700"
                      : "bg-white"
                  }`}
                >
                  {b}
                </button>
              ))}
            </div>
          </div>

          <div className="mb-4">
            <p className="text-sm text-gray-700 mb-2">Salles de bain</p>
            <div className="flex flex-wrap gap-2">
              {bathrooms.map((b, i) => (
                <button
                  key={i}
                  onClick={() => handleChange("bathrooms", b)}
                  className={`px-3 py-1 rounded-full border ${
                    formData.bathrooms === b
                      ? "bg-green-50 border-green-300 text-green-700"
                      : "bg-white"
                  }`}
                >
                  {b}
                </button>
              ))}
            </div>
          </div>

          <div className="flex items-center justify-between">
            <div className="text-sm text-gray-600">Caractéristiques et commodités</div>
            <button className="bg-green-600 text-white px-3 py-1 rounded">Ajouter des commodités</button>
          </div>

          <div className="mt-4 bg-green-50 p-3 rounded-md text-sm text-gray-700">
            Astuce qualité — Ajoutez au moins 5 commodités
          </div>
        </div>
      </div>
    </section>
  );
}