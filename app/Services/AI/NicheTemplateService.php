<?php

namespace App\Services\AI;

class NicheTemplateService
{
    /**
     * Get template for a specific niche
     */
    public function getTemplate(string $niche): ?array
    {
        $templates = $this->getAllTemplates();
        return $templates[$niche] ?? null;
    }

    /**
     * Get title guidance for a niche
     */
    public function getTitleGuidance(string $niche): string
    {
        $guidance = [
            'Mafia' => 'Emotional, curiosity gap, "what he did next", vulnerable character meets feared boss, no one would forget. Match faceless story channels: dramatic caps, ellipses, "— What Happened Next".',
            'True Crime' => 'Viral documentary titles: "The Gruesome Murder That Left Investigators in Shock", "CCTV CAPTURES COLD-BLOODED MURDER IN SILENCE", "CAUGHT ON CAMERA | The Most Terrifying Last Moments Ever Heard", "The Most Shocking Murder You Won\'t Believe is Real", "Chilling Murder in Broad Daylight". End with "| True Crime Documentary". Factual, gripping, real names and dates.',
            'Scam / Con Artist' => 'Documentary-style: real or inspired-by-real con stories. "How She Stole Millions From...", "The [Name] Scam: What Really Happened", "When They Discovered He Wasn\'t Who He Said". Real names/dates where applicable; high CPM niche.',
            'Prison' => 'Life inside, wrongful conviction, survival, "what he did to survive", "the day everything changed". Documentary: "Inside [Prison Name]: The Inmate Who...", "He Was Innocent — What Happened Next". Real or plausible names, specific facilities and dates.',
            'Cartel' => 'Documentary, real or inspired-by events. "The Cartel Boss Who...", "What Happened When [Name] Crossed The Cartel", "Inside The [Region] Drug War". Specific dates, locations, no glorification; factual tone.',
            'Evil Stepmother' => 'Family betrayal, inheritance, "when they found the will", Cinderella-style cruelty. Faceless: "She Treated Them Like Slaves — The Day The Truth Came Out", "What He Left In The Will Changed Everything".',
            'Mystery / Unsolved' => 'Disappearances, cold cases, "what really happened", "the case that baffled". Documentary: "The [Name] Disappearance: What We Know", "Unsolved: The [Location] Mystery". Real or plausible names and dates.',
            'Celebrity Downfall' => 'Rise and fall, "what really happened to...", scandals, comeback. Documentary/faceless: "The [Name] Story: What Nobody Knew", "How [Celebrity] Lost Everything — And What Happened Next". Real or inspired-by names.',
            'Survival / Stranded' => 'Plane crash, island, "how they survived", "what they found in the jungle". Documentary: "They Were Stranded For [X] Days — What They Did To Survive", "The [Place] Survivors: What Really Happened". Binge-worthy, specific dates and locations.',
            'Royal / Historical Drama' => 'Kings, queens, betrayal, "the queen who...", "the king\'s secret". Faceless: "The Queen Who [Specific] — What Happened Next", "The King\'s Secret That Changed Everything". Big reveals, chapter-driven.',
            'Undercover / Secret Agent' => '"He was a cop the whole time", "When they found out who he really was". Thriller/documentary: "The Man Who Infiltrated [Organization] — What He Did Next", "They Never Knew He Was Working For [Agency]".',
            'Redemption / Second Chance' => 'Ex-con, addict, "how he turned his life around". Inspirational but story-driven: "He Spent [X] Years In Prison — What He Did Next Changed Everything", "The Addict Who Became [Outcome]".',
            'Kidnapping / Hostage' => '"What she did to survive", "The day they were taken". High tension: "She Was Held For [X] Days — What She Did To Survive", "The Kidnapping That [Outcome]". Documentary tone, real or plausible names.',
            'African Folktales' => 'Curiosity + dread + vague danger. Modern African moral folktales: "THEY NEVER THOUGHT THIS WOULD HAPPEN…", "HE THREW HER OUT ON VALENTINE\'S DAY — THEN THIS HAPPENED", "YOU WILL NEVER PUT YOUR CHILD IN BOARDING SCHOOL AFTER WATCHING THIS". Luxury hides evil, trust instincts, contrast (beautiful place then hidden danger). Faceless narration, 15–25 min stories, moral lesson, "Every story has a lesson."',
            'Conspiracy' => 'Hidden truth, cover-up, "what they don\'t want you to know", secret societies, suppressed discovery. Documentary/faceless: "The [Subject] They Tried To Bury — What One Researcher Found", "Why [Institution] Never Wanted This Made Public", "The Discovery That Changed Everything — And Was Hidden For [X] Years". Era-based when century chosen (1400s to present).',
            'Adventure' => 'Exploration, expedition, lost place, treasure, survival in unknown land. "The Expedition That Found [X]", "What They Discovered In The [Place] No One Returned From", "Lost For [X] Years — What They Brought Back". Era-based (century selection); can blend historical and discovery.',
            'Discovery' => 'Scientific or historical breakthrough, lost civilization, artifact, "what was really there". "The [Discovery] That Rewrote History", "What They Found Under [Place] After [X] Years", "The Truth About [Subject] — Finally Revealed". Era-based (century); factual, documentary tone.',
        ];

        return $guidance[$niche] ?? 'Emotional hooks, curiosity gap, "what happened next", faceless story style: dramatic, click-worthy, no face in thumbnail.';
    }

    /**
     * Get a random diverse name pool
     */
    public function getRandomDiverseNamePool(): string
    {
        $pools = [
            "South Asian, Eastern European, and West African name styles",
            "Latin American, East Asian, and Nordic name styles",
            "Middle Eastern, Southeast Asian, and Irish name styles",
            "African, Caribbean, and Central European name styles",
            "East European, South Asian, and Hispanic name styles",
            "Mixed cultural origins so the cast feels globally diverse and unique"
        ];

        return $pools[array_rand($pools)];
    }

    /**
     * Get all niche templates
     */
    private function getAllTemplates(): array
    {
        return [
            'Mafia' => [
                'titles' => [
                    'Little Girl Ran To Mafia Boss Crying, "They\'re Beating My Mama!" — What the Mafia Boss Did Left...',
                    'Mafia Boss Was Left At The Altar And Married A Beggar — What Happened Next Left Everyone In Tears',
                    'Mafia Boss Insults Waitress In Sicilian—Then Froze When She Responds Back Fluently.'
                ],
                'openingStyle' => 'Her tiny hands were shaking as she tugged at the stranger\'s sleeve. Tears streaked down her face, her voice breaking with every word. "They hurt my mama. She\'s dying." The room fell silent. Glasses clinked to a stop. Every eye turned. But the man she had chosen to beg was no ordinary stranger. He was the most feared mafia boss in the city. And what he did next, no one in that room would ever forget.',
                'storyBeats' => "MAFIA BOSS STORY BEATS (use for tone and structure):\n- Cold, feared crime boss in his domain (restaurant, club); methodical, no mercy, \"sentiment was weakness.\"\n- Disruptive entrance: vulnerable person (e.g. little girl, woman in distress) bursts in and goes straight to the boss.\n- One emotional plea that cracks the boss's armor (e.g. \"They hurt my mama. She's dying.\").\n- Boss's hidden backstory: past loss (e.g. wife Maria killed by rivals) that made him close off his heart for decades.\n- Boss chooses to help despite reputation; orders trusted men (doctor, enforcers); gentle with the victim, deadly with the perpetrators.\n- Confrontation with villains (enforcers, rival gang); boss exposes their cruelty (e.g. beating mother over \$67); moral reckoning.\n- Boss meets higher-up (e.g. gang leader); demands restitution to victims, territory cleared, \"lines we don't cross.\"\n- Resolution: victim family safe; boss visits as a different man; theme: \"smallest hands carry the greatest power to change everything.\"\n- Style: dense narration, 1hr+ marathon, cliffhangers, visceral language, 1980s period optional (e.g. Chicago 1987).",
                'thumbnailStyle' => null
            ],

            'True Crime' => [
                'titles' => [
                    'The Gruesome Murder That Left Investigators in Shock | True Crime Documentary',
                    'CCTV CAPTURES COLD-BLOODED MURDER IN SILENCE | True Crime Documentary',
                    'CAUGHT ON CAMERA | The Most Terrifying Last Moments Ever Heard | True Crime Documentary',
                    'The Most Shocking Murder You Won\'t Believe is Real | True Crime Documentary',
                    'Most Demonic Serial Killers You\'ve Never Heard Of | True Crime Documentary',
                    'Her Daughter Was Found Dead During Carnival Cruise — 6 YRS Later, She Saw Her With Kids & Her Husband',
                    'CAUGHT ON CAMERA: Chilling Murder in Broad Daylight | True Crime Documentary',
                    'The [Full Name] Case: What Really Happened the Night Everything Changed | True Crime'
                ],
                'openingStyle' => 'Option A — Timeline hook: "[Exact date], [exact time]. A 911 call comes in... / By sunrise they find... / By noon the search is called off." Option B — Discovery hook: "In [year], a man logged into [chat room / did X]... And what he [typed/did] next would lead investigators down a trail that ended at [storage locker / barrels / etc.]." Option C — Victim lead: "A [student/woman] mysteriously disappeared... The police quickly initiated a search. Step by step, investigators pieced together the truth." Then: [Victim] was born on [exact date] in [city, state/country]. Real names. Real dates. This is what really happened.',
                'storyBeats' => "TRUE CRIME STORY BEATS (match viral documentary structure and tone):\n\nSINGLE-CASE / SERIAL KILLER:\n1. VICTIM OR KILLER BACKGROUND: Full names, birth dates and places, family, upbringing. For killers: \"nothing that screamed future predator\" or early signs (head injury, abuse). For victims: education, jobs, traits. Specific details (Eagle Scout, choir, forged letters, BDSM screen names, barrels, toy box).\n2. PATTERN & CRIMES: Exact dates. Method (strangulation, hammer, barrels, chemical preservation). Typed letters to families, forged adoption papers, internet as hunting tool. Real locations (storage locker Raymore, Lasia Kansas, Elephant Butte, cruise ship deck 7).\n3. DISCOVERY: \"The smell hit them first.\" Barrels, drums, storage units. Who found, when. Forensic evidence (firing pin match, bite marks, fingerprints).\n4. ARREST & TRIAL: Interrogation (suspect claims innocence, \"someone planted the bodies\"). Longest trial in state history. Jury deliberation (e.g. 15 minutes, 48 minutes). Verdict and sentence (death row, life without parole, execution date).\n5. AFTERMATH: \"Today [Name] sits on death row at [facility].\" First to use internet as primary hunting tool, etc. Sign-off: \"If you found this case fascinating, like and subscribe. We post crime case videos every night. Until next time, stay sharp, stay vigilant.\"\n\nNARRATIVE TWIST (fake death / years-later sighting):\n1. INCIDENT: Specific time (3:47 a.m., 911 from cruise ship). Girl missing, phone by railing, empty coffin. Mother's grief (screams at grave, 6 years).\n2. FAMILY & SUSPECT: Mother's backstory (single mother, nurse, met Derek). Suspect (stepfather): charming, \"knew what you wanted to hear\", gambling debts, grooming. Victim's journal entries (age 14–16): \"He makes me feel seen\", kiss, \"we're planning something.\"\n3. THE LIE: Cruise, camera blind spots, crew member paid, fake passport, Guatemala. Divorce 6 weeks later, no forwarding address. Phone records and wire transfers (subpoenaed but case closed).\n4. SIGHTING: Years later, marketplace (e.g. Puerto Rico). Mother sees woman who looks like dead daughter — birthmark, walk, children. Man kisses her head: stepfather. \"Mama.\" Confrontation at yellow house, gun, arrest.\n5. TRIAL & AFTERMATH: Voluntary manslaughter, 8 years. Daughter in federal prison (fraud, conspiracy). Children with aunt. \"Was it justice or murder? You decide.\" Sign-off and CTA.\n\n- Use REAL NAMES, REAL DATES, REAL LOCATIONS for actual cases. For inspired-by stories use plausible full names and specific dates/places in the chosen country.\n- Tone: investigative, dense narration, 20–28 min. Match country's legal terms. Channel CTA at end.",
                'thumbnailStyle' => "TRUE CRIME THUMBNAIL — choose one style for the story:\n(1) SPLIT CCTV + PORTRAIT: Left half grainy CCTV/surveillance moment (hallway, elevator, caution tape at bottom). Right half clear photorealistic portrait of victim, smiling or neutral. Red arrow or \"last moments\" banner. 16:9, photorealistic, 8K.\n(2) FAMILY/CRUISE + RED CIRCLE: Family of three on dock or vacation (woman, child, man), cruise ship or beach behind. Red circle drawn around the key person (the \"daughter\" or victim). Smiling, normal moment contrasting with tragic title. Photorealistic, 8K, high contrast.\n(3) DARK GRAPHIC: Deep red or black background. Silhouette of gallows with noose, or male figure. Bold white text: \"MALEVOLENT\" or \"SERIAL KILLERS THAT WALKED AMONG US\". Minimalist, ominous, no faces. For serial-killer / demonic-killer titles.\nOutput one coherent 16:9 prompt. Photorealistic unless style (3). High YouTube engagement."
            ],

            'Scam / Con Artist' => [
                'titles' => [
                    'How She Stole Millions From The Rich — What Happened When They Found Out | Documentary',
                    'The Con Artist Who Married 5 Women — When The FBI Caught Up | True Story',
                    'He Posed As A Doctor For 12 Years — The Day One Patient Discovered The Truth',
                    'The [Full Name] Scam: What Really Happened To The Money | Documentary'
                ],
                'openingStyle' => 'Option A — Timeline: "[Exact date]. [Specific place]. [Victim/target] received a [call/email/meeting] that would cost them [amount] and years of their life." Option B — Revelation: "For [X] years they believed he was [identity]. Then one [document/phone call/witness] changed everything." Option C — Aftermath: "By the time [authority] caught up with [Name], [number] people had lost [total]. This is how it happened." Use real or plausible full names, specific dates and amounts; documentary tone.',
                'storyBeats' => "SCAM / CON ARTIST STORY BEATS (documentary-style; real or inspired-by-real events):\n\n1. SETUP: Con artist background—real name (or plausible), origin, early schemes. How they built the persona (fake credentials, forged documents, invented backstory). Specific dates, banks, companies, locations. Victims: who they targeted and why (wealthy, lonely, trusting). Use UNIQUE character names; avoid overused names (Emily, Sarah, Marcus, Vance, Thorne, James Mitchell).\n2. THE CON: Step-by-step how the scam worked. Specific amounts, wire transfers, fake contracts, meetings. Emotional hooks: promises made, trust earned, red flags ignored. Real-world details (account numbers, jurisdictions, paper trails).\n3. UNRAVELING: First victim who got suspicious; investigator or journalist who started digging; one document or witness that broke the case. Timeline of discovery. Do not use a template—vary whether it's law enforcement, a victim, or media that exposes.\n4. FALL: Arrest, extradition, trial. Specific charges, verdict, sentence. Where they are now (prison, released, still at large). Quotes or courtroom moments that feel real.\n5. AFTERMATH: Victims' lives after; reforms or regulations that followed. Sign-off: documentary CTA. Tone: factual, no glorification; high CPM-friendly (advertiser-safe). Each story must feel like a distinct case—different structure and pacing where appropriate.",
                'thumbnailStyle' => 'SCAM/CON ARTIST THUMBNAIL: (1) Split: con artist (or silhouette) one side, victim or detective other side; OR (2) courtroom/arrest moment; OR (3) money/documents with red "EXPOSED" text. Photorealistic, 16:9, 8K, high contrast. No glorification; serious documentary feel.'
            ],

            // Add remaining 25 niches...
            'Prison' => [
                'titles' => [
                    'Inside [Prison Name]: The Inmate Who Changed Everything | Documentary',
                    'He Was Sentenced To Life — What He Did Next Shocked The Guards',
                    'Wrongfully Convicted: The 23 Years He Lost — What Happened When The Truth Came Out',
                    'The Prison Riot That Left [Number] Dead — What Really Happened That Day'
                ],
                'openingStyle' => 'Option A — Inside: "[Prison name], [year]. [Inmate name] had [X] years left on his sentence. What happened that [day/month] would [change everything/put him in the infirmary/expose a conspiracy]." Option B — Wrongful conviction: "[Name] was [age] when they sent him away. [X] years later, one [DNA test/witness/lawyer] would prove what he had said all along." Use specific facilities, dates, and real or plausible names; documentary tone.',
                'storyBeats' => "PRISON STORY BEATS (documentary-style; real or inspired-by-real events):\n\n1. WHO & WHY: Inmate(s) and their backstory—crime, sentence, facility. Specific prison name, location, year. Guards or staff who play a role. Use UNIQUE full names; avoid overused names (Emily, Sarah, Marcus, Vance, Thorne). If wrongful conviction: evidence that convicted, lawyer, family fighting for them.\n2. LIFE INSIDE: Routine, hierarchy, tensions. Specific incidents (assault, lockdown, riot trigger). Relationships (cellmate, guard, counselor). One event that sets the main story in motion—varies per story (escape attempt, riot, exoneration push, death).\n3. TURNING POINT: The day or moment everything shifts. Riot, escape, legal breakthrough, death, transfer. Concrete details (who did what, when, where). Do not repeat the same formula—some stories are redemption, some survival, some injustice.\n4. AFTERMATH: Outcome (parole, exoneration, death, transfer). Where key figures are now. Reforms or investigations that followed. Documentary sign-off. Tone: factual, humanizing where appropriate; advertiser-safe.",
                'thumbnailStyle' => "PRISON THUMBNAIL: (1) Prison exterior or cell block; (2) inmate in jumpsuit (back or silhouette) with dramatic lighting; (3) courtroom or release moment. Photorealistic, 16:9, 8K, gritty but not gratuitously violent."
            ],

            'Cartel' => [
                'titles' => [
                    'The Cartel Boss Who [Specific Action] — What Happened When [Authority] Closed In | Documentary',
                    'Inside The [Region] Drug War: The Day [Name] Was Captured',
                    'He Worked For The Cartel For [X] Years — What He Did Next Changed Everything',
                    'The [Name] Case: How One Raid Exposed A Billion-Dollar Operation'
                ],
                'openingStyle' => 'Option A — Timeline: "[Exact date], [city/region]. [Cartel name or figure] had controlled [route/plaza] for [X] years. That [day/week], [event] would change everything." Option B — Capture/raid: "When [agency] moved in on [location], they found [specific]. What led them there took [X] years of [investigation/wiretaps/informants]." Use real or plausible names, specific dates and places; factual, no glorification.',
                'storyBeats' => "CARTEL STORY BEATS (documentary-style; real or inspired-by-real events):\n\n1. PLAYERS & TERRITORY: Cartel or organization name (or plausible stand-in), region, period. Key figures—real or plausible full names; avoid overused names (Emily, Sarah, Marcus, Vance, Thorne). Rivals, law enforcement, informants. Specific cities, routes, seizures.\n2. RISE OR REIGN: How they gained power; key incidents (hits, bribes, trials). Concrete details (amounts, dates, locations). One thread that will drive the story (manhunt, betrayal, raid, extradition).\n3. FALL: Operation that brought them down—wiretaps, defectors, raid. Arrest(s), extradition, trial. Vary structure—some stories focus on one kingpin, others on a single operation or informant.\n4. VERDICT & NOW: Sentences, where they are now. Impact on region. Documentary sign-off. Tone: factual, no glorification; advertiser-safe.",
                'thumbnailStyle' => "CARTEL THUMBNAIL: (1) Raid/arrest moment (tactical, not graphic); (2) map or convoy with dramatic lighting; (3) courtroom or perp walk. Photorealistic, 16:9, 8K, serious documentary. No glorification of violence."
            ],

            'Evil Stepmother' => [
                'titles' => [
                    'She Treated Them Like Slaves — The Day The Will Was Read, Everything Changed',
                    'Their Father Left Everything To The Stepmother — What The Kids Did Next',
                    'The Stepmother Who Stole Their Inheritance — How One Lawyer Exposed The Truth',
                    'He Died Without A Will — What The Stepmother Did To His Children'
                ],
                'openingStyle' => 'Option A — Death and will: "When [Father name] died in [year], his children believed [he had provided for them/the estate was secure]. The reading of the will would reveal something none of them expected." Option B — Cruelty first: "[Stepmother name] had made [children names]\'s lives [hell/miserable] for [X] years. Then [event] gave them a chance to fight back." Use distinct full names; avoid overused names (Emily, Sarah, Marcus, Vance, Thorne).',
                'storyBeats' => "EVIL STEPMOTHER STORY BEATS (emotional, justice-oriented; real or plausible names):\n\n1. FAMILY SETUP: Father, children (names, ages), stepmother. How she came into the family; early signs of cruelty or control. Specific incidents (inheritance withheld, abuse, favoritism). Use UNIQUE names fitting country/setting.\n2. DEATH & WILL: Father's death; will reading or discovery of documents. What was left to whom; forgery or manipulation if applicable. Legal stakes (house, business, money).\n3. FIGHT: Children (or one ally) decide to challenge. Lawyer, evidence, courtroom or settlement. One twist (hidden will, witness, document) that shifts the outcome. Vary structure—some stories are court-heavy, others family confrontation.\n4. RESOLUTION: Outcome (inheritance restored, stepmother exposed, partial victory). Where they are now. Emotional closure. Tone: satisfying justice, advertiser-safe.",
                'thumbnailStyle' => "EVIL STEPMOTHER THUMBNAIL: (1) Family tension—stepmother and children in same frame, clear emotion (anger, tears); (2) courtroom or lawyer moment; (3) will/document with dramatic lighting. Photorealistic, 16:9, 8K, emotional."
            ],

            'Mystery / Unsolved' => [
                'titles' => [
                    'The [Name] Disappearance: What We Know So Far | Unsolved Mystery',
                    'She Vanished In [Year] — The Clue That Changed Everything',
                    'Unsolved: The [Location] Case That Baffled Investigators For [X] Years',
                    'The [Name] Case: What Really Happened That Night | Documentary'
                ],
                'openingStyle' => 'Option A — Disappearance: "[Exact date]. [Name], [age], was last seen [specific location and time]. What happened next would become one of [country]\'s most [baffling/enduring] mysteries." Option B — Discovery: "When [who] found [what] in [where], it reopened a case that had been cold for [X] years." Use real or plausible full names and dates; documentary tone; avoid overused names.',
                'storyBeats' => "MYSTERY / UNSOLVED STORY BEATS (documentary-style; real or inspired-by-real):\n\n1. THE CASE: Victim or subject—full name, age, background, last known whereabouts. Specific date, location, circumstances. Why it remains unsolved or what was later discovered. Use UNIQUE names; avoid Emily, Sarah, Marcus, Vance, Thorne.\n2. INVESTIGATION: What police or others did; dead ends, suspects, theories. Key evidence (or lack of it). Family and advocates. Vary structure—some stories focus on one theory, others on timeline of discoveries.\n3. TURNING POINTS: Break in the case (if any), new evidence, or why it stayed cold. Real or plausible details (witnesses, forensics, confessions).\n4. WHERE IT STANDS NOW: Status (solved, partial, still open). Impact on family or policy. Documentary sign-off. Tone: factual, respectful; advertiser-safe.",
                'thumbnailStyle' => 'MYSTERY/UNSOLVED THUMBNAIL: (1) Victim photo or last-known image with "MISSING" or "UNSOLVED" treatment; (2) location (empty road, building) with ominous tone; (3) detective or evidence board. Photorealistic, 16:9, 8K, serious.'
            ],

            'Celebrity Downfall' => [
                'titles' => [
                    'The [Name] Story: What Really Happened — Rise, Fall & Comeback | Documentary',
                    'How [Celebrity] Lost Everything — What Happened Next Changed Everyone',
                    'The Scandal That Destroyed [Name]\'s Career — What Nobody Knew',
                    'From [Peak] To [Low]: The [Name] Story | Behind The Scenes'
                ],
                'openingStyle' => 'Option A — Peak: "In [year], [Name] had [achievement]. By [later year], [what changed]. This is what really happened." Option B — Fall: "The [scandal/event] broke on [exact date]. Within [time], [Name] went from [status] to [new reality]." Use real or inspired-by names; documentary tone; avoid overused names.',
                'storyBeats' => "CELEBRITY DOWNFALL STORY BEATS (documentary-style; rise and fall, scandals, comeback):\n\n1. RISE: Who they were—name, breakthrough moment, peak (awards, fame, wealth). Specific dates, projects, public image. Use UNIQUE names; avoid Emily, Sarah, Marcus, Vance, Thorne.\n2. CRACKS: First signs (rumors, lawsuit, addiction, betrayal). Key incident that started the fall. Behind-the-scenes details that feel real.\n3. FALL: Scandal, arrest, bankruptcy, or exile. Public reaction; what they lost. Vary structure—some stories focus on one scandal, others on a long decline.\n4. AFTERMATH: Where they are now; comeback attempt or quiet life. Documentary sign-off. Tone: factual, no glorification; advertiser-safe.",
                'thumbnailStyle' => "CELEBRITY DOWNFALL THUMBNAIL: (1) Split: glamorous past vs. present (courtroom, mugshot, or humble setting); (2) single dramatic moment (perp walk, press conference); (3) headline-style text overlay. Photorealistic, 16:9, 8K."
            ],

            'Survival / Stranded' => [
                'titles' => [
                    'They Were Stranded For [X] Days — What They Did To Survive | Documentary',
                    'The [Place] Survivors: What Really Happened That Day',
                    'Plane Crash In [Location]: How [Number] People Survived The Impossible',
                    'What They Found In The Jungle Changed Everything | Survival Story'
                ],
                'openingStyle' => 'Option A — Incident: "[Exact date]. [Flight/expedition/ship] [what happened]. [Number] survived. This is how." Option B — Discovery: "When rescuers finally reached [location] on [date], they found [who] alive. [X] days had passed." Use real or plausible names and locations; documentary tone.',
                'storyBeats' => "SURVIVAL / STRANDED STORY BEATS (documentary-style; plane crash, island, jungle, etc.):\n\n1. BEFORE: Who was there—names, purpose (flight, hike, boat). Specific date, location, conditions. Use UNIQUE names; avoid Emily, Sarah, Marcus, Vance, Thorne.\n2. THE INCIDENT: What went wrong (crash, storm, lost). First hours; who lived, who didn't. Concrete details (injuries, supplies, terrain).\n3. SURVIVAL: Days or weeks—shelter, food, water, rescue attempts. Key decisions and turning points. Vary structure—some stories focus on one leader, others on group dynamics.\n4. RESCUE & AFTERMATH: How they were found; where they are now. Documentary sign-off. Tone: factual, human; advertiser-safe.",
                'thumbnailStyle' => "SURVIVAL THUMBNAIL: (1) Wreckage or wilderness with survivors (small figures, dramatic sky); (2) close-up of survivor(s) in harsh environment; (3) rescue moment (helicopter, boat). Photorealistic, 16:9, 8K, dramatic but not gratuitous."
            ],

            'Royal / Historical Drama' => [
                'titles' => [
                    'The Queen Who [Specific Action] — What Happened Next Changed History',
                    'The King\'s Secret That Destroyed The Kingdom | Historical Drama',
                    'She Was Never Supposed To Be Queen — The [Name] Story',
                    'The Betrayal That Toppled A Dynasty | Royal Documentary'
                ],
                'openingStyle' => 'Option A — Reign: "In [year], [Monarch name] [achievement or crisis]. But behind the throne, [secret or rival] was already moving." Option B — Betrayal: "They called her [epithet]. By [date], [what changed]. This is the story they never wanted told." Use distinct full names fitting era and country; avoid overused names.',
                'storyBeats' => "ROYAL / HISTORICAL DRAMA STORY BEATS (kings, queens, betrayal, big reveals):\n\n1. COURT & PLAYERS: Monarch, consort, heirs, rivals. Specific era, kingdom, and names. Use UNIQUE names fitting the period; avoid Emily, Sarah, Marcus, Vance, Thorne.\n2. POWER & SECRETS: Alliances, plots, forbidden love, or hidden heir. One secret or betrayal that drives the story. Concrete events (ball, battle, decree).\n3. CRISIS: Revolt, scandal, or succession crisis. Key confrontation or revelation. Vary structure—some stories are tragedy, others redemption or revenge.\n4. FATE: Outcome (deposed, survived, legacy). Documentary or drama sign-off. Tone: dramatic but grounded; advertiser-safe.",
                'thumbnailStyle' => "ROYAL/HISTORICAL THUMBNAIL: (1) Throne room or palace with figure(s) in period dress; (2) confrontation (queen vs. rival, king and heir); (3) crown or document with dramatic lighting. Photorealistic, 16:9, 8K, cinematic."
            ],

            'Undercover / Secret Agent' => [
                'titles' => [
                    'He Was A Cop The Whole Time — What He Did Next Destroyed The [Organization]',
                    'They Never Knew He Was Working For [Agency] — The Day Everything Changed',
                    'The Man Who Infiltrated [Organization] For [X] Years | Documentary',
                    'When They Found Out Who He Really Was — What Happened Next'
                ],
                'openingStyle' => 'Option A — Infiltration: "For [X] years, [Name] was [role in organization]. His real name was [real name]. He worked for [agency]. This is how it ended." Option B — Reveal: "The raid was set for [date]. What [organization] didn\'t know: one of their own had been [agency] all along." Use real or plausible names; thriller/documentary tone.',
                'storyBeats' => "UNDERCOVER / SECRET AGENT STORY BEATS (infiltration, reveal, thriller):\n\n1. COVER: Undercover identity—name, role, how long. Organization targeted (cartel, gang, corp). Specific operations and locations. Use UNIQUE names; avoid Emily, Sarah, Marcus, Vance, Thorne.\n2. INSIDE: What he/she did to gain trust; close calls; evidence gathered. Key relationships (boss, rival, informant). Vary structure—some stories lead to one big bust, others to long trial.\n3. BLOW: How the operation ended (raid, arrest, escape). The moment they found out. Concrete details (wiretaps, handshake, takedown).\n4. AFTERMATH: Sentences, where they are now. Documentary sign-off. Tone: factual, no glorification; advertiser-safe.",
                'thumbnailStyle' => 'UNDERCOVER THUMBNAIL: (1) Undercover figure in two contexts (with bad guys vs. with law enforcement); (2) raid or arrest moment; (3) face half in shadow, "double life" feel. Photorealistic, 16:9, 8K.'
            ],

            'Redemption / Second Chance' => [
                'titles' => [
                    'He Spent [X] Years In Prison — What He Did Next Changed Everything',
                    'The Addict Who Became [Outcome]: A True Story Of Redemption',
                    'They Said He\'d Never Change — The Day He Proved Them Wrong',
                    'From [Rock Bottom] To [Redemption]: The [Name] Story | Documentary'
                ],
                'openingStyle' => 'Option A — Bottom: "In [year], [Name] hit rock bottom—[prison/addiction/crime]. [X] years later, [what changed]. This is how." Option B — Turn: "The moment [Name] decided to change was [specific]. What happened next would [outcome]." Use real or plausible names; inspirational but factual tone.',
                'storyBeats' => "REDEMPTION / SECOND CHANCE STORY BEATS (ex-con, addict, turnaround):\n\n1. BEFORE: Who they were—name, crime or addiction, low point. Specific dates, places, consequences. Use UNIQUE names; avoid Emily, Sarah, Marcus, Vance, Thorne.\n2. TURNING POINT: What made them want to change (prison program, person, moment). First steps (sobriety, education, job). Concrete details.\n3. THE CLIMB: Obstacles (parole, stigma, relapse). Key supporters or setbacks. Vary structure—some stories focus on one achievement, others on long journey.\n4. NOW: Where they are today; impact on others. Documentary sign-off. Tone: hopeful but grounded; advertiser-safe.",
                'thumbnailStyle' => "REDEMPTION THUMBNAIL: (1) Before/after (prison vs. free, or addiction vs. sober); (2) single figure in hopeful setting (graduation, job, family); (3) hand reaching or light motif. Photorealistic, 16:9, 8K, uplifting but not cheesy."
            ],

            'Kidnapping / Hostage' => [
                'titles' => [
                    'She Was Held For [X] Days — What She Did To Survive | Documentary',
                    'The Kidnapping That [Outcome]: The [Name] Case',
                    'The Day They Were Taken — What Happened Next',
                    'How [Name] Escaped: The Hostage Story They Never Forgot'
                ],
                'openingStyle' => 'Option A — Taken: "[Exact date]. [Name], [age], was [where] when [what happened]. For [X] days, [what she/he faced]." Option B — Escape: "When [Name] finally [escaped/was freed] on [date], [who] had been holding [her/him] for [X] days. This is what really happened." Use real or plausible names; high tension, documentary tone.',
                'storyBeats' => "KIDNAPPING / HOSTAGE STORY BEATS (what she did to survive, the day they were taken):\n\n1. BEFORE: Victim—name, age, circumstance. Where and when taken. Kidnapper(s) or situation. Use UNIQUE names; avoid Emily, Sarah, Marcus, Vance, Thorne.\n2. CAPTIVITY: Conditions (location, duration, treatment). What victim did to survive (comply, resist, plan). Key moments (move, threat, small kindness). Concrete details.\n3. TURNING POINT: Escape attempt, negotiation, or rescue. How it ended. Vary structure—some stories are escape-focused, others rescue or trial.\n4. AFTERMATH: Recovery, trial, where they are now. Documentary sign-off. Tone: respectful, high tension; advertiser-safe.",
                'thumbnailStyle' => "KIDNAPPING/HOSTAGE THUMBNAIL: (1) Victim in confined or tense setting (not graphic); (2) moment of escape or rescue; (3) family reunion or courtroom. Photorealistic, 16:9, 8K, tense but not exploitative."
            ],

            'African Folktales' => [
                'titles' => [
                    'HE THREW HER OUT ON VALENTINE\'S DAY — THEN THIS HAPPENED',
                    'THEY NEVER THOUGHT THIS WOULD HAPPEN…',
                    'YOU WILL NEVER PUT YOUR CHILD IN BOARDING SCHOOL AFTER WATCHING THIS',
                    'The Only Girl With Hair In a Bald Village',
                    'THEY SAID THE JOB CAME WITH FREE ACCOMMODATION… SHE NEVER SAW HER ROOM AGAIN',
                    'THE BEAUTIFUL HOUSE ON THE HILL… NOBODY LEAVES AFTER SUNSET',
                    'SHE MOVED TO LAGOS FOR OPPORTUNITY… LAGOS HAD OTHER PLANS',
                    'THE CHURCH WAS ALWAYS FULL… THE BASEMENT WAS NEVER EMPTY',
                    'THEY SAID THE ORPHANAGE WAS SAFE… UNTIL THE CHILDREN STARTED DISAPPEARING',
                    'THE LUXURY ESTATE HAD ONE RULE: DON\'T ASK ABOUT THE OTHER WING'
                ],
                'openingStyle' => 'Option A — Authority + warning: "This is not just a story. It\'s a warning. And once you start watching, you won\'t be able to stop." Option B — Contrast hook: Describe a calm, beautiful, or luxurious setting (spa, village, boarding school, Lagos street) with sensory detail—soft cream and gold, music, perfume, laughter—then imply something has already gone wrong. "From the road, the place didn\'t look dangerous. That\'s the part that still confuses people." Option C — Cold open: Start with impact. "When the building was finally demolished, they found more than dust." Option D — Framing: "This is the circle of African tales where African wisdom lives. And every story has a lesson." Use African settings (Lagos, Abuja, villages, compounds, schools); real or plausible names; calm, cinematic narration—no shouting or overdramatic tone. Build slow-burn suspense.',
                'storyBeats' => "AFRICAN FOLKTALES STORY BEATS (modern moral folktales, faceless narration, 15–25 min feel; can extend to 30–60 min marathon):\n\n1. ATMOSPHERIC OPENING (2–3 mins): Calm environment, luxury or ordinary life, sensory immersion (sounds, smells, sights). Subtle dread. Make it feel safe first. African setting: city (Lagos, Abuja), village, compound, spa, school, church.\n\n2. HOPEFUL ENTRY: Introduce a character with a dream—job offer, marriage, scholarship, relocation, \"accommodation provided, good pay.\" Viewer attaches emotionally here. Use African names (e.g. Emmanuela, Abigail, Chisum, Zara, Adaeze, Tunde, Ngozi). Specific details (voice notes to mother, small bag, big hopes).\n\n3. SUBTLE WARNINGS: Red flags that build slowly. Locked doors, \"You'll understand later,\" no phones during work, corridors never mentioned, urgency (\"immediate resumption\"), rules no one explains. Nothing explosive—just unsettling. Repetition of words: calm, silence, designed, system.\n\n4. THE SHOCK (first emotional spike): One sentence changes everything. \"You are not working at the spa.\" \"You're not here for that.\" \"I need you to leave.\" Or in traditional folktale: oracle's prophecy, curse revealed, \"The girl with hair is the chosen one.\" Deliver calmly; calm villain or calm fate is more terrifying.\n\n5. SURVIVAL OR CRISIS: Character observes, learns patterns, makes a plan—or is betrayed (e.g. hair shaved, door closed). Intelligence and strategy keep retention high. For supernatural tales: don't answer the voice at 2 a.m.; don't open the door; spirit chooses who sees it. For moral tales: truth vs lies, purity, justice.\n\n6. BIGGER SYSTEM REVEAL: Corruption, powerful names, police or elders complicit, \"Confirm with Madame Charity,\" files disappear. Or: curse origin (wronged woman cursed the village), school buried the truth, authorities finally raid. Evil is systemic, not just one villain.\n\n7. AFTERMATH + MORAL LAYER: Trauma, healing, relocation, \"she didn't hate the world but no longer trusted appearances.\" End with a subtle lesson, not a sermon. \"Every story carries a lesson. And if this one touched you, let it guide your heart.\" Optional: direct question to viewer (\"Would you have chosen silence or risk?\") to drive comments. Sign-off: \"I hope you enjoyed this African folktale. Like, subscribe, comment. The next story is already on your screen.\"\n\nSTYLE: Cinematic narration like a Netflix voiceover. Short, punchy reflection lines: \"Confusion is part of control.\" \"Knowledge was the first real weapon.\" \"Some places look beautiful from the road. They always do.\" Dense scene length (250–300 words per scene). Cliffhangers every 2–4 minutes. Character names: African and diverse; avoid overused Anglo names. Villains: calm, controlled, elegant—not shouting. Mix modern African reality (trafficking, boarding school secrets, betrayal) with traditional folktale elements (curses, oracles, purity, village, queen). Every story must feel distinct in structure and pacing.",
                'thumbnailStyle' => "AFRICAN FOLKTALES THUMBNAIL — choose one style for the story:\n(1) CONTRAST: Beautiful/luxurious setting (spa, palace, wedding, modern building) with 2–3 characters in tension—one figure in distress (woman crying, child clinging, girl in uniform), others calm or menacing. Emotion: fear, betrayal, shock, desperation. African faces and dress when setting is African. 16:9, 85mm Prime f/1.8, photorealistic, 8K, cinematic lighting.\n(2) VILLAGE/TRADITIONAL: Village square, mud houses, or palace; character(s) in traditional or semi-traditional dress; dramatic moment (accusation, shaving, oracle, wedding). Rich colors, dust or golden hour. 2–3 people, clear emotions. Photorealistic, 8K.\n(3) MODERN DREAD: Lagos/Abuja street, luxury car, glass doors; character exiting or surrounded; \"too perfect\" feel with subtle wrongness. Photorealistic, 8K, high YouTube engagement.\nOutput one coherent 16:9 prompt. Always include 2–3 people, specific emotions, and setting. Photorealistic, 8K."
            ],

            'Conspiracy' => [
                'titles' => [
                    'The Discovery They Tried To Bury — What One Researcher Found',
                    'Why They Never Wanted This Made Public | Conspiracy Documentary',
                    'The Truth That Was Hidden For 100 Years — What Really Happened',
                    'The [Institution] Secret That Changed Everything',
                    'What They Found In The Archives Was Never Meant To Be Seen'
                ],
                'openingStyle' => 'Option A — Suppressed truth: "In [year], [who] discovered [what]. Within [time], every trace of that discovery had been [removed/classified]. This is what really happened." Option B — Era hook: "By [century/year], [institution or group] had already [achieved something]. What they did next would be buried for [X] years." Option C — Researcher lead: "When [name] finally gained access to [place/documents], what they found would [outcome]." Use specific era when century is set (technology, language, dress, politics). Documentary tone; real or plausible names.',
                'storyBeats' => "CONSPIRACY STORY BEATS (hidden truth, cover-up, suppressed discovery; era-based when century provided):\n\n1. ERA & SETUP: When century/year range is given, set the ENTIRE story in that era. Pick one specific year within the range (e.g. 1487, 1672, 1944) and ground every detail—technology, transport, dress, politics, language—in that year. If no century given, use a plausible modern or recent-decade setting. Introduce institution, group, or researcher; what was publicly known vs. what was hidden.\n2. THE DISCOVERY OR SECRET: What was found, invented, or documented; who benefited from it being buried; who tried to expose it. Use UNIQUE character names; avoid overused names (Emily, Sarah, Marcus, Vance, Thorne).\n3. SUPPRESSION: How the truth was hidden (documents destroyed, witnesses silenced, narrative controlled). Concrete events and locations.\n4. UNCOVERING: How one person or group finally pieced it together; key evidence; risks they took.\n5. REVELATION & AFTERMATH: What became known; consequences (or continued cover-up). Documentary sign-off. Tone: factual, investigative; advertiser-safe.",
                'thumbnailStyle' => "CONSPIRACY THUMBNAIL: (1) Researcher in period-appropriate dress with documents or artifact; (2) split: official building/insignia one side, hidden evidence other side; (3) figure in shadow with folder or map. Match era if story is historical (costume, setting). 2–3 people when relevant. Photorealistic, 16:9, 85mm, 8K."
            ],

            'Adventure' => [
                'titles' => [
                    'The Expedition That Found What No One Believed Existed',
                    'Lost For 40 Years In The Jungle — What They Brought Back',
                    'The Map That Led To The Place No One Returned From',
                    'What They Discovered At The Edge Of The World | Adventure',
                    'The Ship That Vanished — And What They Found 200 Years Later'
                ],
                'openingStyle' => 'Option A — Expedition: "In [year], [who] set out for [place]. [X] [months/years] later, [only one returned/none came back/what they brought back] would change everything." Option B — Era: When century is set, choose a specific year in that range and set the entire adventure in that era (ships, weapons, dress, no anachronisms). Option C — Discovery: "The [artifact/map] had been lost since [year]. When it surfaced again, the race to [goal] began." Use distinct names; documentary or period-adventure tone.',
                'storyBeats' => "ADVENTURE STORY BEATS (exploration, expedition, lost place, treasure; era-based when century provided):\n\n1. ERA & MISSION: When century/year range is given, set the story in that era. Pick ONE specific year within the range (e.g. 1521, 1890, 1936) and keep technology, transport, weapons, and dialogue period-accurate. If no century given, use a plausible modern or historical setting. Introduce expedition or quest; who is going, why, what they seek. Use UNIQUE character names; avoid overused names.\n2. THE JOURNEY: Obstacles, terrain, encounters; key turning points. Dense, immersive description; specific locations (river, mountain, ruin).\n3. THE DISCOVERY OR CRISIS: What they find or what goes wrong; stakes rise. Period-appropriate details throughout.\n4. RESOLUTION: Escape, triumph, or tragedy; what was brought back (or lost). Where they are now or how it ended.\n5. AFTERMATH: Legacy, follow-up, or mystery left open. Documentary/adventure sign-off. Tone: thrilling but grounded; advertiser-safe.",
                'thumbnailStyle' => "ADVENTURE THUMBNAIL: (1) Expedition in period dress (match century if set)—explorers with map or at cave/ruin; (2) ship or vehicle of the era; (3) treasure/artifact with figure in shadow. 2–3 people, clear emotion (determination, fear, wonder). Photorealistic, 16:9, 85mm, 8K."
            ],

            'Discovery' => [
                'titles' => [
                    'The Discovery That Rewrote History — What They Found Under The Desert',
                    'What Was Really There: The [Place] Truth Finally Revealed',
                    'The Artifact That Shouldn\'t Exist — And What It Proves',
                    'Lost Civilization Found After [X] Years | Discovery Documentary',
                    'The Truth About [Subject] — What Science Tried To Hide'
                ],
                'openingStyle' => 'Option A — Breakthrough: "In [year], [team/person] made a find that would [outcome]. What they discovered had been [lost/buried/denied] for [X] years." Option B — Era: When century is set, choose a specific year in that range and set the discovery story in that era (tools, methods, dress, politics of the time). Option C — Mystery: "For [centuries/years], [question] had no answer. Then [who] found [what]." Documentary tone; real or plausible names.',
                'storyBeats' => "DISCOVERY STORY BEATS (scientific/historical breakthrough, lost civilization, artifact; era-based when century provided):\n\n1. ERA & CONTEXT: When century/year range is given, set the story in that era. Pick ONE specific year within the range and keep methods, tools, and beliefs period-accurate. If no century given, use modern or appropriate historical setting. Introduce the mystery or missing piece; what was known, what was missing. Use UNIQUE character names; avoid overused names.\n2. THE SEARCH: Who looked, where, what they used (period-appropriate). Key setbacks and clues.\n3. THE FIND: What was discovered; how it was verified; why it mattered. Concrete details (site, object, document).\n4. REACTION & DEBATE: How the world (or the era's establishment) responded; controversy or acceptance.\n5. LEGACY: What we know now; what remains debated. Documentary sign-off. Tone: factual, educational; advertiser-safe.",
                'thumbnailStyle' => "DISCOVERY THUMBNAIL: (1) Archaeologist or explorer in period-appropriate dress with artifact or dig site; (2) split: ancient ruin/artifact one side, researcher other side; (3) map or manuscript with figure. Match era if historical. 2–3 people when relevant. Photorealistic, 16:9, 85mm, 8K."
            ],
        ];
    }
}
