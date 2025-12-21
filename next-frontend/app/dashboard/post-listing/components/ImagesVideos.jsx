"use client";
import { useState } from "react";
export default function ImagesVideos(){
  const [files,setFiles]=useState([]);
  function handleFiles(e){
    setFiles(Array.from(e.target.files));
  }
  return (
    <section className="bg-white rounded-lg shadow p-6">
      <div className="grid grid-cols-12 gap-6">
        <div className="col-span-12 md:col-span-3 flex items-start">
          <div className="bg-green-100 p-3 rounded-md inline-flex">🖼️</div>
          <div className="ml-3 hidden md:block"><p className="text-sm text-gray-600">Property Images and Videos</p></div>
        </div>
        <div className="col-span-12 md:col-span-9">
          <div className="border-2 border-dashed rounded p-4">
            <div className="flex items-center gap-4">
              <label className="bg-green-600 text-white px-4 py-2 rounded cursor-pointer">
                Upload Images
                <input type="file" multiple accept="image/*" onChange={handleFiles} className="hidden" />
              </label>
              <button className="bg-white border px-4 py-2 rounded">Image Bank</button>
              <div className="text-sm text-gray-500">Max size 5MB, .jpg .png only</div>
            </div>
            <div className="mt-3 text-sm text-green-700">Quality Tip — Add at least 5 more images</div>
          </div>

          <div className="mt-4">
            <button className="bg-white border px-3 py-1 rounded">Add Video</button>
          </div>
        </div>
      </div>
    </section>
  );
}
