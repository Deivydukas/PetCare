// src/pages/RoomList.jsx
import React, { useState } from "react";
import PetModal from "../components/PetModal/PetModal";

export default function RoomList({ rooms }) {
  const [selectedPet, setSelectedPet] = useState(null);
  const [selectedRoom, setSelectedRoom] = useState(null);

  return (
    <div className="p-6">
      <h1 className="text-3xl font-bold mb-6">Rooms</h1>
      <div className="grid grid-cols-2 gap-4">
        {rooms.map(room => (
          <div key={room.id} className="border p-4 rounded hover:shadow-lg">
            <h2 className="font-semibold mb-2">{room.name}</h2>
            <div className="flex gap-2 flex-wrap">
              {room.pets.map(pet => (
                <div
                  key={pet.id}
                  onClick={() => setSelectedPet(pet)}
                  className="cursor-pointer border p-2 rounded hover:bg-gray-100"
                >
                  {pet.name}
                </div>
              ))}
            </div>
          </div>
        ))}
      </div>

      {/* Pet modal */}
      <PetModal pet={selectedPet} onClose={() => setSelectedPet(null)} />
    </div>
  );
}
