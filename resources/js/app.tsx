
import './bootstrap';
import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import Header from './components/Header';
import GeneratorForm from './components/GeneratorForm';
import OutputView from './components/OutputView';
import MonthlyCalendarView from './components/MonthlyCalendarView';
import LibraryView from './components/LibraryView';
import AdminPanel from './components/AdminPanel';
import UserPanel from './components/UserPanel';
import SkeletonLoader from './components/SkeletonLoader';
import Toast from './components/Toast';
import { generatePlan, generateMonthlyBlueprint } from './services/geminiService';
import {
    Niche, VideoLength, TargetCountry,
    ContentPlan, AppView, UserRole,
    VideoStrategy, GenerationMode, MonthlyPlan
} from './types';

const App: React.FC = () => {
    const [activeView, setActiveView] = useState<AppView>('GENERATOR');
    const [niche, setNiche] = useState<Niche>('Technology & AI');
    const [length, setLength] = useState<VideoLength>(VideoLength.SHORTS);
    const [country, setCountry] = useState<TargetCountry>('United States 🇺🇸');
    const [topic, setTopic] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [plan, setPlan] = useState<ContentPlan | null>(null);
    const [monthlyPlan, setMonthlyPlan] = useState<MonthlyPlan | null>(null);
    const [mode, setMode] = useState<GenerationMode>('SINGLE');

    const [storyCredits, setStoryCredits] = useState(120);
    const [imageCredits, setImageCredits] = useState(25);
    const [theme, setTheme] = useState<'dark' | 'light'>('dark');
    const [showToast, setShowToast] = useState(false);
    const [toastMsg, setToastMsg] = useState('');
    const [role, setRole] = useState<UserRole>('ADMIN');

    useEffect(() => {
        const saved = localStorage.getItem('creatorflow_active_plan');
        const savedMonthly = localStorage.getItem('creatorflow_active_monthly');
        if (saved) setPlan(JSON.parse(saved));
        if (savedMonthly) setMonthlyPlan(JSON.parse(savedMonthly));

        const savedTheme = localStorage.getItem('creatorflow_theme');
        if (savedTheme === 'light') setTheme('light');
    }, []);

    useEffect(() => {
        document.documentElement.className = theme;
        localStorage.setItem('creatorflow_theme', theme);
    }, [theme]);

    const handleGenerate = async () => {
        if (storyCredits <= 0) {
            setToastMsg("Insufficient Credits. Please upgrade your plan.");
            setShowToast(true);
            return;
        }

        setIsLoading(true);
        setPlan(null);
        setMonthlyPlan(null);

        try {
            if (mode === 'SINGLE') {
                const result = await generatePlan(niche, length, country, topic);
                setPlan(result);
                localStorage.setItem('creatorflow_active_plan', JSON.stringify(result));
                setStoryCredits(prev => prev - 1);
            } else {
                const result = await generateMonthlyBlueprint(niche, country, topic);
                setMonthlyPlan(result);
                localStorage.setItem('creatorflow_active_monthly', JSON.stringify(result));
                setStoryCredits(prev => prev - 3);
            }
        } catch (error) {
            setToastMsg("Architecture Error: " + (error as Error).message);
            setShowToast(true);
        } finally {
            setIsLoading(false);
        }
    };

    const handleRestore = (strategy: VideoStrategy) => {
        const mockPlan: ContentPlan = {
            niche: niche,
            targetRegion: country,
            strategies: [strategy]
        };
        setPlan(mockPlan);
        setActiveView('GENERATOR');
        localStorage.setItem('creatorflow_active_plan', JSON.stringify(mockPlan));

        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const handleLoadLast = () => {
        const saved = localStorage.getItem('creatorflow_active_plan');
        if (saved) setPlan(JSON.parse(saved));
        else {
            setToastMsg("No previous project found on this device.");
            setShowToast(true);
        }
    };

    const handleSignOut = () => {
        // In Laravel integration, this would likely trigger a Laravel logout
        setToastMsg("Signing out...");
        setShowToast(true);
        setTimeout(() => {
            window.location.href = '/logout';
        }, 1000);
    };

    return (
        <div className={`min-h-screen transition-colors duration-500 ${theme === 'dark' ? 'bg-[#020617] text-slate-100' : 'bg-slate-50 text-slate-900'}`}>
            <div className="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-blue-600/10 to-transparent pointer-events-none" />

            <Header
                activeView={activeView}
                onNavigate={setActiveView}
                hasActiveBlueprint={!!plan || !!monthlyPlan}
                theme={theme}
                onToggleTheme={() => setTheme(prev => prev === 'dark' ? 'light' : 'dark')}
                storyCredits={storyCredits}
                imageCredits={imageCredits}
                onSignOut={handleSignOut}
                role={role}
            />

            <main className="max-w-7xl mx-auto px-4 pb-20 relative z-10">
                {activeView === 'GENERATOR' && (
                    <div className="space-y-12">
                        <header className="max-w-3xl mx-auto text-center space-y-4">
                            <h2 className="text-4xl md:text-5xl font-black tracking-tight leading-tight">
                                Architect Viral <span className="gradient-text">Empires</span>
                            </h2>
                            <p className="text-slate-400 text-lg md:text-xl font-medium">
                                High-retention video strategies engineered by industrial-grade AI.
                            </p>
                        </header>

                        <div className="max-w-4xl mx-auto">
                            <GeneratorForm
                                niche={niche} setNiche={setNiche}
                                length={length} setLength={setLength}
                                country={country} setCountry={setCountry}
                                topic={topic} setTopic={setTopic}
                                mode={mode} setMode={setMode}
                                onGenerate={handleGenerate}
                                onLoadLast={handleLoadLast}
                                isLoading={isLoading}
                            />
                        </div>

                        {(plan || monthlyPlan || isLoading) && (
                            <div id="results-view" className="pt-20 border-t border-white/5 space-y-12">
                                <div className="flex items-center justify-between mb-8">
                                    <h3 className="text-2xl font-black">Generated <span className="text-blue-500">Output</span></h3>
                                    {(plan || monthlyPlan) && (
                                        <button
                                            onClick={() => {
                                                const saved = localStorage.getItem('creatorflow_full_library') || '[]';
                                                const lib = JSON.parse(saved);
                                                if (plan) lib.push(...plan.strategies);
                                                localStorage.setItem('creatorflow_full_library', JSON.stringify(lib));
                                                setToastMsg("Project archived to your library!");
                                                setShowToast(true);
                                            }}
                                            className="text-xs font-bold text-slate-500 hover:text-blue-400 uppercase tracking-widest flex items-center space-x-2"
                                        >
                                            <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                                            <span>Seal to Library</span>
                                        </button>
                                    )}
                                </div>

                                {isLoading ? (
                                    <SkeletonLoader />
                                ) : (
                                    <>
                                        {mode === 'SINGLE' && plan && (
                                            <OutputView
                                                plan={plan}
                                                useHighQuality={true}
                                                onConsumeImageCredit={() => {
                                                    if (imageCredits > 0) {
                                                        setImageCredits(prev => prev - 1);
                                                        return true;
                                                    }
                                                    setToastMsg("Insufficient Image Credits");
                                                    setShowToast(true);
                                                    return false;
                                                }}
                                                onConsumeStoryCredit={() => {
                                                    if (storyCredits > 0) {
                                                        setStoryCredits(prev => prev - 1);
                                                        return true;
                                                    }
                                                    setToastMsg("Insufficient Story Credits");
                                                    setShowToast(true);
                                                    return false;
                                                }}
                                            />
                                        )}
                                        {mode === 'MONTHLY' && monthlyPlan && (
                                            <MonthlyCalendarView plan={monthlyPlan} />
                                        )}
                                    </>
                                )}
                            </div>
                        )}
                    </div>
                )}

                {activeView === 'LIBRARY' && <LibraryView onRestore={handleRestore} />}
                {activeView === 'ADMIN_PANEL' && <AdminPanel />}
                {activeView === 'USER_PANEL' && <UserPanel storyCredits={storyCredits} imageCredits={imageCredits} />}
            </main>

            {showToast && (
                <Toast
                    message={toastMsg}
                    onDismiss={() => setShowToast(false)}
                    type={toastMsg.includes('Error') ? 'error' : 'success'}
                />
            )}
        </div>
    );
};

const container = document.getElementById('generator-root');
if (container) {
    const root = createRoot(container);
    root.render(<App />);
}

export default App;
