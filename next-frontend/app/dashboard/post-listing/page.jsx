// === FILE: /app/dashboard/post-listing/page.jsx
import LocationPurpose from "./components/LocationPurpose";
import PriceArea from "./components/PriceArea";
import FeaturesAmenities from "./components/FeaturesAmenities";
import AdInfo from "./components/AdInfo";
import ImagesVideos from "./components/ImagesVideos";
import ContactInfo from "./components/ContactInfo";
import PlatformSelection from "./components/PlatformSelection";

export default function PostListingPage() {
  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-[1100px] mx-auto px-4 py-8">
        {/* top small promo banner */}
        <div className="bg-white rounded-lg shadow px-6 py-6 mb-6">
          <div className="text-center text-sm text-gray-600">Atteignez des millions d'acheteurs sur nos plateformes</div>
        </div>

        {/* sections vertical stack */}
        <div className="space-y-6">
          <LocationPurpose />
          <PriceArea />
          <FeaturesAmenities />
          <AdInfo />
          <ImagesVideos />
          <ContactInfo />
          <PlatformSelection />

          <div className="flex justify-end">
            <button className="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md shadow">Submit</button>
          </div>
        </div>
      </div>
    </div>
  );
}