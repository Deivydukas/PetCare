import { useState, useRef, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { FaUser, FaSignInAlt, FaCog, FaSignOutAlt, FaHome, FaPaw, FaBuilding, FaUsers, FaClipboardList } from "react-icons/fa";
import { useAuth } from "../context/AuthContext";
import logo from "../assets/PetCareLogo.jpg";

export default function Header() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const [open, setOpen] = useState(false);
  const dropdownRef = useRef(null);

  // close on outside click
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
        
        {/* Logo */}
        <Link to="/" className="flex items-center gap-3">
          <img src={logo} className="h-14 rounded-full" alt="PetCare Logo" />
          <h1 className="text-4xl font-quicksand text-gray-800">PetCare</h1>
        </Link>

        {/* Guest Links */}
        {!user && (
          <nav className="hidden md:flex gap-4 font-quicksand">
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

        {/* Logged In */}
        {user && (
          <div ref={dropdownRef} className="relative">
            <button
              onClick={() => setOpen(!open)}
              className="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-xl hover:bg-gray-200"
            >
              <FaUser />
              {user.email}
            </button>

            {/* DROPDOWN */}
            {open && (
              <div className="absolute right-0 mt-2 bg-white shadow-xl rounded-xl w-56 overflow-hidden">

                {/* ADMIN LINKS */}
                {user.role === "admin" && (
                  <>
                    <Link to="/admin" className="flex items-center gap-3 px-4 py-3 hover:bg-gray-100">
                      <FaHome />
                      Dashboard
                    </Link>

                    <Link to="/admin/users" className="flex items-center gap-3 px-4 py-3 hover:bg-gray-100">
                      <FaUsers />
                      Users
                    </Link>

                    <Link to="/admin/shelters" className="flex items-center gap-3 px-4 py-3 hover:bg-gray-100">
                      <FaBuilding />
                      Shelters
                    </Link>

                    <Link to="/admin/pets" className="flex items-center gap-3 px-4 py-3 hover:bg-gray-100">
                      <FaPaw />
                      Pets
                    </Link>

                    <Link to="/admin/adoptions" className="flex items-center gap-3 px-4 py-3 hover:bg-gray-100">
                      <FaClipboardList />
                      Adoptions
                    </Link>

                    <div className="border-t my-1" />
                  </>
                )}

                {/* SETTINGS + LOGOUT */}
                <Link
                  to="/profile"
                  className="flex items-center gap-3 px-4 py-3 hover:bg-gray-100"
                >
                  <FaCog />
                  Settings
                </Link>

                <button
                  onClick={handleLogout}
                  className="flex items-center gap-3 px-4 py-3 hover:bg-gray-100 w-full text-left"
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
