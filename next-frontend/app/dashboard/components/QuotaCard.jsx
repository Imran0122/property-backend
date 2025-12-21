"use client";

import React, { useState } from 'react';

const QuotaCard = () => {
  const [activeTab, setActiveTab] = useState('listing');

  const tabs = [
    { id: 'listing', label: 'Listing Quota', count: '(0)' },
    { id: 'refresh', label: 'Refresh Credits', count: '(0)' },
    { id: 'hot', label: 'Hot Credits', count: '(0)' },
    { id: 'superhot', label: 'Super Hot Credits', count: '(0)' },
  ];

  return (
    <div className="bg-white rounded-lg border border-gray-200 p-5">
      <h2 className="text-lg font-semibold text-gray-900 mb-4">Quota and Credits</h2>
      
      {/* Tabs */}
      <div className="flex gap-2 mb-6 border-b border-gray-200 overflow-x-auto">
        {tabs.map((tab) => (
          <button
            key={tab.id}
            onClick={() => setActiveTab(tab.id)}
            className={`px-4 py-2 text-sm font-medium whitespace-nowrap transition-colors ${
              activeTab === tab.id
                ? 'text-green-600 border-b-2 border-green-600'
                : 'text-gray-600 hover:text-gray-900'
            }`}
          >
            {tab.label} {tab.count}
          </button>
        ))}
        <button className="ml-auto text-gray-400 hover:text-gray-600 px-2">
          •••
        </button>
      </div>

      {/* Content */}
      <div className="grid grid-cols-3 gap-6">
        <div className="text-center">
          <p className="text-sm text-gray-600 mb-1">Available Quota</p>
          <p className="text-3xl font-bold text-gray-900">0</p>
        </div>
        <div className="text-center">
          <p className="text-sm text-gray-600 mb-1">Used</p>
          <p className="text-3xl font-bold text-gray-900">0</p>
        </div>
        <div className="text-center">
          <p className="text-sm text-gray-600 mb-1">Total</p>
          <p className="text-3xl font-bold text-gray-900">0</p>
        </div>
      </div>
    </div>
  );
};

export default QuotaCard;