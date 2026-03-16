'use client';
import React from 'react';
import { BarChart3 } from 'lucide-react';

export default function InsightsEmpty() {
  return (
    <div className="flex flex-col items-center justify-center p-10 text-center border border-gray-200 rounded-lg bg-gray-50">
      <BarChart3 className="text-green-600 w-12 h-12 mb-3" />
      <h3 className="text-lg font-semibold text-gray-800 mb-1">
        Voir les analyses détaillées
      </h3>
      <p className="text-sm text-gray-500">
        Consultez le nombre de vues, de clics et de contacts générés par vos annonces.
      </p>
    </div>
  );
}
