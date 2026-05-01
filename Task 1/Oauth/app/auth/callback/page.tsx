'use client';

import { useEffect } from 'react';
import { supabase } from '@/lib/supabaseClient';
import { useRouter } from 'next/navigation';

export default function AuthCallback() {
    const router = useRouter();

    useEffect(() => {
        const handleAuth = async () => {
            const { data, error } = await supabase.auth.getSession();
            if (data?.session) {
                router.push('/quiz');
            } else {
                router.push('/');
            }
        };

        handleAuth();
    }, [router]);

    return (
        <div className="min-h-screen flex items-center justify-center">
            <div className="loader border-pink-500 border-l-transparent w-12 h-12" />
        </div>
    );
}
