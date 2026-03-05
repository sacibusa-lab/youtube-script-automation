
import React from 'react';

interface UserPanelProps {
    storyCredits: number;
    imageCredits: number;
}

const UserPanel: React.FC<UserPanelProps> = ({ storyCredits, imageCredits }) => {
    const stats = [
        { label: 'Total Narratives', value: '24', icon: '📝', change: '+12%' },
        { label: 'Avg Retention Rate', value: '88%', icon: '📈', change: '+5%' },
        { label: 'Monthly Growth', value: '4.2k', icon: '🚀', change: '+22%' },
        { label: 'Empire Worth', value: '$12k', icon: '💰', change: '+8%' },
    ];

    return (
        <div className="space-y-10 animate-in fade-in slide-in-from-bottom-6 duration-700">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {stats.map((stat, i) => (
                    <div key={i} className="glass-panel p-6 rounded-3xl border border-white/5">
                        <div className="flex items-center justify-between mb-4">
                            <span className="text-2xl">{stat.icon}</span>
                            <span className="text-[10px] font-bold text-green-400 bg-green-400/10 px-2 py-1 rounded-full">{stat.change}</span>
                        </div>
                        <p className="text-sm font-bold text-slate-500 uppercase tracking-widest">{stat.label}</p>
                        <p className="text-3xl font-black mt-1">{stat.value}</p>
                    </div>
                ))}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div className="lg:col-span-8 glass-panel p-8 rounded-3xl border border-white/5 space-y-6">
                    <div className="flex items-center justify-between">
                        <h3 className="text-xl font-bold">Credit Consumption Radar</h3>
                        <button className="text-xs font-bold text-blue-400 hover:text-blue-300 transition-colors uppercase tracking-widest">Upgrade Plan</button>
                    </div>

                    <div className="h-64 flex items-end justify-between space-x-4 pt-10">
                        {[45, 78, 56, 90, 34, 67, 89].map((h, i) => (
                            <div key={i} className="flex-1 space-y-2">
                                <div
                                    className="bg-gradient-to-t from-blue-600 to-indigo-500 rounded-t-xl transition-all duration-1000 ease-out hover:opacity-80"
                                    style={{ height: `${h}%` }}
                                />
                                <p className="text-[10px] text-center text-slate-500 font-bold uppercase">Day {i + 1}</p>
                            </div>
                        ))}
                    </div>
                </div>

                <div className="lg:col-span-4 glass-panel p-8 rounded-3xl border border-white/5 space-y-6">
                    <h3 className="text-xl font-bold">Project Pulse</h3>
                    <div className="space-y-4">
                        {[
                            { title: 'The Dark AI Secret', status: 'Generating', p: 45, color: 'bg-blue-500' },
                            { title: 'Finance 2025 Deep Dive', status: 'Complete', p: 100, color: 'bg-green-500' },
                            { title: 'Stoic Habits Vlog', status: 'In Library', p: 100, color: 'bg-indigo-500' },
                        ].map((p, i) => (
                            <div key={i} className="space-y-2">
                                <div className="flex justify-between text-xs font-bold">
                                    <span className="text-slate-300">{p.title}</span>
                                    <span className="text-slate-500 uppercase">{p.status}</span>
                                </div>
                                <div className="w-full h-1.5 bg-slate-900 rounded-full overflow-hidden">
                                    <div className={`h-full ${p.color} transition-all duration-1000`} style={{ width: `${p.p}%` }} />
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default UserPanel;
