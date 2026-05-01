# Steganography Assessment Platform

A comprehensive web application for learning and testing steganography concepts with an integrated admin panel for managing users and test results.

## 🎯 Features

### Theory & Learning
- Interactive theory page with steganography concepts
- Visual demonstrations and examples
- Scroll progress indicator
- Smooth animations and modern UI

### Assessment System
- **MCQ Test**: 10 multiple-choice questions (50 points)
- **Crossword Test**: 10 steganography terms (50 points)
- Combined scoring system (100 points total)
- Certificate generation (PDF & PNG formats)
- Back navigation prevention on result page

### User Management
- Secure signup with Supabase authentication
- Auto-generated password feature
- User data stored in Supabase database
- Email-based user identification

### Admin Dashboard
- Secure admin login (`/admin`)
- View all registered users
- Test results tracking
- Password vault for generated passwords
- CSV export functionality
- Real-time statistics (total users, tests taken, average score)
- Tabbed interface (Test Results | Password Vault)

## 🚀 Tech Stack

### Frontend
- **Vite + React** - Main application (localhost:5173)
- **Next.js 16** - OAuth & Admin panel (localhost:3000)
- **HTML/CSS/JavaScript** - Theory page
- **html2canvas & jsPDF** - Certificate generation

### Backend & Database
- **Supabase** - Authentication & Database
- **PostgreSQL** - Data storage (via Supabase)
- **localStorage** - Fallback storage

### Libraries
- Lucide React - Icons
- React Router - Navigation

## 📁 Project Structure

```
CS T1/
├── index.html              # Theory page (entry point)
├── src/
│   ├── components/
│   │   ├── MCQ/           # Multiple choice test
│   │   ├── Crossword/     # Crossword test
│   │   └── Result/        # Results & certificate
│   ├── lib/
│   │   └── supabase.js    # Supabase client (Vite)
│   ├── style.css          # Main styles
│   └── main.js            # App entry point
├── Oauth/
│   ├── app/
│   │   ├── page.tsx       # Signup page
│   │   └── admin/
│   │       ├── page.tsx           # Admin login
│   │       └── dashboard/
│   │           └── page.tsx       # Admin dashboard
│   ├── lib/
│   │   └── supabaseClient.ts     # Supabase client (Next.js)
│   └── .env.local         # Supabase credentials
├── .env                   # Vite environment variables
└── supabase_setup.sql     # Database schema

```

## 🛠️ Setup Instructions

### Prerequisites
- Node.js 16+ and npm
- Supabase account (optional, app works with localStorage)

### Installation

1. **Clone the repository**
```bash
git clone <your-repo-url>
cd "CS T1"
```

2. **Install dependencies**
```bash
# Install Vite app dependencies
npm install

# Install OAuth app dependencies
cd Oauth
npm install
cd ..
```

3. **Configure Environment Variables**

Create `.env` in root directory:
```env
VITE_SUPABASE_URL=your_supabase_url
VITE_SUPABASE_ANON_KEY=your_supabase_anon_key
```

Update `Oauth/.env.local`:
```env
NEXT_PUBLIC_SUPABASE_URL=your_supabase_url
NEXT_PUBLIC_SUPABASE_ANON_KEY=your_supabase_anon_key
```

4. **Set up Supabase Database (Optional)**
```bash
# Run the SQL script in Supabase SQL Editor
# File: supabase_setup.sql
```

### Running the Application

**Terminal 1 - Vite App:**
```bash
npm run dev
```
Access at: http://localhost:5173

**Terminal 2 - OAuth App:**
```bash
cd Oauth
npm run dev
```
Access at: http://localhost:3000

## 🎮 Usage

### For Students

1. **Learn**: Visit http://localhost:5173/index.html
2. **Login**: Click "Login" button → redirects to signup
3. **Sign Up**: Create account with auto-generated password
4. **Take Tests**: Complete MCQ and Crossword tests
5. **Get Certificate**: Download results as PDF or PNG

### For Admins

1. **Login**: Visit http://localhost:3000/admin
2. **Credentials**:
   - Email: `admin@steganography.com`
   - Password: `Admin@123`
3. **Dashboard**: View users, scores, and export data
4. **Password Vault**: View all generated passwords

## 📊 Database Schema

### Tables

**users**
- id (UUID, primary key)
- email (unique)
- first_name, last_name
- mobile
- user_id (Supabase auth ID)
- registered_at

**test_results**
- id (UUID, primary key)
- user_email (foreign key)
- score, mcq_score, crossword_score
- total_score (default: 100)
- completed_at

**generated_passwords**
- id (UUID, primary key)
- email (unique)
- password
- created_at

## 🔒 Security Features

- Supabase authentication
- Row Level Security (RLS) policies
- Admin-only routes
- Password generation and vault
- Environment variable protection
- Back navigation prevention on results

## 📝 Features Checklist

- ✅ Theory page with interactive content
- ✅ User signup with Supabase auth
- ✅ Password generator
- ✅ MCQ test (10 questions)
- ✅ Crossword test (10 words)
- ✅ Combined scoring system
- ✅ Certificate generation (PDF/PNG)
- ✅ Admin authentication
- ✅ Admin dashboard with statistics
- ✅ Password vault
- ✅ CSV export
- ✅ Supabase database integration
- ✅ localStorage fallback
- ✅ Back navigation prevention

## 🚧 Known Issues

- Cross-origin localStorage limitation (solved with Supabase)
- Windows download notifications show blob IDs (files save correctly)

## 📄 License

This project is for educational purposes.

## 👥 Contributors

- Your Name - Initial work

## 🙏 Acknowledgments

- Supabase for backend infrastructure
- Next.js and Vite teams
- React community
