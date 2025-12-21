"use client";
import React, { useState } from "react";
import { FaCamera, FaVideo, FaFire, FaBolt, FaRegImage } from "react-icons/fa";

const products = [
  {
    category: "Listings",
    items: [
      {
        icon: <FaRegImage className="text-green-600 text-xl" />,
        title: "Listing",
        description: "Get an ad slot for 30 days to post your listing",
        price: "Rs 3,000",
      },
      {
        icon: <FaFire className="text-orange-500 text-xl" />,
        title: "Hot Listing",
        description:
          "Get an ad slot for 30 days and place your ad above normal listings",
        price: "Rs 7,800",
      },
      {
        icon: <FaBolt className="text-red-500 text-xl" />,
        title: "Super Hot Listing",
        description:
          "Get an ad slot for 30 days and place your ad at the top of search results",
        price: "Rs 21,000",
      },
    ],
  },
  {
    category: "Credits (Only applicable on already posted listings)",
    items: [
      {
        icon: <FaRegImage className="text-blue-500 text-xl" />,
        title: "Refresh Credits",
        description:
          "Refresh the time of your posted listings and bring them to the top again",
        price: "Rs 240",
      },
      {
        icon: <FaRegImage className="text-green-500 text-xl" />,
        title: "Story Ad Credits",
        description: "Get more exposure by posting your listing in the story",
        price: "Rs 1,000",
      },
      {
        icon: <FaCamera className="text-sky-500 text-xl" />,
        title: "Verified Photography Credits",
        description:
          "Upgrade your property's visual appeal with our premium professional photoshoot service. Service only available in Karachi, Lahore & Islamabad.",
        price: "Rs 3,600",
        tag: "Recommended",
      },
      {
        icon: <FaVideo className="text-yellow-500 text-xl" />,
        title: "Verified Videography Credits",
        description:
          "Bring your property to life with our captivating videography service. Service only available in Karachi, Lahore & Islamabad.",
        price: "Rs 12,000",
        tag: "Recommended",
      },
    ],
  },
];

const BuyProductsPage = () => {
  const [cart, setCart] = useState([]);

  const addToCart = (item) => {
    setCart((prev) => [...prev, item]);
  };

  return (
    <div className="flex gap-6 p-6 bg-gray-50 min-h-screen">
      {/* MAIN CONTENT */}
      <div className="w-[72%] space-y-6">
        {products.map((section, idx) => (
          <div key={idx} className="bg-white rounded-2xl shadow-sm p-5">
            <h2 className="text-lg font-semibold mb-3">
              {section.category}
            </h2>
            <div className="space-y-3">
              {section.items.map((item, index) => (
                <div
                  key={index}
                  className="flex justify-between items-center border border-gray-100 rounded-xl p-4 hover:shadow-sm transition"
                >
                  {/* Product Info */}
                  <div className="flex items-start gap-3">
                    <div className="p-3 bg-gray-50 rounded-lg">{item.icon}</div>
                    <div>
                      <div className="flex items-center gap-2">
                        <h3 className="font-medium">{item.title}</h3>
                        {item.tag && (
                          <span className="bg-green-100 text-green-600 text-xs px-2 py-0.5 rounded-md">
                            {item.tag}
                          </span>
                        )}
                      </div>
                      <p className="text-sm text-gray-500 mt-1">
                        {item.description}
                      </p>
                    </div>
                  </div>

                  {/* Price & Button */}
                  <div className="flex flex-col items-end">
                    <span className="font-medium mb-2">{item.price}</span>
                    <button
                      onClick={() => addToCart(item)}
                      className="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-medium min-w-[120px]"
                    >
                      Add To Cart
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        ))}
      </div>

      {/* CART SUMMARY */}
      <div className="w-[28%]">
        <div className="bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center justify-center min-h-[300px]">
          {cart.length === 0 ? (
            <>
              <img
                src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png"
                alt="Empty Cart"
                className="w-16 opacity-60 mb-3"
              />
              <p className="text-gray-600 font-medium">No Items</p>
              <p className="text-gray-400 text-sm">Added in cart</p>
            </>
          ) : (
            <>
              <h3 className="font-semibold text-lg mb-3">Order Summary</h3>
              <ul className="w-full text-sm text-gray-600 mb-4">
                {cart.map((item, index) => (
                  <li
                    key={index}
                    className="flex justify-between border-b py-1 last:border-0"
                  >
                    <span>{item.title}</span>
                    <span>{item.price}</span>
                  </li>
                ))}
              </ul>
              <button className="bg-green-600 hover:bg-green-700 text-white w-full py-2 rounded-lg font-medium">
                Proceed To Payment
              </button>
            </>
          )}
        </div>
      </div>
    </div>
  );
};

export default BuyProductsPage;
