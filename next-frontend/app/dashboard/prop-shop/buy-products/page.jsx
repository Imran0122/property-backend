"use client";
import React, { useState } from "react";
import { FaCamera, FaVideo, FaFire, FaBolt, FaRegImage } from "react-icons/fa";

const products = [
  {
    category: "Annonces",
    items: [
      {
        icon: <FaRegImage className="text-green-600 text-xl" />,
        title: "Annonce",
        description: "Obtenez un emplacement publicitaire pendant 30 jours pour publier votre annonce",
        price: "MAD 3,000",
      },
      {
        icon: <FaFire className="text-orange-500 text-xl" />,
        title: "Annonce Hot",
        description: "Obtenez un emplacement publicitaire pendant 30 jours et placez votre annonce au-dessus des annonces normales",
        price: "MAD 7,800",
      },
      {
        icon: <FaBolt className="text-red-500 text-xl" />,
        title: "Annonce Super Hot",
        description: "Obtenez un emplacement publicitaire pendant 30 jours et placez votre annonce en tête des résultats de recherche",
        price: "MAD 21,000",
      },

    ],
  },
  {
    category: "Crédits (Applicable uniquement aux annonces déjà publiées)",
    items: [
      {
        icon: <FaRegImage className="text-blue-500 text-xl" />,
        title: "Crédits de rafraîchissement",
        description:
          "Actualisez l’heure de vos annonces publiées et remontez-les en haut à nouveau",
        price: "MAD 240",
      },
      {
        icon: <FaRegImage className="text-green-500 text-xl" />,
        title: "Crédits pour annonces en story",
        description: "Obtenez plus de visibilité en publiant votre annonce dans la story",
        price: "MAD 1,000",
      },
      {
        icon: <FaCamera className="text-sky-500 text-xl" />,
        title: "Crédits pour photographie vérifiée",
        description:
          "Améliorez l’attrait visuel de votre propriété avec notre service de photographie professionnelle premium. Service uniquement disponible à Casablanca, Rabat et Marrakech.",
        price: "MAD 3,600",
        tag: "Recommandé",
      },
      {
        icon: <FaVideo className="text-yellow-500 text-xl" />,
        title: "Crédits pour vidéographie vérifiée",
        description:
          "Donnez vie à votre propriété avec notre service de vidéographie captivant. Service uniquement disponible à Casablanca, Rabat et Marrakech.",
        price: "MAD 12,000",
        tag: "Recommandé",
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
