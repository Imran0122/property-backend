"use client";

export default function HeaderSection({ total }) {
  return (
    <div className="max-w-7xl mx-auto px-4 md:px-8 mb-6">
      <h1 className="text-2xl md:text-3xl font-bold text-gray-800">
        Properties for Sale
      </h1>
      <p className="text-gray-500 text-sm mt-1">
        {total} listings found
      </p>
    </div>
  );
}
