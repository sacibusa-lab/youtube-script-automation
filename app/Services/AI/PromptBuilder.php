<?php

namespace App\Services\AI;

class PromptBuilder
{
    private NicheTemplateService $nicheTemplateService;
    private SystemInstructionBuilder $systemInstructionBuilder;
    private ContentValidator $contentValidator;

    public function __construct(
        NicheTemplateService $nicheTemplateService,
        SystemInstructionBuilder $systemInstructionBuilder,
        ContentValidator $contentValidator
    ) {
        $this->nicheTemplateService = $nicheTemplateService;
        $this->systemInstructionBuilder = $systemInstructionBuilder;
        $this->contentValidator = $contentValidator;
    }

    /**
     * Build a prompt for a specific Production Role.
     */
    public function buildRolePrompt(string $roleSlug, string $taskPrompt, array $context = []): string
    {
        $roleDirective = $this->getRoleDirective($roleSlug, $context);
        $systemInstruction = $this->systemInstructionBuilder->buildMasterInstruction();
        $niche = $context['niche'] ?? 'Generic';
        $topic = $context['topic'] ?? 'N/A';

        return <<<PROMPT
{$systemInstruction}

**PRODUCTION ROLE: {$roleSlug}**
{$roleDirective}

**CURRENT TASK:**
{$taskPrompt}

**CONTEXT:**
- Niche: {$niche}
- Topic: {$topic}
- Audience: Global (Tier 1)

**OUTPUT REQUIREMENTS:**
1. MANDATORY JSON: No commentary.
2. Ensure the response follows the requested schema in the task.
3. Use the tone and expertise defined in your PRODUCTION ROLE.
PROMPT;
    }

    /**
     * Get specialized directives for each Production Role.
     */
    protected function getRoleDirective(string $roleSlug, array $context = []): string
    {
        return match ($roleSlug) {
            'strategist' => <<<DIR
You are the **Production Strategist**. Your expertise is in YouTube growth, CTR optimization, and retention science.
- Focus on "Curiosity Gaps" and "High-Stakes Emotional Hooks".
- Use psychological triggers (fear, surprise, justice, greed).
- Engineering viral outcomes is your only goal.
DIR,
            'architect' => <<<DIR
You are the **Lead Architect**. You are the master of world-building and "Bible Logic".
- Maintain strict character consistency (exactly 4 characters).
- Design complex plot structures and deep backstories.
- Ensure every narrative element has a logical cause-and-effect.
DIR,
            'narrator' => <<<DIR
You are the **Main Narrator**. You are a master storyteller specializing in long-form "binge-worthy" content.
- Focus on pacing and "Emotional Spikes".
- Use the "Show, Don't Tell" rule religiously.
- Write natural, evocative dialogue and dense, atmospheric narration.
DIR,
            'artist' => <<<DIR
You are the **Visual Artist**. You are a professional cinematic photographer and prompt engineer.
- Use technical specs: 85mm Prime, f/1.8, cinematic lighting, photorealistic 8K.
- Focus on high-tension compositions and clear emotional facial expressions.
- Describe scenes with photographic precision to guide AI image generation.
DIR,
            'discovery' => <<<DIR
You are the **Discovery Engine**. You are a trend-spotter and creative ideator.
- Find unique angles on common topics to avoid "AI genericism".
- Research market trends and viral content patterns.
- Ideate high-concept "what-if" scenarios that demand a click.
DIR,
            default => "You are a production assistant assisting in video automation."
        };
    }

    /**
     * Get the AI Orchestration Engine System Directive.
     * This defines the strict rules, model switching, and structured JSON requirements.
     */
    protected function getSystemDirective(array $context = []): string
    {
        $tier = $context['tier'] ?? 'PRO';
        $niche = $context['niche'] ?? 'Generic';
        
        return <<<DIRECTIVE
SYSTEM ROLE:
You are the AI Orchestration Engine for a high-scale faceless YouTube automation platform.

CORE EXECUTION RULES:
1. MODEL SWITCHING INTELLIGENCE: Operate under strict rate limits. Priority: Flash -> Lite -> Flash 2 -> Pro.
2. TOKEN & COST CONTROL:
   - Hooks: max 60 words
   - Outline: max 150 words
   - Script scenes: max 200 words each
   - Total target: 800–1200 words
3. NICHE ADAPTATION: Curren Niche is [{$niche}]. Adapt tone dynamically (e.g., Suspense for Crime, Authority for Finance).
4. USER TIER: [{$tier}]. (FREE=Compress 30%, PRO=Full Length).
5. MANDATORY JSON: You NEVER output commentary. You ONLY return structured JSON. No markdown backticks unless strictly required by the output schema.

OUTPUT SCHEMA (CONTENT WRAPPER):
{
  "status": "SUCCESS",
  "model_used": "...",
  "api_key_slot": "...",
  "content": { ... specific task data ... }
}
DIRECTIVE;
    }

    /**
     * Build emotional title generation prompt (Stage 1)
     *
     * @param string $nichePool
     * @param array $previousTitles
     * @param string $hybridIntensity (e.g., "Low", "Medium", "High", "Maximum")
     * @param string $riskMode (e.g., "Safe", "Edgy", "Controversial")
     * @return string
     */
    public function buildTitlePrompt(
        string $nichePool,
        array $previousTitles = [],
        string $hybridIntensity = 'Medium',
        string $riskMode = 'Safe',
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['niche' => $nichePool, 'tier' => $tier]);
        $previousTitlesStr = empty($previousTitles) ? 'None' : implode(", ", $previousTitles);
        
        return <<<PROMPT
{$directive}

**TASK: STEP 1 — TITLE REFINEMENT**
Generate 5 high-CTR YouTube titles in the niche: {$nichePool}.

**INPUTS:**
- **Hybrid Intensity**: {$hybridIntensity}
- **Risk Mode**: {$riskMode}

**CONSTRAINTS:**
- Max 70 characters per title.
- Avoid similarity to: {$previousTitlesStr}.
- Content must be monetization-safe.

**OUTPUT SCHEMA (MANDATORY):**
{
  "status": "SUCCESS",
  "content": {
    "titles": [
      {"title": "...", "emotion": "...", "angle": "...", "tension_score": 92}
    ]
  }
}
PROMPT;
    }

    /**
     * Build narrative metadata extraction prompt (Stage 2)
     *
     * @param string $selectedTitle
     * @param string $hybridIntensity
     * @param string $riskMode
     * @return string
     */
    public function buildMetadataPrompt(
        string $selectedTitle,
        string $hybridIntensity = 'Medium',
        string $riskMode = 'Safe',
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['tier' => $tier]);
        
        return <<<PROMPT
{$directive}

**TASK: STEP 2 — METADATA ENGINE**
Extract structured narrative metadata and define the universal cinematic visual identity for: "{$selectedTitle}"

**INPUTS:**
- **Hybrid Intensity**: {$hybridIntensity}
- **Risk Mode**: {$riskMode}

**OUTPUT SCHEMA (MANDATORY):**
{
  "status": "SUCCESS",
  "content": {
    "core_theme": "...",
    "main_character_profile": {
       "name": "...",
       "physical_traits": "...",
       "wardrobe": "..."
    },
    "universal_visual_prompt_data": {
         "lighting": "...",
         "color_palette": "...",
         "overall_mood": "...",
         "detailed_visual_instructions": "Photorealistic, 8K, cinematic widescreen (16:9). [EXTREME DETAIL]"
    },
    "primary_conflict": "...",
    "unique_twist": "..."
  }
}
PROMPT;
    }

    /**
     * Build concept generation prompt (Title + Hook + Thumbnail)
     */
    public function buildConceptPrompt(
        string $topic,
        string $niche,
        string $tier1Country,
        ?string $structure = null,
        ?string $emotion = null,
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['niche' => $niche, 'tier' => $tier]);
        
        $structureInstruction = $structure ? "- **Structure**: {$structure}" : "";
        $emotionInstruction = $emotion ? "- **Emotion**: {$emotion}" : "";

        return <<<PROMPT
{$directive}

**TASK: GENERATE VIRAL VIDEO CONCEPT**
Topic: {$topic}
Niche: {$niche}
Country: {$tier1Country}
{$structureInstruction}
{$emotionInstruction}

**OUTPUT SCHEMA (MANDATORY JSON):**
{
  "status": "SUCCESS",
  "content": {
    "titles": ["Variation 1", "Variation 2", "Variation 3", "Variation 4", "Variation 5"],
    "megaHook": "30s high-retention script opening...",
    "thumbnailConcept": {
      "description": "Extreme detail of the frame...",
      "focalPoint": "...",
      "colorScheme": "...",
      "emotionalTrigger": "...",
      "textOverlay": "...",
      "cameraAngle": "...",
      "lighting": "..."
    }
  }
}
PROMPT;
    }

    /**
     * Build multi-strategy prompt for 5 distinct concepts
     */
    public function buildMultiStrategyPrompt(
        string $topic,
        string $niche,
        string $tier1Country,
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['niche' => $niche, 'tier' => $tier]);
        
        return <<<PROMPT
{$directive}

**TASK: ARCHITECT 5 CINEMATIC VIDEO CONCEPTS**
Niche: {$niche}
Target Audience: {$tier1Country}
Topic: {$topic}

Generate 5 completely distinct and viral concepts. Each must have a unique title, a profound narrative mega-hook, and a visual thumbnail concept.

**HOOK ARCHITECTURE GUIDELINES:**
The "megaHooks" must be a single, cohesive paragraph (approx 80-120 words) that reads like a high-stakes cinematic movie trailer. Do NOT use generic questions. Instead:
1. Establish a vivid, character-centric premise or powerful dichotomy (e.g., "In the heart of Silicon Valley, a quiet titan of industry...").
2. Introduce a specific protagonist or focal entity, their audacious goal, and their unexpected method.
3. Introduce a ticking clock, a powerful adversary, or a dark secret that threatens to derail everything, ending on a cliffhanger.
4. Each hook should be 4-6 sentences (approx 30s read time).

**THUMBNAIL PROMPT GUIDELINES:**
The "prompt" field MUST be approximately 400 words of flowing prose (NOT JSON).
1. FEATURE 2–3 PEOPLE: Describe 2 or 3 characters (e.g., "a feared boss, a small girl clutching his sleeve with tears, and an injured woman in the background").
2. PHOTOREALISTIC & PROFESSIONAL: Include: photorealistic, 8K, cinematic lighting, 85mm Prime f/1.8 lens, shallow depth of field.
3. EMOTION FOR CTR: Include strong emotional cues—tears, fear, desperation, tension, shock, or tenderness.
4. MANDATORY 16:9 WIDESCREEN: Ensure the prompt explicitly describes a YouTube-ready widescreen composition.
5. No bullet lists inside the string.

**OUTPUT SCHEMA (MANDATORY JSON):**
{
  "strategies": [
    {
      "title": "Concept Title",
      "megaHooks": ["The detailed 30s narrative hook."],
      "thumbnailConcepts": [
        {
          "prompt": "The detailed 400-word cinematic thumbnail prompt."
        }
      ]
    }
  ]
}
PROMPT;
    }

    /**
     * Build single hook generation prompt
     */
    public function buildSingleHookPrompt(
        string $topic,
        string $niche,
        string $tier1Country,
        string $title,
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['niche' => $niche, 'tier' => $tier]);
        
        return <<<PROMPT
{$directive}

**TASK: ARCHITECT 1 CINEMATIC VIDEO HOOK**
Niche: {$niche}
Target Audience: {$tier1Country}
Topic: {$topic}
Selected Title: {$title}

Generate a single, profound narrative mega-hook for the given title.

**HOOK ARCHITECTURE GUIDELINES:**
The "hook" must be a single, cohesive paragraph (approx 80-120 words) that reads like a high-stakes cinematic movie trailer. Do NOT use generic questions. Instead:
1. Establish a vivid, character-centric premise or powerful dichotomy.
2. Introduce a specific protagonist or focal entity, their audacious goal, and their unexpected method.
3. Introduce a ticking clock, a powerful adversary, or a dark secret that threatens to derail everything, ending on a cliffhanger.
4. Length: 4-6 sentences (approx 30s read time).

**OUTPUT SCHEMA (MANDATORY JSON):**
{
  "hook": "The detailed narrative hook copy."
}
PROMPT;
    }

    /**
     * Build single thumbnail generation prompt
     */
    public function buildSingleThumbnailPrompt(
        string $topic,
        string $niche,
        string $title,
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['niche' => $niche, 'tier' => $tier]);
        
        return <<<PROMPT
{$directive}

**TASK: ARCHITECT 1 CINEMATIC THUMBNAIL PROMPT**
Niche: {$niche}
Topic: {$topic}
Selected Title: {$title}

Generate a single visual thumbnail concept for the given title.

**THUMBNAIL PROMPT GUIDELINES:**
The "thumbnail" field MUST be approximately 400 words of flowing prose (NOT JSON).
1. FEATURE 2–3 PEOPLE: Describe 2 or 3 characters with REAL impact (e.g. "a protagonist in a desperate moment, an adversary shadowing them, and a bystander in shock").
2. PHOTOREALISTIC & PROFESSIONAL: Include: photorealistic, 8K, cinematic lighting, 85mm Prime f/1.8, shallow depth of field.
3. EMOTION FOR CTR: Include strong emotional cues—tears, fear, desperation, tension, shock, or tenderness.
4. 16:9 widescreen, no bullet lists.

**OUTPUT SCHEMA (MANDATORY JSON):**
{
  "thumbnail": "The full 400-word highly detailed photographic prose prompt."
}
PROMPT;
    }

    /**
     * Build 30-day monthly plan prompt
     */
    public function buildMonthlyPlanPrompt(
        string $topic,
        string $niche,
        string $tier1Country,
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['niche' => $niche, 'tier' => $tier]);
        
        return <<<PROMPT
{$directive}

**TASK: 30-DAY VIRAL CONTENT BLUEPRINT**
Niche: {$niche}
Target Audience: {$tier1Country}
Topic: {$topic}

Generate a strategic monthly calendar with 4 weeks of high-performance video ideas.

**OUTPUT SCHEMA (MANDATORY JSON):**
{
  "strategyName": "...",
  "strategyOverview": "...",
  "ideas": [
    {
      "week": 1,
      "day": "Monday",
      "title": "...",
      "thumbnailConcept": "...",
      "outline": ["Point 1", "Point 2", "Point 3"],
      "objective": "High Retention / Viral / Community"
    }
  ]
}
PROMPT;
    }

    /**
     * Build high-retention outline generation prompt (Stage 3)
     *
     * @param array $metadataJson
     * @param int $durationMinutes
     * @return string
     */
    public function buildOutlinePrompt(
        array $metadataJson,
        int $durationMinutes,
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['tier' => $tier]);
        $metadataStr = json_encode($metadataJson, JSON_PRETTY_PRINT);
        
        return <<<PROMPT
{$directive}

**TASK: STEP 3 — STORY OUTLINE**
Target Duration: {$durationMinutes} minutes.

**INPUTS:**
{$metadataStr}

**CONSTRAINTS:**
- Max 150 words total for the outline summaries.
- Must include exactly 10 chapter beats for scaling.
- Mandatory curiosity loops in sections 1, 4, and 7.

**OUTPUT SCHEMA (MANDATORY):**
{
  "status": "SUCCESS",
  "content": {
    "hook": "Max 60 words...",
    "chapters": [
      {"chapter_number": 1, "title": "...", "summary": "...", "visual_mood": "..."}
    ]
  }
}
PROMPT;
    }

    /**
     * Build full cinematic script generation prompt (Stage 4 - Heavy)
     *
     * @param string $selectedTitle
     * @param array $metadataJson
     * @param array $outlineJson
     * @param int $targetWordCount
     * @return string
     */
    public function buildFullScriptPrompt(
        string $selectedTitle,
        array $metadataJson,
        array $outlineJson,
        int $targetWordCount,
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['tier' => $tier]);
        $metadataStr = json_encode($metadataJson, JSON_PRETTY_PRINT);
        $outlineStr = json_encode($outlineJson, JSON_PRETTY_PRINT);
        
        return <<<PROMPT
{$directive}

**TASK: STEP 4 — FULL SCRIPT (SCENE-BY-SCENE)**
Target Word Count: {$targetWordCount} words.

**INPUTS:**
- Title: {$selectedTitle}
- Metadata: {$metadataStr}
- Outline: {$outlineStr}

**CONSTRAINTS:**
- Max 200 words per scene.
- Total Target: 800–1200 words.
- No filler. Cinematic pacing.

**OUTPUT SCHEMA (MANDATORY):**
{
  "status": "SUCCESS",
  "content": {
    "script": [
      {
        "scene_number": 1,
        "narration": "...",
        "image_prompt": "Photorealistic, 8K ... [DETAIL]"
      }
    ]
  }
}
PROMPT;
    }

    /**
     * Build high-level structure prompt (Mega-hook, Chapters, Emotional Spike Map)
     *
     * @param string $topic
     * @param string $niche
     * @param int $durationMinutes
     * @param string $tier1Country
     * @param string $title
     * @param string $megaHook
     * @return string
     */
    public function buildStructurePrompt(
        string $topic,
        string $niche,
        int $durationMinutes,
        string $tier1Country,
        string $title,
        string $megaHook,
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['niche' => $niche, 'tier' => $tier]);
        $pacing = $this->getDurationPacingGuidelines($durationMinutes);
        
        return <<<PROMPT
{$directive}

**TASK: DEFINING MARATHON STRUCTURE**
Title: {$title}

**INPUTS:**
- Niche: {$niche}
- Topic: {$topic}
- Duration: {$durationMinutes} mins
- Market: {$tier1Country}

**STRICT BIBLE REQUIREMENTS:**
1. CRITICAL — Bible MUST contain exactly 4 characters in character_profiles. Do NOT omit or return an empty array.
2. Character Names MUST be unique and diverse (avoid Sarah, Emily, Marcus, Vance). Use FRESH names fitting the country (e.g., Priya Sharma, Dmitri Volkov, Fatima Okonkwo).
3. For EACH character provide:
   - name: full name
   - role: archetype/epithet (e.g. "THE AVENGING FATHER")
   - appearance: very detailed physical profile (age, build, traits)
   - motive: deep desire or drive
   - backstory: brief but rich backstory
   - objectives: concrete goals in this story
   - role_in_story: narrative function and impact

**PACING GUIDELINES:**
{$pacing}

**OUTPUT SCHEMA (MANDATORY):**
{
  "status": "SUCCESS",
  "content": {
    "character_profiles": [
      {
        "name": "...",
        "role": "...",
        "appearance": "...",
        "motive": "...",
        "backstory": "...",
        "objectives": "...",
        "role_in_story": "..."
      }
    ],
    "emotional_spike_map": [
      {"beat": 1, "target_emotion": "...", "plot_point": "..."}
    ],
    "chapters": [
      {
        "chapter_number": 1,
        "title": "...",
        "hook_text": "Max 60 words...",
        "brief_summary": "..."
      }
    ]
  }
}
PROMPT;
    }

    /**
     * Build detailed narration prompt for a SINGLE chapter
     */
    public function buildChapterNarrationPrompt(
        $video,
        $chapter,
        array $allChapters,
        array $characters,
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['niche' => $video->niche, 'tier' => $tier]);
        $localization = $this->getTier1LocalizationGuidelines($video->tier1_country);
        $charJson = json_encode($characters, JSON_PRETTY_PRINT);
        
        return <<<PROMPT
{$directive}

**TASK: GENERATING GRANULAR CHAPTER NARRATION (6/7 SECOND RULE)**
Project: {$video->selected_title}
Chapter: #{$chapter->chapter_number} - "{$chapter->title}"

**INPUTS:**
- Market: {$video->tier1_country}
- Characters: {$charJson}

**CRITICAL CONSTRAINTS (HIGH RETENTION MODE):**
1. **ONE SENTENCE PER SCENE**: You MUST break the narration into granular fragments. Every single sentence MUST be its own unique scene object.
2. **THE 6/7 SECOND RULE**: Each scene's `duration_seconds` MUST be between 6 and 10 seconds.
3. **VISUAL ENGAGEMENT**: Every scene fragment must be strong enough to warrant a unique visual image.
4. **CONTINUITY**: Ensure the narration flows perfectly across these fragments.
{$localization}

**OUTPUT SCHEMA (MANDATORY):**
{
  "status": "SUCCESS",
  "content": {
    "chapter_number": {$chapter->chapter_number},
    "scenes": [
      {
        "scene_number": 1,
        "narration_text": "Single sentence here.",
        "character_references": ["..."],
        "duration_seconds": 7
      }
    ]
  }
}
PROMPT;
    }

    /**
     * Build visual prompt generator for scenes
     */
    public function buildScenePromptPrompt(
        array $scenes,
        array $characters,
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['tier' => $tier]);
        $charJson = json_encode($characters, JSON_PRETTY_PRINT);
        $scenesJson = json_encode($scenes, JSON_PRETTY_PRINT);

        return <<<PROMPT
{$directive}

**TASK: STEP 5 — GRANULAR IMAGE PROMPTS (SCENE-BY-SCENE)**
Generate a high-fidelity cinematic visual prompt for every individual sentence fragment provided.

**INPUTS:**
- Characters: {$charJson}
- Scenes: {$scenesJson}

**CRITICAL CONSTRAINTS:**
1. **VISUAL DIVERSITY**: Since these are granular fragments (one sentence each), avoid repetitive visual compositions. Ensure each prompt is unique.
2. **STYLE**: Photorealistic, 8K, cinematic.
3. **ASPECT RATIO**: Mandatory 16:9 widescreen YouTube focus.
4. **CONSISTENCY**: Maintain character appearance across all scenes where they appear.

**OUTPUT SCHEMA (MANDATORY):**
{
  "status": "SUCCESS",
  "content": {
    "scenes": [
      {
        "scene_number": 1,
        "prompt_data": {
           "overall_mood": "...",
           "detailed_visual_instructions": "Photorealistic, 8K, cinematic YouTube widescreen (16:9 aspect ratio). [SPECIFIC DETAIL ALIGNED WITH THE SENTENCE]"
        }
      }
    ]
  }
}
PROMPT;
    }

    /**
     * Build scene breakdown prompt (Stage 5)
     *
     * @param string $scriptText
     * @return string
     */
    public function buildSceneBreakdownPrompt(string $scriptText): string
    {
        $systemContext = $this->getAntigravityContext();
        
        return <<<PROMPT
{$systemContext}

**STAGE 5 — SCENE BREAKDOWN PROMPT**

**INPUT:**
- **Script Text**:
{$scriptText}

**TASK:**
Split the script into 12–18 cinematic scenes.

**REQUIREMENTS:**
Each scene must include:
- Short narration summary (2–3 sentences).
- Emotional tone.
- Visual atmosphere.

**OUTPUT FORMAT (JSON ONLY):**
```json
[
  {
    "scene_number": 1,
    "summary": "...",
    "emotion": "...",
    "visual_mood": "..."
  }
]
```
PROMPT;
    }

    /**
     * Build image prompt engine prompt (Stage 6 - Fire)
     *
     * @param array $sceneJson
     * @param string $emotionalDriver
     * @return string
     */
    public function buildImagePromptFromScene(
        array $sceneJson,
        string $emotionalDriver = 'Cinematic'
    ): string {
        $systemContext = $this->getAntigravityContext();
        $sceneStr = json_encode($sceneJson, JSON_PRETTY_PRINT);

        return <<<PROMPT
{$systemContext}

**STAGE 6 — IMAGE PROMPT ENGINE**

**INPUTS:**
- **Scene Data**:
{$sceneStr}
- **Emotional Driver**: {$emotionalDriver}

**TASK:**
For each scene, generate a unique cinematic image generation prompt.

**RULES:**
- MANDATORY 16:9 widescreen aspect ratio (YouTube format).
- Rotate lighting styles (e.g., chiaroscuro, golden hour, neon noir).
- Rotate camera angles (e.g., wide shot, close-up, drone view).
- Avoid repeating descriptive patterns.
- Match emotional tone.
- Ultra-detailed visual language.

**OUTPUT FORMAT (JSON ONLY):**
```json
[
  {
    "scene_number": 1,
    "image_prompt": "...",
    "lighting_style": "...",
    "camera_angle": "...",
    "color_palette": "..."
  }
]
```
PROMPT;
    }

    /**
     * Build thumbnail engine prompt (Stage 7)
     */
    public function buildThumbnailPrompt(
        string $selectedTitle,
        string $emotionalDriver,
        string $primaryConflict,
        string $tier = 'PRO'
    ): string {
        $directive = $this->getSystemDirective(['tier' => $tier]);

        return <<<PROMPT
{$directive}

**TASK: STEP 6 — THUMBNAIL PROMPT**
Generate viral thumbnail assets for: {$selectedTitle}

**INPUTS:**
- Emotion: {$emotionalDriver}
- Conflict: {$primaryConflict}

**OUTPUT SCHEMA (MANDATORY):**
{
  "status": "SUCCESS",
  "content": {
    "thumbnail_text_options": ["...", "..."],
    "prompt_data": {
       "overall_mood": "...",
       "detailed_visual_instructions": "Photorealistic, 8K, cinematic YouTube widescreen (MANDATORY 16:9 aspect ratio). [EXTREME DETAIL]"
    }
  }
}
PROMPT;
    }

    /**
     * Build cinematic image prompt for a scene (Helper method)
     */
    public function buildImagePrompt(
        $scene,
        array $characterReferences = []
    ): string {
        // If scene is already a string (legacy support)
        if (is_string($scene)) {
            $charDetails = !empty($characterReferences) ? "\nCharacters: " . implode(', ', $characterReferences) : "";
            return "16:9 widescreen, photorealistic, cinematic, high quality, {$scene}{$charDetails}";
        }

        // If we have modular prompt data, use the high-fidelity builder
        if (isset($scene['visual_prompt_data']) && !empty($scene['visual_prompt_data'])) {
            return $this->buildUniversalModularPrompt($scene['visual_prompt_data']);
        }

        // Fallback to visual_prompt or narration
        $prompt = $scene['visual_prompt'] ?? $scene['narration_text'] ?? 'Cinematic 8k shot';
        return "16:9 widescreen, photorealistic, cinematic, {$prompt}";
    }

    /**
     * Map structured 13-layer data into a universal high-fidelity cinematic prompt.
     */
    public function buildUniversalModularPrompt(array $data): string
    {
        $layers = [];
        
        // Character Layers
        if (!empty($data['primary_subject'])) $layers[] = "Primary Subject: {$data['primary_subject']}";
        if (!empty($data['antagonist_or_opposing_character'])) $layers[] = "Conflict: {$data['antagonist_or_opposing_character']}";
        if (!empty($data['secondary_character'])) $layers[] = "Secondary: {$data['secondary_character']}";
        
        // Environment Layers
        if (!empty($data['environment_foreground'])) $layers[] = "Foreground: {$data['environment_foreground']}";
        if (!empty($data['environment_midground'])) $layers[] = "Midground: {$data['environment_midground']}";
        if (!empty($data['environment_background'])) $layers[] = "Background: {$data['environment_background']}";
        if (!empty($data['symbolic_overlay'])) $layers[] = "Symbolic: {$data['symbolic_overlay']}";
        
        // Technical Aesthetics
        if (!empty($data['camera_settings'])) $layers[] = "Camera: {$data['camera_settings']}";
        if (!empty($data['lighting'])) $layers[] = "Lighting: {$data['lighting']}";
        if (!empty($data['color_palette'])) $layers[] = "Vibe & Color: {$data['color_palette']}";
        if (!empty($data['overall_mood'])) $layers[] = "Mood: {$data['overall_mood']}";
        if (!empty($data['detailed_visual_instructions'])) $layers[] = "Hyper-Details: {$data['detailed_visual_instructions']}";
        if (!empty($data['composition_guidance'])) $layers[] = "Composition: {$data['composition_guidance']}";

        return "16:9 cinematic shot, 8k photorealistic, unreal engine 5 style, hyper-realistic detail, " . implode(', ', $layers);
    }

    /**
     * Legacy/Combined (Stub for interface compatibility)
     */
    public function buildScriptPrompt(
        string $topic,
        string $niche,
        ?string $subNiche,
        int $durationMinutes,
        string $tier1Country,
        string $title,
        string $megaHook
    ): string {
        return "REFACTORED TO MULTI-STEP. PLEASE USE STEP-SPECIFIC BUILDERS.";
    }

    /**
     * Get Tier-1 localization guidelines
     */
    protected function getTier1LocalizationGuidelines(string $country): string
    {
        $guidelines = [
            'USA' => "   - Dialect: American English (Spelling/Grammar)\n   - Cultural References: US Landmarks, Legal System, Pop Culture, Holidays\n   - Units: Fahrenheit, Miles",
            'UK' => "   - Dialect: British English (Spelling/Grammar)\n   - Cultural References: UK Landmarks, Social Norms, Pop Culture\n   - Units: Celsius, Kilometres (mostly)",
            'Canada' => "   - Dialect: Canadian English\n   - Cultural References: Canadian Cities, Wilderness, Hockey/Sports, Bilingual Context\n   - Units: Celsius, Kilometres",
            'Australia' => "   - Dialect: Australian English\n   - Cultural References: Australian Landmarks, Wildlife, Sport (AFL/Cricket)\n   - Units: Celsius, Kilometres",
            'Ireland' => "   - Dialect: Irish English\n   - Cultural References: Irish History, Landscapes, Local Slang\n   - Units: Celsius, Kilometres",
            'New Zealand' => "   - Dialect: New Zealand English\n   - Cultural References: Maori Culture, NZ Landscapes, Rugby\n   - Units: Celsius, Kilometres",
        ];

        return $guidelines[$country] ?? $guidelines['USA'];
    }

    /**
     * Get duration-specific pacing guidelines
     */
    protected function getDurationPacingGuidelines(int $durationMinutes): string
    {
        $guidelines = [
            30 => "   - Pacing: Fast, high-energy.\n   - Structure: ~3 mins per chapter.\n   - Focus: Rapid plot progression, tight scenes.",
            45 => "   - Pacing: Moderate, balanced.\n   - Structure: ~4.5 mins per chapter.\n   - Focus: Character depth + plot action.",
            60 => "   - Pacing: Slow burn, immersive.\n   - Structure: ~6 mins per chapter.\n   - Focus: Deep world-building, complex character arcs.",
        ];

        return $guidelines[$durationMinutes] ?? $guidelines[45];
    }

    /**
     * Build enhanced structure prompt with Bible Logic and niche templates
     * This is the new method that integrates all extracted components
     */
    public function buildStructurePromptWithBible(
        string $topic,
        string $niche,
        int $durationMinutes,
        string $tier1Country,
        string $title,
        string $megaHook,
        string $tier = 'PRO',
        ?string $century = null
    ): string {
        // Get niche template
        $template = $this->nicheTemplateService->getTemplate($niche);
        $diverseNamePool = $this->nicheTemplateService->getRandomDiverseNamePool();
        
        // Get system instruction with uniqueness
        $systemInstruction = $this->systemInstructionBuilder->buildWithUniqueness();
        
        // Calculate chapter count based on duration
        $chapterCount = $this->calculateChapterCount($durationMinutes);
        
        // Build niche-specific training section
        $trainingSection = '';
        if ($template) {
            $trainingSection = $this->buildTrainingSection($template, $niche);
        }
        
        // Build era section for era-based niches
        $eraSection = '';
        if ($century && in_array($niche, ['Conspiracy', 'Adventure', 'Discovery'])) {
            $eraSection = $this->buildEraSection($century);
        }
        
        // Build Bible Logic constraints
        $bibleConstraints = $this->buildBibleConstraints($tier1Country, $diverseNamePool);
        
        return <<<PROMPT
{$systemInstruction}

**TASK: ARCHITECT MARATHON STORY STRUCTURE WITH BIBLE**

Title: {$title}
Initial Hook: "{$megaHook}"
Niche: {$niche}
Country: {$tier1Country}
Target Duration: {$durationMinutes} minutes
{$eraSection}
{$trainingSection}

{$bibleConstraints}

**CHAPTER REQUIREMENTS:**
1. You MUST generate EXACTLY {$chapterCount} CHAPTERS. Do not generate fewer or more.
2. Chapters must follow a 3-act cinematic arc appropriate to the niche.
3. Keep chapter summaries concise but engaging.
4. Each chapter should end with a cliffhanger or hook.
5. Vary pacing—some chapters slow-burn, others fast-paced.

**AUTHENTICITY REQUIREMENTS:**
- Ground story in real or plausible events with specific details
- Use sensory details (sounds, smells, textures, specific locations)
- Avoid template patterns—make this story feel unique
- Emotional beats must be earned through character actions
- No AI slang (tapestry, delve, shrouded, unravel, vibrant, testament, realm, whirlwind, odyssey)

**OUTPUT SCHEMA (MANDATORY JSON):**
{
  "bible": {
    "characters": [
      {
        "name": "Full unique name",
        "role": "Archetype/epithet (e.g., THE AVENGING FATHER)",
        "appearance": "Very detailed physical profile (age, stature, build, skin, eyes, hair, clothing, distinctive features)",
        "motive": "Deep desire or drive",
        "backstory": "Brief but rich backstory",
        "objectives": "Concrete goals/arc in this story",
        "roleInStory": "Narrative function, how they drive plot"
      }
    ],
    "locations": [
      {
        "name": "Specific location name",
        "description": "Detailed description with sensory details",
        "significance": "Why this location matters to the story"
      }
    ],
    "keyItems": [
      {
        "name": "Item name",
        "description": "What it is",
        "significance": "Why it matters"
      }
    ],
    "plotPoints": [
      "Key plot point 1",
      "Key plot point 2",
      "Key plot point 3"
    ]
  },
  "chapters": [
    {
      "chapter_number": 1,
      "title": "Chapter title",
      "hook_text": "A high-stakes narrative hook for this specific chapter",
      "summary": "Detailed narrative summary (100-150 words)",
      "emotional_beat": "Target emotion for this chapter",
      "production_directives": {
        "pacing": "...",
        "visual_direction": "...",
        "camera_movement": "...",
        "sound_design": "..."
      }
    }
  ]
}

Output ONLY valid JSON. No markdown backticks, no commentary.
PROMPT;
    }

    /**
     * Build training section from niche template
     */
    private function buildTrainingSection(?array $template, string $niche): string
    {
        if (!$template) {
            return '';
        }

        $training = "\n**TRAINING — FOLLOW THIS STORY STRUCTURE AND TONE:**\n";
        
        if (isset($template['storyBeats'])) {
            $training .= $template['storyBeats'] . "\n";
        }
        
        if (isset($template['openingStyle'])) {
            $training .= "\n**OPENING TONE (match this emotional pull):**\n";
            $training .= $template['openingStyle'] . "\n";
        }
        
        return $training;
    }

    /**
     * Build era section for era-based stories
     */
    private function buildEraSection(string $century): string
    {
        $centuryOptions = [
            '1400s' => ['label' => '1400s-1500s (Age of Discovery)', 'yearMin' => 1400, 'yearMax' => 1599],
            '1600s' => ['label' => '1600s-1700s (Colonial Era)', 'yearMin' => 1600, 'yearMax' => 1799],
            '1800s' => ['label' => '1800s (Industrial Revolution)', 'yearMin' => 1800, 'yearMax' => 1899],
            '1900s' => ['label' => '1900s-1940s (World Wars Era)', 'yearMin' => 1900, 'yearMax' => 1949],
            '1950s' => ['label' => '1950s-1980s (Cold War Era)', 'yearMin' => 1950, 'yearMax' => 1989],
            '1990s' => ['label' => '1990s-2020s (Modern Era)', 'yearMin' => 1990, 'yearMax' => 2020],
        ];

        $centuryData = $centuryOptions[$century] ?? null;
        if (!$centuryData) {
            return '';
        }

        return <<<ERA

**STORY ERA (CRITICAL):**
The user selected {$centuryData['label']}. Set the ENTIRE story in this period.
Choose ONE specific year between {$centuryData['yearMin']} and {$centuryData['yearMax']} and ground every detail—technology, transport, dress, dialogue, politics—in that single year.
State the chosen year in the first chapter summary or in plotPoints.

ERA;
    }

    /**
     * Build Bible Logic constraints
     */
    private function buildBibleConstraints(string $country, string $diverseNamePool): string
    {
        $forbiddenNames = implode(', ', $this->systemInstructionBuilder->getForbiddenNames());
        
        return <<<CONSTRAINTS
**BIBLE REQUIREMENTS (CRITICAL):**

1. **EXACTLY 4 CHARACTERS**: The bible MUST contain exactly 4 characters in bible.characters.
   - Do NOT omit or return an empty array
   - Do NOT generate fewer than 4 or more than 4
   - This is a strict requirement

2. **DETAILED CHARACTER PROFILES**: For EACH of the 4 characters provide:
   - **name**: Full unique name—MUST be diverse and fit the story's country ({$country})
   - **role**: Archetype/epithet (e.g., "THE AVENGING FATHER", "THE ARCHITECT OF GREED")
   - **appearance**: Very detailed physical profile (age, stature, build, skin, eyes, hair, clothing, distinctive features, cinematic notes)
   - **motive**: What they want (deep desire or drive)
   - **backstory**: Brief but rich backstory that shapes who they are
   - **objectives**: What they are looking to accomplish in this story (concrete goals/arc)
   - **roleInStory**: Their role in the whole story—narrative function, how they drive the plot

3. **CHARACTER NAME DIVERSITY**:
   - FORBIDDEN NAMES (NEVER USE): {$forbiddenNames}
   - USE DIVERSE NAMES: For this story, prefer {$diverseNamePool}
   - Names must fit the story's country ({$country}) and setting
   - Examples of good diverse names: Priya Sharma, Dmitri Volkov, Fatima Okonkwo, Liam O'Brien, Yuki Tanaka, Amara Diallo, Rafael Mendoza, Zara Kowalski, Ingrid Bergström, Koji Yamamoto
   - Never repeat the same names across stories—each story gets a distinct cast

4. **LOCATIONS**: Include at least 2-3 specific locations with detailed descriptions

5. **KEY ITEMS**: Include at least 1-2 key items that drive the plot

6. **PLOT POINTS**: Include at least 3-5 major plot points

These profiles will be used to write scripts that keep viewers hooked till the very end.
CONSTRAINTS;
    }

    /**
     * Calculate chapter count based on duration
     */
    private function calculateChapterCount(int $durationMinutes): int
    {
        if ($durationMinutes <= 15) {
            return 5;
        } elseif ($durationMinutes <= 30) {
            return 8;
        } elseif ($durationMinutes <= 60) {
            return 12;
        } else {
            return 15;
        }
    }

    /**
     * Build fallback character generation prompt
     * Used when initial generation fails to produce exactly 4 characters
     */
    public function buildFallbackCharacterPrompt(
        string $title,
        string $niche,
        string $country,
        string $megaHook
    ): string {
        $diverseNamePool = $this->nicheTemplateService->getRandomDiverseNamePool();
        $bibleConstraints = $this->buildBibleConstraints($country, $diverseNamePool);
        $systemInstruction = $this->systemInstructionBuilder->buildWithUniqueness();

        return <<<PROMPT
{$systemInstruction}

**TASK: REGENERATE CHARACTERS FOR STORY BIBLE**

We are building a story bible for:
Title: "{$title}"
Niche: {$niche}
Hook: "{$megaHook}"

The previous generation failed to provide exactly 4 characters.
You must now generate ONLY the character list.

{$bibleConstraints}

**OUTPUT SCHEMA (MANDATORY JSON):**
{
  "characters": [
    {
      "name": "Full unique name",
      "role": "Archetype/epithet",
      "appearance": "Very detailed physical profile",
      "motive": "Deep desire or drive",
      "backstory": "Brief but rich backstory",
      "objectives": "Concrete goals/arc",
      "roleInStory": "Narrative function"
    }
  ]
}

Output ONLY valid JSON.
PROMPT;
    }
}
