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
                    <span>User Settings</span>
                  </button>

                  <button
                    onClick={() => setActiveTab("preferences")}
                    className={`flex items-center w-full px-4 py-3 rounded-md text-sm font-medium transition-all text-left
                      ${activeTab === "preferences" ? "bg-green-50 text-green-700" : "text-gray-700 hover:bg-gray-50"}`}
                  >
                    <FaSlidersH className="mr-3 text-lg" />
                    <span>Preferences</span>
                  </button>

                  <button
                    onClick={() => setActiveTab("password")}
                    className={`flex items-center w-full px-4 py-3 rounded-md text-sm font-medium transition-all text-left
                      ${activeTab === "password" ? "bg-green-50 text-green-700" : "text-gray-700 hover:bg-gray-50"}`}
                  >
                    <FaLock className="mr-3 text-lg" />
                    <span>Change Password</span>
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
                  <h2 className="text-2xl font-semibold mb-6">User Settings</h2>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">Name</label>
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
                          <option>+92</option>
                        </select>
                        <input className="flex-1 px-4 py-3 rounded-md border border-gray-200 bg-white" placeholder="Enter mobile" />
                      </div>
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">Landline</label>
                      <div className="flex gap-3">
                        <select className="px-3 py-3 rounded-md border border-gray-200 bg-white">
                          <option>+92</option>
                        </select>
                        <input className="flex-1 px-4 py-3 rounded-md border border-gray-200 bg-white" placeholder="Enter landline" />
                      </div>
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                      <div className="flex gap-3">
                        <select className="px-3 py-3 rounded-md border border-gray-200 bg-white">
                          <option>+92</option>
                        </select>
                        <input className="flex-1 px-4 py-3 rounded-md border border-gray-200 bg-white" placeholder="WhatsApp number" />
                      </div>
                    </div>

                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">City</label>
                      <select className="w-full px-4 py-3 rounded-md border border-gray-200 bg-white">
                        <option>Select City</option>
                      </select>
                    </div>

                    <div className="md:col-span-2">
                      <label className="block text-sm font-medium text-gray-700 mb-2">Address</label>
                      <input className="w-full px-4 py-3 rounded-md border border-gray-200 bg-white" placeholder="Enter address" />
                    </div>

                    <div className="md:col-span-2">
                      <label className="block text-sm font-medium text-gray-700 mb-3">Upload a picture</label>

                      <div className="border-2 border-dashed border-green-200 rounded-md p-6 flex items-center gap-4">
                        <div className="bg-green-50 rounded-full p-3">
                          <FaUpload className="text-green-600 text-xl" />
                        </div>
                        <div className="flex-1">
                          <div className="text-sm font-medium">Browse and Upload</div>
                          <div className="text-xs text-gray-500">PNG, JPG - max 5MB</div>
                        </div>
                        <button className="px-4 py-2 bg-white border border-gray-200 rounded-md">Choose</button>
                      </div>
                    </div>
                  </div>

                  <div className="flex items-center gap-3 mt-6">
                    <input type="checkbox" id="updateAll" className="h-4 w-4 accent-green-600" />
                    <label htmlFor="updateAll" className="text-sm text-gray-700">Update details in all property listings</label>
                  </div>

                  <div className="mt-6 flex justify-end">
                    <button className="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">Save Changes</button>
                  </div>
                </section>
              )}

              {/* PREFERENCES */}
              {activeTab === "preferences" && (
                <section>
                  <h2 className="text-2xl font-semibold mb-6">Preferences</h2>

                  <div className="space-y-6">
                    <div className="flex items-center justify-between border-b pb-4">
                      <div>
                        <div className="font-medium text-gray-800">Email Notification</div>
                        <div className="text-sm text-gray-500">Allow to receive email notifications</div>
                      </div>
                      <Toggle checked={emailNotif} onChange={setEmailNotif} />
                    </div>

                    <div className="flex items-center justify-between border-b pb-4">
                      <div>
                        <div className="font-medium text-gray-800">Newsletters</div>
                        <div className="text-sm text-gray-500">Allow to receive newsletters</div>
                      </div>
                      <Toggle checked={newsletters} onChange={setNewsletters} />
                    </div>

                    <div className="flex items-center justify-between border-b pb-4">
                      <div>
                        <div className="font-medium text-gray-800">Automated Reports</div>
                        <div className="text-sm text-gray-500">Send automated reports</div>
                      </div>
                      <Toggle checked={automatedReports} onChange={setAutomatedReports} />
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label className="block text-sm text-gray-700 mb-2">Currency</label>
                        <select className="w-full px-4 py-3 rounded-md border border-gray-200 bg-white">
                          <option>PKR</option>
                        </select>
                      </div>

                      <div>
                        <label className="block text-sm text-gray-700 mb-2">Area Unit</label>
                        <select className="w-full px-4 py-3 rounded-md border border-gray-200 bg-white">
                          <option>Marla</option>
                        </select>
                      </div>
                    </div>

                    <div className="flex justify-end">
                      <button className="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">Save Changes</button>
                    </div>
                  </div>
                </section>
              )}

              {/* CHANGE PASSWORD */}
              {activeTab === "password" && (
                <section>
                  <h2 className="text-2xl font-semibold mb-6">Change Password</h2>

                  <div className="max-w-xl space-y-4">
                    <div>
                      <label className="block text-sm text-gray-700 mb-2">Enter Old Password</label>
                      <input type="password" className="w-full px-4 py-3 rounded-md border border-gray-200" placeholder="Enter old password" />
                    </div>

                    <div>
                      <label className="block text-sm text-gray-700 mb-2">Enter New Password</label>
                      <input type="password" className="w-full px-4 py-3 rounded-md border border-gray-200" placeholder="Enter new password" />
                    </div>

                    <div>
                      <label className="block text-sm text-gray-700 mb-2">Confirm Password</label>
                      <input type="password" className="w-full px-4 py-3 rounded-md border border-gray-200" placeholder="Confirm password" />
                    </div>
                  </div>

                  <div className="mt-6 flex justify-end">
                    <button className="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition">Confirm</button>
                  </div>
                </section>
              )}

            </div>

            <p className="text-center text-gray-400 text-sm mt-6">© 2025 – Powered by Zameen.com</p>
          </main>
        </div>
      </div>
    </div>
  );
}
