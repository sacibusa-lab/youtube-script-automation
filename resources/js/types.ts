
export type UserRole = 'USER' | 'ADMIN';

export interface PlatformSpecific {
  titleVariations: string[];
  thumbnailAdjustments: string;
  scriptTweaks: string;
  engagementStrategy: string;
  algorithmicFocus: string;
  conversionRationale: string;
}

export interface ThumbnailConcept {
  description: string;
  focalPoint: string;
  backgroundDetail: string;
  colorScheme: string;
  textOverlay: string;
  textPlacement: string;
  emotionalTrigger: string;
  callToAction: string;
  cameraAngle: string;
  lighting: string;
}

export interface ThumbnailVariation extends ThumbnailConcept {
  variationReasoning: string;
  variationLabel: string;
}

export interface ScriptSegment {
  act: string; 
  scene: string;
  content?: string; 
  estimatedTime: string;
  pacing: string;
  visualDirection: string;
  cameraMovement: string;
  soundDesign: string;
  detailedImagePrompt: string;
  isGenerated?: boolean;
}

export interface VideoStrategy {
  title: string;
  thumbnailConcepts: ThumbnailConcept[];
  megaHooks: string[];
  script: ScriptSegment[];
  isMarathonMode: boolean;
  targetDuration?: string;
  platformAdaptations: {
    youtube: PlatformSpecific;
    facebook: PlatformSpecific;
  };
}

export interface ContentPlan {
  strategies: VideoStrategy[];
}

export interface MonthlyVideoIdea {
  week: number;
  day: string;
  title: string;
  thumbnailConcept: string;
  outline: string[];
  objective: string;
}

export interface MonthlyPlan {
  strategyName: string;
  strategyOverview: string;
  ideas: MonthlyVideoIdea[];
}

export enum Niche {
  TRUE_CRIME = 'True Crime',
  FINANCE = 'Personal Finance & Crypto',
  LUXURY = 'Luxury & Lifestyle',
  TECH = 'Tech & Future AI',
  HEALTH = 'Health & Biohacking',
  HISTORY = 'Untold History',
  MOTIVATION = 'Motivation & Stoicism',
  TRAVEL = 'Travel & Global Adventure',
  GAMING = 'Gaming & Esports Culture',
  FOOD = 'Cooking & Food Science',
  DIY = 'DIY & Creative Crafts',
  SCIENCE = 'Science & Universal Wonders',
  CELEBRITY = 'Celebrity & Pop Culture',
  BUSINESS = 'Entrepreneurship & Startups'
}

export enum VideoLength {
  REELS = 'Reels (15-30s)',
  SHORTS = 'Shorts (30-60s)',
  MIN5 = 'Long-form (5m)',
  MIN10 = 'Long-form (10m)',
  MIN15 = 'Long-form (15m)',
  MIN30 = 'Long-form (30m)',
  MIN60 = 'Long-form (60m)'
}

export type TargetCountry = 'USA' | 'UK' | 'Germany' | 'India' | 'Brazil' | 'Japan';
export type GenerationMode = 'SINGLE' | 'MONTHLY';
export type AppView = 'GENERATOR' | 'LIBRARY' | 'USER_PANEL' | 'ADMIN_PANEL';
