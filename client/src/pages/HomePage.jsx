import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import shelter from "../assets/shelter.jpg";
import { FaPhone, FaEnvelope, FaMapMarkerAlt } from "react-icons/fa";

export default function Home() {
  const [shelters, setShelters] = useState([]);

    useEffect(() => {
    fetch("http://localhost:8000/api/shelters")
        .then(res => res.json())
        .then(data => setShelters(data));
    }, []);



  return (
    <div className="min-h-screen py-5 bg-gradient-to-br from-blue-900 to-blue-400 text-white">
      {/* SEARCH SECTION
      <section className="max-w-4xl mx-auto mt-6 px-4">
        <div className="font-quicksand flex gap-3">
          <input
            placeholder="Search shelter…"
            className="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-xl text-xl outline-none"
          />
          <button className="bg-gray-200 text-black px-6 py-3 rounded-xl shadow text-xl hover:bg-gray-300 transition">
            Search
          </button>
        </div>
      </section> */}

      {/* SHELTER GRID */}
      <section className="font-quicksand max-w-4xl mx-auto mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-12 px-4 pb-24">
        {shelters.map(s => (
          <Link key ={s.id} to={`/shelters/${s.id}`}>
          <div key={s.id} className="bg-gray-200 rounded-2xl p-4 text-black shadow-lg transform transition hover:shadow-2xl hover:-translate-y-1">
            <div className="w-full h-60 bg-black rounded-2xl mb-4 overflow-hidden">
              <img
                src={shelter}
                alt={s.name}
                className="w-full h-auto object-cover"
              />
            </div>
            <h3 className="text-2xl font-bold">{s.name}</h3>
            {/* PHONE */}
        <div className="flex items-center gap-2 text-xl">
          <FaPhone className="text-blue-600 w-5 h-5" />
          <span>{s.phone}</span>
        </div>

        {/* EMAIL */}
        <div className="flex items-center gap-2 text-xl">
          <FaEnvelope className="text-blue-600 w-5 h-5" />
          <span>{s.email}</span>
        </div>

        {/* ADDRESS */}
        <div className="flex items-center gap-2 text-xl">
          <FaMapMarkerAlt className="text-blue-600 w-5 h-5" />
          <span>{s.address}</span>
        </div>
          </div>
          </Link>
        ))}
      </section>
    </div>
  );
}
