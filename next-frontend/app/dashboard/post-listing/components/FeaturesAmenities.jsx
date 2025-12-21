"use client";
import { useState } from "react";
export default function FeaturesAmenities(){
  const bedrooms = ['Studio',1,2,3,4,5,6,7,8,9,10,'10+'];
  const bathrooms = [1,2,3,4,5,6,'6+'];
  const [selectedBed, setSelectedBed] = useState(null);
  const [selectedBath, setSelectedBath] = useState(null);

  return (
    <section className="bg-white rounded-lg shadow p-6">
      <div className="grid grid-cols-12 gap-6">
        <div className="col-span-12 md:col-span-3 flex items-start">
          <div className="bg-green-100 p-3 rounded-md inline-flex">🏷️</div>
          <div className="ml-3 hidden md:block"><p className="text-sm text-gray-600">Feature and Amenities</p></div>
        </div>
        <div className="col-span-12 md:col-span-9">
          <div className="mb-4">
            <p className="text-sm text-gray-700 mb-2">Bedrooms</p>
            <div className="flex flex-wrap gap-2">
              {bedrooms.map((b,i)=> (
                <button key={i} onClick={()=>setSelectedBed(b)} className={`px-3 py-1 rounded-full border ${selectedBed===b? 'bg-green-50 border-green-300 text-green-700' : 'bg-white'}`}>
                  {b}
                </button>
              ))}
            </div>
          </div>

          <div className="mb-4">
            <p className="text-sm text-gray-700 mb-2">Bathrooms</p>
            <div className="flex flex-wrap gap-2">
              {bathrooms.map((b,i)=> (
                <button key={i} onClick={()=>setSelectedBath(b)} className={`px-3 py-1 rounded-full border ${selectedBath===b? 'bg-green-50 border-green-300 text-green-700' : 'bg-white'}`}>
                  {b}
                </button>
              ))}
            </div>
          </div>

          <div className="flex items-center justify-between">
            <div className="text-sm text-gray-600">Feature and Amenities</div>
            <button className="bg-green-600 text-white px-3 py-1 rounded">Add Amenities</button>
          </div>

          <div className="mt-4 bg-green-50 p-3 rounded-md text-sm text-gray-700">Quality Tip — Add at least 5 amenities</div>
        </div>
      </div>
    </section>
  );
}