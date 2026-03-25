import React from 'react';
import { Routes, Route } from 'react-router-dom';
import LoginPage from './pages/Auth/LoginPage';
import RegisterPage from './pages/Auth/RegisterPage';
import DashboardPage from './pages/DashboardPage';
import GuestLayout from './layouts/GuestLayout';
import AuthLayout from './layouts/AuthLayout';

export default function App() {
    return (
        <Routes>
            <Route element={<GuestLayout />}>
                <Route path="/login" element={<LoginPage />} />
                <Route path="/register" element={<RegisterPage />} />
            </Route>
            <Route element={<AuthLayout />}>
                <Route path="/dashboard" element={<DashboardPage />} />
            </Route>
            <Route path="/" element={<LoginPage />} />
        </Routes>
    );
}
