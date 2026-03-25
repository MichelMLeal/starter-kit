import React from 'react';
import { useAuth } from '../contexts/AuthContext';

export default function DashboardPage() {
    const { user } = useAuth();

    return (
        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div className="p-6">
                <h2 className="text-2xl font-bold text-gray-900 mb-4">Dashboard</h2>
                <p className="text-gray-600">
                    Welcome, <span className="font-semibold">{user?.name}</span>! You're logged in.
                </p>
            </div>
        </div>
    );
}
