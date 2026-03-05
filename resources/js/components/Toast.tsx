
import React, { useEffect, useState } from 'react';

interface ToastProps {
    message: string;
    type?: 'error' | 'info' | 'success';
    onDismiss: () => void;
    duration?: number;
}

const Toast: React.FC<ToastProps> = ({ message, type = 'error', onDismiss, duration = 5000 }) => {
    const [progress, setProgress] = useState(100);

    useEffect(() => {
        const startTime = Date.now();
        const interval = setInterval(() => {
            const elapsed = Date.now() - startTime;
            const remaining = Math.max(0, 100 - (elapsed / duration) * 100);
            setProgress(remaining);
            if (remaining === 0) {
                clearInterval(interval);
                onDismiss();
            }
        }, 10);

        return () => clearInterval(interval);
    }, [duration, onDismiss]);

    const icons = {
        error: (
            <svg className="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        ),
        info: (
            <svg className="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        ),
        success: (
            <svg className="w-6 h-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        ),
    };

    const bgColors = {
        error: 'border-red-500/30 bg-red-500/10 shadow-red-500/10',
        info: 'border-blue-500/30 bg-blue-500/10 shadow-blue-500/10',
        success: 'border-green-500/30 bg-green-500/10 shadow-green-500/10',
    };

    const progressColors = {
        error: 'bg-red-500',
        info: 'bg-blue-500',
        success: 'bg-green-500',
    };

    return (
        <div className={`fixed bottom-8 right-8 z-[100] max-w-sm w-full animate-in fade-in slide-in-from-right-10 duration-500`}>
            <div className={`glass-panel border p-5 rounded-2xl shadow-2xl relative overflow-hidden flex items-start space-x-4 ${bgColors[type]}`}>
                <div className="flex-shrink-0 mt-0.5">
                    {icons[type]}
                </div>
                <div className="flex-grow">
                    <p className="text-sm font-medium text-slate-200 leading-relaxed pr-6">
                        {message}
                    </p>
                </div>
                <button
                    onClick={onDismiss}
                    className="absolute top-4 right-4 text-slate-500 hover:text-white transition-colors"
                >
                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {/* Progress bar */}
                <div className="absolute bottom-0 left-0 h-1 w-full bg-slate-800/50">
                    <div
                        className={`h-full transition-all ease-linear ${progressColors[type]}`}
                        style={{ width: `${progress}%` }}
                    />
                </div>
            </div>
        </div>
    );
};

export default Toast;
