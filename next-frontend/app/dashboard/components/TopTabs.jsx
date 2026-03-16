'use client';
import { PlusCircle, Search } from 'lucide-react';

export default function Topbar() {
  return (
    <header className="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6">
      <div className="flex items-center space-x-2 text-sm text-gray-600">
        <a href="#" className="hover:underline">Aller sur Hectare.ma</a>
      </div>
      <div className="flex items-center space-x-4">
        <button className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md flex items-center text-sm">
          <PlusCircle className="w-4 h-4 mr-2" /> Publier une annonce
        </button>
        <button className="flex items-center text-sm text-gray-700 border px-3 py-1.5 rounded-md">
          <Search className="w-4 h-4 mr-2" /> Mes annonces
        </button>
        <div className="flex items-center">
          <img src="/avatar.png" alt="user" className="w-8 h-8 rounded-full border" />
        </div>
      </div>
    </header>
  );
}
