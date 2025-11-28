// src/pages/RoomPets.jsx
import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import PetModal from "../components/PetModal";

export default function RoomPets() {
  const { roomId } = useParams();
  const [pets, setPets] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedPet, setSelectedPet] = useState(null);

  useEffect(() => {
    fetch(`http://localhost:8000/api/rooms/${roomId}/pets`)
      .then(res => res.json())
      .then(data => {console.log("Pets data:", data);
       setPets(data)}) // adjust according to your API
      .catch(err => console.error(err))
      .finally(() => setLoading(false));
  }, [roomId]);

  if (loading) return <p className="text-center mt-20 text-white">Loading pets...</p>;
  {!pets.length && (
  <p className="text-center text-white mt-10">No pets in this room yet.</p>
)}


  return (
    <div className="min-h-screen py-5 bg-gradient-to-br from-blue-800 to-blue-400 text-white">
      <section className="max-w-4xl mx-auto mt-6 px-4">
        <h1 className="font-quicksand text-4xl font-bold mb-6">Pets in this Room</h1>
      </section>

      <section className="font-quicksand max-w-4xl mx-auto mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-4 pb-24">
        {pets.map(pet => (
          <div
            key={pet.id}
            onClick={() => setSelectedPet(pet)}
            className="bg-white text-black rounded-xl p-4 shadow-lg cursor-pointer hover:shadow-2xl"
          >
            <img
              src={pet.photos?.[0]?.url || "/src/assets/pet-placeholder.jpg"}
              alt={pet.name}
              className="w-full h-48 object-cover rounded"
            />
            <h3 className="text-xl font-bold mt-2">{pet.name}</h3>
            <p>{pet.species} {pet.breed && `· ${pet.breed}`}</p>
          </div>
        ))}
      </section>

      <PetModal pet={selectedPet} onClose={() => setSelectedPet(null)} />
    </div>
  );
}
