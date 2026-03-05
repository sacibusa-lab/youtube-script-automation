
import React, { useState, useEffect } from 'react';
import { MonthlyPlan, MonthlyVideoIdea } from '../types';

interface MonthlyCalendarViewProps {
    plan: MonthlyPlan;
}

const IdeaCard: React.FC<{
    idea: MonthlyVideoIdea;
    isCompleted: boolean;
    onToggle: () => void;
}> = ({ idea, isCompleted, onToggle }) => {
    return (
        <div className={`relative border transition-all duration-300 group cursor-default p-5 rounded-2xl ${isCompleted
                ? 'bg-green-500/10 border-green-500/30 opacity-75'
                : 'bg-slate-800/50 border-white/5 hover:border-indigo-500/50'
            }`}>
            <button
                onClick={(e) => {
                    e.stopPropagation();
                    onToggle();
                }}
                className={`absolute top-4 right-4 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all ${isCompleted
                        ? 'bg-green-500 border-green-500 text-white'
                        : 'border-slate-600 hover:border-indigo-400 bg-transparent'
                    }`}
            >
                {isCompleted && (
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd" />
                    </svg>
                )}
            </button>

            <div className="flex justify-between items-start mb-3 pr-8">
                <span className={`text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded transition-colors ${isCompleted ? 'text-green-400 bg-green-400/10' : 'text-indigo-400 bg-indigo-400/10'
                    }`}>
                    Week {idea.week} • {idea.day}
                </span>
                <span className="text-[10px] font-bold text-slate-500 uppercase">{idea.objective}</span>
            </div>

            <h4 className={`text-lg font-bold mb-3 transition-colors line-clamp-2 ${isCompleted ? 'text-green-100' : 'text-white group-hover:text-indigo-300'
                }`}>
                {idea.title}
            </h4>

            <div className="space-y-4">
                <div>
                    <p className="text-[10px] font-bold text-slate-500 uppercase tracking-tighter mb-1">Visual Concept</p>
                    <p className="text-xs text-slate-400 italic line-clamp-2 leading-relaxed">"{idea.thumbnailConcept}"</p>
                </div>
                <div>
                    <p className="text-[10px] font-bold text-slate-500 uppercase tracking-tighter mb-1">Outline</p>
                    <ul className="space-y-1">
                        {idea.outline.slice(0, 3).map((item, idx) => (
                            <li key={idx} className="text-[11px] text-slate-300 flex items-start">
                                <span className={`mr-2 ${isCompleted ? 'text-green-500' : 'text-indigo-500'}`}>•</span>
                                <span className="line-clamp-1">{item}</span>
                            </li>
                        ))}
                        {idea.outline.length > 3 && <li className="text-[10px] text-slate-500 italic">+ more details</li>}
                    </ul>
                </div>
            </div>
        </div>
    );
};

const MonthlyCalendarView: React.FC<MonthlyCalendarViewProps> = ({ plan }) => {
    const [isCopied, setIsCopied] = useState(false);
    const [completedIds, setCompletedIds] = useState<Set<string>>(new Set());
    const weeks = [1, 2, 3, 4];

    // Unique key for storage based on the plan title
    const storageKey = `creatorflow_progress_${plan.strategyName.replace(/\s+/g, '_').toLowerCase()}`;

    // Load persistence
    useEffect(() => {
        const saved = localStorage.getItem(storageKey);
        if (saved) {
            try {
                setCompletedIds(new Set(JSON.parse(saved)));
            } catch (e) {
                console.error("Failed to parse progress storage", e);
            }
        }
    }, [storageKey]);

    // Save persistence
    useEffect(() => {
        localStorage.setItem(storageKey, JSON.stringify(Array.from(completedIds)));
    }, [completedIds, storageKey]);

    const toggleIdea = (ideaTitle: string) => {
        setCompletedIds(prev => {
            const next = new Set(prev);
            if (next.has(ideaTitle)) next.delete(ideaTitle);
            else next.add(ideaTitle);
            return next;
        });
    };

    const completionRate = Math.round((completedIds.size / plan.ideas.length) * 100);

    const copyToClipboard = () => {
        let text = `--- 30-DAY CONTENT BLUEPRINT: ${plan.strategyName} ---\n`;
        text += `Completion Progress: ${completionRate}%\n\n`;
        text += `STRATEGY OVERVIEW:\n${plan.strategyOverview}\n\n`;

        plan.ideas.forEach(idea => {
            const status = completedIds.has(idea.title) ? '[COMPLETED] ' : '[ ] ';
            text += `${status}[WEEK ${idea.week} - ${idea.day}]\n`;
            text += `Title: ${idea.title}\n`;
            text += `Thumbnail Concept: ${idea.thumbnailConcept}\n`;
            text += `Objective: ${idea.objective}\n`;
            text += `Outline:\n`;
            idea.outline.forEach(o => text += `  - ${o}\n`);
            text += `\n`;
        });

        navigator.clipboard.writeText(text);
        setIsCopied(true);
        setTimeout(() => setIsCopied(false), 2000);
    };

    return (
        <div className="space-y-8 animate-in fade-in slide-in-from-bottom-6 duration-700">
            <div className="glass-panel p-8 rounded-3xl border border-indigo-500/20 bg-gradient-to-br from-indigo-900/10 to-transparent">
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                    <div className="space-y-2">
                        <h3 className="text-2xl font-bold">{plan.strategyName}</h3>
                        <p className="text-slate-400 leading-relaxed max-w-4xl">{plan.strategyOverview}</p>
                    </div>
                    <button
                        onClick={copyToClipboard}
                        className={`flex items-center justify-center space-x-2 px-6 py-3 rounded-xl font-bold transition-all border shrink-0 ${isCopied
                                ? 'bg-green-500/10 border-green-500/50 text-green-400'
                                : 'bg-indigo-600 border-indigo-500 text-white hover:bg-indigo-500 shadow-lg shadow-indigo-600/20'
                            }`}
                    >
                        {isCopied ? (
                            <>
                                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" /></svg>
                                <span>Copied Blueprint!</span>
                            </>
                        ) : (
                            <>
                                <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                <span>Copy Full Blueprint</span>
                            </>
                        )}
                    </button>
                </div>

                <div className="space-y-2">
                    <div className="flex justify-between items-end">
                        <span className="text-xs font-bold text-slate-500 uppercase tracking-widest">Global Roadmap Progress</span>
                        <span className="text-lg font-black text-indigo-400">{completionRate}%</span>
                    </div>
                    <div className="w-full h-3 bg-slate-900 rounded-full overflow-hidden border border-white/5">
                        <div
                            className="h-full bg-gradient-to-r from-indigo-600 to-blue-400 transition-all duration-1000 ease-out"
                            style={{ width: `${completionRate}%` }}
                        />
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {weeks.map(week => (
                    <div key={week} className="space-y-4">
                        <h4 className="text-sm font-bold text-slate-500 uppercase tracking-widest border-b border-white/5 pb-2">Week {week}</h4>
                        <div className="space-y-4">
                            {plan.ideas.filter(i => i.week === week).map((idea, idx) => (
                                <IdeaCard
                                    key={idx}
                                    idea={idea}
                                    isCompleted={completedIds.has(idea.title)}
                                    onToggle={() => toggleIdea(idea.title)}
                                />
                            ))}
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default MonthlyCalendarView;
