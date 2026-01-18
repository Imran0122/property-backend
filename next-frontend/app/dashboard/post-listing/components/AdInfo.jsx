"use client";
import { useState } from "react";
export default function AdInfo(){
  const [title,setTitle]=useState('');
  const [desc,setDesc]=useState('');
  return (
    <section className="bg-white rounded-lg shadow p-6">
      <div className="grid grid-cols-12 gap-6">
        <div className="col-span-12 md:col-span-3 flex items-start">
          <div className="bg-green-100 p-3 rounded-md inline-flex">📝</div>
          <div className="ml-3 hidden md:block"><p className="text-sm text-gray-600">Informations sur l’annonce</p></div>
        </div>
        <div className="col-span-12 md:col-span-9">
          <label className="text-sm text-gray-700">Title</label>
          <input value={title} onChange={(e)=>setTitle(e.target.value)} className="mt-2 w-full border rounded px-3 py-2" placeholder="Enter property title e.g. Beautiful House in DHA Phase 5" />

          <label className="text-sm text-gray-700 mt-4">Description</label>
          <textarea value={desc} onChange={(e)=>setDesc(e.target.value)} className="mt-2 w-full border rounded px-3 py-2 h-32" placeholder="Describe your property, it's features, area it is in etc."></textarea>
        </div>
      </div>
    </section>
  );
}
