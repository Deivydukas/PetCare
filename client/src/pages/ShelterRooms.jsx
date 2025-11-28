import { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";
import { FaTimes } from "react-icons/fa";

export default function ShelterRooms() {
  const { id } = useParams();
  const { user, token } = useAuth();
  const navigate = useNavigate();

  const [rooms, setRooms] = useState([]);
  const [shelter, setShelter] = useState(null);
  const [loading, setLoading] = useState(true);
  const [selectedRoom, setSelectedRoom] = useState(null);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchData = async () => {
      try {
        const shelterRes = await fetch(`http://localhost:8000/api/shelters/${id}`);
        const shelterData = await shelterRes.json();
        setShelter(shelterData);

        const roomsRes = await fetch(`http://localhost:8000/api/shelters/${id}/rooms`);
        const roomsData = await roomsRes.json();
        setRooms(roomsData.rooms || []);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [id]);

  if (loading) return <p className="text-white text-center mt-20">Loading...</p>;
  if (!shelter) return <p className="text-white text-center mt-20">Shelter not found</p>;

  const handleRoomClick = (room) => {
    if (user?.role === "admin") {
      setSelectedRoom(room);
    } else {
      navigate(`/rooms/${room.id}/pets`);
    }
  };

  const handleChange = (e) => {
    setSelectedRoom({ ...selectedRoom, [e.target.name]: e.target.value });
  };

  const handleSave = async () => {
    setSaving(true);
    setError("");
    try {
      const payload = {
        name: selectedRoom.name,
        capacity: selectedRoom.capacity,
        type: selectedRoom.type,
      };

      await fetch(`http://localhost:8000/api/rooms/${selectedRoom.id}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(payload),
      });

      setRooms(rooms.map(r => (r.id === selectedRoom.id ? { ...r, ...payload } : r)));
      setSelectedRoom(null);
    } catch (err) {
      console.error("Failed to save room:", err);
      setError("Failed to save room");
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="min-h-screen py-5 bg-gradient-to-br from-blue-900 to-blue-400 text-white">
      <section className="max-w-4xl mx-auto mt-6 px-4">
        <h1 className="text-4xl font-bold mb-6">{shelter.name} - Rooms</h1>
      </section>

      <section className="max-w-4xl mx-auto mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-12 px-4 pb-24">
        {rooms.map(room => (
          <div
            key={room.id}
            onClick={() => handleRoomClick(room)}
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

      {/* Room Modal */}
      {selectedRoom && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
          onClick={() => setSelectedRoom(null)}
        >
          <div
            className="bg-white rounded-xl max-w-md w-full p-6 relative text-black"
            onClick={(e) => e.stopPropagation()}
          >
            <button
              className="absolute top-3 right-3 text-gray-600 text-xl"
              onClick={() => setSelectedRoom(null)}
            >
              <FaTimes />
            </button>

            <h2 className="text-2xl font-bold mb-4">Edit Room</h2>
            {error && <p className="text-red-600 mb-2">{error}</p>}

            <div className="flex flex-col gap-4">
              <input
                type="text"
                name="name"
                value={selectedRoom.name}
                onChange={handleChange}
                className="border px-3 py-2 rounded"
                placeholder="Name"
              />
              <input
                type="number"
                name="capacity"
                value={selectedRoom.capacity}
                onChange={handleChange}
                className="border px-3 py-2 rounded"
                placeholder="Capacity"
              />
              <input
                type="text"
                name="type"
                value={selectedRoom.type || ""}
                onChange={handleChange}
                className="border px-3 py-2 rounded"
                placeholder="Type"
              />

              <button
                onClick={handleSave}
                disabled={saving}
                className="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 disabled:opacity-50"
              >
                {saving ? "Saving..." : "Save Changes"}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
