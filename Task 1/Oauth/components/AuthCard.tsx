'use client';

import { motion } from 'framer-motion';
import { ReactNode } from 'react';

interface AuthCardProps {
    children: ReactNode;
    title: string;
    subtitle: string;
}

export default function AuthCard({ children, title, subtitle }: AuthCardProps) {
    return (
        <div className="min-h-screen flex items-center justify-center p-4">
            <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5 }}
                className="w-full max-w-md glass-card p-8 sm:p-10 relative overflow-hidden"
            >
                {/* Decorative elements */}
                <div className="absolute -top-20 -right-20 w-40 h-40 bg-pink-500/10 rounded-full blur-3xl pointer-events-none" />
                <div className="absolute -bottom-20 -left-20 w-40 h-40 bg-violet-600/10 rounded-full blur-3xl pointer-events-none" />

                <div className="relative z-10">
                    <div className="text-center mb-8">
                        <h1 className="text-3xl font-bold mb-2 gradient-text">{title}</h1>
                        <p className="text-gray-500 text-sm">{subtitle}</p>
                    </div>
                    {children}
                </div>
            </motion.div>
        </div>
    );
}
