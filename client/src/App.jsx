import { useEffect, useState } from 'react';
import PetList from './components/PetList';

function App() {
  const [pets, setPets] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const API_URL = 'http://127.0.0.1:8000/api'; // Laravel running via php artisan serve

  useEffect(() => {
    fetch(`${API_URL}/shelters/1/pets`) // replace 1 with your shelter ID
      .then((res) => {
        if (!res.ok) throw new Error('Failed to fetch pets');
        return res.json();
      })
      .then((data) => {
        setPets(data.pets);
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="min-h-screen bg-gray-100">

      <header className="bg-blue-600 text-white p-4 text-center font-bold text-2xl">
        PetCare Shelter
      </header>

      <main className="p-4">
        {loading && <p>Loading pets...</p>}
        {error && <p className="text-red-500">{error}</p>}
        {!loading && !error && <PetList pets={pets} />}
      </main>

      <footer className="bg-gray-800 text-white p-4 text-center">
        &copy; 2025 PetCare
      </footer>
    </div>
  );
}

export default App;
