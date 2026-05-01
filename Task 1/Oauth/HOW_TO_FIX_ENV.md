# 🔍 HOW TO VERIFY YOUR SUPABASE CREDENTIALS

## Current Status: ❌ INCORRECT KEY FORMAT

Your `.env.local` currently has:
```
NEXT_PUBLIC_SUPABASE_ANON_KEY=sb_publishable_cePcYCFvwBObh2CBDJ9RnQ_u3m2Njza
```

This is **WRONG**. The `sb_publishable_...` is NOT the anon key.

## ✅ CORRECT FORMAT

The anon key should look like this:
```
NEXT_PUBLIC_SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1jcG9nYWR6bmRkanlleGR2YXZ6Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3MDAwMDAwMDAsImV4cCI6MjAwMDAwMDAwMH0.YOUR_SECRET_HERE
```

Notice:
- ✅ Starts with `eyJ`
- ✅ Very long (~300+ characters)
- ✅ Has dots (.) separating sections
- ✅ This is a JWT token

## 📍 WHERE TO FIND THE CORRECT KEY

### Step 1: Go to Supabase Dashboard
URL: https://app.supabase.com/project/mcpogadznddjzexdvavz/settings/api

### Step 2: Look for "Project API keys" section
You'll see TWO keys:
1. **anon public** ← ✅ THIS IS THE ONE YOU NEED
2. **service_role** ← ❌ DO NOT USE THIS

### Step 3: Copy the "anon public" key
- Click the copy icon next to "anon public"
- It should start with `eyJ...`
- It should be very long

### Step 4: Update .env.local
Replace line 8 in your `.env.local`:

**BEFORE (WRONG):**
```env
NEXT_PUBLIC_SUPABASE_ANON_KEY=sb_publishable_cePcYCFvwBObh2CBDJ9RnQ_u3m2Njza
```

**AFTER (CORRECT):**
```env
NEXT_PUBLIC_SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im1jcG9nYWR6bmRkanlleGR2YXZ6Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3MDAwMDAwMDAsImV4cCI6MjAwMDAwMDAwMH0.PASTE_YOUR_ACTUAL_KEY_HERE
```

### Step 5: Restart Dev Server
```bash
# Press Ctrl+C to stop
# Then run:
npm run dev
```

## 🧪 VERIFY IT WORKS

After updating, visit these pages to verify:

1. **Verification Page**: http://localhost:3000/verify
   - Shows detailed check of your environment variables
   - Tells you exactly what's wrong/right

2. **Test Page**: http://localhost:3000/test
   - Attempts to create a test user
   - Shows if connection works

3. **Auth Page**: http://localhost:3000/auth
   - The actual signup form
   - Try creating a real user

## ❌ COMMON MISTAKES

### Mistake 1: Using "Publishable Key"
```
sb_publishable_... ← WRONG
```
This is for Supabase Realtime, NOT for Auth.

### Mistake 2: Using "service_role" key
```
eyJ...role":"service_role"... ← WRONG (too powerful, security risk)
```
Only use "anon public" key.

### Mistake 3: Not restarting server
After changing `.env.local`, you MUST restart the dev server.

## ✅ SUCCESS INDICATORS

When your key is correct, you'll see:
- ✅ Verification page shows all green checkmarks
- ✅ Test page creates a user successfully
- ✅ Console shows: `✅ USER CREATED: [user-id]`
- ✅ Supabase Dashboard → Auth → Users shows new users

## 🆘 STILL NOT WORKING?

1. Double-check you copied the ENTIRE key (should be ~300+ chars)
2. Make sure there are NO spaces before/after the key
3. Make sure the key is on ONE line (no line breaks)
4. Verify you're looking at the right project in Supabase
5. Try creating a NEW anon key in Supabase settings

## 📞 NEED HELP?

Visit the verification page to see exactly what's wrong:
http://localhost:3000/verify
