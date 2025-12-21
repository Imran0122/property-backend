"use client";
import { useState } from "react";
import { MapPin, Home, Building } from "lucide-react";

export default function LocationPurpose() {
  const [purpose, setPurpose] = useState("sell");
  const [propertyTab, setPropertyTab] = useState("home");
  const [city, setCity] = useState("");
  const [location, setLocation] = useState("");

  return (
    <section className="bg-white rounded-lg shadow p-6">
      <div className="grid grid-cols-12 gap-6 items-start">
        <div className="col-span-12 md:col-span-3 flex flex-col items-center md:items-start">
          <div className="bg-green-100 p-3 rounded-md inline-flex"><MapPin className="text-green-600" /></div>
          <p className="mt-3 text-sm text-gray-600">Location and Purpose</p>
        </div>

        <div className="col-span-12 md:col-span-9">
          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-2">Select Purpose</label>
            <div className="flex gap-3">
              <button onClick={() => setPurpose('sell')} className={`px-3 py-1.5 rounded-full border ${purpose==='sell' ? 'bg-green-50 border-green-300 text-green-700' : 'bg-white border-gray-200'}`}>
                Sell
              </button>
              <button onClick={() => setPurpose('rent')} className={`px-3 py-1.5 rounded-full border ${purpose==='rent' ? 'bg-green-50 border-green-300 text-green-700' : 'bg-white border-gray-200'}`}>
                Rent
              </button>
            </div>
          </div>

          <div className="mb-4">
            <label className="block text-sm font-medium text-gray-700 mb-2">Select Property Type</label>
            <div className="flex gap-3 items-center mb-3">
              <button onClick={() => setPropertyTab('home')} className={`px-3 py-1.5 rounded-md ${propertyTab==='home' ? 'border-b-2 border-green-600 text-green-700' : 'text-gray-600'}`}>Home</button>
              <button onClick={() => setPropertyTab('plots')} className={`px-3 py-1.5 rounded-md ${propertyTab==='plots' ? 'border-b-2 border-green-600 text-green-700' : 'text-gray-600'}`}>Plots</button>
              <button onClick={() => setPropertyTab('commercial')} className={`px-3 py-1.5 rounded-md ${propertyTab==='commercial' ? 'border-b-2 border-green-600 text-green-700' : 'text-gray-600'}`}>Commercial</button>
            </div>

            <div className="flex flex-wrap gap-3">
              <button className="px-3 py-2 rounded-full border bg-white text-gray-700">House</button>
              <button className="px-3 py-2 rounded-full border bg-white text-gray-700">Flat</button>
              <button className="px-3 py-2 rounded-full border bg-white text-gray-700">Upper Portion</button>
              <button className="px-3 py-2 rounded-full border bg-white text-gray-700">Lower Portion</button>
              <button className="px-3 py-2 rounded-full border bg-white text-gray-700">Farm House</button>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="text-sm text-gray-700">City</label>
              <select value={city} onChange={(e)=>setCity(e.target.value)} className={`mt-2 w-full border rounded px-3 py-2 ${!city ? 'border-red-300' : 'border-gray-200'}`}>
                <option value="">Select City</option>
                <option>Lahore</option>
                <option>Karachi</option>
                <option>Islamabad</option>
              </select>
              {!city && <p className="text-xs text-red-500 mt-1">Please select a City</p>}
            </div>

            <div>
              <label className="text-sm text-gray-700">Location</label>
              <input value={location} onChange={(e)=>setLocation(e.target.value)} className="mt-2 w-full border rounded px-3 py-2 border-gray-200" placeholder="Search Location" />
            </div>
          </div>

          <div className="mt-4 bg-gray-50 rounded-md h-36 flex items-center justify-center border border-dashed border-gray-200">
            <button className="text-sm bg-white border rounded px-3 py-2">Set Location on Map</button>
          </div>
        </div>
      </div>
    </section>
  );
}