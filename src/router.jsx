import {createBrowserRouter} from "react-router-dom";
import Login from "./views/Login.jsx";
import SignUp from "./views/SignUp.jsx";
import Users from "./views/Users.jsx";
import NotFound from "./views/NotFound.jsx";
import DefaultLayout from "./components/DefaultLayout.jsx";
import GuestLayout from "./components/GuestLayout.jsx";
import Dashboard from "./views/Dashboard.jsx";

const router = createBrowserRouter([
    {path: "/", element: <><DefaultLayout/></>, children: [
        {path: "/users", element: <><Users/></>},
        {path: "/dashboard", element: <><Dashboard/></>},
    ]
    },
    {path: "/", element: <><GuestLayout/></>, children: [
        {path: "/login", element: <><Login/></>},
        {path: "/signUp", element: <><SignUp/></>},
    ]},

    
    {path: "*", element: <><NotFound/></>},
])

export default router;