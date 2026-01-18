'use client';
import { useState } from 'react';

const ListingGrid = () => {
  const [activeTab, setActiveTab] = useState('All');
const tabs = ['Tous', 'À vendre', 'À louer'];
const metrics = [
  { name: 'Clics', value: 0 },
  { name: 'Contacts', value: 0 },
  { name: 'Appels', value: 0 },
  { name: 'WhatsApp', value: 0 },
  { name: 'SMS', value: 0 },
  { name: 'Emails', value: 0 },
];


  return (
    <div className="bg-white border border-gray-200 rounded-lg p-5 shadow-sm">
      <div className="flex flex-col sm:flex-row justify-between items-center mb-4">
        <div className="flex bg-gray-50 rounded-md border border-gray-200 overflow-hidden">
          {tabs.map((tab) => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={`px-4 py-1.5 text-sm font-medium transition-all ${
                activeTab === tab
                  ? 'bg-green-100 text-green-700'
                  : 'text-gray-700 hover:bg-gray-100'
              }`}
            >
              {tab}
            </button>
          ))}
        </div>
        <div className="flex items-center border border-gray-300 rounded-md px-3 py-1 text-sm bg-white mt-3 sm:mt-0">
          30 derniers jours 📅
        </div>
      </div>

      <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6 text-center">
        {metrics.map((m) => (
          <div key={m.name} className="flex flex-col items-center">
            <p className="text-gray-500 text-sm">{m.name}</p>
            <p className="text-2xl font-bold text-gray-800">{m.value}</p>
            <p className="text-xs text-gray-400">No Data</p>
          </div>
        ))}
      </div>
    </div>
  );
};

export default ListingGrid;
