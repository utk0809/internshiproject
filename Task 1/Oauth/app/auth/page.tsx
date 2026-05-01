'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { User, Phone, Mail, Eye, EyeOff, Lock } from 'lucide-react';
import { supabase } from '@/lib/supabaseClient';

export default function AuthPage() {
    const router = useRouter();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [success, setSuccess] = useState<string | null>(null);
    const [showPassword, setShowPassword] = useState(false);

    const [form, setForm] = useState({
        firstName: '',
        lastName: '',
        mobile: '',
        email: '',
        password: ''
    });

    // REAL Supabase signup function
    const handleSignup = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError(null);
        setSuccess(null);

        console.log('=== SIGNUP ATTEMPT ===');
        console.log('Email:', form.email);
        console.log('Password length:', form.password.length);

        try {
            // ACTUAL Supabase Auth signup
            const { data, error: signupError } = await supabase.auth.signUp({
                email: form.email,
                password: form.password,
                options: {
                    data: {
                        first_name: form.firstName,
                        last_name: form.lastName,
                        mobile: form.mobile,
                    }
                }
            });

            console.log('=== SIGNUP RESPONSE ===');
            console.log('Data:', data);
            console.log('Error:', signupError);

            if (signupError) {
                throw signupError;
            }

            if (data.user) {
                console.log('✅ USER CREATED:', data.user.id);
                console.log('User email:', data.user.email);
                console.log('User metadata:', data.user.user_metadata);

                setSuccess(`Account created successfully! User ID: ${data.user.id}`);

                // Store user info for quiz
                if (typeof window !== 'undefined') {
                    localStorage.setItem('quizUser', JSON.stringify({
                        ...form,
                        userId: data.user.id
                    }));
                }

                // Redirect to quiz after 2 seconds
                setTimeout(() => {
                    router.push('/quiz');
                }, 2000);
            } else {
                console.warn('⚠️ No user returned but no error');
                setError('Signup completed but user data not returned');
            }

        } catch (err: any) {
            console.error('❌ SIGNUP ERROR:', err);
            setError(err.message || 'Failed to create account');
        } finally {
            setLoading(false);
        }
    };



    return (
        <div className="min-h-screen flex flex-col items-center justify-center p-4 relative">
            <div className="max-w-md w-full glass-card p-8 space-y-8 relative z-10">
                <div className="text-center space-y-2">
                    <h1 className="text-4xl font-bold gradient-text">Quiz Master Pro</h1>
                    <p className="text-gray-500">Create your account to get started</p>
                </div>

                {error && (
                    <div className="p-3 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm">
                        {error}
                    </div>
                )}

                {success && (
                    <div className="p-3 rounded-xl bg-green-50 border border-green-100 text-green-600 text-sm">
                        {success}
                    </div>
                )}

                <form onSubmit={handleSignup} className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div className="relative group">
                            <User className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-pink-500 transition-colors" />
                            <input
                                type="text"
                                placeholder="First Name"
                                className="input-field pl-12"
                                required
                                value={form.firstName}
                                onChange={(e) => setForm({ ...form, firstName: e.target.value })}
                            />
                        </div>
                        <div className="relative group">
                            <User className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-pink-500 transition-colors" />
                            <input
                                type="text"
                                placeholder="Last Name"
                                className="input-field pl-12"
                                required
                                value={form.lastName}
                                onChange={(e) => setForm({ ...form, lastName: e.target.value })}
                            />
                        </div>
                    </div>

                    <div className="relative group">
                        <Phone className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-purple-500 transition-colors" />
                        <input
                            type="tel"
                            placeholder="Mobile Number"
                            className="input-field pl-12"
                            required
                            pattern="[0-9]{10}"
                            title="Please enter a valid 10-digit mobile number"
                            value={form.mobile}
                            onChange={(e) => setForm({ ...form, mobile: e.target.value })}
                        />
                    </div>

                    <div className="relative group">
                        <Mail className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-violet-500 transition-colors" />
                        <input
                            type="email"
                            placeholder="Email Address"
                            className="input-field pl-12"
                            required
                            value={form.email}
                            onChange={(e) => setForm({ ...form, email: e.target.value })}
                        />
                    </div>

                    <div className="relative group">
                        <Lock className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-pink-500 transition-colors" />
                        <input
                            type={showPassword ? "text" : "password"}
                            placeholder="Password (min 6 characters)"
                            className="input-field pl-12 pr-12"
                            required
                            minLength={6}
                            value={form.password}
                            onChange={(e) => setForm({ ...form, password: e.target.value })}
                        />
                        <button
                            type="button"
                            onClick={() => setShowPassword(!showPassword)}
                            className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-pink-500 transition-colors"
                        >
                            {showPassword ? (
                                <EyeOff className="w-5 h-5" />
                            ) : (
                                <Eye className="w-5 h-5" />
                            )}
                        </button>
                    </div>

                    <button
                        type="submit"
                        disabled={loading}
                        className="btn-primary group w-full"
                    >
                        {loading ? (
                            <div className="flex items-center justify-center gap-2">
                                <div className="loader border-white/20 border-l-white w-5 h-5" />
                                <span>Creating Account...</span>
                            </div>
                        ) : (
                            <span>Sign Up</span>
                        )}
                    </button>
                </form>


            </div>
        </div>
    );
}
