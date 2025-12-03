// src/pages/Profile.jsx
import { useEffect, useState } from "react";
import axios from "axios";
import { useAuth } from "../context/AuthContext";
import { FaUser, FaEnvelope, FaPaw, FaCheckCircle, FaTimesCircle, FaEdit } from "react-icons/fa";

export default function Profile() {
  const { user, token, setUser } = useAuth();
  const [adoptions, setAdoptions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [editMode, setEditMode] = useState(false);
  const [form, setForm] = useState({
    name: "",
    email: "",
    password: "",
    address: "",
  });
  const [updating, setUpdating] = useState(false);
  const [successMessage, setSuccessMessage] = useState("");

  const axiosConfig = { headers: { Authorization: `Bearer ${token}` } };

  // Fetch user adoptions
  useEffect(() => {
    const fetchAdoptions = async () => {
      try {
        const res = await axios.get("http://localhost:8000/api/adoptions", axiosConfig);
        setAdoptions(res.data.requests || []);
      } catch (err) {
        console.error("Failed to fetch adoptions:", err.response || err);
        setError("Failed to fetch your adoptions.");
      } finally {
        setLoading(false);
      }
    };

    if (user) {
      setForm({ name: user.name, email: user.email, password: "", address: user.address || "" });
      fetchAdoptions();
    }
  }, [token, user]);

  if (!user) return <p className="text-center mt-20 text-white">You need to log in to view your profile.</p>;

  // Handle form input changes
  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  // Submit updated profile
  const handleUpdate = async (e) => {
    e.preventDefault();
    setUpdating(true);
    setSuccessMessage("");
    setError("");

    try {
      const payload = { ...form };
      if (!payload.password) delete payload.password; // don't send empty password
      const res = await axios.put(`http://localhost:8000/api/users/${user.id}`, payload, axiosConfig);

      setUser(res.data.data); // update auth context
      setSuccessMessage("Profile updated successfully!");
      setEditMode(false);
    } catch (err) {
      console.error("Failed to update profile:", err.response?.data || err);
      setError(err.response?.data?.error || "Failed to update profile.");
    } finally {
      setUpdating(false);
    }
  };

  return (
    <div className="min-h-screen py-10 bg-gradient-to-br from-blue-800 to-blue-400 text-white">
      <section className="max-w-4xl mx-auto px-4">

        {/* Basic Info */}
        <div className="bg-white text-black rounded-xl p-6 shadow-lg mb-10">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-3xl font-bold flex items-center gap-3">
              <FaUser /> Profile
            </h2>
            <button
              className="flex items-center gap-2 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
              onClick={() => setEditMode(!editMode)}
            >
              <FaEdit /> {editMode ? "Cancel" : "Edit"}
            </button>
          </div>

          {!editMode ? (
            <>
              <p className="text-lg"><FaUser className="inline mr-2 text-blue-600" /> Name: <strong>{user.name}</strong></p>
              <p className="text-lg mt-2"><FaEnvelope className="inline mr-2 text-blue-600" /> Email: <strong>{user.email}</strong></p>
              <p className="text-lg mt-2">Address: <strong>{user.address || "N/A"}</strong></p>
              <p className="text-lg mt-2">Role: <strong>{user.role}</strong></p>
            </>
          ) : (
            <form onSubmit={handleUpdate} className="space-y-4">
              <div>
                <label className="block mb-1 font-semibold">Name</label>
                <input
                  type="text"
                  name="name"
                  value={form.name}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded border"
                  required
                />
              </div>

              <div>
                <label className="block mb-1 font-semibold">Email</label>
                <input
                  type="email"
                  name="email"
                  value={form.email}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded border"
                  required
                />
              </div>

              <div>
                <label className="block mb-1 font-semibold">Password (leave blank to keep)</label>
                <input
                  type="password"
                  name="password"
                  value={form.password}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded border"
                />
              </div>

              <div>
                <label className="block mb-1 font-semibold">Address</label>
                <input
                  type="text"
                  name="address"
                  value={form.address}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded border"
                />
              </div>

              <button
                type="submit"
                className="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600 text-white font-semibold"
                disabled={updating}
              >
                {updating ? "Updating..." : "Update Profile"}
              </button>

              {successMessage && <p className="text-green-600 mt-2">{successMessage}</p>}
              {error && <p className="text-red-600 mt-2">{error}</p>}
            </form>
          )}
        </div>

        {/* Adoption Requests */}
        <div className="bg-white text-black rounded-xl p-6 shadow-lg">
          <h2 className="text-3xl font-bold mb-6 flex items-center gap-3">
            <FaPaw /> Your Adoption Requests
          </h2>

          {loading && <p>Loading your adoptions...</p>}
          {error && !editMode && <p className="text-red-600">{error}</p>}
          {!loading && !adoptions.length && <p className="text-gray-700">You haven't submitted any adoption requests yet.</p>}

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {adoptions.map((adoption) => (
              <div key={adoption.id} className="border rounded-xl p-4 bg-gray-100 text-black shadow hover:shadow-lg transition">
                <h3 className="text-xl font-bold mb-2">{adoption.pet.name}</h3>
                <p>Species: {adoption.pet.species}</p>
                {adoption.pet.breed && <p>Breed: {adoption.pet.breed}</p>}
                <p>Status: 
                  {adoption.status === "approved" ? (
                    <span className="ml-2 text-green-600 font-semibold flex items-center gap-2">
                      <FaCheckCircle /> Approved
                    </span>
                  ) : adoption.status === "rejected" ? (
                    <span className="ml-2 text-red-600 font-semibold flex items-center gap-2">
                      <FaTimesCircle /> Rejected
                    </span>
                  ) : (
                    <span className="ml-2 text-yellow-600 font-semibold">Pending</span>
                  )}
                </p>
                {adoption.application_text && (
                  <p className="mt-2 italic">"{adoption.application_text}"</p>
                )}
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
