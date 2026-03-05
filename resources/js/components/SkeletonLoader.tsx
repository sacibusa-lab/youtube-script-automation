
import React from 'react';

const SkeletonLoader: React.FC = () => {
    return (
        <div className="animate-pulse space-y-8">
            <div className="flex space-x-2">
                <div className="h-10 w-32 bg-slate-800 rounded-xl"></div>
                <div className="h-10 w-32 bg-slate-800 rounded-xl"></div>
            </div>
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div className="space-y-6">
                    <div className="h-64 bg-slate-800/50 rounded-2xl"></div>
                    <div className="h-48 bg-slate-800/50 rounded-2xl"></div>
                </div>
                <div className="h-[600px] bg-slate-800/50 rounded-2xl"></div>
            </div>
        </div>
    );
};

export default SkeletonLoader;
