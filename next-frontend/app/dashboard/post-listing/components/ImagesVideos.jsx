"use client";

export default function ImagesVideos({ formData, handleChange }) {
  function handleFiles(e) {
    handleChange("images", Array.from(e.target.files));
  }
  return (
    <section className="bg-white rounded-lg shadow p-6">
      <div className="grid grid-cols-12 gap-6">
        <div className="col-span-12 md:col-span-3 flex items-start">
          <div className="bg-green-100 p-3 rounded-md inline-flex">🖼️</div>
          <div className="ml-3 hidden md:block">
            <p className="text-sm text-gray-600">Images et vidéos de la propriété</p>
          </div>
        </div>
        <div className="col-span-12 md:col-span-9">
          <div className="border-2 border-dashed rounded p-4">
            <div className="flex items-center gap-4">
              <label className="bg-green-600 text-white px-4 py-2 rounded cursor-pointer">
                Téléverser des images
                <input
                  type="file"
                  multiple
                  accept="image/*"
                  onChange={handleFiles}
                  className="hidden"
                />
              </label>
              <button className="bg-white border px-4 py-2 rounded">Banque d’images</button>
              <div className="text-sm text-gray-500">Taille max 5 Mo, uniquement .jpg et .png</div>
            </div>
            <div className="mt-3 text-sm text-green-700">
              Astuce qualité — Ajoutez au moins 5 images supplémentaires
            </div>
          </div>

          <div className="mt-4">
            <button className="bg-white border px-3 py-1 rounded">Ajouter une vidéo</button>
          </div>
        </div>
      </div>
    </section>
  );
}