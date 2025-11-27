import Homepage from "./pages/Homepage";
import Header from "./components/Header";
import Footer from "./components/Footer";
import { Routes, Route } from "react-router-dom";
import Login from "./pages/Login";
import PrivateRoute from "./components/PrivateRoute";
import AdminDashboard from "./pages/AdminDashboard";
import WorkerDashboard from "./pages/WorkerDashboard";
import ShelterRooms from "./pages/ShelterRooms";
import Register from "./pages/Register";

export default function App() {
  return (
    <div className="min-h-screen flex flex-col font-sans">
      <Header />
      <main className="flex-1 container mx-auto px-4 py-6">
        <Routes>
          <Route path="/" element={<Homepage />} />
           <Route path="/login" element={<Login />} />
            <Route path="/admin" element={
              <PrivateRoute roleRequired="admin">
                <AdminDashboard />
              </PrivateRoute>
            } />
          <Route path="/worker" element={
            <PrivateRoute roleRequired="worker">
              <WorkerDashboard />
            </PrivateRoute>
          } />
           <Route path="/shelters/:id" element={<ShelterRooms />} />
           <Route path="/register" element={<Register />} />
        </Routes>
      </main>

      <Footer />
    </div>
  )
}




// import { useState } from 'react'
// import Homepage from './pages/Homepage'
// import Header from './components/Header'
// import Footer from './components/Footer'
// import { BrowserRouter, Route, Routes } from 'react-router-dom'
// import Login from './pages/Login'

// export default function App() {
//   return (
//     <BrowserRouter>
//       <div className="min-h-screen flex flex-col font-sans">
//         <Header />
//         <main className="flex-1 container mx-auto px-4 py-6">
//           <Routes>
//             <Route path="/" element={<Homepage />} />
//             <Route path="/login" element={<Login />} />
//           </Routes>
//         </main>
//         <Footer />
//       </div>
//     </BrowserRouter>
//   );
// }

// export default function App() {
//   return (
//     <div className="min-h-screen flex flex-col font-sans">
//       <Header />
//       <main className="flex-1 container mx-auto px-4 py-6">
//         <Homepage />
//       </main>
//       <Footer />
//     </div>
//   )
// }

// import { useEffect, useState } from 'react';
// import PetList from './components/PetList';

// function App() {
//   const [pets, setPets] = useState([]);
//   const [loading, setLoading] = useState(true);
//   const [error, setError] = useState(null);
//   const API_URL = 'http://127.0.0.1:8000/api'; // Laravel running via php artisan serve

//   useEffect(() => {
//     fetch(`${API_URL}/shelters/1/pets`)
//       .then((res) => {
//         if (!res.ok) throw new Error('Failed to fetch pets');
//         return res.json();
//       })
//       .then((data) => {
//         setPets(data.pets);
//       })
//       .catch((err) => setError(err.message))
//       .finally(() => setLoading(false));
//   }, []);

//   return (
//     <div className="min-h-screen bg-gray-100">

//       <header className="bg-blue-600 text-white p-4 text-center font-bold text-2xl">
//         PetCare Shelter
//       </header>

//       <main className="p-4">
//         {loading && <p>Loading pets...</p>}
//         {error && <p className="text-red-500">{error}</p>}
//         {!loading && !error && <PetList pets={pets} />}
//       </main>

//       <footer className="bg-gray-800 text-white p-4 text-center">
//         &copy; 2025 PetCare
//       </footer>
//     </div>
//   );
// }

// export default App;