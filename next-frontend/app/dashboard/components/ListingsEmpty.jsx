import React from 'react';
import { Plus } from 'lucide-react';

const ListingsEmpty = () => {
  return (
    <div className="bg-white rounded-lg border border-gray-200 p-8">
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-lg font-semibold text-gray-900">Vos annonces</h2>
        <a href="#" className="text-sm text-green-600 hover:text-green-700 flex items-center gap-1">
          Voir toutes les annonces
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 4l4 4-4 4" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
          </svg>
        </a>
      </div>

      <div className="text-center py-16">
        {/* Empty State Illustration */}
        <div className="inline-flex items-center justify-center w-32 h-32 bg-gray-50 rounded-full mb-6">
          <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="15" y="20" width="50" height="40" rx="4" stroke="#D1D5DB" strokeWidth="2" fill="none"/>
            <circle cx="25" cy="30" r="3" fill="#D1D5DB"/>
            <rect x="32" y="28" width="25" height="4" rx="2" fill="#D1D5DB"/>
            <rect x="20" y="38" width="40" height="3" rx="1.5" fill="#E5E7EB"/>
            <rect x="20" y="44" width="35" height="3" rx="1.5" fill="#E5E7EB"/>
            <rect x="20" y="50" width="30" height="3" rx="1.5" fill="#E5E7EB"/>
          </svg>
        </div>

        <h3 className="text-xl font-semibold text-gray-900 mb-2">Aucune annonce active</h3>
        <p className="text-gray-600 mb-8 max-w-md mx-auto">
          Vos annonces actives apparaîtront ici
        </p>

        <button className="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium shadow-sm">
          <Plus size={20} />
          Publier une annonce
        </button>
      </div>
    </div>
  );
};

export default ListingsEmpty;