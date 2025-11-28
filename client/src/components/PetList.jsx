// src/pages/PetList.jsx
import React, { useState } from 'react';
import PetModal from '../components/PetModal/PetModal';

export default function PetList({ pets }) {
  const [selectedPet, setSelectedPet] = useState(null);

  return (
    <div className="p-6">
      <h1 className="text-3xl font-bold mb-6">Our Pets</h1>
      <div className="grid grid-cols-3 gap-4">
        {pets.map(pet => (
          <div 
            key={pet.id} 
            onClick={() => setSelectedPet(pet)} 
            className="cursor-pointer border p-2 rounded hover:shadow-lg"
          >
            <h2 className="font-semibold">{pet.name}</h2>
            <p className="text-sm text-gray-500">{pet.species}</p>
          </div>
        ))}
      </div>

      <PetModal pet={selectedPet} onClose={() => setSelectedPet(null)} />
    </div>
  );
}
