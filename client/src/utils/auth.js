export function getUser() {
  return {
    token: localStorage.getItem("access_token"),
    role: localStorage.getItem("role"),
    email: localStorage.getItem("email"),
    name: localStorage.getItem("name"),
  };
}
