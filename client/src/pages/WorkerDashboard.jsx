// src/pages/WorkerDashboard.jsx
import { useEffect, useState } from "react";

export default function WorkerDashboard() {
  const [shelters, setShelters] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const API_URL = "http://127.0.0.1:8000/api"; // Adjust if needed

  useEffect(() => {
    // Fetch shelters assigned to this worker
    const token = localStorage.getItem("token"); // JWT
    fetch(`${API_URL}/shelters`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
      .then((res) => {
        if (!res.ok) throw new Error("Failed to fetch shelters");
        return res.json();
      })
      .then((data) => setShelters(data))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="min-h-screen bg-gray-100">
      <header className="bg-blue-600 text-white p-4 text-center font-bold text-2xl">
        Worker Dashboard
      </header>

      <main className="p-4 container mx-auto">
        {loading && <p>Loading shelters...</p>}
        {error && <p className="text-red-500">{error}</p>}
        {!loading && !error && shelters.length === 0 && (
          <p>No shelters assigned to you.</p>
        )}

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
          {shelters.map((shelter) => (
            <div
              key={shelter.id}
              className="bg-white p-4 rounded-xl shadow hover:shadow-lg transition"
            >
              <h2 className="text-xl font-bold mb-2">{shelter.name}</h2>
              <p>📍 {shelter.location}</p>
              <p>📞 {shelter.phone}</p>
              <button className="mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                View Details
              </button>
            </div>
          ))}
        </div>
      </main>
    </div>
  );
}
