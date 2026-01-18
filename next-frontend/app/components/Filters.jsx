"use client";
import { useState } from "react";

export default function Filters({ onSearch }) {
  const [filters, setFilters] = useState({
    city: "",
    minPrice: "",
    maxPrice: "",
    bedrooms: "",
    keyword: "",
  });

  const handleChange = (e) => {
    setFilters({ ...filters, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onSearch(filters);
  };

  return (
    <form
      onSubmit={handleSubmit}
      className="bg-white p-4 rounded-lg shadow-md mb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4"
    >
      {/* City Filter */}
      <select
        name="city"
        value={filters.city}
        onChange={handleChange}
        className="border border-gray-300 p-2 rounded-md focus:ring-1 focus:ring-green-500"
      >
        <option value="">All Cities</option>
        <option value="Casablanca">Casablanca</option>
        <option value="Marrakech">Marrakech</option>
        <option value="Rabat">Rabat</option>
        <option value="Fes">Fes</option>
        <option value="Tangier">Tangier</option>
        <option value="Agadir">Agadir</option>
      </select>

      {/* Price Range */}
      <input
        type="number"
        name="minPrice"
        value={filters.minPrice}
        onChange={handleChange}
        placeholder="Min Price"
        className="border border-gray-300 p-2 rounded-md focus:ring-1 focus:ring-green-500"
      />
      <input
        type="number"
        name="maxPrice"
        value={filters.maxPrice}
        onChange={handleChange}
        placeholder="Max Price"
        className="border border-gray-300 p-2 rounded-md focus:ring-1 focus:ring-green-500"
      />

      {/* Bedrooms */}
      <select
        name="bedrooms"
        value={filters.bedrooms}
        onChange={handleChange}
        className="border border-gray-300 p-2 rounded-md focus:ring-1 focus:ring-green-500"
      >
        <option value="">Toutes les chambres </option>
        <option value="1">1 Lit</option>
        <option value="2">2 Lit</option>
        <option value="3">3 Lit</option>
        <option value="4">4 Lit</option>
      </select>

      {/* Keyword */}
      <input
        type="text"
        name="keyword"
        value={filters.keyword}
        onChange={handleChange}
        placeholder="Keyword (e.g. DHA)"
        className="border border-gray-300 p-2 rounded-md md:col-span-2 focus:ring-1 focus:ring-green-500"
      />

      {/* Search Button */}
      <button
        type="submit"
        className="bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition md:col-span-6"
      >
        Rechercher 
      </button>
    </form>
  );
}
