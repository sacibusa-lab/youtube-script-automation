<?php

namespace App\Services\Video;

use App\Models\Video;
use App\Models\Scene;
use FFMpeg\Format\Video\X264;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VideoAssemblyService
{
    /**
     * Assemble a full video from project scenes.
     * Note: This currently handles image-only assembly. 
     * Audio integration will be added in the next step.
     */
    public function assemble(Video $project): string
    {
        $project->load('chapters.scenes');
        
        $scenes = $project->chapters->flatMap->scenes->sortBy('scene_number');
        
        if ($scenes->isEmpty()) {
            throw new \Exception("No scenes found for project assembly.");
        }

        $tempFileName = "project_{$project->id}_" . time() . ".mp4";
        $exportPath = "public/exports/" . $tempFileName;

        Log::info("Starting video assembly for project: {$project->id}");

        $relativePaths = [];
        $filterParts = [];
        $inputs = "";
        $inputCount = 0;

        foreach ($scenes as $index => $scene) {
            if ($scene->image_url) {
                $relativePath = str_replace('/storage/', '', parse_url($scene->image_url, PHP_URL_PATH));
                $fullPath = storage_path('app/public/' . str_replace('/', DIRECTORY_SEPARATOR, $relativePath));

                if (file_exists($fullPath)) {
                    $relativePaths[] = $relativePath;
                    
                    $duration = $scene->duration_seconds ?: 5;
                    // Filter to set duration for each image: loop=1, framerate=25, trim/setpts
                    $filterParts[] = "[{$inputCount}:v]loop=loop=" . ($duration * 25) . ":size=1:start=0,setpts=PTS-STARTPTS[v{$inputCount}]";
                    $inputs .= "[v{$inputCount}]";
                    
                    $inputCount++;
                }
            }
        }

        if (empty($relativePaths)) {
            throw new \Exception("No valid images found to assemble.");
        }

        // Concatenate all loops without any spaces to avoid Windows CMD argument splitting issues
        $complexFilter = implode(';', $filterParts) . ";{$inputs}concat=n={$inputCount}:v=1:a=0[outv]";

        // Ensure directory exists
        if (!Storage::disk('public')->exists('exports')) {
            Storage::disk('public')->makeDirectory('exports');
        }

        FFMpeg::fromDisk('public')->open($relativePaths)
            ->export()
            ->addFormatOutputMapping(new X264('aac', 'libx264'), \ProtoneMedia\LaravelFFMpeg\Filesystem\Media::make('public', "exports/{$tempFileName}"), ['[outv]'], true)
            ->addFilter('', '"' . $complexFilter . '"', '')
            ->save();

        Log::info("Video assembly complete: {$exportPath}");

        return Storage::disk('public')->url("exports/{$tempFileName}");
    }
}
