'use client';

import { supabase } from '@/lib/supabaseClient';
import { useEffect, useState } from 'react';

export default function QuizPage() {
    const [user, setUser] = useState<{ firstName: string; lastName: string; email?: string; mobile?: string } | null>(null);

    useEffect(() => {
        const getUserDetails = async () => {
            // 1. Check for authenticated Supabase session (e.g. from Google Login)
            const { data: { user: supabaseUser } } = await supabase.auth.getUser();

            if (supabaseUser) {
                const fullName = supabaseUser.user_metadata?.full_name || '';
                const [first, ...rest] = fullName.split(' ');

                setUser({
                    firstName: first || 'Guest',
                    lastName: rest.join(' ') || '',
                    email: supabaseUser.email || ''
                });
                return;
            }

            // 2. Fallback to local storage (for the manual entry form)
            const stored = localStorage.getItem('quizUser');
            if (stored) {
                setUser(JSON.parse(stored));
            }
        };

        getUserDetails();
    }, []);

    return (
        <div className="min-h-screen flex flex-col items-center justify-center text-center p-4">
            <div className="glass-card p-10 max-w-lg w-full space-y-8">
                <div className="space-y-4">
                    <h1 className="text-4xl font-bold gradient-text">
                        Welcome to the Quiz{user ? `, ${user.firstName}!` : '!'}
                    </h1>
                    <p className="text-gray-500">
                        Get ready to test your knowledge, {user?.firstName} {user?.lastName}.
                    </p>
                    {user?.mobile && (
                        <p className="text-xs text-gray-400 font-mono tracking-widest bg-gray-50 py-1 px-3 rounded-full inline-block">
                            ID: {user.mobile}
                        </p>
                    )}
                </div>

                <div className="p-8 bg-gradient-to-br from-gray-50 to-white rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div className="relative z-10 text-gray-500">
                        Quiz session is starting soon...
                    </div>
                    <div className="absolute inset-0 bg-gradient-to-r from-pink-500/5 to-violet-600/5 opacity-0 group-hover:opacity-100 transition-opacity" />
                </div>
            </div>
        </div>
    );
}
