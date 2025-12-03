import React from "react";
import { FaPaw, FaTag, FaCalendarAlt, FaCheckCircle, FaTimesCircle } from "react-icons/fa";

import { useAuth } from "../context/AuthContext";
import axios from "axios";
import { useState } from "react";

export default function PetModal({ pet, onClose }) {
  const { token, user } = useAuth();
  const [applicationText, setApplicationText] = useState("");
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState("");

  const adoptPet = async () => {
    if (!applicationText.trim()) return setMessage("Explain why you want to adopt");

    try {
      setLoading(true);
      const res = await axios.post(
        "http://localhost:8000/api/adoptions",
        { pet_id: pet.id, application_text: applicationText },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      setMessage("Application sent successfully");
    } catch (err) {
      setMessage(err.response?.data?.error || "Failed to submit request");
    } finally {
      setLoading(false);
    }
  };

// export default function PetModal({ pet, onClose }) {
  if (!pet) return null;

  const img = pet.photos?.length ? pet.photos[0].url : null;

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
      onClick={onClose}
      role="dialog"
      aria-modal="true"
    >
      <div
        onClick={e => e.stopPropagation()}
        className="bg-gray-100 rounded-2xl max-w-3xl w-full p-6 relative shadow-2xl"
      >
        {/* close */}
        <button
          onClick={onClose}
          className="absolute right-4 top-4 text-gray-600 hover:text-black text-2xl font-bold"
        >
          ✕
        </button>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {/* PHOTO SIDE */}
          <div className="rounded-xl overflow-hidden bg-gray-300">
            {img ? (
              <img
                src={img}
                alt={pet.name}
                className="w-full h-80 object-cover"
              />
            ) : (
              <div className="h-80 flex items-center justify-center text-gray-400">
                No Image
              </div>
            )}
          </div>

          {/* DETAILS SIDE */}
          <div className="text-black font-quicksand">
            <h2 className="text-3xl font-bold">{pet.name}</h2>

            {/* STATUS BADGE */}
            <div className="mt-2">
              {pet.status === "adopted" ? (
                <span className="flex items-center gap-2 text-green-600 text-lg font-semibold">
                  <FaCheckCircle /> Adopted
                </span>
              ) : (
                <span className="flex items-center gap-2 text-yellow-600 text-lg font-semibold">
                  <FaTimesCircle /> Available
                </span>
              )}
            </div>

            {/* INFO ROWS */}
            <div className="mt-6 space-y-4 text-lg">
              <div className="flex items-center gap-3">
                <FaPaw className="text-blue-600" />
                <span>
                  Species: <strong>{pet.species}</strong>
                </span>
              </div>

              {pet.breed && (
                <div className="flex items-center gap-3">
                  <FaTag className="text-blue-600" />
                  <span>
                    Breed: <strong>{pet.breed}</strong>
                  </span>
                </div>
              )}

              <div className="flex items-center gap-3">
                <FaCalendarAlt className="text-blue-600" />
                <span>
                  Age: <strong>{pet.age ?? "N/A"}</strong>
                </span>
              </div>
            </div>

            {/* ACTIONS */}
            <div className="mt-8 space-y-4">
              {user?.role === "user" && pet.status === "available" && (
                <div className="space-y-3">
                  <textarea
                    className="w-full border p-2 rounded"
                    rows="4"
                    placeholder="Why do you want to adopt this pet?"
                    onChange={e => setApplicationText(e.target.value)}
                  />

                  <button
                    disabled={loading}
                    onClick={adoptPet}
                    className="w-full bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 disabled:opacity-50"
                  >
                    {loading ? "Sending..." : "Apply for Adoption"}
                  </button>

                  {message && (
                    <p className="text-center text-black font-semibold">{message}</p>
                  )}
                </div>
              )}

              <button
                onClick={onClose}
                className="w-full bg-gray-300 py-3 rounded-xl hover:bg-gray-400"
              >
                Close
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>
  );
}
