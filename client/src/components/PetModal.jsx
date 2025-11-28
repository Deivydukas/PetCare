import React from "react";
import { FaPaw, FaTag, FaCalendarAlt, FaCheckCircle, FaTimesCircle } from "react-icons/fa";

export default function PetModal({ pet, onClose }) {
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

            {/* ACTION */}
            <div className="mt-8 flex justify-end">
              <button
                onClick={onClose}
                className="bg-blue-600 text-white px-6 py-3 rounded-xl text-lg hover:bg-blue-700 transition shadow"
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
