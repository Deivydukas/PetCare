// src/api.js
import axios from "axios";

const API = axios.create({
  baseURL: "http://localhost:8000/api",
  withCredentials: true,
});

API.interceptors.request.use((config) => {
  const token = localStorage.getItem("access_token");
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

API.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    if (error.response && error.response.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      try {
        const refreshResp = await axios.post("http://localhost:8000/api/refresh", {}, {
          headers: { "Authorization": `Bearer ${localStorage.getItem("access_token")}` }
        });

        const newToken = refreshResp.data.access_token;
        localStorage.setItem("access_token", newToken);
        originalRequest.headers["Authorization"] = `Bearer ${newToken}`;
        return axios(originalRequest);
      } catch (err) {
        // Redirect to login if refresh fails
        localStorage.removeItem("access_token");
        window.location.href = "/login";
        return Promise.reject(err);
      }
    }

    return Promise.reject(error);
  }
);


export default API;
