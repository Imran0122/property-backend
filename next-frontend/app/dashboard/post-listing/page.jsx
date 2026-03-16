"use client";

import { useState } from "react";
import LocationPurpose from "./components/LocationPurpose";
import PriceArea from "./components/PriceArea";
import FeaturesAmenities from "./components/FeaturesAmenities";
import AdInfo from "./components/AdInfo";
import ImagesVideos from "./components/ImagesVideos";
import ContactInfo from "./components/ContactInfo";
import PlatformSelection from "./components/PlatformSelection";

export default function PostListingPage() {
  const [formData, setFormData] = useState({
    title: "",
    description: "",
    email: "",
    phone: "",
    phoneCountryCode: "+212",
    landline: "",
    landlineCountryCode: "+212",
    bedrooms: null,
    bathrooms: null,
    images: [],
    purpose: "sell",
    propertyTab: "home",
    propertyType: "",
    city: "",
    location: "",
    price: "",
    landArea: "",
    unit: "Marla",
  });

  function handleChange(field, value) {
    setFormData((prev) => ({
      ...prev,
      [field]: value,
    }));
  }

  // function handleSubmit() {
  //   // Example API call structure using fetch:
  //   fetch("/api/post-listing", {
  //     method: "POST",
  //     headers: { "Content-Type": "application/json" },
  //     body: JSON.stringify(formData),
  //   })
  //     .then((res) => {
  //       if (!res.ok) throw new Error("Network response was not ok");
  //       return res.json();
  //     })
  //     .then((data) => {
  //       alert("Property submitted successfully!");
  //       // Reset form or redirect if needed
  //     })
  //     .catch((error) => {
  //       alert("Error submitting property: " + error.message);
  //     });
  // }

  async function handleSubmit() {
  try {
    const res = await fetch("/api/properties", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    });

    const result = await res.json();

    if (!res.ok) {
      throw new Error(result.message || "Something went wrong");
    }

    alert("Property submitted successfully!");
    console.log(result);

  } catch (error) {
    alert("Error submitting property: " + error.message);
  }
}

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-[1100px] mx-auto px-4 py-8">
        {/* top small promo banner */}
        <div className="bg-white rounded-lg shadow px-6 py-6 mb-6">
          <div className="text-center text-sm text-gray-600">
            Atteignez des millions d'acheteurs sur nos plateformes
          </div>
        </div>

        {/* sections vertical stack */}
        <div className="space-y-6">
          <LocationPurpose formData={formData} handleChange={handleChange} />
          <PriceArea formData={formData} handleChange={handleChange} />
          <FeaturesAmenities formData={formData} handleChange={handleChange} />
          <AdInfo formData={formData} handleChange={handleChange} />
          <ImagesVideos formData={formData} handleChange={handleChange} />
          <ContactInfo formData={formData} handleChange={handleChange} />
          <PlatformSelection />

          <div className="flex justify-end">
            <button
              onClick={handleSubmit}
              className="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md shadow"
            >
              Submit
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}