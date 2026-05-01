'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { Download, LogOut, Users, FileText, TrendingUp, Shield, Lock, Mail, Trash2 } from 'lucide-react';
import { supabase } from '../../../lib/supabaseClient';

interface RegisteredUser {
    firstName: string;
    lastName: string;
    email: string;
    mobile: string;
    userId: string;
    registeredAt: string;
}

interface TestResult {
    firstName: string;
    lastName: string;
    email: string;
    mobile: string;
    score: number;
    totalScore: number;
    mcqScore: number;
    crosswordScore: number;
    completedAt: string;
}

interface UserWithTestStatus extends RegisteredUser {
    testTaken: boolean;
    score?: number;
    totalScore?: number;
    mcqScore?: number;
    crosswordScore?: number;
    completedAt?: string;
}

export default function AdminDashboard() {
    const router = useRouter();
    const [allUsers, setAllUsers] = useState<UserWithTestStatus[]>([]);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState<'results' | 'passwords'>('results');
    const [savedPasswords, setSavedPasswords] = useState<Record<string, string>>({});

    useEffect(() => {
        // Check admin authentication
        const isAdmin = localStorage.getItem('adminAuth');
        if (!isAdmin) {
            router.push('/admin');
            return;
        }

        // LISTEN FOR CROSS-ORIGIN DATA SYNC
        const handleMessage = (event: MessageEvent) => {
            // Only accept messages from localhost:5173
            if (event.origin !== 'http://localhost:5173') return;

            if (event.data.type === 'SYNC_DATA') {
                console.log('📥 Received data from test app:', event.data);

                // Save test result
                if (event.data.testResult) {
                    const { key, value } = event.data.testResult;
                    localStorage.setItem(key, JSON.stringify(value));
                    console.log('✅ Saved test result:', key);
                }

                // Save registered users
                if (event.data.registeredUsers) {
                    localStorage.setItem('registeredUsers', event.data.registeredUsers);
                    console.log('✅ Saved registered users');
                }

                // Reload data
                setTimeout(() => {
                    loadAllUsers();
                    console.log('🔄 Dashboard refreshed with new data');
                }, 500);
            }
        };

        window.addEventListener('message', handleMessage);

        // Load all data
        loadAllUsers();
        loadPasswords();

        return () => {
            window.removeEventListener('message', handleMessage);
        };
    }, [router]);

    const loadAllUsers = async () => {
        setLoading(true);
        try {
            // Check if Supabase is configured
            const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL;
            const supabaseKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY;

            if (!supabaseUrl || !supabaseKey || supabaseKey === 'YOUR_ANON_KEY_HERE') {
                console.warn('⚠️ Supabase not configured, using localStorage');
                loadFromLocalStorage();
                return;
            }

            // FETCH FROM SUPABASE DATABASE
            const { data: usersData, error: usersError } = await supabase
                .from('users')
                .select('*')
                .order('registered_at', { ascending: false });

            if (usersError) {
                console.error('❌ Error fetching users from Supabase:', usersError.message);
                console.warn('⚠️ Falling back to localStorage');
                loadFromLocalStorage();
                return;
            }

            const { data: testResultsData, error: resultsError } = await supabase
                .from('test_results')
                .select('*')
                .order('created_at', { ascending: false });

            if (resultsError) {
                console.error('❌ Error fetching test results:', resultsError.message);
            }

            // Convert database format to app format
            const registeredUsers: RegisteredUser[] = (usersData || []).map(user => ({
                firstName: user.first_name,
                lastName: user.last_name,
                email: user.email,
                mobile: user.mobile,
                userId: user.user_id,
                registeredAt: user.registered_at
            }));

            const testResults: TestResult[] = (testResultsData || []).map(result => ({
                firstName: result.first_name,
                lastName: result.last_name,
                email: result.user_email,
                mobile: result.mobile,
                score: result.score,
                totalScore: result.total_score,
                mcqScore: result.mcq_score,
                crosswordScore: result.crossword_score,
                completedAt: result.completed_at
            }));

            console.log('✅ Loaded from Supabase:', {
                users: registeredUsers.length,
                testResults: testResults.length
            });

            // Merge users with test results
            const usersWithStatus: UserWithTestStatus[] = registeredUsers.map(user => {
                const testResult = testResults.find(result => result.email === user.email);

                if (testResult) {
                    return {
                        ...user,
                        testTaken: true,
                        score: testResult.score,
                        totalScore: testResult.totalScore,
                        mcqScore: testResult.mcqScore,
                        crosswordScore: testResult.crosswordScore,
                        completedAt: testResult.completedAt
                    };
                } else {
                    return {
                        ...user,
                        testTaken: false
                    };
                }
            });

            setAllUsers(usersWithStatus);
        } catch (error: any) {
            console.error('❌ Supabase connection error:', error.message);
            console.warn('⚠️ Falling back to localStorage');
            loadFromLocalStorage();
        } finally {
            setLoading(false);
        }
    };

    // Fallback function to load from localStorage if database fails
    const loadFromLocalStorage = () => {
        try {
            // Get all registered users
            const registeredUsersData = localStorage.getItem('registeredUsers');
            const registeredUsers: RegisteredUser[] = registeredUsersData ? JSON.parse(registeredUsersData) : [];

            // Get all test results
            const testResults: TestResult[] = [];
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key && key.startsWith('testResult_')) {
                    const resultData = localStorage.getItem(key);
                    if (resultData) {
                        try {
                            testResults.push(JSON.parse(resultData));
                        } catch (e) {
                            console.error('Failed to parse result:', e);
                        }
                    }
                }
            }

            // Merge users with test results
            const usersWithStatus: UserWithTestStatus[] = registeredUsers.map(user => {
                const testResult = testResults.find(result => result.email === user.email);

                if (testResult) {
                    return {
                        ...user,
                        testTaken: true,
                        score: testResult.score,
                        totalScore: testResult.totalScore,
                        mcqScore: testResult.mcqScore,
                        crosswordScore: testResult.crosswordScore,
                        completedAt: testResult.completedAt
                    };
                } else {
                    return {
                        ...user,
                        testTaken: false
                    };
                }
            });

            // Sort by registration date (newest first)
            usersWithStatus.sort((a, b) => new Date(b.registeredAt).getTime() - new Date(a.registeredAt).getTime());

            setAllUsers(usersWithStatus);
            console.log('⚠️ Loaded from localStorage (fallback)');
        } catch (error) {
            console.error('Error loading from localStorage:', error);
        }
    };

    const loadPasswords = async () => {
        try {
            // Check if Supabase is configured
            const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL;
            const supabaseKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY;

            if (!supabaseUrl || !supabaseKey || supabaseKey === 'YOUR_ANON_KEY_HERE') {
                console.warn('⚠️ Supabase not configured for passwords, using localStorage');
                loadPasswordsFromLocalStorage();
                return;
            }

            // FETCH FROM SUPABASE DATABASE
            const { data, error } = await supabase
                .from('generated_passwords')
                .select('*');

            if (error) {
                console.error('❌ Error fetching passwords from Supabase:', error.message);
                console.warn('⚠️ Falling back to localStorage for passwords');
                loadPasswordsFromLocalStorage();
                return;
            }

            // Convert to format expected by component
            const passwordsObj: Record<string, string> = {};
            (data || []).forEach((item: any) => {
                passwordsObj[item.email] = item.password;
            });

            setSavedPasswords(passwordsObj);
            console.log('✅ Loaded passwords from Supabase');
        } catch (error: any) {
            console.error('❌ Password loading error:', error.message);
            loadPasswordsFromLocalStorage();
        }
    };

    const loadPasswordsFromLocalStorage = () => {
        const saved = localStorage.getItem('generated_passwords');
        if (saved) {
            try {
                setSavedPasswords(JSON.parse(saved));
                console.log('⚠️ Loaded passwords from localStorage (fallback)');
            } catch (e) {
                console.error('Failed to parse passwords', e);
            }
        }
    };

    const deletePassword = (email: string) => {
        const newPasswords = { ...savedPasswords };
        delete newPasswords[email];
        setSavedPasswords(newPasswords);
        localStorage.setItem('generated_passwords', JSON.stringify(newPasswords));
    };

    const exportToCSV = () => {
        if (allUsers.length === 0) {
            alert('No users to export');
            return;
        }

        const headers = ['First Name', 'Last Name', 'Email', 'Phone Number', 'MCQ Score', 'Crossword Score', 'Total Score', 'Percentage', 'Test Status'];
        const rows = allUsers.map(user => [
            user.firstName || '',
            user.lastName || '',
            user.email || '',
            user.mobile || '',
            user.testTaken ? (user.mcqScore || 0) : '--',
            user.testTaken ? (user.crosswordScore || 0) : '--',
            user.testTaken ? (user.score || 0) : '--',
            user.testTaken ? `${((user.score! / user.totalScore!) * 100).toFixed(2)}%` : '--',
            user.testTaken ? 'Completed' : 'Not Taken'
        ]);

        const csvContent = [
            headers.join(','),
            ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
        ].join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);

        link.setAttribute('href', url);
        link.setAttribute('download', `all_users_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    const handleLogout = () => {
        localStorage.removeItem('adminAuth');
        localStorage.removeItem('adminEmail');
        router.push('/admin');
    };

    const testsTaken = allUsers.filter(u => u.testTaken).length;
    const stats = {
        totalUsers: allUsers.length,
        testsTaken: testsTaken,
        testsNotTaken: allUsers.length - testsTaken,
        averageScore: testsTaken > 0
            ? (allUsers.filter(u => u.testTaken).reduce((sum, u) => sum + (u.score || 0), 0) / testsTaken).toFixed(1)
            : 0,
        passRate: testsTaken > 0
            ? ((allUsers.filter(u => u.testTaken && (u.score! / u.totalScore!) >= 0.6).length / testsTaken) * 100).toFixed(1)
            : 0
    };

    const passwordEmails = Object.keys(savedPasswords);

    return (
        <div className="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-white p-6">
            <div className="max-w-7xl mx-auto space-y-6">
                {/* Header */}
                <div className="glass-card p-6 flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white">
                            <Shield className="w-6 h-6" />
                        </div>
                        <div>
                            <h1 className="text-3xl font-bold gradient-text">Admin Dashboard</h1>
                            <p className="text-gray-500 text-sm">Steganography Test Management</p>
                        </div>
                    </div>
                    <button
                        onClick={handleLogout}
                        className="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors text-gray-600"
                    >
                        <LogOut className="w-4 h-4" />
                        Logout
                    </button>
                </div>

                {/* Tab Navigation */}
                <div className="glass-card p-2 flex gap-2">
                    <button
                        onClick={() => setActiveTab('results')}
                        className={`flex-1 px-6 py-3 rounded-xl font-medium transition-all ${activeTab === 'results'
                            ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg'
                            : 'text-gray-600 hover:bg-gray-50'
                            }`}
                    >
                        <div className="flex items-center justify-center gap-2">
                            <FileText className="w-5 h-5" />
                            All Users ({allUsers.length})
                        </div>
                    </button>
                    <button
                        onClick={() => setActiveTab('passwords')}
                        className={`flex-1 px-6 py-3 rounded-xl font-medium transition-all ${activeTab === 'passwords'
                            ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg'
                            : 'text-gray-600 hover:bg-gray-50'
                            }`}
                    >
                        <div className="flex items-center justify-center gap-2">
                            <Lock className="w-5 h-5" />
                            Password Vault ({passwordEmails.length})
                        </div>
                    </button>
                </div>

                {/* All Users Tab */}
                {activeTab === 'results' && (
                    <>
                        {/* Stats Cards */}
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div className="glass-card p-6">
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <Users className="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-500">Total Users</p>
                                        <p className="text-2xl font-bold text-gray-800">{stats.totalUsers}</p>
                                    </div>
                                </div>
                            </div>

                            <div className="glass-card p-6">
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                        <FileText className="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-500">Tests Taken</p>
                                        <p className="text-2xl font-bold text-gray-800">{stats.testsTaken}</p>
                                    </div>
                                </div>
                            </div>

                            <div className="glass-card p-6">
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center text-pink-600">
                                        <TrendingUp className="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-500">Average Score</p>
                                        <p className="text-2xl font-bold text-gray-800">{stats.averageScore}/100</p>
                                    </div>
                                </div>
                            </div>

                            <div className="glass-card p-6">
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                        <Users className="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p className="text-sm text-gray-500">Not Taken</p>
                                        <p className="text-2xl font-bold text-gray-800">{stats.testsNotTaken}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Export Button */}
                        <div className="glass-card p-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <h2 className="text-xl font-bold text-gray-800">All Registered Users</h2>
                                    <p className="text-sm text-gray-500">Export all users (with or without test results)</p>
                                </div>
                                <button
                                    onClick={exportToCSV}
                                    disabled={allUsers.length === 0}
                                    className="btn-primary flex items-center gap-2"
                                >
                                    <Download className="w-4 h-4" />
                                    Export to CSV
                                </button>
                            </div>
                        </div>

                        {/* Users Table */}
                        <div className="glass-card p-6">
                            {loading ? (
                                <div className="text-center py-12">
                                    <div className="loader border-purple-200 border-l-purple-600 w-12 h-12 mx-auto mb-4" />
                                    <p className="text-gray-500">Loading users...</p>
                                </div>
                            ) : allUsers.length === 0 ? (
                                <div className="text-center py-12">
                                    <Users className="w-16 h-16 text-gray-300 mx-auto mb-4" />
                                    <p className="text-gray-500">No users registered yet</p>
                                    <p className="text-sm text-gray-400 mt-2">Users will appear here after signup</p>
                                </div>
                            ) : (
                                <div className="overflow-x-auto">
                                    <table className="w-full">
                                        <thead>
                                            <tr className="border-b border-gray-200">
                                                <th className="text-left py-3 px-4 text-sm font-semibold text-gray-600">Name</th>
                                                <th className="text-left py-3 px-4 text-sm font-semibold text-gray-600">Email</th>
                                                <th className="text-left py-3 px-4 text-sm font-semibold text-gray-600">Phone</th>
                                                <th className="text-center py-3 px-4 text-sm font-semibold text-gray-600">MCQ</th>
                                                <th className="text-center py-3 px-4 text-sm font-semibold text-gray-600">Crossword</th>
                                                <th className="text-center py-3 px-4 text-sm font-semibold text-gray-600">Total</th>
                                                <th className="text-center py-3 px-4 text-sm font-semibold text-gray-600">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {allUsers.map((user, index) => {
                                                const percentage = user.testTaken && user.score && user.totalScore
                                                    ? ((user.score / user.totalScore) * 100).toFixed(1)
                                                    : null;
                                                const passed = percentage ? parseFloat(percentage) >= 60 : false;

                                                return (
                                                    <tr key={index} className="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                                        <td className="py-3 px-4 text-sm text-gray-800 font-medium">
                                                            {user.firstName} {user.lastName}
                                                        </td>
                                                        <td className="py-3 px-4 text-sm text-gray-600">{user.email}</td>
                                                        <td className="py-3 px-4 text-sm text-gray-600">{user.mobile}</td>
                                                        <td className="py-3 px-4 text-sm text-center text-gray-800">
                                                            {user.testTaken ? `${user.mcqScore}/50` : '--'}
                                                        </td>
                                                        <td className="py-3 px-4 text-sm text-center text-gray-800">
                                                            {user.testTaken ? `${user.crosswordScore}/50` : '--'}
                                                        </td>
                                                        <td className="py-3 px-4 text-sm text-center font-bold text-purple-600">
                                                            {user.testTaken ? `${user.score}/100` : '--'}
                                                        </td>
                                                        <td className="py-3 px-4 text-center">
                                                            {user.testTaken ? (
                                                                <span className={`inline-block px-2 py-1 rounded-full text-xs font-semibold ${passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                                                                    }`}>
                                                                    {percentage}%
                                                                </span>
                                                            ) : (
                                                                <span className="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                                                    Not Taken
                                                                </span>
                                                            )}
                                                        </td>
                                                    </tr>
                                                );
                                            })}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                    </>
                )}

                {/* Password Vault Tab */}
                {activeTab === 'passwords' && (
                    <div className="glass-card p-8 space-y-6">
                        <div className="space-y-2">
                            <h2 className="text-2xl font-bold gradient-text">Password Vault</h2>
                            <p className="text-gray-500">All generated passwords saved in browser storage</p>
                        </div>

                        {passwordEmails.length === 0 ? (
                            <div className="py-12 text-center space-y-4">
                                <div className="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto text-gray-300">
                                    <Lock className="w-8 h-8" />
                                </div>
                                <p className="text-gray-400 font-medium">No passwords generated yet</p>
                                <p className="text-sm text-gray-400">Passwords will appear here when users generate them during signup</p>
                            </div>
                        ) : (
                            <div className="grid gap-4">
                                {passwordEmails.map((email) => (
                                    <div
                                        key={email}
                                        className="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4 group hover:bg-white hover:border-pink-100 hover:shadow-md transition-all duration-300"
                                    >
                                        <div className="flex items-center gap-4">
                                            <div className="bg-violet-100 p-3 rounded-xl text-violet-600">
                                                <Mail className="w-5 h-5" />
                                            </div>
                                            <div>
                                                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider">Email Address</p>
                                                <p className="text-gray-700 font-medium">{email}</p>
                                            </div>
                                        </div>

                                        <div className="flex items-center gap-6">
                                            <div className="text-right">
                                                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider">Saved Password</p>
                                                <p className="font-mono text-pink-600 font-bold bg-pink-50 px-3 py-1 rounded-lg border border-pink-100">
                                                    {savedPasswords[email]}
                                                </p>
                                            </div>
                                            <button
                                                onClick={() => deletePassword(email)}
                                                className="text-gray-300 hover:text-red-500 transition-colors p-2"
                                                title="Delete record"
                                            >
                                                <Trash2 className="w-5 h-5" />
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        <div className="p-4 bg-amber-50 rounded-2xl border border-amber-100 text-amber-800 text-sm">
                            <strong>Note:</strong> These passwords are saved in browser localStorage for this domain. They are not sent to the cloud unless users complete the signup form.
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
