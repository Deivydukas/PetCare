import { useEffect, useState } from "react";
import { FaUsers, FaHome, FaPaw } from "react-icons/fa";
import { useAuth } from "../context/AuthContext";

export default function AdminDashboard() {
  const { user, token } = useAuth();
  const [stats, setStats] = useState({
    users: 0,
    shelters: 0,
    pendingAdoptions: 0,
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const res = await fetch("http://localhost:8000/api/admin/stats", {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        });
        if (!res.ok) throw new Error("Failed to fetch stats");
        const data = await res.json();
        setStats(data);
      } catch (err) {
        console.error(err);
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, [token]);

  if (loading) return <p className="text-center mt-20 text-white">Loading stats...</p>;
  if (!user || user.role !== "admin")
    return <p className="text-center mt-20 text-white">Access denied</p>;

  const statCards = [
    {
      title: "Total Users",
      value: stats.users,
      icon: <FaUsers className="text-blue-600 w-6 h-6" />,
      bg: "bg-gray-200",
    },
    {
      title: "Total Shelters",
      value: stats.shelters,
      icon: <FaHome className="text-blue-600 w-6 h-6" />,
      bg: "bg-gray-200",
    },
    {
      title: "Pending Adoptions",
      value: stats.pendingAdoptions,
      icon: <FaPaw className="text-blue-600 w-6 h-6" />,
      bg: "bg-gray-200",
    },
  ];

  return (
    <div className="min-h-screen py-5 bg-gradient-to-br from-blue-900 to-blue-400 text-white">
      <section className="max-w-4xl mx-auto mt-6 px-4">
        <h1 className="font-quicksand text-4xl font-bold mb-6">Admin Dashboard</h1>
      </section>

      <section className="font-quicksand max-w-4xl mx-auto mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12 px-4 pb-24">
        {statCards.map((card, idx) => (
          <div
            key={idx}
            className={`${card.bg} rounded-2xl p-6 text-black shadow-lg transform transition hover:shadow-2xl hover:-translate-y-1`}
          >
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-2xl font-bold">{card.title}</h3>
              {card.icon}
            </div>
            <p className="text-3xl font-bold">{card.value}</p>
          </div>
        ))}
      </section>
    </div>
  );
}
