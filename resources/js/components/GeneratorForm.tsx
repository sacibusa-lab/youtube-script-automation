
import React from 'react';
import { Niche, VideoLength, TargetCountry, GenerationMode } from '../types';
import { NICHES, VIDEO_LENGTHS, COUNTRIES } from '../constants';

interface GeneratorFormProps {
    niche: Niche;
    setNiche: (n: Niche) => void;
    length: VideoLength;
    setLength: (l: VideoLength) => void;
    country: TargetCountry;
    setCountry: (c: TargetCountry) => void;
    topic: string;
    setTopic: (t: string) => void;
    mode: GenerationMode;
    setMode: (m: GenerationMode) => void;
    onGenerate: () => void;
    onLoadLast: () => void;
    isLoading: boolean;
}

const GeneratorForm: React.FC<GeneratorFormProps> = ({
    niche, setNiche,
    length, setLength,
    country, setCountry,
    topic, setTopic,
    mode, setMode,
    onGenerate,
    onLoadLast,
    isLoading
}) => {
    const presets = [
        { label: 'Viral Shorts (30s)', value: VideoLength.SHORTS, icon: '⚡' },
        { label: 'Tutorials (5m)', value: VideoLength.MIN5, icon: '🎓' },
        { label: 'Deep Dives (15m)', value: VideoLength.MIN15, icon: '🕵️' },
    ];

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onGenerate();
    };

    return (
        <div className="space-y-4">
            <div className="flex justify-end">
                <button
                    onClick={onLoadLast}
                    className="text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-blue-400 transition-colors flex items-center space-x-1.5"
                >
                    <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    <span>Load Last Project</span>
                </button>
            </div>

            <form onSubmit={handleSubmit} className="space-y-6 glass-panel p-8 rounded-2xl shadow-2xl relative overflow-hidden">
                <div className="flex bg-slate-900/50 p-1 rounded-xl w-full border border-white/5 mb-6">
                    <button
                        type="button"
                        onClick={() => setMode('SINGLE')}
                        className={`flex-1 py-3 rounded-lg text-sm font-bold transition-all flex items-center justify-center space-x-2 ${mode === 'SINGLE' ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:text-white'
                            }`}
                    >
                        <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 110 4v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 110-4V6z" /></svg>
                        <span>Single Video Strategy</span>
                    </button>
                    <button
                        type="button"
                        onClick={() => setMode('MONTHLY')}
                        className={`flex-1 py-3 rounded-lg text-sm font-bold transition-all flex items-center justify-center space-x-2 ${mode === 'MONTHLY' ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-white'
                            }`}
                    >
                        <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clipRule="evenodd" /></svg>
                        <span>30-Day Content Blueprint</span>
                    </button>
                </div>

                <div className="space-y-3">
                    <label className="text-xs font-bold text-slate-500 uppercase tracking-widest block">Quick Presets</label>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        {presets.map((preset) => (
                            <button
                                key={preset.value}
                                type="button"
                                disabled={mode === 'MONTHLY'}
                                onClick={() => setLength(preset.value)}
                                className={`p-3 rounded-xl border text-left transition-all flex flex-col items-start ${mode === 'MONTHLY'
                                        ? 'opacity-50 cursor-not-allowed border-white/5'
                                        : length === preset.value
                                            ? 'bg-blue-600/10 border-blue-500 ring-1 ring-blue-500/20'
                                            : 'bg-slate-800/30 border-white/10 hover:border-white/30'
                                    }`}
                            >
                                <span className="text-xl mb-1">{preset.icon}</span>
                                <span className={`text-xs font-bold ${length === preset.value ? 'text-blue-400' : 'text-slate-300'}`}>
                                    {preset.label}
                                </span>
                            </button>
                        ))}
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="space-y-2">
                        <label className="text-sm font-semibold text-slate-400">Niche / Category</label>
                        <select
                            value={niche}
                            onChange={(e) => setNiche(e.target.value as Niche)}
                            className="w-full bg-slate-800/50 border border-white/10 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all"
                        >
                            {NICHES.map(n => <option key={n} value={n}>{n}</option>)}
                        </select>
                    </div>

                    <div className="space-y-2">
                        <label className="text-sm font-semibold text-slate-400">Target Region</label>
                        <select
                            value={country}
                            onChange={(e) => setCountry(e.target.value as TargetCountry)}
                            className="w-full bg-slate-800/50 border border-white/10 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all"
                        >
                            {COUNTRIES.map(c => <option key={c} value={c}>{c}</option>)}
                        </select>
                    </div>

                    <div className="space-y-2">
                        <label className="text-sm font-semibold text-slate-400">Custom Format</label>
                        <select
                            disabled={mode === 'MONTHLY'}
                            value={length}
                            onChange={(e) => setLength(e.target.value as VideoLength)}
                            className={`w-full bg-slate-800/50 border border-white/10 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all ${mode === 'MONTHLY' ? 'opacity-50 cursor-not-allowed' : ''
                                }`}
                        >
                            {VIDEO_LENGTHS.map(l => <option key={l} value={l}>{l}</option>)}
                        </select>
                    </div>
                </div>

                <div className="space-y-2">
                    <label className="text-sm font-semibold text-slate-400">Main Topic or Keywords (Optional)</label>
                    <input
                        type="text"
                        placeholder="e.g. AI tools for 2025, Healthy keto recipes..."
                        value={topic}
                        onChange={(e) => setTopic(e.target.value)}
                        className="w-full bg-slate-800/50 border border-white/10 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all"
                    />
                </div>

                <button
                    type="submit"
                    disabled={isLoading}
                    className={`w-full py-4 rounded-xl font-bold text-lg transition-all transform active:scale-[0.98] flex items-center justify-center space-x-2 shadow-lg shadow-blue-500/20 ${isLoading
                            ? 'bg-slate-700 cursor-not-allowed text-slate-400'
                            : mode === 'SINGLE'
                                ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white'
                                : 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white'
                        }`}
                >
                    {isLoading ? (
                        <>
                            <svg className="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Architecting Content...</span>
                        </>
                    ) : (
                        <>
                            <span>{mode === 'SINGLE' ? 'Generate Full Strategy' : 'Generate 30-Day Blueprint'}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clipRule="evenodd" />
                            </svg>
                        </>
                    )}
                </button>
            </form>
        </div>
    );
};

export default GeneratorForm;
