
import React, { useState } from 'react';

const AdminPanel: React.FC = () => {
    const [thinkingBudget, setThinkingBudget] = useState(25000);

    const metrics = [
        { label: 'System Uptime', value: '99.98%', icon: '⚡' },
        { label: 'Global Tokens/Sec', value: '1,422', icon: '🧠' },
        { label: 'Daily Gen Volume', value: '14k', icon: '🎞️' },
        { label: 'Revenue (MRR)', value: '$45,200', icon: '💎' },
    ];

    return (
        <div className="space-y-10 animate-in fade-in slide-in-from-bottom-6 duration-700">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h2 className="text-3xl font-black">Command <span className="text-purple-500">Center</span></h2>
                    <p className="text-slate-500 text-sm font-medium">Global System Oversight & Configuration</p>
                </div>
                <div className="flex space-x-3">
                    <span className="flex items-center space-x-2 bg-green-500/10 text-green-400 px-4 py-2 rounded-xl text-xs font-bold border border-green-500/20">
                        <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse" />
                        <span>GEMINI PRO ACTIVE</span>
                    </span>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {metrics.map((m, i) => (
                    <div key={i} className="glass-panel p-6 rounded-3xl border border-purple-500/10 bg-gradient-to-tr from-purple-500/5 to-transparent">
                        <div className="flex items-center justify-between mb-4">
                            <span className="text-2xl">{m.icon}</span>
                            <svg className="w-4 h-4 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </div>
                        <p className="text-sm font-bold text-slate-500 uppercase tracking-widest">{m.label}</p>
                        <p className="text-3xl font-black mt-1">{m.value}</p>
                    </div>
                ))}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div className="lg:col-span-4 glass-panel p-8 rounded-3xl border border-white/5 space-y-6">
                    <h3 className="text-xl font-bold flex items-center space-x-2">
                        <svg className="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                        <span>Model Config</span>
                    </h3>

                    <div className="space-y-4">
                        <div className="space-y-2">
                            <div className="flex justify-between text-xs font-bold uppercase tracking-widest text-slate-500">
                                <span>Thinking Budget</span>
                                <span className="text-purple-400">{thinkingBudget} Tokens</span>
                            </div>
                            <input
                                type="range" min="0" max="32768" step="1024"
                                value={thinkingBudget}
                                onChange={(e) => setThinkingBudget(parseInt(e.target.value))}
                                className="w-full accent-purple-500"
                            />
                        </div>

                        <div className="space-y-2">
                            <label className="text-xs font-bold uppercase tracking-widest text-slate-500 block">System Instruction Overlay</label>
                            <textarea
                                className="w-full bg-slate-900 border border-white/10 rounded-xl p-4 text-xs font-mono text-slate-400 outline-none focus:border-purple-500/50 h-32"
                                defaultValue="Always prioritize cinematic pacing and retention hooks in true crime niches. Ensure thumbnail concepts use contrasting colors."
                            />
                        </div>
                    </div>
                </div>

                <div className="lg:col-span-8 glass-panel p-8 rounded-3xl border border-white/5 space-y-6 overflow-hidden">
                    <h3 className="text-xl font-bold">Top Performing Creators</h3>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="text-[10px] font-black uppercase text-slate-500 border-b border-white/5">
                                    <th className="pb-4">Creator</th>
                                    <th className="pb-4">Active Projects</th>
                                    <th className="pb-4">Credits Spent</th>
                                    <th className="pb-4">Plan Status</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-white/5">
                                {[
                                    { name: 'Alex Media', p: 12, s: 450, t: 'Enterprise' },
                                    { name: 'Stoic Journey', p: 8, s: 220, t: 'Founders' },
                                    { name: 'Tech Radar', p: 15, s: 890, t: 'Pro' },
                                    { name: 'Crypto King', p: 5, s: 150, t: 'Standard' },
                                ].map((u, i) => (
                                    <tr key={i} className="group hover:bg-white/5 transition-colors">
                                        <td className="py-4 text-sm font-bold text-slate-200">{u.name}</td>
                                        <td className="py-4 text-sm text-slate-400">{u.p}</td>
                                        <td className="py-4 text-sm text-slate-400">{u.s}</td>
                                        <td className="py-4">
                                            <span className="text-[10px] font-bold bg-purple-500/10 text-purple-400 px-2 py-1 rounded-full">{u.t}</span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default AdminPanel;
