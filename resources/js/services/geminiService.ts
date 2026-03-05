
import { GoogleGenAI, Type } from "@google/genai";
import { ContentPlan, MonthlyPlan, Niche, VideoLength, TargetCountry, ThumbnailConcept, ThumbnailVariation, VideoStrategy, ScriptSegment } from "../types";

// Helper to get a fresh instance, ensuring it uses the most current API_KEY from process.env
const getAI = () => new GoogleGenAI({ apiKey: (import.meta as any).env.VITE_GEMINI_API_KEY || "PLACEHOLDER_API_KEY" });

export const generatePlan = async (
    niche: Niche,
    length: VideoLength,
    country: TargetCountry,
    topic?: string
): Promise<ContentPlan> => {
    const isMarathon = length === VideoLength.MIN30 || length === VideoLength.MIN60;
    // Guidelines: Complex text reasoning uses gemini-3-pro-preview
    const model = 'gemini-3-pro-preview';

    const prompt = `Generate 3 completely distinct and cinematic video strategies for a ${niche} channel.
    Target Audience Country: ${country}
    Target Video Format: ${length}
    Specific Topic/Keywords: ${topic || 'Trending ' + niche + ' topics'}
    
    ${isMarathon ? 'This is MARATHON MODE (Long-form content). Do NOT write full scripts. Instead, provide a detailed 10-CHAPTER OUTLINE for the video. Each chapter should have a scene title and a production prompt.' : 'Write full scripts for these strategies (min 6 scenes).'}
    
    FOR EACH STRATEGY, you must provide:
    1. A High-CTR Title.
    2. 3 psychological thumbnail concepts.
    3. 3 MASTER RETENTION HOOKS.
    4. ${isMarathon ? 'A 10-CHAPTER STRATEGIC OUTLINE (scene objects with empty content).' : 'A professional script (scenes with content).'}
    5. Platform Adaptations for YouTube and Facebook.

    Format the response as a JSON object matching the ContentPlan structure with an array of 'strategies'.
    Set isMarathonMode to ${isMarathon}.`;

    const strategySchema = {
        type: Type.OBJECT,
        properties: {
            title: { type: Type.STRING },
            isMarathonMode: { type: Type.BOOLEAN },
            targetDuration: { type: Type.STRING },
            thumbnailConcepts: {
                type: Type.ARRAY,
                items: {
                    type: Type.OBJECT,
                    properties: {
                        description: { type: Type.STRING },
                        focalPoint: { type: Type.STRING },
                        backgroundDetail: { type: Type.STRING },
                        colorScheme: { type: Type.STRING },
                        textOverlay: { type: Type.STRING },
                        textPlacement: { type: Type.STRING },
                        emotionalTrigger: { type: Type.STRING },
                        callToAction: { type: Type.STRING },
                        cameraAngle: { type: Type.STRING },
                        lighting: { type: Type.STRING }
                    },
                    required: ["description", "focalPoint", "backgroundDetail", "colorScheme", "textOverlay", "textPlacement", "emotionalTrigger", "callToAction", "cameraAngle", "lighting"]
                }
            },
            megaHooks: { type: Type.ARRAY, items: { type: Type.STRING } },
            script: {
                type: Type.ARRAY,
                items: {
                    type: Type.OBJECT,
                    properties: {
                        act: { type: Type.STRING },
                        scene: { type: Type.STRING },
                        content: { type: Type.STRING }, // Might be empty in Marathon mode
                        estimatedTime: { type: Type.STRING },
                        pacing: { type: Type.STRING },
                        visualDirection: { type: Type.STRING },
                        cameraMovement: { type: Type.STRING },
                        soundDesign: { type: Type.STRING },
                        detailedImagePrompt: { type: Type.STRING }
                    },
                    required: ["act", "scene", "content", "estimatedTime", "pacing", "visualDirection", "cameraMovement", "soundDesign", "detailedImagePrompt"]
                }
            },
            platformAdaptations: {
                type: Type.OBJECT,
                properties: {
                    youtube: { type: Type.OBJECT, properties: { titleVariations: { type: Type.ARRAY, items: { type: Type.STRING } }, conversionRationale: { type: Type.STRING }, algorithmicFocus: { type: Type.STRING } } },
                    facebook: { type: Type.OBJECT, properties: { titleVariations: { type: Type.ARRAY, items: { type: Type.STRING } }, conversionRationale: { type: Type.STRING }, algorithmicFocus: { type: Type.STRING } } }
                },
                required: ["youtube", "facebook"]
            }
        },
        required: ["title", "thumbnailConcepts", "megaHooks", "script", "platformAdaptations", "isMarathonMode"]
    };

    const ai = getAI();
    const response = await ai.models.generateContent({
        model,
        contents: prompt,
        config: {
            temperature: 0.9,
            responseMimeType: "application/json",
            responseSchema: {
                type: Type.OBJECT,
                properties: { strategies: { type: Type.ARRAY, items: strategySchema } },
                required: ["strategies"]
            }
        }
    });

    return JSON.parse(response.text || "{\"strategies\": []}") as ContentPlan;
};

/**
 * Architect a specific chapter script for Marathon Mode
 */
export const generateChapterScript = async (
    videoTitle: string,
    chapterTitle: string,
    contextHooks: string[],
    niche: string
): Promise<string> => {
    // Guidelines: Basic text generation uses gemini-3-flash-preview
    const model = 'gemini-3-flash-preview';
    const prompt = `You are a world-class YouTube scriptwriter. 
    Video Title: ${videoTitle}
    Current Chapter: ${chapterTitle}
    Strategy Hooks: ${contextHooks.join(', ')}
    Niche: ${niche}

    Task: Write a deep, high-retention script for THIS CHAPTER ONLY (~300-500 words). 
    Focus on pattern interrupts and storytelling. Return ONLY the script content text.`;

    const ai = getAI();
    const response = await ai.models.generateContent({
        model,
        contents: prompt,
        config: { temperature: 0.85 }
    });

    return response.text?.trim() || "Script generation failed.";
};

export const generateImage = async (
    prompt: string,
    aspectRatio: "1:1" | "16:9" | "4:3" | "9:16" = "16:9",
    useHighQuality: boolean = false
): Promise<string> => {
    // Guidelines: Create a new instance right before image generation calls
    const dynamicAi = new GoogleGenAI({ apiKey: (import.meta as any).env.VITE_GEMINI_API_KEY || "PLACEHOLDER_API_KEY" });
    const model = useHighQuality ? 'gemini-3-pro-image-preview' : 'gemini-2.5-flash-image';

    const response = await dynamicAi.models.generateContent({
        model,
        contents: { parts: [{ text: prompt }] },
        config: {
            imageConfig: {
                aspectRatio,
                ...(useHighQuality ? { imageSize: "1K" } : {})
            }
        }
    });

    // Guidelines: Iterate through all parts to find the image part
    const candidates = response.candidates?.[0]?.content?.parts || [];
    for (const part of candidates) {
        if (part.inlineData) {
            return `data:${part.inlineData.mimeType};base64,${part.inlineData.data}`;
        }
    }

    throw new Error("No image data found in model response.");
};

export const regenerateScript = async (title: string, hooks: string[]) => {
    const ai = getAI();
    const response = await ai.models.generateContent({
        model: 'gemini-3-flash-preview',
        contents: `Write a script for "${title}" using hooks: ${hooks.join(', ')}`,
        config: { responseMimeType: "application/json" }
    });
    return JSON.parse(response.text || "[]");
};

export const generateThumbnailVariations = async (concepts: any, niche: string) => { return []; };
export const refineVisualDirection = async (content: string, concept: any, niche: string) => { return ""; };

export const generateMonthlyBlueprint = async (niche: Niche, country: TargetCountry, topic?: string): Promise<MonthlyPlan> => {
    const ai = getAI();
    const response = await ai.models.generateContent({
        model: 'gemini-3-pro-preview',
        contents: `Monthly plan for ${niche} in ${country}. Topic: ${topic || 'Trending'}`,
        config: { responseMimeType: "application/json" }
    });
    return JSON.parse(response.text || "{}") as MonthlyPlan;
};
