"use client";
import { useState } from "react";
export default function ContactInfo(){
  const [email,setEmail]=useState('');
  const [mobile,setMobile]=useState('');
  return (
    <section className="bg-white rounded-lg shadow p-6">
      <div className="grid grid-cols-12 gap-6">
        <div className="col-span-12 md:col-span-3 flex items-start">
          <div className="bg-green-100 p-3 rounded-md inline-flex">✉️</div>
          <div className="ml-3 hidden md:block"><p className="text-sm text-gray-600">Contact Information</p></div>
        </div>
        <div className="col-span-12 md:col-span-9 space-y-3">
          <div>
            <label className="text-sm text-gray-700">Email</label>
            <input value={email} onChange={(e)=>setEmail(e.target.value)} className="mt-2 w-full border rounded px-3 py-2" />
          </div>

          <div>
            <label className="text-sm text-gray-700">Mobile</label>
            <div className="flex gap-2">
              <select className="border rounded px-3 py-2 w-24"> <option>+92</option></select>
              <input value={mobile} onChange={(e)=>setMobile(e.target.value)} className="flex-1 border rounded px-3 py-2" />
              <button className="bg-green-600 text-white px-3 py-2 rounded">+</button>
            </div>
          </div>

          <div>
            <label className="text-sm text-gray-700">Landline</label>
            <div className="flex gap-2">
              <select className="border rounded px-3 py-2 w-24"> <option>+92</option></select>
              <input className="flex-1 border rounded px-3 py-2" />
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}