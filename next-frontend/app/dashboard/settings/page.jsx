// next-frontend/app/dashboard/settings/page.jsx
"use client";

import React, { useState } from "react";
import { FaUser, FaSlidersH, FaLock, FaUpload } from "react-icons/fa";

const Toggle = ({ checked, onChange }) => {
  return (
    <button
      aria-pressed={checked}
      onClick={() => onChange(!checked)}
      className={`w-12 h-7 flex items-center p-1 rounded-full transition-colors duration-200
        ${checked ? "bg-green-500" : "bg-gray-200"}`}
    >
      <div
        className={`bg-white w-5 h-5 rounded-full shadow-sm transform transition-transform duration-200
          ${checked ? "translate-x-5" : "translate-x-0"}`}
      />
    </button>
  );
};

export default function SettingsPage() {
  const [activeTab, setActiveTab] = useState("user");
  const [emailNotif, setEmailNotif] = useState(false);
  const [newsletters, setNewsletters] = useState(false);
  const [automatedReports, setAutomatedReports] = useState(false);

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-7xl mx-auto px-6 py-8">

        <div className="grid grid-cols-12 gap-6">
          {/* Sidebar (left) */}
          <aside className="col-span-12 md:col-span-3">
            <div className="sticky top-20">
              <div className="bg-white rounded-xl shadow-sm p-4">
                <div className="space-y-2">
                  <button
                    onClick={() => setActiveTab("user")}
                    className={`flex items-center w-full px-4 py-3 rounded-md text-sm font-medium transition-all text-left
                      ${activeTab === "user" ? "bg-green-50 text-green-700" : "text-gray-700 hover:bg-gray-50"}`}
                  >
                    <FaUser className="mr-3 text-lg" />
                    <span>Paramètres utilisateur</span>
                  </button>

                  <button
                    onClick={() => setActiveTab("preferences")}
                    className={`flex items-center w-full px-4 py-3 rounded-md text-sm font-medium transition-all text-left
                      ${activeTab === "preferences" ? "bg-green-50 text-green-700" : "text-gray-700 hover:bg-gray-50"}`}
                  >
                    <FaSlidersH className="mr-3 text-lg" />
                    <span>Préférences</span>
                  </button>

                  <button
                    onClick={() => setActiveTab("password")}
                    className={`flex items-center w-full px-4 py-3 rounded-md text-sm font-medium transition-all text-left
                      ${activeTab === "password" ? "bg-green-50 text-green-700" : "text-gray-700 hover:bg-gray-50"}`}
                  >
                    <FaLock className="mr-3 text-lg" />
                    <span>Changer le mot de passe</span>
                  </button>
                </div>
              </div>
            </div>
          </aside>

          {/* Main content */}
          <main className="col-span-12 md:col-span-9">
            <div className="bg-white rounded-xl shadow-sm p-8">

              {/* USER */}
              {activeTab === "user" && (
                <section>
                  <h2 className="text-2xl font-semibold mb-6">Paramètres utilisateur</h2>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                      <input className="w-full px-4 py-3 rounded-md border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-green-100" placeholder="Enter Name" />
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">Email</label>
                      <input className="w-full px-4 py-3 rounded-md border border-gray-200 bg-gray-50 focus:outline-none" placeholder="Email" disabled />
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">Mobile</label>
                      <div className="flex gap-3">
                        <select className="px-3 py-3 rounded-md border border-gray-200 bg-white">
                          <option>+212</option>
                        </select>
                        <input className="flex-1 px-4 py-3 rounded-md border border-gray-200 bg-white" placeholder="Enter mobile" />
                      </div>
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">Landline</label>
                      <div className="flex gap-3">
                        <select className="px-3 py-3 rounded-md border border-gray-200 bg-white">
                          <option>+212</option>
                        </select>
                        <input className="flex-1 px-4 py-3 rounded-md border border-gray-200 bg-white" placeholder="Enter landline" />
                      </div>
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                      <div className="flex gap-3">
                        <select className="px-3 py-3 rounded-md border border-gray-200 bg-white">
                          <option>+212</option>
                        </select>
                        <input className="flex-1 px-4 py-3 rounded-md border border-gray-200 bg-white" placeholder="WhatsApp number" />
                      </div>
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">ville</label>
                      <select className="w-full px-4 py-3 rounded-md border border-gray-200 bg-white">
                        <option>Sélectionner la ville</option>
                      </select>
                    </div>

                    <div className="md:col-span-2">
                      <label className="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                      <input className="w-full px-4 py-3 rounded-md border border-gray-200 bg-white" placeholder="Enter address" />
                    </div>

                    <div className="md:col-span-2">
                      <label className="block text-sm font-medium text-gray-700 mb-3">Télécharger une image</label>

                      <div className="border-2 border-dashed border-green-200 rounded-md p-6 flex items-center gap-4">
                        <div className="bg-green-50 rounded-full p-3">
                          <FaUpload className="text-green-600 text-xl" />
                        </div>
                        <div className="flex-1">
                          <div className="text-sm font-medium">Parcourir et télécharger</div>
                          <div className="text-xs text-gray-500">PNG, JPG – max 5 Mo</div>
                        </div>
                        <button className="px-4 py-2 bg-white border border-gray-200 rounded-md">Choisir</button>
                      </div>
                    </div>
                  </div>

                  <div className="flex items-center gap-3 mt-6">
                    <input type="checkbox" id="updateAll" className="h-4 w-4 accent-green-600" />
                    <label htmlFor="updateAll" className="text-sm text-gray-700">Mettre à jour les détails dans toutes les annonces immobilières</label>
                  </div>

                  <div className="mt-6 flex justify-end">
                    <button className="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">Enregistrer les modifications</button>
                  </div>
                </section>
              )}

              {/* PREFERENCES */}
              {activeTab === "preferences" && (
                <section>
                  <h2 className="text-2xl font-semibold mb-6">Préférences</h2>

                  <div className="space-y-6">
                    <div className="flex items-center justify-between border-b pb-4">
                      <div>
                        <div className="font-medium text-gray-800">Notifications par e-mail</div>
                        <div className="text-sm text-gray-500">Autoriser la réception de notifications par e-mail</div>

                      </div>
                      <Toggle checked={emailNotif} onChange={setEmailNotif} />
                    </div>

                    <div className="flex items-center justify-between border-b pb-4">
                      <div>
                        <div className="font-medium text-gray-800">Bulletins d'information</div>
                        <div className="text-sm text-gray-500">Autoriser la réception des bulletins d'information</div>

                      </div>
                      <Toggle checked={newsletters} onChange={setNewsletters} />
                    </div>

                    <div className="flex items-center justify-between border-b pb-4">
                      <div>
                        <div className="font-medium text-gray-800">Rapports automatisés</div>
                        <div className="text-sm text-gray-500">Envoyer des rapports automatisés</div>

                      </div>
                      <Toggle checked={automatedReports} onChange={setAutomatedReports} />
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label className="block text-sm text-gray-700 mb-2">Devise</label>
                        <select className="w-full px-4 py-3 rounded-md border border-gray-200 bg-white">
                          <option>MAD</option>
                        </select>
                      </div>

                      <div>
                        <label className="block text-sm text-gray-700 mb-2">Unité de superficie</label>
                        <select className="w-full px-4 py-3 rounded-md border border-gray-200 bg-white">
                          <option>m²</option>
                        </select>
                      </div>
                    </div>

                    <div className="flex justify-end">
                      <button className="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">Enregistrer les modifications</button>
                    </div>
                  </div>
                </section>
              )}

              {/* CHANGE PASSWORD */}
              {activeTab === "password" && (
                <section>
                  <h2 className="text-2xl font-semibold mb-6">Changer le mot de passe</h2>

                  <div className="max-w-xl space-y-4">
                    <div>
                      <label className="block text-sm text-gray-700 mb-2">Entrez l’ancien mot de passe</label>
                      <input type="password" className="w-full px-4 py-3 rounded-md border border-gray-200" placeholder="Entrez l’ancien mot de passe" />
                    </div>

                    <div>
                      <label className="block text-sm text-gray-700 mb-2">Entrez le nouveau mot de passe</label>
                      <input type="password" className="w-full px-4 py-3 rounded-md border border-gray-200" placeholder="Entrez le nouveau mot de passe" />
                    </div>

                    <div>
                      <label className="block text-sm text-gray-700 mb-2">Confirmer le mot de passe</label>
                      <input type="password" className="w-full px-4 py-3 rounded-md border border-gray-200" placeholder="Confirmer le mot de passe" />
                    </div>
                  </div>

                  <div className="mt-6 flex justify-end">
                    <button className="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">Confirmer</button>
                  </div>
                </section>
              )}

            </div>

            <p className="text-center text-gray-400 text-sm mt-6">© 2025 – Propulsé par Hectare.ma</p>
          </main>
        </div>
      </div>
    </div>
  );
}
