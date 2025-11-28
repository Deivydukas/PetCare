import { useEffect, useState } from "react";
import axios from "axios";
import { useAuth } from "../context/AuthContext";
import { FaTimes } from "react-icons/fa";

export default function UsersPage() {
  const { token } = useAuth();
  const [users, setUsers] = useState([]);
  const [shelters, setShelters] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedUser, setSelectedUser] = useState(null);
  const [creating, setCreating] = useState(false); 
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");

  // Fetch users
  useEffect(() => {
    const fetchUsers = async () => {
      try {
        const res = await axios.get("http://localhost:8000/api/users", {
          headers: { Authorization: `Bearer ${token}` },
        });
        setUsers(res.data);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    };
    fetchUsers();
  }, [token]);

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
      }
    };
    fetchShelters();
  }, [token]);

  if (loading) return <p className="text-center mt-20">Loading users...</p>;
  if (!users.length) return <p className="text-center mt-20">No users found.</p>;

  const handleChange = (e) => {
    setSelectedUser({ ...selectedUser, [e.target.name]: e.target.value });
  };

  const handleSave = async () => {
    setSaving(true);
    setError("");
    try {
      if (creating) {
        const payload = {
          name: selectedUser.name,
          email: selectedUser.email,
          password: selectedUser.password,
          role: selectedUser.role,
        };
        if (selectedUser.role === "worker") payload.shelter_id = selectedUser.shelter_id;

        const res = await axios.post(
          "http://localhost:8000/api/users",
          payload,
          { headers: { Authorization: `Bearer ${token}` } }
        );

        setUsers([...users, res.data.data]); 
      } else {
        const payload = {
          name: selectedUser.name,
          email: selectedUser.email,
          role: selectedUser.role,
        };
        if (selectedUser.role === "worker") payload.shelter_id = selectedUser.shelter_id;

        await axios.put(
          `http://localhost:8000/api/users/${selectedUser.id}`,
          payload,
          { headers: { Authorization: `Bearer ${token}` } }
        );

        setUsers(users.map(u => u.id === selectedUser.id ? { ...u, ...payload } : u));
      }

      setSelectedUser(null);
      setCreating(false);
    } catch (err) {
      console.error("Failed to save user:", err.response || err);
      setError(
        err.response?.data?.error || 
        err.response?.data?.message || 
        "Failed to save user"
      );
    } finally {
      setSaving(false);
    }
  };

  const handleCreateUser = () => {
    setSelectedUser({ name: "", email: "", password: "", role: "user", shelter_id: "" });
    setCreating(true);
  };

  const handleDeleteUser = async () => {
    if (!confirm(`Are you sure you want to delete ${selectedUser.name}?`)) return;
    try {
      await axios.delete(`http://localhost:8000/api/users/${selectedUser.id}`, {
        headers: { Authorization: `Bearer ${token}` },
      });
      setUsers(users.filter(u => u.id !== selectedUser.id));
      setSelectedUser(null);
      setCreating(false);
    } catch (err) {
      console.error("Failed to delete user:", err.response || err);
      alert("Failed to delete user.");
    }
  };

  return (
    <div className="min-h-screen py-5 bg-gradient-to-br from-blue-900 to-blue-400 text-white">
      <section className="max-w-5xl mx-auto px-4">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-4xl font-bold">Users</h1>
          <button
            onClick={handleCreateUser}
            className="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700"
          >
            Create User
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {users.map(u => (
            <div
              key={u.id}
              onClick={() => { setSelectedUser(u); setCreating(false); }}
              className="bg-gray-200 text-black p-4 rounded-xl shadow-lg transform transition hover:shadow-2xl hover:-translate-y-1 cursor-pointer"
            >
              <h3 className="text-xl font-bold">{u.name}</h3>
              <p>Email: {u.email}</p>
              <p>Role: {u.role}</p>
              <p>Shelter ID: {u.shelter_id || "N/A"}</p>
            </div>
          ))}
        </div>
      </section>

      {/* User Modal */}
      {selectedUser && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
          onClick={() => setSelectedUser(null)}
        >
          <div
            className="bg-white rounded-xl max-w-md w-full p-6 relative text-black"
            onClick={(e) => e.stopPropagation()}
          >
            <button
              className="absolute top-3 right-3 text-gray-600 text-xl"
              onClick={() => setSelectedUser(null)}
            >
              <FaTimes />
            </button>

            <h2 className="text-2xl font-bold mb-4">
              {creating ? "Create User" : "Edit User"}
            </h2>

            {error && <p className="text-red-600 mb-2">{error}</p>}

            <div className="flex flex-col gap-4">
              <input
                type="text"
                name="name"
                value={selectedUser.name}
                onChange={handleChange}
                className="border px-3 py-2 rounded"
                placeholder="Name"
              />
              <input
                type="email"
                name="email"
                value={selectedUser.email}
                onChange={handleChange}
                className="border px-3 py-2 rounded"
                placeholder="Email"
              />
              {creating && (
                <input
                  type="password"
                  name="password"
                  value={selectedUser.password || ""}
                  onChange={handleChange}
                  className="border px-3 py-2 rounded"
                  placeholder="Password"
                />
              )}
              <select
                name="role"
                value={selectedUser.role}
                onChange={handleChange}
                className="border px-3 py-2 rounded"
              >
                <option value="user">User</option>
                <option value="worker">Worker</option>
                <option value="admin">Admin</option>
              </select>

              {selectedUser.role === "worker" && (
                <select
                  name="shelter_id"
                  value={selectedUser.shelter_id || ""}
                  onChange={handleChange}
                  className="border px-3 py-2 rounded"
                >
                  <option value="">Select Shelter</option>
                  {shelters.map(s => (
                    <option key={s.id} value={s.id}>{s.name}</option>
                  ))}
                </select>
              )}

              <div className="flex justify-between gap-2 mt-2">
                <button
                  onClick={handleSave}
                  disabled={saving}
                  className="flex-1 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 disabled:opacity-50"
                >
                  {saving ? "Saving..." : creating ? "Create User" : "Save Changes"}
                </button>

                {!creating && (
                  <button
                    onClick={handleDeleteUser}
                    className="flex-1 bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700"
                  >
                    Delete User
                  </button>
                )}
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
