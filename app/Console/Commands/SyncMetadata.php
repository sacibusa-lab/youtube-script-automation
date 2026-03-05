<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Video;
use App\Models\GeneratedTitle;
use Illuminate\Support\Str;

class SyncMetadata extends Command
{
    protected $signature = 'sync:metadata';

    public function handle()
    {
        $videos = Video::whereNotNull('title_variations')->get();
        
        foreach ($videos as $video) {
            $this->info("Syncing metadata for Video ID: {$video->id}");
            
            $concepts = $video->title_variations;
            if (!is_array($concepts)) continue;

            foreach ($concepts as $concept) {
                $title = is_array($concept) ? ($concept['title'] ?? '') : $concept;
                if (empty($title)) continue;

                $normalized = Str::slug($title);
                $hash = md5($normalized);

                GeneratedTitle::updateOrCreate(
                    [
                        'video_id' => $video->id,
                        'hash' => $hash
                    ],
                    [
                        'title' => $title,
                        'metadata' => is_array($concept) ? $concept : null
                    ]
                );
            }
        }
        
        $this->info("Sync completed!");
    }
}
