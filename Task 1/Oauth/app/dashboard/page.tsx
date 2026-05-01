'use client';

import ProtectedRoute from '@/components/ProtectedRoute';
import { supabase } from '@/lib/supabaseClient';
import { LogOut, User } from 'lucide-react';
import { useRouter } from 'next/navigation';
import { useEffect, useState } from 'react';

export default function Dashboard() {
    const router = useRouter();
    const [userEmail, setUserEmail] = useState<string | null>(null);

    useEffect(() => {
        const getUser = async () => {
            const { data: { user } } = await supabase.auth.getUser();
            setUserEmail(user?.email || 'User');
        };
        getUser();
    }, []);

    const handleLogout = async () => {
        await supabase.auth.signOut();
        router.push('/auth/login');
    };

    return (
        <ProtectedRoute>
            <div className="min-h-screen bg-gray-50/50">
                <nav className="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between sticky top-0 z-50 bg-white/80 backdrop-blur-md">
                    <div className="text-xl font-bold gradient-text">SecureApp</div>
                    <div className="flex items-center gap-4">
                        <div className="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100">
                            <User className="w-4 h-4 text-violet-600" />
                            <span>{userEmail}</span>
                        </div>
                        <button
                            onClick={handleLogout}
                            className="p-2 rounded-full hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors"
                            title="Logout"
                        >
                            <LogOut className="w-5 h-5" />
                        </button>
                    </div>
                </nav>

                <main className="max-w-6xl mx-auto p-8">
                    <div className="relative overflow-hidden rounded-3xl bg-gradient-to-r from-pink-500 to-violet-600 p-10 text-white shadow-2xl mb-12">
                        <div className="relative z-10">
                            <h1 className="text-4xl font-bold mb-4">Welcome Back!</h1>
                            <p className="text-pink-100 max-w-xl text-lg">
                                You are securely logged in. This dashboard is protected by Supabase Authentication. Example content below demonstrating the theme consistency.
                            </p>
                        </div>
                        {/* Abstract circles */}
                        <div className="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-white/10 rounded-full blur-3xl" />
                        <div className="absolute bottom-0 left-0 -ml-20 -mb-20 w-60 h-60 bg-black/10 rounded-full blur-3xl" />
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {[1, 2, 3].map((i) => (
                            <div key={i} className="glass-card p-6 hover:shadow-2xl transition-shadow duration-300">
                                <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-pink-500/10 to-violet-600/10 flex items-center justify-center mb-4">
                                    <span className="text-xl font-bold text-violet-600">{i}</span>
                                </div>
                                <h3 className="text-lg font-bold text-gray-800 mb-2">Feature Card {i}</h3>
                                <p className="text-gray-500">
                                    This is a placeholder for your application features. It inherits the clean glassmorphism style from the auth pages.
                                </p>
                            </div>
                        ))}
                    </div>
                </main>
            </div>
        </ProtectedRoute>
    );
}
