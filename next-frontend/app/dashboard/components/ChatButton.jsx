// next-frontend/app/dashboard/components/ChatButton.jsx
"use client";
import React from "react";

export default function ChatButton() {
  return (
    <div className="fixed left-6 bottom-8 z-40">
      <button className="bg-green-600 text-white px-6 py-3 rounded-full shadow-md flex items-center gap-3">
        <span className="text-xl">💬</span>
        <span className="font-medium">Chat</span>
      </button>
    </div>
  );
}







