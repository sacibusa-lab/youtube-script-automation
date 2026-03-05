
import React, { useState, useEffect } from 'react';
import { VideoStrategy } from '../types';

interface LibraryViewProps {
    onRestore: (strategy: VideoStrategy) => void;
}

const LibraryView: React.FC<LibraryViewProps> = ({ onRestore }) => {
    const [items, setItems] = useState<VideoStrategy[]>([]);

    useEffect(() => {
        const saved = localStorage.getItem('creatorflow_full_library');
        if (saved) setItems(JSON.parse(saved));
    }, []);

    const handleDelete = (title: string) => {
        const next = items.filter(i => i.title !== title);
        setItems(next);
        localStorage.setItem('creatorflow_full_library', JSON.stringify(next));
    };

    if (items.length === 0) {
        return (
            <div className="text-center py-20 glass-panel rounded-3xl border-dashed border-white/10">
                <div className="bg-slate-800/50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg className="w-10 h-10 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                </div>
                <h3 className="text-2xl font-bold mb-2">Your Library is Empty</h3>
                <p className="text-slate-400 max-w-sm mx-auto">Start architecting content and bookmark your favorite strategies to see them here.</p>
            </div>
        );
    }

    return (
        <div className="space-y-8 animate-in fade-in slide-in-from-bottom-6 duration-700">
            <div className="flex items-center justify-between">
                <h2 className="text-3xl font-black">My <span className="gradient-text">Vault</span></h2>
                <span className="text-xs font-bold text-slate-500 uppercase tracking-widest">{items.length} SAVED STRATEGIES</span>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {items.map((item, idx) => (
                    <div key={idx} className="glass-panel p-6 rounded-2xl flex flex-col h-full border hover:border-blue-500/30 transition-all group">
                        <div className="aspect-video bg-slate-800 rounded-xl mb-4 flex items-center justify-center p-4 text-center relative overflow-hidden">
                            <div className="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent" />
                            <p className="relative z-10 text-lg font-black italic uppercase text-white leading-tight line-clamp-2">
                                {item.thumbnailConcepts[0]?.textOverlay || "PREVIEW"}
                            </p>
                        </div>

                        <h4 className="text-sm font-bold text-white mb-2 line-clamp-2">{item.title}</h4>

                        <div className="flex items-center space-x-2 mb-6">
                            <span className="text-[10px] font-bold bg-blue-500/10 text-blue-400 px-2 py-0.5 rounded border border-blue-500/20">
                                {item.script.length} Scenes
                            </span>
                            <span className="text-[10px] font-bold bg-indigo-500/10 text-indigo-400 px-2 py-0.5 rounded border border-indigo-500/20">
                                Full Script
                            </span>
                        </div>

                        <div className="flex items-center space-x-2 mt-auto pt-4 border-t border-white/5">
                            <button
                                onClick={() => onRestore(item)}
                                className="flex-grow bg-blue-600 hover:bg-blue-500 text-white text-[11px] font-bold uppercase py-2.5 rounded-lg transition-all shadow-lg shadow-blue-500/20"
                            >
                                Restore to Desk
                            </button>
                            <button
                                onClick={() => handleDelete(item.title)}
                                className="p-2.5 bg-white/5 hover:bg-red-500/10 text-slate-400 hover:text-red-400 border border-white/5 rounded-lg transition-all"
                                title="Delete from Library"
                            >
                                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default LibraryView;
