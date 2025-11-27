import { useState, useRef, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { FaUser, FaSignInAlt, FaCog, FaSignOutAlt } from "react-icons/fa";
import { useAuth } from "../context/AuthContext";
import logo from "../assets/PetCareLogo.jpg";

export default function Header() {
  const { user, logout, loading } = useAuth();
  const navigate = useNavigate();
  const [open, setOpen] = useState(false);
  const dropdownRef = useRef(null);

  // Close dropdown when clicking elsewhere
  useEffect(() => {
    const close = (e) => {
      if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
        setOpen(false);
      }
    };
    document.addEventListener("click", close);
    return () => document.removeEventListener("click", close);
  }, []);
  
  const handleLogout = async () => {
    await logout();
    navigate("/");
  };

  return (
    <header className="bg-white shadow">
      <div className="container mx-auto px-4 py-2 flex items-center justify-between">

        <Link to="/" className="flex items-center gap-3">
          <img src={logo} className="h-14 rounded-full" alt="PetCare Logo" />
          <h1 className="text-3xl font-bold text-gray-800">PetCare</h1>
        </Link>

        {/* Guest Links */}
        {!user && (
          <nav className="hidden md:flex gap-4">
            <Link className="flex items-center gap-2 font-semibold hover:text-blue-600" to="/login">
              <FaSignInAlt />
              Login
            </Link>
            <Link className="flex items-center gap-2 font-semibold hover:text-blue-600" to="/register">
              <FaUser />
              Register
            </Link>
          </nav>
        )}

        {/* Logged in */}
        {user && (
          <div ref={dropdownRef} className="relative">
            <button
              onClick={() => setOpen(!open)}
              className="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-xl hover:bg-gray-200"
            >
              <FaUser />
              {user.email}
            </button>

            {open && (
              <div className="absolute right-0 mt-2 bg-white shadow-lg rounded-xl w-48">
                <Link
                  to="/profile"
                  className="flex items-center gap-2 px-4 py-3 hover:bg-gray-100"
                >
                  <FaCog />
                  Settings
                </Link>

                <button
                  onClick={handleLogout}
                  className="flex items-center gap-2 px-4 py-3 hover:bg-gray-100 w-full text-left"
                >
                  <FaSignOutAlt />
                  Logout
                </button>
              </div>
            )}
          </div>
        )}
      </div>
    </header>
  );
}
