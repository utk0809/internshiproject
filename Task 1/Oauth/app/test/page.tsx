'use client';

import { useState, useEffect } from 'react';
import { supabase } from '@/lib/supabaseClient';

export default function TestSupabasePage() {
    const [status, setStatus] = useState<string>('Testing...');
    const [details, setDetails] = useState<any>(null);

    useEffect(() => {
        const testConnection = async () => {
            try {
                // Test 1: Check if client is initialized
                console.log('Supabase client:', supabase);

                // Test 2: Try to get session (should be null if not logged in)
                const { data: sessionData, error: sessionError } = await supabase.auth.getSession();
                console.log('Session test:', { sessionData, sessionError });

                // Test 3: Check connection by attempting to sign up with test data
                const testEmail = `test-${Date.now()}@example.com`;
                const testPassword = 'test123456';

                console.log('Attempting signup with:', testEmail);

                const { data: signupData, error: signupError } = await supabase.auth.signUp({
                    email: testEmail,
                    password: testPassword,
                });

                console.log('Signup result:', { signupData, signupError });

                if (signupError) {
                    setStatus(`❌ Error: ${signupError.message}`);
                    setDetails(signupError);
                } else if (signupData.user) {
                    setStatus(`✅ SUCCESS! User created: ${signupData.user.id}`);
                    setDetails(signupData);
                } else {
                    setStatus('⚠️ No error but no user returned');
                    setDetails(signupData);
                }

            } catch (err: any) {
                console.error('Test error:', err);
                setStatus(`❌ Exception: ${err.message}`);
                setDetails(err);
            }
        };

        testConnection();
    }, []);

    return (
        <div className="min-h-screen p-8 bg-gray-50">
            <div className="max-w-4xl mx-auto">
                <h1 className="text-3xl font-bold mb-6">Supabase Connection Test</h1>

                <div className="bg-white p-6 rounded-lg shadow mb-6">
                    <h2 className="text-xl font-semibold mb-4">Status</h2>
                    <p className="text-lg">{status}</p>
                </div>

                {details && (
                    <div className="bg-white p-6 rounded-lg shadow">
                        <h2 className="text-xl font-semibold mb-4">Details</h2>
                        <pre className="bg-gray-100 p-4 rounded overflow-auto text-sm">
                            {JSON.stringify(details, null, 2)}
                        </pre>
                    </div>
                )}

                <div className="mt-6">
                    <a
                        href="/auth"
                        className="inline-block bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition"
                    >
                        Go to Auth Page
                    </a>
                </div>
            </div>
        </div>
    );
}
