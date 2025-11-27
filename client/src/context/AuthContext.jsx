// src/context/AuthContext.jsx
import { createContext, useContext, useState, useEffect } from "react";

export const AuthContext = createContext();

export function useAuth() {
  return useContext(AuthContext);
}

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState("");
  const [loading, setLoading] = useState(true); // loading while checking refresh

  // On mount: refresh access token
  useEffect(() => {
    const refreshAccessToken = async () => {
      try {
        const res = await fetch("http://localhost:8000/api/refresh", {
          method: "POST",
          credentials: "include", // send refresh token cookie
        });

        const data = await res.json();

        if (res.ok && data.access_token) {
          setToken(data.access_token);

          // Fetch user info
          const meRes = await fetch("http://localhost:8000/api/me", {
            headers: {
              Authorization: `Bearer ${data.access_token}`
            },
              credentials: "include",
          });

          if (meRes.ok) {
            const meData = await meRes.json();
            setUser({ email: meData.email, role: meData.role, name: meData.name });
          } else {
            setUser(null);
            setToken("");
          }
        } else {
          setUser(null);
          setToken("");
        }
      } catch (err) {
        setUser(null);
        setToken("");
      } finally {
        setLoading(false);
      }
    };

    refreshAccessToken();
  }, []);

  // Login
  const login = async (email, password) => {
  try {
    const res = await fetch("http://localhost:8000/api/login", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify({ email, password }),
    });

    const data = await res.json();

    if (!res.ok) throw new Error(data.error || "Login failed");

    // Update context
    setToken(data.access_token);
    setUser({ email: data.email, role: data.role, name: data.name });

    return data;
  } catch (err) {
    throw err;
  }
};

  // Logout
  const logout = async () => {
    await fetch("http://localhost:8000/api/logout", {
      method: "POST",
      credentials: "include",
    });
    setUser(null);
    setToken("");
  };

  return (
    <AuthContext.Provider value={{ user, token, login, logout, loading }}>
      {children}
    </AuthContext.Provider>
  );
}
