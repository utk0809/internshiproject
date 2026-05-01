'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { Lock, Mail, ArrowRight, Shield } from 'lucide-react';

export default function AdminLogin() {
    const router = useRouter();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [credentials, setCredentials] = useState({ email: '', password: '' });

    // Simple admin credentials (in production, use proper authentication)
    const ADMIN_EMAIL = 'admin@steganography.com';
    const ADMIN_PASSWORD = 'Admin@123';

    const handleLogin = (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        // Simulate authentication delay
        setTimeout(() => {
            if (credentials.email === ADMIN_EMAIL && credentials.password === ADMIN_PASSWORD) {
                // Store admin session
                if (typeof window !== 'undefined') {
                    localStorage.setItem('adminAuth', 'true');
                    localStorage.setItem('adminEmail', credentials.email);
                }
                router.push('/admin/dashboard');
            } else {
                setError('Invalid credentials. Please try again.');
                setLoading(false);
            }
        }, 500);
    };

    return (
        <div className="min-h-screen flex flex-col items-center justify-center p-4 relative">
            <div className="max-w-md w-full glass-card p-8 space-y-8 relative z-10">
                <div className="text-center space-y-4">
                    <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 text-white mb-2">
                        <Shield className="w-8 h-8" />
                    </div>
                    <h1 className="text-4xl font-bold gradient-text">Admin Panel</h1>
                    <p className="text-gray-500">
                        Secure access to test results and analytics
                    </p>
                </div>

                {error && (
                    <div className="p-3 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm text-center">
                        {error}
                    </div>
                )}

                <form onSubmit={handleLogin} className="space-y-4">
                    <div className="relative group">
                        <Mail className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-purple-500 transition-colors" />
                        <input
                            type="email"
                            placeholder="Admin Email"
                            className="input-field pl-12"
                            required
                            value={credentials.email}
                            onChange={(e) => setCredentials({ ...credentials, email: e.target.value })}
                        />
                    </div>

                    <div className="relative group">
                        <Lock className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-pink-500 transition-colors" />
                        <input
                            type="password"
                            placeholder="Admin Password"
                            className="input-field pl-12"
                            required
                            value={credentials.password}
                            onChange={(e) => setCredentials({ ...credentials, password: e.target.value })}
                        />
                    </div>

                    <button type="submit" disabled={loading} className="btn-primary group w-full">
                        {loading ? (
                            <div className="flex items-center justify-center gap-2">
                                <div className="loader border-white/20 border-l-white w-5 h-5" />
                                <span>Authenticating...</span>
                            </div>
                        ) : (
                            <div className="flex items-center justify-center gap-2">
                                <span>Login to Dashboard</span>
                                <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                            </div>
                        )}
                    </button>
                </form>

                <div className="text-center">
                    <p className="text-xs text-gray-400">
                        Default credentials: admin@steganography.com / Admin@123
                    </p>
                </div>

                <div className="text-center">
                    <a href="/" className="text-sm text-purple-600 hover:text-purple-700 transition-colors">
                        ← Back to Home
                    </a>
                </div>
            </div>
        </div>
    );
}
