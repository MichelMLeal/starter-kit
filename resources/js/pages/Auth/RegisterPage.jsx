import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

export default function RegisterPage() {
    const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '' });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const { register } = useAuth();
    const navigate = useNavigate();

    const handleChange = (e) => setForm({ ...form, [e.target.name]: e.target.value });

    const handleSubmit = async (e) => {
        e.preventDefault();
        setErrors({});
        setLoading(true);

        try {
            await register(form.name, form.email, form.password, form.password_confirmation);
            navigate('/login');
        } catch (err) {
            if (err.response?.data?.errors) {
                setErrors(err.response.data.errors);
            } else {
                setErrors({ general: [err.response?.data?.message || 'Registration failed.'] });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 className="text-center text-3xl font-bold text-gray-900 mb-8">Create account</h2>
            <div className="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                {errors.general && (
                    <div className="mb-4 p-3 bg-red-50 text-red-700 rounded-md text-sm">{errors.general[0]}</div>
                )}
                <form onSubmit={handleSubmit} className="space-y-6">
                    {['name', 'email', 'password', 'password_confirmation'].map((field) => (
                        <div key={field}>
                            <label htmlFor={field} className="block text-sm font-medium text-gray-700 capitalize">
                                {field.replace('_', ' ')}
                            </label>
                            <input
                                id={field}
                                name={field}
                                type={field.includes('password') ? 'password' : field === 'email' ? 'email' : 'text'}
                                value={form[field]}
                                onChange={handleChange}
                                required
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 border"
                            />
                            {errors[field] && (
                                <p className="mt-1 text-sm text-red-600">{errors[field][0]}</p>
                            )}
                        </div>
                    ))}
                    <button
                        type="submit"
                        disabled={loading}
                        className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        {loading ? 'Creating...' : 'Create account'}
                    </button>
                </form>
                <p className="mt-6 text-center text-sm text-gray-600">
                    Already have an account?{' '}
                    <Link to="/login" className="font-medium text-indigo-600 hover:text-indigo-500">Sign in</Link>
                </p>
            </div>
        </div>
    );
}
