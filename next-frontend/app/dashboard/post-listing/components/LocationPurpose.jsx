"use client";
import { MapPin } from "lucide-react";

export default function LocationPurpose({ formData, handleChange }) {
  return (
    <section className="bg-white rounded-lg shadow p-6">
      <div className="grid grid-cols-12 gap-6 items-start">
        <div className="col-span-12 md:col-span-3 flex flex-col items-center md:items-start">
          <div className="bg-green-100 p-3 rounded-md inline-flex">
            <MapPin className="text-green-600" />
          </div>
          <p className="mt-3 text-sm text-gray-600">Emplacement et usage</p>
        </div>

        <div className="col-span-12 md:col-span-9">
          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-2">Sélectionner l’usage</label>
            <div className="flex gap-3">
              <button
                onClick={() => handleChange("purpose", "sell")}
                className={`px-3 py-1.5 rounded-full border ${
                  formData.purpose === "sell"
                    ? "bg-green-50 border-green-300 text-green-700"
                    : "bg-white border-gray-200"
                }`}
              >
                Vendre
              </button>
              <button
                onClick={() => handleChange("purpose", "rent")}
                className={`px-3 py-1.5 rounded-full border ${
                  formData.purpose === "rent"
                    ? "bg-green-50 border-green-300 text-green-700"
                    : "bg-white border-gray-200"
                }`}
              >
                Louer
              </button>
            </div>
          </div>

          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-2">Sélectionner le type de propriété</label>
            <div className="flex gap-3 items-center mb-3">
              <button
                onClick={() => handleChange("propertyTab", "home")}
                className={`px-3 py-1.5 rounded-md ${
                  formData.propertyTab === "home"
                    ? "border-b-2 border-green-600 text-green-700"
                    : "text-gray-600"
                }`}
              >
                Home
              </button>
              <button
                onClick={() => handleChange("propertyTab", "plots")}
                className={`px-3 py-1.5 rounded-md ${
                  formData.propertyTab === "plots"
                    ? "border-b-2 border-green-600 text-green-700"
                    : "text-gray-600"
                }`}
              >
                Plots
              </button>
              <button
                onClick={() => handleChange("propertyTab", "commercial")}
                className={`px-3 py-1.5 rounded-md ${
                  formData.propertyTab === "commercial"
                    ? "border-b-2 border-green-600 text-green-700"
                    : "text-gray-600"
                }`}
              >
                Commercial
              </button>
            </div>

            <div className="flex flex-wrap gap-3">
              {["Maison", "Appartement", "Partie supérieure", "Partie inférieure", "Ferme"].map(
                (type) => (
                  <button
                    key={type}
                    onClick={() => handleChange("propertyType", type)}
                    className={`px-3 py-2 rounded-full border ${
                      formData.propertyType === type
                        ? "bg-green-50 border-green-300 text-green-700"
                        : "bg-white text-gray-700"
                    }`}
                  >
                    {type}
                  </button>
                )
              )}
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="text-sm text-gray-700">City</label>
              <select
                value={formData.city || ""}
                onChange={(e) => handleChange("city", e.target.value)}
                className={`mt-2 w-full border rounded px-3 py-2 ${
                  !formData.city ? "border-red-300" : "border-gray-200"
                }`}
              >
                <option value="">Sélectionner la ville</option>
                <option>Casablanca</option>
                <option>Rabat</option>
                <option>Marrakech</option>
              </select>
              {!formData.city && (
                <p className="text-xs text-red-500 mt-1">Veuillez sélectionner une ville</p>
              )}
            </div>

            <div>
              <label className="text-sm text-gray-700">Location</label>
              <input
                value={formData.location || ""}
                onChange={(e) => handleChange("location", e.target.value)}
                className="mt-2 w-full border rounded px-3 py-2 border-gray-200"
                placeholder="Search Location"
              />
            </div>
          </div>

          <div className="mt-4 bg-gray-50 rounded-md h-36 flex items-center justify-center border border-dashed border-gray-200">
            <button className="text-sm bg-white border rounded px-3 py-2">
              Définir l’emplacement sur la carte
            </button>
          </div>
        </div>
      </div>
    </section>
  );
}