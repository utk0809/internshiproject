'use client';

import { useState, useEffect } from 'react';
import { User, Phone, ArrowRight, CheckCircle2, Mail, Eye, EyeOff, Lock } from 'lucide-react';
import { supabase } from '@/lib/supabaseClient';

export default function Home() {
  const [loading, setLoading] = useState(false);
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [showPassword, setShowPassword] = useState(false);
  const [form, setForm] = useState({ firstName: '', lastName: '', mobile: '', email: '', password: '' });
  const [savedPasswords, setSavedPasswords] = useState<Record<string, string>>(() => {
    if (typeof window !== 'undefined') {
      const saved = localStorage.getItem('generated_passwords');
      if (saved) {
        try {
          return JSON.parse(saved);
        } catch (e) {
          console.error('Failed to parse saved passwords', e);
        }
      }
    }
    return {};
  });

  // Automatically fetch password when email changes
  useEffect(() => {
    if (form.email && savedPasswords[form.email]) {
      setForm(prev => ({ ...prev, password: savedPasswords[form.email] }));
    }
  }, [form.email, savedPasswords]);

  const generatePassword = () => {
    if (!form.email) {
      setError('Please enter an email address first');
      return;
    }

    const lowercase = "abcdefghijklmnopqrstuvwxyz";
    const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const numbers = "0123456789";
    const symbols = "!@#$%^&*()_+-=[]{}";
    const all = lowercase + uppercase + numbers + symbols;

    let password = "";
    // Ensure at least one character of each type
    password += lowercase[Math.floor(Math.random() * lowercase.length)];
    password += uppercase[Math.floor(Math.random() * uppercase.length)];
    password += numbers[Math.floor(Math.random() * numbers.length)];
    password += symbols[Math.floor(Math.random() * symbols.length)];

    // Fill the rest to 12 characters
    for (let i = password.length; i < 12; i++) {
      password += all[Math.floor(Math.random() * all.length)];
    }

    // Shuffle the password
    const generated = password.split('').sort(() => Math.random() - 0.5).join('');

    // Update form
    setForm(prev => ({ ...prev, password: generated }));

    // Save to state and localStorage
    const newSavedPasswords = { ...savedPasswords, [form.email]: generated };
    setSavedPasswords(newSavedPasswords);
    localStorage.setItem('generated_passwords', JSON.stringify(newSavedPasswords));

    setError(null);
  };

  const handleSignup = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    console.log('=== ATTEMPTING SUPABASE SIGNUP ===');
    console.log('Email:', form.email);

    try {
      // Connectivity Check
      try {
        console.log('Testing connectivity to Supabase URL...');
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 5000);
        await fetch(process.env.NEXT_PUBLIC_SUPABASE_URL!, {
          method: 'GET',
          mode: 'no-cors',
          signal: controller.signal
        });
        clearTimeout(timeoutId);
        console.log('✅ Supabase URL is reachable');
      } catch (connErr: any) {
        console.error('❌ Connectivity Check Failed:', connErr);
        throw new Error('Could not connect to Supabase. Please disable ad-blockers and try again.');
      }

      // Create user in Supabase Auth
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

      if (signupError) throw signupError;

      if (data.user) {
        console.log('✅ User created in Supabase Auth:', data.user.id);
        setIsLoggedIn(true);

        // Store user data in localStorage
        if (typeof window !== 'undefined') {
          const userData = {
            firstName: form.firstName,
            lastName: form.lastName,
            mobile: form.mobile,
            email: form.email,
            userId: data.user.id,
            registeredAt: new Date().toISOString()
          };

          localStorage.setItem('userData', JSON.stringify(userData));

          // Also save to registered users list for admin
          const registeredUsers = localStorage.getItem('registeredUsers');
          let usersList = registeredUsers ? JSON.parse(registeredUsers) : [];

          // Check if user already exists (by email)
          const existingIndex = usersList.findIndex((u: any) => u.email === form.email);
          if (existingIndex >= 0) {
            // Update existing user
            usersList[existingIndex] = userData;
          } else {
            // Add new user
            usersList.push(userData);
          }

          localStorage.setItem('registeredUsers', JSON.stringify(usersList));

          // SAVE TO SUPABASE DATABASE
          try {
            // Insert user into users table
            const { error: userInsertError } = await supabase
              .from('users')
              .upsert({
                email: form.email,
                first_name: form.firstName,
                last_name: form.lastName,
                mobile: form.mobile,
                user_id: data.user.id,
                registered_at: new Date().toISOString()
              }, { onConflict: 'email' });

            if (userInsertError) {
              console.error('Error saving user to database:', userInsertError);
            } else {
              console.log('✅ User saved to Supabase database');
            }

            // Save generated password to database if exists
            if (savedPasswords[form.email]) {
              const { error: passwordInsertError } = await supabase
                .from('generated_passwords')
                .upsert({
                  email: form.email,
                  password: savedPasswords[form.email]
                }, { onConflict: 'email' });

              if (passwordInsertError) {
                console.error('Error saving password to database:', passwordInsertError);
              } else {
                console.log('✅ Password saved to Supabase database');
              }
            }
          } catch (dbError) {
            console.error('Database save error:', dbError);
            // Don't block signup if database save fails
          }
        }
      } else {
        setError('Signup failed. Please try again.');
      }
    } catch (err: any) {
      console.error('❌ Signup Error:', err);
      if (err.message === 'Failed to fetch' || err.name === 'AbortError') {
        setError('Connection Blocked: Your browser is blocking Supabase. Try disabling ad-blockers or using Incognito mode.');
      } else {
        setError(err.message || 'An error occurred during signup');
      }
    } finally {
      setLoading(false);
    }
  };

  const handleProceedToTests = () => {
    // Get userData from localStorage
    const userDataString = localStorage.getItem('userData');

    if (userDataString) {
      const userData = JSON.parse(userDataString);
      // Pass user data via URL parameters to React app
      const params = new URLSearchParams({
        firstName: userData.firstName || '',
        lastName: userData.lastName || '',
        mobile: userData.mobile || '',
        email: userData.email || '',
        userId: userData.userId || ''
      });
      window.location.href = `http://localhost:5173/test/crossword?${params.toString()}`;
    } else {
      // Fallback if no userData found
      window.location.href = 'http://localhost:5173/test/crossword';
    }
  };

  return (
    <div className="min-h-screen flex flex-col items-center justify-center p-4 relative">
      <div className="max-w-md w-full glass-card p-8 space-y-8 relative z-10 transition-all duration-500">
        <div className="text-center space-y-2">
          <h1 className="text-4xl font-bold gradient-text">Steganography Assessment</h1>
          <p className="text-gray-500">
            {isLoggedIn
              ? "Account created! Ready to start your test."
              : "Enter your information to begin"}
          </p>
        </div>

        {error && (
          <div className="space-y-4">
            <div className="p-3 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm text-center">
              {error}
            </div>
            {error.includes('Connection Blocked') && (
              <div className="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs space-y-2">
                <p className="font-bold">🛠️ Diagnostic Steps:</p>
                <ul className="list-disc pl-4 space-y-1">
                  <li>Open <b>Incognito Mode</b> and try again.</li>
                  <li>Disable any <b>Ad-Blockers</b> (uBlock, AdBlock, etc).</li>
                  <li>Check if <b>Brave Shields</b> is active and turn it off.</li>
                </ul>
              </div>
            )}
          </div>
        )}

        {!isLoggedIn ? (
          <div className="space-y-6">
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
                  onChange={(e) => {
                    const newEmail = e.target.value;
                    setForm({ ...form, email: newEmail });
                    if (savedPasswords[newEmail]) {
                      setForm(prev => ({ ...prev, email: newEmail, password: savedPasswords[newEmail] }));
                    } else {
                      setForm(prev => ({ ...prev, email: newEmail, password: '' }));
                    }
                  }}
                />
              </div>

              <div className="space-y-2">
                <div className="relative group">
                  <Lock className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-pink-500 transition-colors" />
                  <input
                    type={showPassword ? "text" : "password"}
                    placeholder="Generated Password"
                    className="input-field pl-12 pr-12 bg-gray-50/50"
                    required
                    readOnly
                    minLength={8}
                    value={form.password}
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
                  type="button"
                  onClick={generatePassword}
                  className="w-full py-2 px-4 rounded-xl text-sm font-medium border border-pink-200 text-pink-600 hover:bg-pink-50 transition-all flex items-center justify-center gap-2"
                >
                  <Lock className="w-4 h-4" />
                  {form.password ? "Regenerate Password" : "Generate Secure Password"}
                </button>
              </div>

              <button type="submit" disabled={loading} className="btn-primary group">
                {loading ? (
                  <div className="flex items-center justify-center gap-2">
                    <div className="loader border-white/20 border-l-white w-5 h-5" />
                    <span>Creating Account...</span>
                  </div>
                ) : (
                  <div className="flex items-center justify-center gap-2">
                    <span>Create Account</span>
                    <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                  </div>
                )}
              </button>
            </form>
          </div>
        ) : (
          <div className="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div className="bg-pink-50 p-6 rounded-2xl border border-pink-100 flex flex-col items-center text-center space-y-3">
              <div className="bg-white p-2 rounded-full shadow-sm text-pink-500">
                <CheckCircle2 className="w-8 h-8" />
              </div>
              <div>
                <p className="text-sm font-medium text-pink-600 uppercase tracking-wider">Account Active</p>
                <h2 className="text-2xl font-bold text-gray-800">
                  {form.firstName} {form.lastName}
                </h2>
                <p className="text-gray-500">{form.email}</p>
                <p className="text-sm text-gray-400">{form.mobile}</p>
              </div>
            </div>

            <button
              onClick={handleProceedToTests}
              disabled={loading}
              className="btn-primary group"
            >
              {loading ? (
                <div className="flex items-center justify-center gap-2">
                  <div className="loader border-white/20 border-l-white w-5 h-5" />
                  <span>Loading Test...</span>
                </div>
              ) : (
                <div className="flex items-center justify-center gap-2">
                  <span>Start Crossword Test</span>
                  <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </div>
              )}
            </button>

            <button
              onClick={() => setIsLoggedIn(false)}
              className="w-full text-sm text-gray-400 hover:text-gray-600 transition-colors"
            >
              Back to form
            </button>
          </div>
        )}
      </div>
    </div>
  );
}
