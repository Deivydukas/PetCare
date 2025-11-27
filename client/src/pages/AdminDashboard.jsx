import { useState } from "react";

export default function AdminDashboard() {
  const [sidebarOpen, setSidebarOpen] = useState(true);

  return (
    <div className="flex h-screen bg-gray-100">
      {/* Sidebar */}
      <aside
        className={`bg-gray-800 text-white w-64 p-4 space-y-6 transition-transform duration-300 ${
          sidebarOpen ? "translate-x-0" : "-translate-x-full"
        } md:translate-x-0 fixed md:relative h-full`}
      >
        <div className="text-2xl font-bold mb-8">Admin Panel</div>
        <nav className="flex flex-col gap-4">
          <a href="/" className="hover:bg-gray-700 px-3 py-2 rounded">Dashboard</a>
          <a href="/users" className="hover:bg-gray-700 px-3 py-2 rounded">Users</a>
          <a href="/shelters" className="hover:bg-gray-700 px-3 py-2 rounded">Shelters</a>
          <a href="/pets" className="hover:bg-gray-700 px-3 py-2 rounded">Pets</a>
          <a href="/adoptions" className="hover:bg-gray-700 px-3 py-2 rounded">Adoptions</a>
          <a href="/logout" className="hover:bg-gray-700 px-3 py-2 rounded">Logout</a>
        </nav>
      </aside>

      {/* Main content */}
      <div className="flex-1 flex flex-col md:ml-64">
        {/* Header */}
        <header className="bg-white shadow flex items-center justify-between px-6 py-4">
          <button
            className="md:hidden text-gray-600 hover:text-gray-900"
            onClick={() => setSidebarOpen(!sidebarOpen)}
          >
            ☰
          </button>
          <h1 className="text-2xl font-semibold">Dashboard</h1>
          <div className="flex items-center gap-4">
            <span>Admin</span>
            <img
              src="https://via.placeholder.com/32"
              alt="avatar"
              className="w-8 h-8 rounded-full"
            />
          </div>
        </header>

        {/* Dashboard content */}
        <main className="flex-1 p-6 overflow-auto">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {/* Example cards */}
            <div className="bg-white p-6 rounded-xl shadow flex flex-col">
              <h2 className="text-xl font-bold mb-2">Total Users</h2>
              <p className="text-gray-700 text-3xl">120</p>
            </div>
            <div className="bg-white p-6 rounded-xl shadow flex flex-col">
              <h2 className="text-xl font-bold mb-2">Total Shelters</h2>
              <p className="text-gray-700 text-3xl">8</p>
            </div>
            <div className="bg-white p-6 rounded-xl shadow flex flex-col">
              <h2 className="text-xl font-bold mb-2">Pending Adoptions</h2>
              <p className="text-gray-700 text-3xl">5</p>
            </div>
          </div>

          {/* Placeholder for more sections */}
          <div className="mt-8 bg-white p-6 rounded-xl shadow">
            <h2 className="text-xl font-bold mb-4">Recent Activity</h2>
            <p className="text-gray-600">No recent activity to display.</p>
          </div>
        </main>
      </div>
    </div>
  );
}
