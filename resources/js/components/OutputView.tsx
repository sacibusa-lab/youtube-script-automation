
import React, { useState, useEffect } from 'react';
import { ContentPlan, VideoStrategy, PlatformSpecific, ThumbnailConcept, ScriptSegment } from '../types';
import { generateChapterScript, generateImage } from '../services/geminiService';

interface OutputViewProps {
    plan: ContentPlan;
    onConsumeImageCredit: () => boolean;
    onConsumeStoryCredit: () => boolean;
    useHighQuality: boolean;
}

type AspectRatio = "1:1" | "16:9" | "4:3" | "9:16";

const ImagePreview: React.FC<{
    src: string | null;
    isLoading: boolean;
    onGenerate: (ratio: AspectRatio) => void;
    aspectRatio?: AspectRatio;
    placeholderText?: string;
}> = ({ src, isLoading, onGenerate, placeholderText = "PREVIEW" }) => {
    const [selectedRatio, setSelectedRatio] = useState<AspectRatio>("16:9");

    const ratioClassMap: Record<AspectRatio, string> = {
        "1:1": "aspect-square",
        "16:9": "aspect-video",
        "4:3": "aspect-[4/3]",
        "9:16": "aspect-[9/16]"
    };

    if (isLoading) {
        return (
            <div className={`${ratioClassMap[selectedRatio]} bg-slate-900 rounded-xl flex flex-col items-center justify-center space-y-3 relative overflow-hidden border border-blue-500/20 w-full`}>
                <div className="absolute inset-0 bg-gradient-to-t from-blue-600/10 to-transparent animate-pulse" />
                <svg className="animate-spin h-8 w-8 text-blue-500" viewBox="0 0 24 24"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
        );
    }

    if (src) {
        return (
            <div className={`${ratioClassMap[selectedRatio]} bg-slate-900 rounded-xl relative overflow-hidden group shadow-2xl w-full`}>
                <img src={src} className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Generated Asset" />
                <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <button onClick={() => onGenerate(selectedRatio)} className="bg-white/20 hover:bg-white/40 p-2 rounded-full backdrop-blur-md">
                        <svg className="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </button>
                </div>
            </div>
        );
    }

    return (
        <div className="space-y-3 w-full">
            <div className="flex items-center justify-center space-x-2 p-1 bg-slate-900/50 rounded-lg border border-white/5">
                {(["16:9", "1:1", "9:16"] as AspectRatio[]).map((r) => (
                    <button
                        key={r}
                        onClick={() => setSelectedRatio(r)}
                        className={`px-3 py-1 rounded text-[10px] font-bold transition-all ${selectedRatio === r ? 'bg-blue-600 text-white' : 'text-slate-500 hover:text-slate-300'}`}
                    >
                        {r}
                    </button>
                ))}
            </div>
            <div className={`${ratioClassMap[selectedRatio]} bg-slate-900/50 border border-dashed border-white/10 rounded-xl flex flex-col items-center justify-center p-4 text-center group cursor-pointer hover:border-blue-500/50 transition-all`} onClick={() => onGenerate(selectedRatio)}>
                <svg className="w-6 h-6 text-slate-700 mb-2 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span className="text-[10px] font-bold text-slate-500 uppercase tracking-widest group-hover:text-blue-400 transition-colors">{placeholderText}</span>
            </div>
        </div>
    );
};

const ScriptSegmentCard: React.FC<{
    segment: ScriptSegment;
    index: number;
    isMarathon: boolean;
    onArchitect: (idx: number) => void;
    onUpdate: (idx: number, content: string) => void;
    onGenerateImg: (idx: number, ratio: AspectRatio) => void;
    isArchitecting: boolean;
    isGeneratingImg: boolean;
    imgSrc: string | null;
}> = ({ segment, index, isMarathon, onArchitect, onUpdate, onGenerateImg, isArchitecting, isGeneratingImg, imgSrc }) => {
    const [isEditing, setIsEditing] = useState(false);
    const [localContent, setLocalContent] = useState(segment.content || '');
    const [showProductionDetails, setShowProductionDetails] = useState(false);

    const handleSave = () => {
        onUpdate(index, localContent);
        setIsEditing(false);
    };

    const hasContent = !!segment.content && segment.content.length > 10;

    if (isMarathon && !hasContent) {
        return (
            <div className="relative pl-10 border-l border-white/10 group pb-12">
                <div className="absolute -left-2 top-0 w-4 h-4 rounded-full bg-slate-800 ring-4 ring-slate-900" />
                <div className="bg-black/20 rounded-3xl p-12 text-center space-y-8 border border-white/5">
                    <div className="w-20 h-20 bg-slate-900/50 rounded-full flex items-center justify-center mx-auto border border-dashed border-white/10">
                        <svg className="w-8 h-8 text-slate-700" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" /></svg>
                    </div>
                    <div className="space-y-4">
                        <h3 className="text-2xl font-bold text-white uppercase tracking-tight">Script Segment {index + 1} Needed</h3>
                        <p className="text-slate-400 text-sm max-w-sm mx-auto">Architect this scene to contribute to your 1-hour marathon goal.</p>
                    </div>
                    <button
                        onClick={() => onArchitect(index)}
                        disabled={isArchitecting}
                        className="bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed text-white px-8 py-4 rounded-xl text-xs font-black uppercase tracking-widest transition-all transform active:scale-95 shadow-2xl shadow-blue-500/20"
                    >
                        {isArchitecting ? 'Architecting Chapter...' : `Architect: ${segment.scene}`}
                    </button>
                </div>
            </div>
        );
    }

    return (
        <div className="relative pl-10 border-l border-white/10 group pb-12">
            <div className={`absolute -left-2 top-0 w-4 h-4 rounded-full ring-4 ring-slate-900 ${isArchitecting ? 'bg-red-500 animate-pulse' : 'bg-blue-600'}`} />
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div className="lg:col-span-7 space-y-6">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center space-x-3">
                            <span className="text-[10px] font-black text-blue-500 uppercase tracking-widest bg-blue-500/10 px-3 py-1 rounded">{segment.scene}</span>
                            <span className="text-xs text-slate-500 font-mono">{segment.estimatedTime}</span>
                        </div>
                        <button
                            onClick={() => setShowProductionDetails(!showProductionDetails)}
                            className="text-[10px] font-bold text-slate-500 uppercase hover:text-white transition-colors"
                        >
                            {showProductionDetails ? 'Hide Directives' : 'Show Directives'}
                        </button>
                    </div>

                    <div className="space-y-4">
                        {isEditing ? (
                            <textarea
                                value={localContent}
                                onChange={(e) => setLocalContent(e.target.value)}
                                onBlur={handleSave}
                                autoFocus
                                className="w-full bg-slate-900 border border-blue-500/30 rounded-2xl p-6 text-lg leading-relaxed outline-none min-h-[150px]"
                            />
                        ) : (
                            <p onClick={() => setIsEditing(true)} className="text-lg leading-relaxed text-slate-200 cursor-text hover:bg-white/5 p-4 -m-4 rounded-2xl transition-all whitespace-pre-wrap">
                                {segment.content}
                            </p>
                        )}
                    </div>

                    {showProductionDetails && (
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 animate-in fade-in slide-in-from-top-2 duration-300">
                            <div className="bg-slate-900/50 p-4 rounded-xl border border-white/5">
                                <p className="text-[9px] font-black text-slate-500 uppercase mb-2">Visual Direction</p>
                                <p className="text-xs text-slate-300 leading-relaxed">{segment.visualDirection}</p>
                                <p className="text-[9px] font-black text-blue-500 uppercase mt-3 mb-1">Camera</p>
                                <p className="text-xs text-slate-400 font-mono italic">{segment.cameraMovement}</p>
                            </div>
                            <div className="bg-slate-900/50 p-4 rounded-xl border border-white/5">
                                <p className="text-[9px] font-black text-slate-500 uppercase mb-2">Audio Engineering</p>
                                <p className="text-xs text-slate-300 leading-relaxed">{segment.soundDesign}</p>
                                <p className="text-[9px] font-black text-indigo-500 uppercase mt-3 mb-1">Pacing</p>
                                <p className="text-xs text-slate-400">{segment.pacing}</p>
                            </div>
                        </div>
                    )}
                </div>
                <div className="lg:col-span-5">
                    <div className="glass-panel p-5 rounded-2xl border border-white/5 space-y-4">
                        <h5 className="text-[10px] font-black text-slate-500 uppercase tracking-widest">Story Concept Frame</h5>
                        <ImagePreview
                            src={imgSrc}
                            isLoading={isGeneratingImg}
                            onGenerate={(ratio) => onGenerateImg(index, ratio)}
                            placeholderText="RENDER FRAME"
                        />
                        <p className="text-[10px] text-slate-400 font-mono italic leading-tight">Prompt: "{segment.detailedImagePrompt}"</p>
                    </div>
                </div>
            </div>
        </div>
    );
};

const OutputView: React.FC<OutputViewProps> = ({ plan, onConsumeImageCredit, onConsumeStoryCredit, useHighQuality }) => {
    const [activeStrategyIdx, setActiveStrategyIdx] = useState(0);
    const [activeTab, setActiveTab] = useState<'strategy' | 'script'>('strategy');
    const [activePlatform, setActivePlatform] = useState<'youtube' | 'facebook'>('youtube');
    const [currentStrategies, setCurrentStrategies] = useState<VideoStrategy[]>(plan.strategies);
    const [architectingIdx, setArchitectingIdx] = useState<number | null>(null);
    const [generatingImgIdx, setGeneratingImgIdx] = useState<number | null>(null);
    const [generatedSceneAssets, setGeneratedSceneAssets] = useState<Record<string, string>>({});

    useEffect(() => { setCurrentStrategies(plan.strategies); }, [plan]);

    const activeStrategy = currentStrategies[activeStrategyIdx];
    const platformData = activeStrategy.platformAdaptations[activePlatform];

    const completedChapters = activeStrategy.script.filter(s => !!s.content && s.content.length > 10).length;
    const progressPercent = Math.round((completedChapters / activeStrategy.script.length) * 100);

    const handleArchitectChapter = async (idx: number) => {
        if (!onConsumeStoryCredit()) return;
        setArchitectingIdx(idx);
        try {
            const content = await generateChapterScript(
                activeStrategy.title,
                activeStrategy.script[idx].scene,
                activeStrategy.megaHooks,
                "Story Architect"
            );
            const nextStrats = [...currentStrategies];
            nextStrats[activeStrategyIdx].script[idx].content = content;
            setCurrentStrategies(nextStrats);
        } catch (e) { console.error(e); } finally { setArchitectingIdx(null); }
    };

    const handleGenerateScene = async (idx: number, ratio: AspectRatio) => {
        if (!onConsumeImageCredit()) return;

        if (useHighQuality) {
            try {
                // @ts-ignore
                const hasKey = await window.aistudio.hasSelectedApiKey();
                // @ts-ignore
                if (!hasKey) await window.aistudio.openSelectKey();
            } catch (e) { }
        }

        setGeneratingImgIdx(idx);
        try {
            const url = await generateImage(activeStrategy.script[idx].detailedImagePrompt, ratio, useHighQuality);
            setGeneratedSceneAssets(prev => ({ ...prev, [`${activeStrategy.title}_scene_${idx}`]: url }));
        } catch (e) { console.error(e); } finally { setGeneratingImgIdx(null); }
    };

    const handleGenerateThumbConcept = async (idx: number, ratio: AspectRatio) => {
        if (!onConsumeImageCredit()) return;
        if (useHighQuality) {
            try {
                // @ts-ignore
                const hasKey = await window.aistudio.hasSelectedApiKey();
                // @ts-ignore
                if (!hasKey) await window.aistudio.openSelectKey();
            } catch (e) { }
        }
        setGeneratingImgIdx(-100 - idx);
        try {
            const concept = activeStrategy.thumbnailConcepts[idx];
            const prompt = `Professional Content Thumbnail: ${concept.description}. Focal point: ${concept.focalPoint}. Lighting: ${concept.lighting}. Colors: ${concept.colorScheme}. Headline Overlay: "${concept.textOverlay}".`;
            const url = await generateImage(prompt, ratio, useHighQuality);
            setGeneratedSceneAssets(prev => ({ ...prev, [`${activeStrategy.title}_thumb_${idx}`]: url }));
        } catch (e) { console.error(e); } finally { setGeneratingImgIdx(null); }
    };

    const handleExportStrategy = () => {
        const strategy = currentStrategies[activeStrategyIdx];
        const blob = new Blob([JSON.stringify(strategy, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `${strategy.title.replace(/\s+/g, '_').toLowerCase()}_strategy.json`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    };

    return (
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div className="lg:col-span-3 space-y-4">
                <div className="glass-panel p-4 rounded-2xl">
                    <h3 className="text-[10px] font-black uppercase text-slate-500 mb-4 tracking-widest">Active Blueprints</h3>
                    <div className="space-y-2">
                        {currentStrategies.map((strat, i) => (
                            <button
                                key={i}
                                onClick={() => setActiveStrategyIdx(i)}
                                className={`w-full text-left p-4 rounded-xl text-xs font-bold transition-all border ${i === activeStrategyIdx ? 'bg-blue-600/10 border-blue-500 text-white shadow-xl shadow-blue-500/5' : 'bg-white/5 border-transparent text-slate-400 hover:bg-white/10'
                                    }`}
                            >
                                {strat.title}
                            </button>
                        ))}
                    </div>
                </div>
            </div>

            <div className="lg:col-span-9 space-y-8">
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div className="flex space-x-2 bg-slate-900/50 p-1 rounded-xl w-fit border border-white/5">
                        <button onClick={() => setActiveTab('strategy')} className={`px-6 py-2.5 rounded-lg text-xs font-black uppercase tracking-widest transition-all ${activeTab === 'strategy' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'}`}>Strategy</button>
                        <button onClick={() => setActiveTab('script')} className={`px-6 py-2.5 rounded-lg text-xs font-black uppercase tracking-widest transition-all ${activeTab === 'script' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white'}`}>Story Script</button>
                    </div>

                    <div className="flex items-center space-x-3">
                        <div className="flex items-center space-x-4">
                            <span className="text-[10px] font-black text-slate-500 uppercase">Optimize For:</span>
                            <div className="flex space-x-2 bg-slate-900/50 p-1 rounded-xl border border-white/5">
                                <button onClick={() => setActivePlatform('youtube')} className={`px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all ${activePlatform === 'youtube' ? 'bg-red-600 text-white' : 'text-slate-500'}`}>YouTube</button>
                                <button onClick={() => setActivePlatform('facebook')} className={`px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-all ${activePlatform === 'facebook' ? 'bg-blue-600 text-white' : 'text-slate-500'}`}>Facebook</button>
                            </div>
                        </div>

                        <button
                            onClick={handleExportStrategy}
                            className="flex items-center space-x-2 bg-slate-900/40 hover:bg-slate-800 p-2.5 rounded-xl border border-white/5 text-[10px] font-black uppercase text-slate-400 hover:text-white transition-all shadow-sm"
                        >
                            <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M16 9l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Export JSON</span>
                        </button>
                    </div>
                </div>

                {activeTab === 'strategy' && (
                    <div className="animate-in fade-in duration-500 space-y-8">
                        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <section className="lg:col-span-8 glass-panel p-8 rounded-3xl border-l-4 border-blue-600">
                                <h3 className="text-sm font-black text-slate-500 uppercase tracking-widest mb-6">Optimized Title Matrix</h3>
                                <div className="space-y-4">
                                    {platformData.titleVariations.map((title, i) => (
                                        <div key={i} className="bg-slate-900/50 p-5 rounded-2xl border border-white/5 flex items-center justify-between group">
                                            <span className="text-lg font-bold text-slate-200">"{title}"</span>
                                            <button className="text-[10px] font-black text-slate-500 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity hover:text-blue-400">Copy</button>
                                        </div>
                                    ))}
                                </div>
                            </section>

                            <section className="lg:col-span-4 glass-panel p-8 rounded-3xl border-l-4 border-indigo-600 space-y-6">
                                <div>
                                    <h4 className="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Algorithmic Focus</h4>
                                    <p className="text-sm font-bold text-indigo-400">{platformData.algorithmicFocus}</p>
                                </div>
                                <div className="h-px bg-white/5" />
                                <div>
                                    <h4 className="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Conversion Rationale</h4>
                                    <p className="text-xs text-slate-400 leading-relaxed italic">{platformData.conversionRationale}</p>
                                </div>
                            </section>
                        </div>

                        <section className="glass-panel p-8 rounded-3xl border-l-4 border-red-600">
                            <h3 className="text-sm font-black text-slate-500 uppercase tracking-widest mb-6">Retention Hook Magnet (30s Mega-Hooks)</h3>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {activeStrategy.megaHooks.map((h, i) => (
                                    <div key={i} className="bg-slate-900/50 p-6 rounded-2xl border border-white/5 relative overflow-hidden group">
                                        <div className="absolute top-0 right-0 p-2 text-[10px] font-black text-slate-800 opacity-20">0{i + 1}</div>
                                        <p className="text-sm italic text-slate-300 leading-relaxed font-medium">"{h}"</p>
                                    </div>
                                ))}
                            </div>
                        </section>

                        <h3 className="text-sm font-black text-slate-500 uppercase tracking-widest">Psychological Storyboard Concepts</h3>
                        <section className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {activeStrategy.thumbnailConcepts.map((c, i) => (
                                <div key={i} className="glass-panel p-5 rounded-2xl border border-white/5 space-y-4 hover:scale-[1.02] hover:border-blue-500/30 transition-all cursor-default group flex flex-col">
                                    <ImagePreview
                                        src={generatedSceneAssets[`${activeStrategy.title}_thumb_${i}`] || null}
                                        isLoading={generatingImgIdx === -100 - i}
                                        onGenerate={(ratio) => handleGenerateThumbConcept(i, ratio)}
                                        placeholderText="GENERATE CONCEPT"
                                    />
                                    <div className="bg-slate-800 rounded-xl p-4 border border-white/5 flex-grow">
                                        <span className="text-sm font-black italic uppercase text-white drop-shadow-lg text-center block mb-2 leading-tight group-hover:scale-105 transition-transform">"{c.textOverlay}"</span>
                                        <p className="text-[11px] text-slate-400 leading-relaxed line-clamp-2">"{c.description}"</p>
                                    </div>
                                    <button
                                        onClick={() => handleGenerateThumbConcept(i, "16:9")}
                                        disabled={generatingImgIdx === -100 - i}
                                        className="w-full py-2 bg-slate-900/50 hover:bg-blue-600/20 text-[10px] font-black uppercase text-slate-400 hover:text-blue-400 border border-white/5 rounded-lg transition-all flex items-center justify-center space-x-2 disabled:opacity-50 mt-2"
                                    >
                                        <svg className={`w-3 h-3 ${generatingImgIdx === -100 - i ? 'animate-spin' : ''}`} fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        <span>{generatingImgIdx === -100 - i ? 'Rendering...' : 'Regenerate Concept'}</span>
                                    </button>
                                </div>
                            ))}
                        </section>
                    </div>
                )}

                {activeTab === 'script' && (
                    <div className="animate-in fade-in duration-500 space-y-8">
                        <header className="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-white/5 pb-8">
                            <div className="space-y-1">
                                <div className="flex items-center space-x-3">
                                    <span className="bg-red-600 text-white text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-widest">Story Floor</span>
                                    <h2 className="text-3xl font-black">{activeStrategy.title}</h2>
                                </div>
                                <div className="flex items-center text-[10px] font-black space-x-3 text-slate-500 tracking-widest uppercase">
                                    <span className="flex items-center"><span className="w-2 h-2 rounded-full bg-blue-500 mr-2" /> Content Depth: {activeStrategy.script.length} Scenes</span>
                                    <span>• Status: {completedChapters}/{activeStrategy.script.length} Complete</span>
                                </div>
                            </div>
                            {activeStrategy.isMarathonMode && (
                                <button
                                    onClick={() => handleArchitectChapter(completedChapters)}
                                    disabled={architectingIdx !== null || completedChapters >= activeStrategy.script.length}
                                    className="bg-white text-black px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all disabled:opacity-30 flex items-center space-x-2"
                                >
                                    <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                    <span>Architect Next Segment</span>
                                </button>
                            )}
                        </header>

                        <div className="w-full h-1 bg-slate-900 rounded-full overflow-hidden border border-white/5">
                            <div className="h-full bg-blue-600 transition-all duration-1000" style={{ width: `${progressPercent}%` }} />
                        </div>

                        <div className="space-y-12 pb-20">
                            {activeStrategy.script.map((seg, idx) => (
                                <ScriptSegmentCard
                                    key={idx}
                                    segment={seg}
                                    index={idx}
                                    isMarathon={activeStrategy.isMarathonMode}
                                    onArchitect={handleArchitectChapter}
                                    onUpdate={(idx, val) => {
                                        const nextStrats = [...currentStrategies];
                                        nextStrats[activeStrategyIdx].script[idx].content = val;
                                        setCurrentStrategies(nextStrats);
                                    }}
                                    onGenerateImg={handleGenerateScene}
                                    isArchitecting={architectingIdx === idx}
                                    isGeneratingImg={generatingImgIdx === idx}
                                    imgSrc={generatedSceneAssets[`${activeStrategy.title}_scene_${idx}`] || null}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default OutputView;
