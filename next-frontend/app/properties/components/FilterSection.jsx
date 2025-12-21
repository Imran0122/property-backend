"use client";
import { useState } from "react";

export default function FilterSection({ onSearch }) {
  const [filters, setFilters] = useState({
    purpose: "Buy",
    city: "Lahore",
    location: "",
    propertyType: "Commercial",
    minArea: "",
    maxArea: "",
    minPrice: "",
    maxPrice: "",
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
      className="bg-black text-gray-800 p-4 rounded-md shadow-md grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3"
    >
      {/* PURPOSE */}
      <div className="flex flex-col bg-gray-100 rounded-md p-2">
        <label className="text-[10px] font-semibold text-gray-500 uppercase">Purpose</label>
        <select
          name="purpose"
          value={filters.purpose}
          onChange={handleChange}
          className="bg-transparent outline-none text-sm"
        >
          <option>Buy</option>
          <option>Rent</option>
        </select>
      </div>

      {/* CITY */}
      <div className="flex flex-col bg-gray-100 rounded-md p-2">
        <label className="text-[10px] font-semibold text-gray-500 uppercase">City</label>
        <select
          name="city"
          value={filters.city}
          onChange={handleChange}
          className="bg-transparent outline-none text-sm"
        >
          <option value="Lahore">Lahore</option>
          <option value="Karachi">Karachi</option>
          <option value="Islamabad">Islamabad</option>
        </select>
      </div>

      {/* LOCATION */}
      <div className="flex flex-col bg-gray-100 rounded-md p-2">
        <label className="text-[10px] font-semibold text-gray-500 uppercase">Location</label>
        <input
          type="text"
          name="location"
          placeholder="Search Location"
          value={filters.location}
          onChange={handleChange}
          className="bg-transparent outline-none text-sm"
        />
      </div>

      {/* PROPERTY TYPE */}
      <div className="flex flex-col bg-gray-100 rounded-md p-2">
        <label className="text-[10px] font-semibold text-gray-500 uppercase">Property Type</label>
        <select
          name="propertyType"
          value={filters.propertyType}
          onChange={handleChange}
          className="bg-transparent outline-none text-sm"
        >
          <option value="Commercial">Commercial</option>
          <option value="Building">Building</option>
          <option value="Shop">Shop</option>
          <option value="Office">Office</option>
          <option value="Warehouse">Warehouse</option>
        </select>
      </div>

      {/* AREA (MARLA) */}
      <div className="flex flex-col bg-gray-100 rounded-md p-2">
        <label className="text-[10px] font-semibold text-gray-500 uppercase">Area (Marla)</label>
        <div className="flex items-center text-sm gap-1">
          <input
            type="number"
            name="minArea"
            value={filters.minArea}
            onChange={handleChange}
            placeholder="0"
            className="bg-transparent w-1/2 outline-none"
          />
          <span className="text-gray-500 text-xs">to</span>
          <input
            type="number"
            name="maxArea"
            value={filters.maxArea}
            onChange={handleChange}
            placeholder="Any"
            className="bg-transparent w-1/2 outline-none"
          />
        </div>
      </div>

      {/* PRICE (PKR) */}
      <div className="flex flex-col bg-gray-100 rounded-md p-2">
        <label className="text-[10px] font-semibold text-gray-500 uppercase">Price (PKR)</label>
        <div className="flex items-center text-sm gap-1">
          <input
            type="number"
            name="minPrice"
            value={filters.minPrice}
            onChange={handleChange}
            placeholder="0"
            className="bg-transparent w-1/2 outline-none"
          />
          <span className="text-gray-500 text-xs">to</span>
          <input
            type="number"
            name="maxPrice"
            value={filters.maxPrice}
            onChange={handleChange}
            placeholder="Any"
            className="bg-transparent w-1/2 outline-none"
          />
        </div>
      </div>

      {/* KEYWORD */}
      <div className="flex flex-col bg-gray-100 rounded-md p-2">
        <label className="text-[10px] font-semibold text-gray-500 uppercase">Keyword</label>
        <input
          type="text"
          name="keyword"
          placeholder="e.g. DHA Phase 6"
          value={filters.keyword}
          onChange={handleChange}
          className="bg-transparent outline-none text-sm"
        />
      </div>

      {/* MORE OPTIONS */}
      <div className="flex flex-col bg-gray-100 rounded-md p-2">
        <label className="text-[10px] font-semibold text-gray-500 uppercase">More Options</label>
        <select className="bg-transparent outline-none text-sm">
          <option>0 Selected</option>
        </select>
      </div>
    </form>
  );
}
