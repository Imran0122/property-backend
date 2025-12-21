// pages/properties.js
import { useState } from "react";

export default function Properties() {
  const [properties] = useState([
    {
      id: 1,
      title: "10 Marla Residential Plot for Sale",
      location: "Raiwind Road, Lahore",
      price: "PKR 95 Lakh",
      image: "https://via.placeholder.com/400x250",
      beds: null,
      baths: null,
      area: "10 Marla",
    },
    {
      id: 2,
      title: "5 Marla House for Sale",
      location: "Bahria Town, Lahore",
      price: "PKR 1.5 Crore",
      image: "https://via.placeholder.com/400x250",
      beds: 3,
      baths: 4,
      area: "5 Marla",
    },
    {
      id: 3,
      title: "1 Kanal Commercial Plot",
      location: "DHA Phase 6, Lahore",
      price: "PKR 12 Crore",
      image: "https://via.placeholder.com/400x250",
      beds: null,
      baths: null,
      area: "1 Kanal",
    },
  ]);

  return (
    <div className="bg-gray-100 min-h-screen">
      {/* Breadcrumb & Header */}
      <div className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
          <div>
            <p className="text-sm text-gray-500">
              <span className="text-green-600">Home</span> › Plots › Lahore
            </p>
            <h1 className="text-xl font-semibold text-gray-800">
              Plots for Sale in Lahore
            </h1>
            <p className="text-gray-500 text-sm">Showing 1–20 of 1000 Results</p>
          </div>
          <select className="border rounded px-3 py-2 text-sm">
            <option>Newest</option>
            <option>Lowest Price</option>
            <option>Highest Price</option>
          </select>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 py-6 grid grid-cols-12 gap-6">
        {/* Sidebar Filters */}
        <aside className="col-span-3 bg-white p-4 rounded-lg shadow-sm">
          <h2 className="text-lg font-semibold mb-4">Filters</h2>

          {/* Property Type */}
          <div className="mb-4">
            <h3 className="font-medium text-sm mb-2">Property Type</h3>
            <select className="w-full border rounded px-2 py-1">
              <option>Homes</option>
              <option>Plots</option>
              <option>Commercial</option>
            </select>
          </div>

          {/* Price Range */}
          <div className="mb-4">
            <h3 className="font-medium text-sm mb-2">Price Range</h3>
            <div className="flex gap-2">
              <input
                type="number"
                placeholder="Min"
                className="w-1/2 border rounded px-2 py-1"
              />
              <input
                type="number"
                placeholder="Max"
                className="w-1/2 border rounded px-2 py-1"
              />
            </div>
          </div>

          {/* Area */}
          <div className="mb-4">
            <h3 className="font-medium text-sm mb-2">Area Size</h3>
            <div className="flex gap-2">
              <input
                type="text"
                placeholder="Min"
                className="w-1/2 border rounded px-2 py-1"
              />
              <input
                type="text"
                placeholder="Max"
                className="w-1/2 border rounded px-2 py-1"
              />
            </div>
          </div>

          {/* Bedrooms */}
          <div className="mb-4">
            <h3 className="font-medium text-sm mb-2">Bedrooms</h3>
            <div className="flex gap-2 flex-wrap">
              {[1, 2, 3, 4, 5].map((num) => (
                <button
                  key={num}
                  className="px-3 py-1 border rounded text-sm hover:bg-green-100"
                >
                  {num}
                </button>
              ))}
            </div>
          </div>

          <button className="bg-green-600 text-white w-full py-2 rounded mt-2 hover:bg-green-700">
            Apply Filters
          </button>
        </aside>

        {/* Property Listings */}
        <main className="col-span-9 space-y-4">
          {properties.map((property) => (
            <div
              key={property.id}
              className="bg-white shadow-sm rounded-lg flex border"
            >
              <img
                src={property.image}
                alt={property.title}
                className="w-56 h-40 object-cover rounded-l-lg"
              />
              <div className="flex-1 p-4 flex flex-col justify-between">
                <div>
                  <h2 className="text-lg font-semibold text-green-600">
                    {property.price}
                  </h2>
                  <p className="text-gray-800 font-medium">{property.title}</p>
                  <p className="text-gray-500 text-sm">{property.location}</p>
                </div>
                <div className="text-sm text-gray-600 mt-2 flex gap-4">
                  {property.beds && <span>{property.beds} Beds</span>}
                  {property.baths && <span>{property.baths} Baths</span>}
                  <span>{property.area}</span>
                </div>
              </div>
            </div>
          ))}

          {/* Pagination */}
          <div className="flex justify-center items-center gap-2 mt-6">
            <button className="px-3 py-1 border rounded">Prev</button>
            <button className="px-3 py-1 border bg-green-600 text-white rounded">
              1
            </button>
            <button className="px-3 py-1 border rounded">2</button>
            <button className="px-3 py-1 border rounded">3</button>
            <button className="px-3 py-1 border rounded">Next</button>
          </div>
        </main>
      </div>
    </div>
  );
}
