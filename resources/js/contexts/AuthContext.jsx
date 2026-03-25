import React, { createContext, useContext, useState, useEffect, useCallback, useMemo } from 'react';
import api, { setTokens, clearTokens } from '../services/api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    const fetchUser = useCallback(async () => {
        const token = localStorage.getItem('access_token');
        if (!token) {
            setLoading(false);
            return;
        }

        try {
            const { data } = await api.get('/auth/me');
            setUser(data.data);
        } catch {
            clearTokens();
            setUser(null);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchUser();
    }, [fetchUser]);

    const login = useCallback(async (email, password) => {
        const { data } = await api.post('/auth/login', { email, password });
        setTokens(data.data.access_token, data.data.refresh_token);
        setUser(data.data.user);
        return data;
    }, []);

    const register = useCallback(async (name, email, password, password_confirmation) => {
        const { data } = await api.post('/auth/register', {
            name, email, password, password_confirmation,
        });
        return data;
    }, []);

    const logout = useCallback(async () => {
        try {
            await api.post('/auth/logout');
        } finally {
            clearTokens();
            setUser(null);
        }
    }, []);

    const value = useMemo(
        () => ({ user, loading, login, register, logout }),
        [user, loading, login, register, logout],
    );

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
}
