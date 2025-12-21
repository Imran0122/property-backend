"use client";
import React, { useState } from "react";
import { FaInbox, FaTrash } from "react-icons/fa";

const InboxPage = () => {
  const [activeTab, setActiveTab] = useState("inbox");

  return (
    <div className="min-h-screen bg-[#f8f9fc] px-6 py-6 flex flex-col justify-between">
      {/* Main Content Section */}
      <div className="flex flex-col lg:flex-row gap-6">
        {/* Left Sidebar Tabs */}
        <div className="bg-white rounded-xl shadow-sm w-full lg:w-1/4 h-fit">
          <div className="flex flex-col">
            {/* Inbox Tab */}
            <button
              onClick={() => setActiveTab("inbox")}
              className={`flex items-center gap-3 px-6 py-4 rounded-lg mx-3 my-2 text-sm font-medium transition-all duration-200 ${
                activeTab === "inbox"
                  ? "bg-[#e8f7ee] text-[#00A651]"
                  : "text-gray-700 hover:bg-gray-50"
              }`}
            >
              <FaInbox className="text-lg" />
              Inbox
            </button>

            {/* Trash Tab */}
            <button
              onClick={() => setActiveTab("trash")}
              className={`flex items-center gap-3 px-6 py-4 rounded-lg mx-3 mb-3 text-sm font-medium transition-all duration-200 ${
                activeTab === "trash"
                  ? "bg-[#e8f7ee] text-[#00A651]"
                  : "text-gray-700 hover:bg-gray-50"
              }`}
            >
              <FaTrash className="text-lg" />
              Trash
            </button>
          </div>
        </div>

        {/* Right Content Area */}
        <div className="bg-white rounded-xl shadow-sm flex-1 flex items-center justify-center py-20">
          <div className="flex flex-col items-center">
            <img
              src="https://www.zameen.com/assets/images/empty-states/no-records.svg"
              alt="No record found"
              className="w-40 mb-4 opacity-70"
            />
            <p className="text-[#222] text-base font-semibold">
              No Record Found
            </p>
          </div>
        </div>
      </div>

      {/* Footer */}
      <footer className="text-center text-sm text-gray-500 mt-10">
        © 2025 – Powered by Zameen.com
      </footer>
    </div>
  );
};

export default InboxPage;
