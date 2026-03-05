<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GeneratedTitle;
use App\Models\Video;

class DebugMetadata extends Command
{
    protected $signature = 'debug:metadata {video_id}';

    public function handle()
    {
        $video = Video::find($this->argument('video_id'));
        if (!$video) {
            $this->error("Video not found");
            return;
        }

        $this->info("Video Status: {$video->status}");
        $this->info("Title Variations in Video model: " . json_encode($video->title_variations, JSON_PRETTY_PRINT));

        $titles = GeneratedTitle::where('video_id', $video->id)->get();
        foreach ($titles as $t) {
            $this->info("Title: {$t->title}");
            $this->info("Metadata: " . json_encode($t->metadata, JSON_PRETTY_PRINT));
            $this->line("---");
        }
    }
}
