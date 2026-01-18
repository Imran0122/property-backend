"use client";
// QuotaBox.jsx
export default function QuotaBox() {
  return (
    <div className="bg-white rounded-lg border border-gray-200 p-6 shadow-sm h-full">
      <h4 className="text-base font-semibold">Quota et crédits</h4>

      <div className="mt-4">
        <div className="text-xs text-gray-500">Quota d'annonces</div>
        <div className="flex items-center gap-6 mt-3">
          <div>
            <div className="text-sm text-gray-400">Quota disponible</div>
            <div className="font-bold text-lg">0</div>
          </div>
          <div>
            <div className="text-sm text-gray-400">Utilisé</div>
            <div className="font-bold text-lg">0</div>
          </div>
          <div>
            <div className="text-sm text-gray-400">Total</div>
            <div className="font-bold text-lg">0</div>
          </div>
        </div>

        <div className="h-3 bg-gray-100 rounded-full mt-4 overflow-hidden">
          <div style={{ width: "20%" }} className="h-full bg-green-200"></div>
        </div>
      </div>
    </div>
  );
}
