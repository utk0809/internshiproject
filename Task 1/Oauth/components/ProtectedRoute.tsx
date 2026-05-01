'use client';

import { supabase } from '@/lib/supabaseClient';
import { useRouter } from 'next/navigation';
import { useEffect, useState } from 'react';

export default function ProtectedRoute({ children }: { children: React.ReactNode }) {
    const router = useRouter();
    const [loading, setLoading] = useState(true);
    const [authenticated, setAuthenticated] = useState(false);

    useEffect(() => {
        const checkUser = async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (!session) {
                router.push('/');
            } else {
                setAuthenticated(true);
            }
            setLoading(false);
        };

        checkUser();

        // Listen for auth changes (e.g. logout in another tab)
        const { data: { subscription } } = supabase.auth.onAuthStateChange((event, _session) => {
            if (event === 'SIGNED_OUT') {
                router.push('/');
            }
        });

        return () => {
            subscription.unsubscribe();
        };
    }, [router]);

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-white">
                <div className="loader w-10 h-10 !border-4 !border-pink-500" />
            </div>
        );
    }

    if (!authenticated) {
        return null; // Will redirect
    }

    return <>{children}</>;
}
