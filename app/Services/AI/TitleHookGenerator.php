<?php

namespace App\Services\AI;

class TitleHookGenerator
{
    private NicheTemplateService $nicheTemplateService;
    private SystemInstructionBuilder $systemInstructionBuilder;

    public function __construct(
        NicheTemplateService $nicheTemplateService,
        SystemInstructionBuilder $systemInstructionBuilder
    ) {
        $this->nicheTemplateService = $nicheTemplateService;
        $this->systemInstructionBuilder = $systemInstructionBuilder;
    }

    /**
     * Generate titles, mega-hook, and thumbnail concept
     * Implements logic from source function Jk
     */
    public function generate(string $niche, string $country): string
    {
        $template = $this->nicheTemplateService->getTemplate($niche);
        $titleGuidance = $this->nicheTemplateService->getTitleGuidance($niche);
        $systemInstruction = $this->systemInstructionBuilder->buildConceptInstruction();
        
        $userExampleTitles = "";
        if ($template && !empty($template['titles'])) {
            $titles = implode("\n- ", $template['titles']);
            $userExampleTitles = "USER STORY STYLE EXAMPLES (MATCH THIS VIBE):\n- {$titles}";
        }

        // Logic for specific niches (Mafia/True Crime/etc)
        $nicheSpecificInstruction = "";
        if ($niche === 'Mafia') {
            $nicheSpecificInstruction = "Focus on emotional hooks, curiosity gaps, 'what he did next', vulnerable character meets feared boss.";
        } elseif ($niche === 'True Crime') {
            $nicheSpecificInstruction = "Use viral documentary titles, factual, gripping, real names and dates. End with '| True Crime Documentary'.";
        }

        return <<<PROMPT
{$systemInstruction}

**TASK: GENERATE VIRAL TITLES, HOOK, AND THUMBNAIL CONCEPT**

Niche: {$niche}
Country: {$country}

{$userExampleTitles}

**TITLE GUIDANCE:**
{$titleGuidance}
{$nicheSpecificInstruction}

**REQUIREMENTS:**
1. **5 VIRAL TITLES**: High CTR, emotional, curiosity-inducing. Must be unique to this user.
2. **MEGA-HOOK (30s)**: A gripping 4-6 sentence opening hook that grabs attention immediately.
   - Must match the niche's opening style
   - Use sensory details and emotional stakes
   - No "In this video..." or generic intros
3. **THUMBNAIL CONCEPT**: A detailed 150-200 word description of the perfect thumbnail.
   - 2-3 people in frame
   - Specific emotions (fear, tears, shock)
   - Photorealistic, 8K, cinematic lighting
   - 16:9 aspect ratio

**OUTPUT SCHEMA (MANDATORY JSON):**
{
  "titles": [
    "Title 1",
    "Title 2",
    "Title 3",
    "Title 4",
    "Title 5"
  ],
  "megaHook": "The full 30-second hook text...",
  "thumbnailConcept": "Concise 150-200 word thumbnail prompt..."
}

Output ONLY valid JSON.
PROMPT;
    }

    /**
     * Regenerate titles only
     * Implements logic from source function Qk
     */
    public function regenerateTitles(string $niche, string $country, string $existingHook): string
    {
        $titleGuidance = $this->nicheTemplateService->getTitleGuidance($niche);
        $systemInstruction = $this->systemInstructionBuilder->buildWithUniqueness();

        return <<<PROMPT
{$systemInstruction}

**TASK: GENERATE 5 NEW VIRAL TITLES**

Niche: {$niche}
Country: {$country}
Existing Hook: "{$existingHook}"

**TITLE GUIDANCE:**
{$titleGuidance}

**REQUIREMENTS:**
- Generate 5 NEW, DISTINCT titles that fit the existing hook
- specific, emotional, high CTR
- Do NOT repeat previous titles

**OUTPUT SCHEMA (MANDATORY JSON):**
{
  "titles": [
    "Title 1",
    "Title 2",
    "Title 3",
    "Title 4",
    "Title 5"
  ]
}

Output ONLY valid JSON.
PROMPT;
    }

    /**
     * Regenerate hook only
     * Implements logic from source function Zk
     */
    public function regenerateHook(string $niche, string $country, string $title): string
    {
        $template = $this->nicheTemplateService->getTemplate($niche);
        $systemInstruction = $this->systemInstructionBuilder->buildWithUniqueness();
        
        $openingStyle = "";
        if ($template && isset($template['openingStyle'])) {
            $openingStyle = "**OPENING STYLE (MATCH THIS):**\n" . $template['openingStyle'];
        }

        return <<<PROMPT
{$systemInstruction}

**TASK: GENERATE A NEW 30-SECOND MEGA-HOOK**

Niche: {$niche}
Country: {$country}
Title: "{$title}"

{$openingStyle}

**REQUIREMENTS:**
- 4-6 sentences, approx 30 seconds
- Gripping, emotional, immediate immersion
- No "Welcome back" or generic intros
- Must hook the viewer instantly

**OUTPUT SCHEMA (MANDATORY JSON):**
{
  "megaHook": "The new hook text..."
}

Output ONLY valid JSON.
PROMPT;
    }
}
