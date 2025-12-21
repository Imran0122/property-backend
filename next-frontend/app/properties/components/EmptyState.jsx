'use client';
import React from 'react';

export default function EmptyState({ title, message, buttonText, onClick }) {
  return (
    <div className="bg-gray-50 border border-gray-200 rounded-2xl p-10 text-center">
      <h3 className="text-gray-800 font-semibold text-lg mb-2">
        {title || 'No Active Listings Found'}
      </h3>
      <p className="text-gray-500 text-sm mb-4">
        {message || 'Start by adding your first property listing to get insights and leads.'}
      </p>
      <button
        onClick={onClick}
        className="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md text-sm font-medium"
      >
        {buttonText || 'Add Property'}
      </button>
    </div>
  );
}
