
import React, { useState } from 'react';
import { AppView, UserRole } from '../types';

interface HeaderProps {
    activeView: AppView;
    onNavigate: (view: AppView) => void;
    hasActiveBlueprint: boolean;
    theme: 'dark' | 'light';
    onToggleTheme: () => void;
    storyCredits: number;
    imageCredits: number;
    onSignOut: () => void;
    role: UserRole;
}

const Header: React.FC<HeaderProps> = ({
    activeView,
    onNavigate,
    hasActiveBlueprint,
    theme,
    onToggleTheme,
    storyCredits,
    imageCredits,
    onSignOut,
    role
}) => {
    const [showProfileMenu, setShowProfileMenu] = useState(false);

    const scrollToBlueprint = () => {
        document.getElementById('results-view')?.scrollIntoView({ behavior: 'smooth' });
    };

    return (
        <header className="py-4 px-4 mb-8 border-b border-white/10 glass-panel sticky top-0 z-50">
            <div className="max-w-7xl mx-auto flex items-center justify-between">
                <div className="flex items-center space-x-3 cursor-pointer group" onClick={() => onNavigate('GENERATOR')}>
                    <div className="w-9 h-9 bg-gradient-to-tr from-red-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-red-500/20 group-hover:scale-105 transition-transform">
                        <span className="text-white font-bold text-lg">C</span>
                    </div>
                    <h1 className="text-xl font-extrabold tracking-tight hidden sm:block">
                        Creator<span className="gradient-text">Flow</span>
                    </h1>
                </div>

                <nav className="hidden lg:flex items-center space-x-2 text-sm font-semibold">
                    <button
                        onClick={() => onNavigate('GENERATOR')}
                        className={`transition-all flex items-center space-x-1.5 px-3 py-1.5 rounded-lg ${activeView === 'GENERATOR' ? 'bg-blue-500/10 text-blue-400' : 'text-slate-400 hover:text-blue-400'}`}
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        <span>Architect</span>
                    </button>

                    <button
                        onClick={() => onNavigate('LIBRARY')}
                        className={`transition-all flex items-center space-x-1.5 px-3 py-1.5 rounded-lg ${activeView === 'LIBRARY' ? 'bg-blue-500/10 text-blue-400' : 'text-slate-400 hover:text-blue-400'}`}
                    >
                        <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                        <span>Library</span>
                    </button>

                    {role === 'ADMIN' && (
                        <button
                            onClick={() => onNavigate('ADMIN_PANEL')}
                            className={`transition-all flex items-center space-x-1.5 px-3 py-1.5 rounded-lg ${activeView === 'ADMIN_PANEL' ? 'bg-purple-500/10 text-purple-400' : 'text-slate-400 hover:text-purple-400'}`}
                        >
                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            <span>Command Center</span>
                        </button>
                    )}

                    {hasActiveBlueprint && (
                        <button
                            onClick={scrollToBlueprint}
                            className="text-indigo-400 hover:text-indigo-300 transition-colors flex items-center space-x-1.5 animate-pulse ml-4"
                        >
                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" /></svg>
                            <span>Blueprint Live</span>
                        </button>
                    )}
                </nav>

                <div className="flex items-center space-x-3 lg:space-x-4">
                    <div className="hidden md:flex items-center space-x-2 bg-slate-900/40 p-1 rounded-full border border-white/5">
                        <div className="flex items-center space-x-1.5 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20" title="Story Credits">
                            <span className="text-[10px]">✨</span>
                            <span className="text-[10px] font-bold text-indigo-400">{storyCredits}</span>
                        </div>
                        <div className="flex items-center space-x-1.5 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20" title="Image Credits">
                            <span className="text-[10px]">🖼️</span>
                            <span className="text-[10px] font-bold text-blue-400">{imageCredits}</span>
                        </div>
                    </div>

                    <button
                        onClick={onToggleTheme}
                        className="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-900/40 border border-white/5 hover:border-blue-500/50 transition-all"
                    >
                        {theme === 'dark' ? (
                            <svg className="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        ) : (
                            <svg className="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        )}
                    </button>

                    <div className="relative">
                        <button
                            onClick={() => setShowProfileMenu(!showProfileMenu)}
                            className="w-9 h-9 rounded-full bg-gradient-to-tr from-slate-700 to-slate-800 border-2 border-white/10 flex items-center justify-center overflow-hidden hover:scale-105 transition-transform"
                        >
                            <img src={`https://ui-avatars.com/api/?name=${role}&background=random`} alt="User Profile" />
                        </button>

                        {showProfileMenu && (
                            <>
                                <div className="fixed inset-0 z-10" onClick={() => setShowProfileMenu(false)} />
                                <div className="absolute right-0 mt-3 w-56 rounded-2xl glass-panel shadow-2xl z-20 overflow-hidden animate-in fade-in zoom-in-95 origin-top-right">
                                    <div className="p-4 border-b border-white/5 bg-white/5">
                                        <p className="text-xs font-bold text-slate-500 uppercase tracking-widest">Logged in as</p>
                                        <p className="text-sm font-bold text-blue-400 mt-1">{role} Profile</p>
                                    </div>
                                    <div className="p-2">
                                        <button
                                            onClick={() => { onNavigate('USER_PANEL'); setShowProfileMenu(false); }}
                                            className="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-white/5 transition-colors flex items-center space-x-3"
                                        >
                                            <svg className="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            <span>Creator Dashboard</span>
                                        </button>
                                        <button className="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-white/5 transition-colors flex items-center space-x-3">
                                            <svg className="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                            <span>Billing & Analytics</span>
                                        </button>
                                        <div className="h-px bg-white/5 my-2" />
                                        <button
                                            onClick={onSignOut}
                                            className="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-red-500/10 text-red-400 transition-colors flex items-center space-x-3"
                                        >
                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                            <span>Sign Out</span>
                                        </button>
                                    </div>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </header>
    );
};

export default Header;
