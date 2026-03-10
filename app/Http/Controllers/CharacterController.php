<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CharacterController extends Controller
{
    /**
     * Display the user's character library.
     */
    public function index()
    {
        $characters = Character::availableTo(Auth::id())
            ->orderBy('is_global', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('characters.index', compact('characters'));
    }

    /**
     * Show the form to create a new character.
     */
    public function create()
    {
        return view('characters.create');
    }

    /**
     * Store a new character.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                           => 'required|string|max:100',
            'description'                    => 'required|string|max:1000',
            'niche'                          => 'nullable|string|max:100',
            'visual_traits.age'              => 'nullable|string|max:50',
            'visual_traits.ethnicity'        => 'nullable|string|max:100',
            'visual_traits.hair'             => 'nullable|string|max:200',
            'visual_traits.eyes'             => 'nullable|string|max:100',
            'visual_traits.build'            => 'nullable|string|max:100',
            'visual_traits.style'            => 'nullable|string|max:300',
            'visual_traits.signature_detail' => 'nullable|string|max:300',
        ]);

        Character::create([
            'user_id'      => Auth::id(),
            'name'         => $validated['name'],
            'description'  => $validated['description'],
            'niche'        => $validated['niche'] ?? null,
            'visual_traits' => $validated['visual_traits'] ?? [],
            'is_global'    => false,
        ]);

        return redirect()->route('characters.index')
            ->with('success', "Character \"{$validated['name']}\" saved to your library!");
    }

    /**
     * Show a single character's details.
     */
    public function show(Character $character)
    {
        $this->authorizeAccess($character);

        return view('characters.show', compact('character'));
    }

    /**
     * Show the form to edit a character.
     */
    public function edit(Character $character)
    {
        $this->authorizeAccess($character, requireOwnership: true);

        return view('characters.edit', compact('character'));
    }

    /**
     * Update a character.
     */
    public function update(Request $request, Character $character)
    {
        $this->authorizeAccess($character, requireOwnership: true);

        $validated = $request->validate([
            'name'                           => 'required|string|max:100',
            'description'                    => 'required|string|max:1000',
            'niche'                          => 'nullable|string|max:100',
            'visual_traits.age'              => 'nullable|string|max:50',
            'visual_traits.ethnicity'        => 'nullable|string|max:100',
            'visual_traits.hair'             => 'nullable|string|max:200',
            'visual_traits.eyes'             => 'nullable|string|max:100',
            'visual_traits.build'            => 'nullable|string|max:100',
            'visual_traits.style'            => 'nullable|string|max:300',
            'visual_traits.signature_detail' => 'nullable|string|max:300',
        ]);

        $character->update([
            'name'          => $validated['name'],
            'description'   => $validated['description'],
            'niche'         => $validated['niche'] ?? null,
            'visual_traits' => $validated['visual_traits'] ?? [],
        ]);

        return redirect()->route('characters.index')
            ->with('success', "Character \"{$character->name}\" updated.");
    }

    /**
     * Delete a character.
     */
    public function destroy(Character $character)
    {
        $this->authorizeAccess($character, requireOwnership: true);
        $name = $character->name;
        $character->delete();

        return redirect()->route('characters.index')
            ->with('success', "Character \"{$name}\" removed from your library.");
    }

    /**
     * API: Return the user's characters as JSON for Studio selection.
     */
    public function apiList()
    {
        $characters = Character::availableTo(Auth::id())
            ->select(['id', 'name', 'slug', 'niche', 'is_global', 'reference_image_url'])
            ->get()
            ->map(function ($c) {
                return [
                    'id'             => $c->id,
                    'name'           => $c->name,
                    'slug'           => $c->slug,
                    'niche'          => $c->niche,
                    'is_global'      => $c->is_global,
                    'reference_image'=> $c->reference_image_url,
                ];
            });

        return response()->json($characters);
    }

    // ─── Private ─────────────────────────────────────────────────────────────

    private function authorizeAccess(Character $character, bool $requireOwnership = false): void
    {
        $userId = Auth::id();

        if ($requireOwnership) {
            if ($character->is_global || $character->user_id !== $userId) {
                abort(403, 'You cannot modify this character.');
            }
        } else {
            if (!$character->is_global && $character->user_id !== $userId) {
                abort(403);
            }
        }
    }
}
