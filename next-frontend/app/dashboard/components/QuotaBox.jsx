'use client';

export default function QuotaBox() {
  return (
    <div className="bg-gradient-to-r from-green-50 to-white border border-green-200 rounded-lg p-5 shadow-sm mb-6">
      <h2 className="text-sm font-semibold text-green-800 mb-2">Ad Quota</h2>
      <div className="flex justify-between items-center text-sm text-gray-700">
        <p>Utilisé: <span className="font-semibold">3</span></p>
        <p>Restant: <span className="font-semibold">7</span></p>
        <p>Total: <span className="font-semibold">10</span></p>
      </div>
      <div className="w-full bg-gray-200 h-2 rounded-full mt-3">
        <div className="bg-green-600 h-2 rounded-full" style={{ width: '30%' }}></div>
      </div>
    </div>
  );
}
