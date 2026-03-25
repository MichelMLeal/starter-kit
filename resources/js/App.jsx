import React, { lazy, Suspense } from 'react';
import { Routes, Route } from 'react-router-dom';
import GuestLayout from './layouts/GuestLayout';
import AuthLayout from './layouts/AuthLayout';

const LoginPage = lazy(() => import('./pages/Auth/LoginPage'));
const RegisterPage = lazy(() => import('./pages/Auth/RegisterPage'));
const DashboardPage = lazy(() => import('./pages/DashboardPage'));

const PageLoader = () => (
    <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
    </div>
);

export default function App() {
    return (
        <Suspense fallback={<PageLoader />}>
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
        </Suspense>
    );
}
