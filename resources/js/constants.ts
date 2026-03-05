
import { Niche, VideoLength, TargetCountry } from './types';

export const NICHES: Niche[] = Object.values(Niche);
export const VIDEO_LENGTHS: VideoLength[] = Object.values(VideoLength);
export const COUNTRIES: TargetCountry[] = ['USA', 'UK', 'Germany', 'India', 'Brazil', 'Japan'];

export const VOICES = [
    { id: 'Zephyr', name: 'Zephyr (Energetic)' },
    { id: 'Puck', name: 'Puck (Narrative)' },
    { id: 'Kore', name: 'Kore (Calm)' },
    { id: 'Fenrir', name: 'Fenrir (Deep/Serious)' }
];
