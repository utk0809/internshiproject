'use client';

import { useEffect, useState } from 'react';

export default function VerifyEnvPage() {
    const [results, setResults] = useState<any>({});

    useEffect(() => {
        const verify = async () => {
            const url = process.env.NEXT_PUBLIC_SUPABASE_URL;
            const key = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY;

            const checks = {
                urlExists: !!url,
                urlFormat: url?.startsWith('https://') && url?.includes('.supabase.co'),
                urlValue: url,
                keyExists: !!key,
                keyLength: key?.length || 0,
                keyFormat: key?.startsWith('eyJ'),
                keyPrefix: key?.substring(0, 20) + '...',
                expectedKeyFormat: 'Should start with "eyJ" and be ~300+ characters',
                currentKeyFormat: key?.startsWith('sb_publishable') ? '❌ WRONG - This is a publishable key, not anon key' : key?.startsWith('eyJ') ? '✅ CORRECT JWT format' : '❌ UNKNOWN format'
            };

            // Try to import and test Supabase client
            try {
                const { supabase } = await import('@/lib/supabaseClient');

                // Test connection
                const { data, error } = await supabase.auth.getSession();

                checks.supabaseClientCreated = true;
                checks.connectionTest = error ? `❌ Error: ${error.message}` : '✅ Connection successful';
                checks.sessionData = data;
            } catch (err: any) {
                checks.supabaseClientCreated = false;
                checks.connectionError = err.message;
            }

            setResults(checks);
        };

        verify();
    }, []);

    return (
        <div className="min-h-screen p-8 bg-gray-50">
            <div className="max-w-4xl mx-auto space-y-6">
                <div className="bg-white p-6 rounded-lg shadow">
                    <h1 className="text-3xl font-bold mb-4">🔍 Environment Variables Verification</h1>

                    <div className="space-y-4">
                        <div className="border-b pb-4">
                            <h2 className="text-xl font-semibold mb-2">NEXT_PUBLIC_SUPABASE_URL</h2>
                            <div className="space-y-1 text-sm">
                                <p>✓ Exists: <span className="font-mono">{results.urlExists ? '✅ Yes' : '❌ No'}</span></p>
                                <p>✓ Format: <span className="font-mono">{results.urlFormat ? '✅ Valid' : '❌ Invalid'}</span></p>
                                <p>✓ Value: <span className="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{results.urlValue}</span></p>
                            </div>
                        </div>

                        <div className="border-b pb-4">
                            <h2 className="text-xl font-semibold mb-2">NEXT_PUBLIC_SUPABASE_ANON_KEY</h2>
                            <div className="space-y-1 text-sm">
                                <p>✓ Exists: <span className="font-mono">{results.keyExists ? '✅ Yes' : '❌ No'}</span></p>
                                <p>✓ Length: <span className="font-mono">{results.keyLength} characters {results.keyLength > 200 ? '✅' : '❌ (should be ~300+)'}</span></p>
                                <p>✓ Format: <span className="font-mono">{results.keyFormat ? '✅ Starts with "eyJ"' : '❌ Does NOT start with "eyJ"'}</span></p>
                                <p>✓ Preview: <span className="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{results.keyPrefix}</span></p>
                                <p className="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                    <strong>Status:</strong> {results.currentKeyFormat}
                                </p>
                            </div>
                        </div>

                        <div className="border-b pb-4">
                            <h2 className="text-xl font-semibold mb-2">Supabase Client Test</h2>
                            <div className="space-y-1 text-sm">
                                <p>✓ Client Created: <span className="font-mono">{results.supabaseClientCreated ? '✅ Yes' : '❌ No'}</span></p>
                                {results.connectionTest && (
                                    <p>✓ Connection: <span className="font-mono">{results.connectionTest}</span></p>
                                )}
                                {results.connectionError && (
                                    <p className="text-red-600">❌ Error: {results.connectionError}</p>
                                )}
                            </div>
                        </div>

                        <div className="bg-blue-50 border border-blue-200 rounded p-4">
                            <h3 className="font-semibold mb-2">📋 How to Get Correct Keys:</h3>
                            <ol className="list-decimal list-inside space-y-1 text-sm">
                                <li>Go to: <a href="https://app.supabase.com/project/mcpogadznddjzexdvavz/settings/api" target="_blank" className="text-blue-600 underline">Supabase API Settings</a></li>
                                <li>Find section: <strong>"Project API keys"</strong></li>
                                <li>Copy <strong>"anon public"</strong> key (NOT "service_role")</li>
                                <li>The key should start with <code className="bg-gray-200 px-1 rounded">eyJ...</code></li>
                                <li>Paste into .env.local as NEXT_PUBLIC_SUPABASE_ANON_KEY</li>
                                <li>Restart dev server</li>
                            </ol>
                        </div>

                        <div className="bg-red-50 border border-red-200 rounded p-4">
                            <h3 className="font-semibold mb-2">⚠️ Common Mistake:</h3>
                            <p className="text-sm">
                                You might have copied the <strong>"Publishable key"</strong> which starts with <code className="bg-gray-200 px-1 rounded">sb_publishable_...</code>
                                <br />
                                This is NOT the same as the <strong>"anon public"</strong> JWT key!
                            </p>
                        </div>
                    </div>
                </div>

                <div className="flex gap-4">
                    <a
                        href="/auth"
                        className="inline-block bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition"
                    >
                        Go to Auth Page
                    </a>
                    <a
                        href="/test"
                        className="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition"
                    >
                        Test Signup
                    </a>
                </div>

                <div className="bg-white p-6 rounded-lg shadow">
                    <h3 className="font-semibold mb-2">Full Results (Debug):</h3>
                    <pre className="bg-gray-100 p-4 rounded overflow-auto text-xs">
                        {JSON.stringify(results, null, 2)}
                    </pre>
                </div>
            </div>
        </div>
    );
}
