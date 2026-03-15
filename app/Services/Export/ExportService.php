<?php

namespace App\Services\Export;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExportService
{
    /**
     * Export the full script including all chapters and scenes.
     *
     * @param int $videoId
     * @return string
     */
    public function exportFullScript(int $videoId): string
    {
        $video = Video::with(['chapters.scenes'])->findOrFail($videoId);

        $script = "TITLE: {$video->selected_title}\n";
        $script .= "TOPIC: {$video->topic}\n";
        $script .= "MEGA-HOOK: {$video->mega_hook}\n\n";
        $script .= str_repeat("-", 40) . "\n\n";

        foreach ($video->chapters as $chapter) {
            $script .= "CHAPTER {$chapter->chapter_number}: {$chapter->title}\n";
            $script .= "HOOK: {$chapter->hook_text}\n\n";

            foreach ($chapter->scenes as $scene) {
                $script .= "[SCENE {$scene->scene_number}]\n";
                $script .= "VISUAL: {$scene->visual_prompt}\n";
                $script .= "NARRATION: {$scene->narration_text}\n\n";
            }
            $script .= str_repeat("-", 20) . "\n\n";
        }

        return $script;
    }

    /**
     * Export all generated assets (images) and scripts into a ZIP file.
     *
     * @param int $videoId
     * @return string|null Path to the ZIP file
     */
    public function exportAssets(int $videoId): ?string
    {
        $video = Video::with(['chapters.scenes'])->findOrFail($videoId);
        $zipFileName = "storybee_project_{$videoId}.zip";
        $zipFilePath = storage_path("app/public/exports/{$zipFileName}");

        // Ensure directory exists
        if (!file_exists(dirname($zipFilePath))) {
            mkdir(dirname($zipFilePath), 0755, true);
        }

        if (!class_exists('ZipArchive')) {
            throw new \RuntimeException("The PHP 'zip' extension is not enabled in your web server's PHP configuration. Please enable it in php.ini to use the export feature.");
        }

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            
            // Add Full Script
            $zip->addFromString('full_script.txt', $this->exportFullScript($videoId));

            // Add Images
            foreach ($video->chapters as $chapter) {
                foreach ($chapter->scenes as $scene) {
                    if ($scene->image_url) {
                        // In production, you'd download the file from S3 or storage
                        // For this local setup, we assume generated/ path in public disk
                        $relativePath = str_replace('/storage/', '', parse_url($scene->image_url, PHP_URL_PATH));
                        $fullPath = storage_path('app/public/' . $relativePath);
                        
                        if (file_exists($fullPath)) {
                            $fileName = "Chapter_{$chapter->chapter_number}_Scene_{$scene->scene_number}.png";
                            $zip->addFile($fullPath, "images/{$fileName}");
                        }
                    }
                }
            }

            $zip->close();
            return Storage::url("exports/{$zipFileName}");
        }

        return null;
    }
}
