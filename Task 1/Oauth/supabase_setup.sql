-- ============================================
-- SUPABASE DATABASE SETUP FOR STEGANOGRAPHY APP
-- ============================================

-- Table 1: Users (registered users from signup)
CREATE TABLE IF NOT EXISTS users (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  email TEXT UNIQUE NOT NULL,
  first_name TEXT NOT NULL,
  last_name TEXT NOT NULL,
  mobile TEXT NOT NULL,
  user_id TEXT,
  registered_at TIMESTAMP DEFAULT NOW(),
  created_at TIMESTAMP DEFAULT NOW()
);

-- Table 2: Test Results (scores from completed tests)
CREATE TABLE IF NOT EXISTS test_results (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  user_email TEXT NOT NULL,
  first_name TEXT NOT NULL,
  last_name TEXT NOT NULL,
  mobile TEXT NOT NULL,
  score INTEGER NOT NULL,
  total_score INTEGER DEFAULT 100,
  mcq_score INTEGER NOT NULL,
  crossword_score INTEGER NOT NULL,
  completed_at TIMESTAMP DEFAULT NOW(),
  created_at TIMESTAMP DEFAULT NOW(),
  FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE
);

-- Table 3: Generated Passwords (password vault)
CREATE TABLE IF NOT EXISTS generated_passwords (
  id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  email TEXT UNIQUE NOT NULL,
  password TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Create indexes for better query performance
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_test_results_email ON test_results(user_email);
CREATE INDEX IF NOT EXISTS idx_test_results_created ON test_results(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_passwords_email ON generated_passwords(email);

-- Enable Row Level Security (RLS)
ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE test_results ENABLE ROW LEVEL SECURITY;
ALTER TABLE generated_passwords ENABLE ROW LEVEL SECURITY;

-- RLS Policies: Allow all operations for authenticated and anon users
-- (In production, you'd want more restrictive policies)

-- Users table policies
CREATE POLICY "Allow all operations on users" ON users
  FOR ALL USING (true) WITH CHECK (true);

-- Test results table policies
CREATE POLICY "Allow all operations on test_results" ON test_results
  FOR ALL USING (true) WITH CHECK (true);

-- Generated passwords table policies  
CREATE POLICY "Allow all operations on generated_passwords" ON generated_passwords
  FOR ALL USING (true) WITH CHECK (true);

-- ============================================
-- INSTRUCTIONS:
-- ============================================
-- 1. Go to your Supabase project: https://app.supabase.com/project/mcpogadznddjzexdvavz
-- 2. Click on "SQL Editor" in the left sidebar
-- 3. Click "New Query"
-- 4. Copy and paste this entire SQL script
-- 5. Click "Run" button
-- 6. Verify tables are created in "Table Editor"
-- ============================================
