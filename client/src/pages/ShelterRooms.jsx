import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";

export default function ShelterRooms() {
  const { id } = useParams();
  const [rooms, setRooms] = useState([]);
  const [shelter, setShelter] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Fetch shelter info (if endpoint exists)
    fetch(`http://localhost:8000/api/shelters/${id}`)
      .then(res => res.json())
      .then(data => setShelter(data))
      .catch(err => console.error(err));

    // Fetch rooms
    fetch(`http://localhost:8000/api/shelters/${id}/rooms`)
      .then(res => res.json())
      .then(data => setRooms(data.rooms)) // <-- use data.rooms
      .catch(err => console.error(err))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) return <p className="text-white text-center mt-20">Loading...</p>;
  if (!shelter) return <p className="text-white text-center mt-20">Shelter not found</p>;

  return (
    <div className="min-h-screen py-5 bg-gradient-to-br from-blue-900 to-blue-400 text-white">
      <section className="max-w-4xl mx-auto mt-6 px-4">
        <h1 className="text-4xl font-bold mb-6">{shelter.name} - Rooms</h1>
      </section>

      <section className="max-w-4xl mx-auto mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-12 px-4 pb-24">
        {rooms.map(room => (
          <div
            key={room.id}
            className="bg-gray-200 rounded-2xl p-4 text-black shadow-lg transform transition hover:shadow-2xl hover:-translate-y-1 cursor-pointer"
          >
            <div className="w-full h-40 bg-black rounded-2xl mb-4 overflow-hidden">
              <img
                src={room.image || "/src/assets/shelterRoom.jpg"}
                alt={room.name}
                className="w-full h-auto object-cover"
              />
            </div>
            <h3 className="text-2xl font-bold">{room.name}</h3>
            <p className="text-xl">Capacity: {room.capacity}</p>
            {room.type && <p className="text-xl">Type: {room.type}</p>}
          </div>
        ))}
      </section>
    </div>
  );
}
