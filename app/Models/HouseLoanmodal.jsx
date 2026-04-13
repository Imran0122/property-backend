"use client";

import { useDispatch, useSelector } from "react-redux";
import { closeHouseLoanmodal } from "../reduxStore/dataSlice";

export default function HouseLoanmodal() {
  const dispatch = useDispatch();
  const isOpen = useSelector((state) => state.data.isHouseLoanOpen);

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-[9999] bg-black/40 flex items-center justify-center">
      <div className="bg-white w-full max-w-3xl rounded-lg shadow-lg relative max-h-[90vh] overflow-y-auto">
        <button
          type="button"
          onClick={() => dispatch(closeHouseLoanmodal())}
          className="absolute top-4 right-4 text-2xl"
        >
          ×
        </button>

        <div className="p-8">
          <h2 className="text-3xl font-bold mb-6">Mortgage loan application</h2>

          {/* form yahan rahega */}
        </div>
      </div>
    </div>
  );
}