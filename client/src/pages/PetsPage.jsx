import { useEffect, useState } from "react";
import axios from "axios";
import { useAuth } from "../context/AuthContext";
import { FaTimes, FaPlus } from "react-icons/fa";

export default function PetsPage() {
  const { token } = useAuth();
  const [pets, setPets] = useState([]);
  const [shelters, setShelters] = useState([]);
  const [rooms, setRooms] = useState([]);
  const [selectedPet, setSelectedPet] = useState(null);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");
  const [showNewPetModal, setShowNewPetModal] = useState(false);

  const axiosConfig = { headers: { Authorization: `Bearer ${token}` } };

  const fetchPets = async () => {
    try {
      const res = await axios.get("http://localhost:8000/api/admin/pets", axiosConfig);
      setPets(res.data.pets || []);
    } catch (err) {
      console.error("Failed to fetch pets:", err.response || err);
    }
  };

  const fetchShelters = async () => {
    try {
      const res = await axios.get("http://localhost:8000/api/shelters", axiosConfig);
      setShelters(res.data);
    } catch (err) {
      console.error("Failed to fetch shelters:", err.response || err);
    }
  };

  const fetchRooms = async (shelterId) => {
    if (!shelterId) return setRooms([]);
    try {
      const res = await axios.get(`http://localhost:8000/api/shelters/${shelterId}/rooms`, axiosConfig);
      setRooms(Array.isArray(res.data) ? res.data : res.data.rooms || []);
    } catch (err) {
      console.error("Failed to fetch rooms:", err.response || err);
      setRooms([]);
    }
  };

  useEffect(() => {
    fetchPets();
    fetchShelters();
  }, [token]);

  const handleSelectPet = (pet) => {
    setSelectedPet({
      ...pet,
      shelter_id: pet.room?.shelter?.id || "",
      room_id: pet.room?.id || "",
    });
    if (pet.room?.shelter?.id) fetchRooms(pet.room.shelter.id);
  };

  const handleChange = (e) => setSelectedPet({ ...selectedPet, [e.target.name]: e.target.value });

  const handleSave = async () => {
    if (!selectedPet) return;
    setSaving(true);
    setError("");
    try {
      const payload = {
        name: selectedPet.name,
        species: selectedPet.species,
        breed: selectedPet.breed,
        age: selectedPet.age,
        status: selectedPet.status,
        room_id: selectedPet.room_id,
      };

      if (showNewPetModal) {
        const res = await axios.post("http://localhost:8000/api/pets", payload, axiosConfig);
        setPets([...pets, res.data.pet]);
        setShowNewPetModal(false);
      } else {
        const res = await axios.put(`http://localhost:8000/api/pets/${selectedPet.id}`, payload, axiosConfig);
        setPets(pets.map(p => (p.id === selectedPet.id ? res.data.pet : p)));
      }
      setSelectedPet(null);
    } catch (err) {
      console.error("Failed to save pet:", err.response || err);
      setError(err.response?.data?.message || "Failed to save pet");
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="min-h-screen py-5 bg-gradient-to-br from-blue-900 to-blue-400 text-white">
      <section className="max-w-5xl mx-auto px-4">
        <h1 className="text-xl font-bold mb-6 flex justify-between items-center">
          Pets
          <button
            className="bg-green-600 hover:bg-green-700 px-4 py-2 rounded flex items-center gap-2"
            onClick={() => {
              setSelectedPet({ name: "", species: "", breed: "", age: "", shelter_id: "", room_id: "" });
              setRooms([]);
              setShowNewPetModal(true);
            }}
          >
            <FaPlus /> New Pet
          </button>
        </h1>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {pets.map(p => (
            <div
              key={p.id}
              onClick={() => handleSelectPet(p)}
              className="bg-gray-200 text-black p-4 rounded-xl shadow-lg transform transition hover:shadow-2xl hover:-translate-y-1 cursor-pointer"
            >
              <h3 className="text-xl font-bold">{p.name}</h3>
              <p>Species: {p.species}</p>
              <p>Breed: {p.breed}</p>
              <p>Age: {p.age}</p>
              <p>Status: {p.status}</p>
              <p>Shelter: {p.room?.shelter?.name || "N/A"}</p>
              <p>Room: {p.room?.name || "N/A"}</p>
            </div>
          ))}
        </div>
      </section>

      {selectedPet && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50" onClick={() => { setSelectedPet(null); setShowNewPetModal(false); }}>
          <div className="bg-white rounded-xl max-w-md w-full p-6 relative text-black" onClick={(e) => e.stopPropagation()}>
            <button className="absolute top-3 right-3 text-gray-600 text-xl" onClick={() => { setSelectedPet(null); setShowNewPetModal(false); }}>
              <FaTimes />
            </button>

            <h2 className="text-2xl font-bold mb-4">{showNewPetModal ? "Create Pet" : "Edit Pet"}</h2>
            {error && <p className="text-red-600 mb-2">{error}</p>}

            <div className="flex flex-col gap-4">
              <input name="name" value={selectedPet.name} onChange={handleChange} placeholder="Name" className="border px-3 py-2 rounded" />
              <input name="species" value={selectedPet.species} onChange={handleChange} placeholder="Species" className="border px-3 py-2 rounded" />
              <input name="breed" value={selectedPet.breed} onChange={handleChange} placeholder="Breed" className="border px-3 py-2 rounded" />
              <input type="number" name="age" value={selectedPet.age} onChange={handleChange} placeholder="Age" className="border px-3 py-2 rounded" />
              <select name="status" value={selectedPet.status || "available"} onChange={handleChange} className="border px-3 py-2 rounded">
                <option value="available">Available</option>
                <option value="adopted">Adopted</option>
              </select>
              <select name="shelter_id" value={selectedPet.shelter_id || ""} onChange={e => { const id = e.target.value; setSelectedPet({ ...selectedPet, shelter_id: id, room_id: "" }); fetchRooms(id); }} className="border px-3 py-2 rounded">
                <option value="">Select Shelter</option>
                {shelters.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
              </select>
              <select name="room_id" value={selectedPet.room_id || ""} onChange={e => setSelectedPet({ ...selectedPet, room_id: e.target.value })} className="border px-3 py-2 rounded">
                <option value="">Select Room</option>
                {Array.isArray(rooms) && rooms.map(r => <option key={r.id} value={r.id}>{r.name}</option>)}
              </select>

              <button onClick={handleSave} disabled={saving} className="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 disabled:opacity-50">
                {saving ? "Saving..." : "Save"}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
