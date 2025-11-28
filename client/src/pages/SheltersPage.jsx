import { useEffect, useState } from "react";
import axios from "axios";
import { useAuth } from "../context/AuthContext";
import { FaTimes } from "react-icons/fa";

export default function SheltersPage() {
  const { token } = useAuth();
  const [shelters, setShelters] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedShelter, setSelectedShelter] = useState(null);
  const [selectedRoom, setSelectedRoom] = useState(null);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");
  const [creating, setCreating] = useState(false);

  // Fetch shelters
  useEffect(() => {
    const fetchShelters = async () => {
      try {
        const res = await axios.get("http://localhost:8000/api/shelters", {
          headers: { Authorization: `Bearer ${token}` },
        });
        setShelters(res.data);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    };
    fetchShelters();
  }, [token]);

  if (loading) return <p className="text-center mt-20">Loading shelters...</p>;
  if (!shelters.length) return <p className="text-center mt-20">No shelters found.</p>;

  const handleShelterClick = async (shelter) => {
    try {
      const res = await axios.get(`http://localhost:8000/api/shelters/${shelter.id}/rooms`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      setSelectedShelter({ ...shelter, rooms: res.data.rooms });
      setSelectedRoom(null);
      setCreating(false);
    } catch (err) {
      console.error(err);
    }
  };

  const handleShelterSave = async () => {
    setSaving(true);
    setError("");
    try {
      const payload = {
        name: selectedShelter.name,
        phone: selectedShelter.phone,
        email: selectedShelter.email,
        address: selectedShelter.address,
      };

      let res;
      if (creating) {
        res = await axios.post("http://localhost:8000/api/shelters", payload, {
          headers: { Authorization: `Bearer ${token}` },
        });
        setShelters([...shelters, res.data.data]);
      } else {
        await axios.put(`http://localhost:8000/api/shelters/${selectedShelter.id}`, payload, {
          headers: { Authorization: `Bearer ${token}` },
        });
        setShelters(shelters.map(s => s.id === selectedShelter.id ? { ...s, ...payload } : s));
      }

      setSelectedShelter(null);
      setCreating(false);
    } catch (err) {
      console.error("Failed to save shelter:", err.response?.data || err);
      setError(err.response?.data?.message || "Failed to save shelter");
    } finally {
      setSaving(false);
    }
  };

  const handleShelterDelete = async () => {
    if (!confirm(`Are you sure you want to delete ${selectedShelter.name}?`)) return;
    try {
      await axios.delete(`http://localhost:8000/api/shelters/${selectedShelter.id}`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      setShelters(shelters.filter(s => s.id !== selectedShelter.id));
      setSelectedShelter(null);
      setCreating(false);
    } catch (err) {
      console.error("Failed to delete shelter:", err.response || err);
      alert("Failed to delete shelter.");
    }
  };

  const handleCreateShelter = () => {
    setSelectedShelter({ name: "", phone: "", email: "", address: "", rooms: [] });
    setCreating(true);
  };

  const handleRoomChange = (e) => {
    setSelectedRoom({ ...selectedRoom, [e.target.name]: e.target.value });
  };

  const handleRoomSave = async () => {
    setSaving(true);
    setError("");
    try {
      const payload = {
        name: selectedRoom.name,
        type: selectedRoom.type,
        capacity: selectedRoom.capacity,
      };

      await axios.put(`http://localhost:8000/api/rooms/${selectedRoom.id}`, payload, {
        headers: { Authorization: `Bearer ${token}` },
      });

      const updatedRooms = selectedShelter.rooms.map(r =>
        r.id === selectedRoom.id ? { ...r, ...payload } : r
      );
      setSelectedShelter({ ...selectedShelter, rooms: updatedRooms });
      setShelters(shelters.map(s => s.id === selectedShelter.id ? { ...s, rooms: updatedRooms } : s));
      setSelectedRoom(null);
    } catch (err) {
      console.error("Failed to save room:", err.response?.data || err);
      setError(err.response?.data?.message || "Failed to save room");
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="min-h-screen py-5 bg-gradient-to-br from-blue-900 to-blue-400 text-white">
      <section className="max-w-5xl mx-auto px-4">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-4xl font-bold">Shelters</h1>
          <button
            onClick={handleCreateShelter}
            className="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700"
          >
            Create Shelter
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {shelters.map(s => (
            <div
              key={s.id}
              onClick={() => handleShelterClick(s)}
              className="bg-gray-200 text-black p-4 rounded-xl shadow-lg transform transition hover:shadow-2xl hover:-translate-y-1 cursor-pointer"
            >
              <h3 className="text-xl font-bold">{s.name}</h3>
              <p>Phone: {s.phone}</p>
              <p>Email: {s.email}</p>
              <p>Address: {s.address}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Shelter Modal */}
      {selectedShelter && (
        <div
          className="fixed inset-0 z-50 flex items-start justify-center bg-black/50 overflow-auto pt-10 pb-10"
          onClick={() => { setSelectedShelter(null); setSelectedRoom(null); }}
        >
          <div
            className="bg-white rounded-xl max-w-3xl w-full p-6 relative text-black"
            onClick={(e) => e.stopPropagation()}
          >
            <button
              className="absolute top-3 right-3 text-gray-600 text-xl"
              onClick={() => { setSelectedShelter(null); setSelectedRoom(null); }}
            >
              <FaTimes />
            </button>

            <h2 className="text-2xl font-bold mb-4">
              {creating ? "Create Shelter" : "Edit Shelter"}
            </h2>

            {error && <p className="text-red-600 mb-2">{error}</p>}

            <div className="flex flex-col gap-4 mb-4">
              <input
                type="text"
                name="name"
                value={selectedShelter.name}
                onChange={(e) => setSelectedShelter({ ...selectedShelter, name: e.target.value })}
                className="border px-3 py-2 rounded"
                placeholder="Name"
              />
              <input
                type="text"
                name="phone"
                value={selectedShelter.phone}
                onChange={(e) => setSelectedShelter({ ...selectedShelter, phone: e.target.value })}
                className="border px-3 py-2 rounded"
                placeholder="Phone"
              />
              <input
                type="email"
                name="email"
                value={selectedShelter.email}
                onChange={(e) => setSelectedShelter({ ...selectedShelter, email: e.target.value })}
                className="border px-3 py-2 rounded"
                placeholder="Email"
              />
              <input
                type="text"
                name="address"
                value={selectedShelter.address}
                onChange={(e) => setSelectedShelter({ ...selectedShelter, address: e.target.value })}
                className="border px-3 py-2 rounded"
                placeholder="Address"
              />
              <div className="flex justify-between gap-2 mt-2">
                <button
                  onClick={handleShelterSave}
                  disabled={saving}
                  className="flex-1 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 disabled:opacity-50"
                >
                  {saving ? "Saving..." : creating ? "Create Shelter" : "Save Shelter Changes"}
                </button>

                {!creating && (
                  <button
                    onClick={handleShelterDelete}
                    className="flex-1 bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700"
                  >
                    Delete Shelter
                  </button>
                )}
              </div>
            </div>

            <h3 className="text-xl font-bold mb-2">Rooms</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              {selectedShelter.rooms?.map(room => (
                <div
                  key={room.id}
                  onClick={() => setSelectedRoom(room)}
                  className="bg-gray-100 p-3 rounded-xl text-black cursor-pointer hover:shadow-lg transform hover:-translate-y-1 transition"
                >
                  <p className="font-bold">{room.name}</p>
                  <p>Type: {room.type}</p>
                  <p>Capacity: {room.capacity}</p>
                </div>
              ))}
            </div>

            {/* Room Modal */}
            {selectedRoom && (
              <div className="bg-white rounded-xl p-6 mt-4 text-black border shadow-lg">
                <h4 className="text-xl font-bold mb-3">Edit Room</h4>
                <div className="flex flex-col gap-3">
                  <input
                    type="text"
                    name="name"
                    value={selectedRoom.name}
                    onChange={handleRoomChange}
                    className="border px-3 py-2 rounded"
                    placeholder="Room Name"
                  />
                  <input
                    type="text"
                    name="type"
                    value={selectedRoom.type}
                    onChange={handleRoomChange}
                    className="border px-3 py-2 rounded"
                    placeholder="Type"
                  />
                  <input
                    type="number"
                    name="capacity"
                    value={selectedRoom.capacity}
                    onChange={handleRoomChange}
                    className="border px-3 py-2 rounded"
                    placeholder="Capacity"
                  />
                  <button
                    onClick={handleRoomSave}
                    disabled={saving}
                    className="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 disabled:opacity-50"
                  >
                    {saving ? "Saving..." : "Save Room Changes"}
                  </button>
                </div>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
