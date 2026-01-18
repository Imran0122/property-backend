"use client";  // ✅ Next.js 13+ me frontend ke liye zaroori hai
import { useEffect, useState } from "react";
import Pusher from "pusher-js";

export default function Chat() {
  const [messages, setMessages] = useState([]);
  const [input, setInput] = useState("");

  useEffect(() => {
    // ✅ Pusher connection setup
    const pusher = new Pusher("abcdEFGHijklMNop", {
      cluster: "us2",  // tumne Pusher dashboard me jo select kiya
    });

    const channel = pusher.subscribe("chat");

    channel.bind("MessageSent", function (data) {
      setMessages((prev) => [...prev, data.message]);
    });

    return () => {
      channel.unbind_all();
      channel.unsubscribe();
    };
  }, []);

  const sendMessage = async () => {
    if (!input.trim()) return;

    await fetch("http://127.0.0.1:8000/api/send-message", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ message: input }),
    });

    setInput("");
  };

  return (
    <div className="max-w-md mx-auto mt-10 bg-white shadow-lg rounded-lg p-6">
      <h2 className="text-xl font-bold mb-4">💬 Chat en temps réel</h2>

      {/* Messages */}
      <div className="h-64 overflow-y-auto border p-3 mb-4">
        {messages.map((msg, i) => (
          <div key={i} className="p-2 bg-gray-100 rounded mb-2">
            {msg}
          </div>
        ))}
      </div>

      {/* Input */}
      <div className="flex gap-2">
        <input
          type="text"
          value={input}
          onChange={(e) => setInput(e.target.value)}
          placeholder="Type a message..."
          className="flex-1 border rounded p-2"
        />
        <button
          onClick={sendMessage}
          className="bg-green-600 text-white px-4 py-2 rounded"
        >
          Envoyer
        </button>
      </div>
    </div>
  );
}
