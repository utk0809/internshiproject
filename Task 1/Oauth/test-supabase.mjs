// Quick test script to verify Supabase credentials
// Run with: node test-supabase.mjs

import { createClient } from '@supabase/supabase-js';

// Load environment variables manually
const supabaseUrl = 'https://mcpogadznddjzexdvavz.supabase.co';
const supabaseAnonKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1jcG9nYWR6bmRkanpleGR2YXZ6Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3Njk5Mjg2ODEsImV4cCI6MjA4NTUwNDY4MX0.06v8ppw1z-rYX4IN81kI96CD4Rp2FC5r43DMYDgLmNk';

console.log('=== SUPABASE CREDENTIALS TEST ===\n');

console.log('✓ URL:', supabaseUrl);
console.log('✓ Anon Key Length:', supabaseAnonKey.length, 'characters');
console.log('✓ Anon Key Format:', supabaseAnonKey.startsWith('eyJ') ? '✅ Correct (JWT)' : '❌ Wrong');
console.log('✓ Anon Key Preview:', supabaseAnonKey.substring(0, 50) + '...\n');

// Create Supabase client
console.log('Creating Supabase client...');
const supabase = createClient(supabaseUrl, supabaseAnonKey);
console.log('✅ Client created successfully\n');

// Test 1: Get session (should be null if not logged in)
console.log('Test 1: Getting session...');
try {
    const { data: sessionData, error: sessionError } = await supabase.auth.getSession();
    if (sessionError) {
        console.log('❌ Session Error:', sessionError.message);
    } else {
        console.log('✅ Session check successful (session:', sessionData.session ? 'exists' : 'null', ')\n');
    }
} catch (err) {
    console.log('❌ Exception:', err.message, '\n');
}

// Test 2: Try to sign up a test user
console.log('Test 2: Creating test user...');
const testEmail = `test-${Date.now()}@example.com`;
const testPassword = 'TestPassword123!';

console.log('  Email:', testEmail);
console.log('  Password: ******\n');

try {
    const { data, error } = await supabase.auth.signUp({
        email: testEmail,
        password: testPassword,
        options: {
            data: {
                first_name: 'Test',
                last_name: 'User',
                mobile: '1234567890'
            }
        }
    });

    if (error) {
        console.log('❌ Signup Error:', error.message);
        console.log('   Error Details:', error);
    } else if (data.user) {
        console.log('✅ USER CREATED SUCCESSFULLY!');
        console.log('   User ID:', data.user.id);
        console.log('   Email:', data.user.email);
        console.log('   Created At:', data.user.created_at);
        console.log('   Metadata:', data.user.user_metadata);
        console.log('\n🎉 SUCCESS! Your Supabase credentials are working correctly!');
        console.log('   Check your Supabase Dashboard → Auth → Users to see the new user.');
    } else {
        console.log('⚠️ No error but no user returned');
        console.log('   Data:', data);
    }
} catch (err) {
    console.log('❌ Exception:', err.message);
    console.log('   Full error:', err);
}

console.log('\n=== TEST COMPLETE ===');
