<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Modules\Products\Entities\ProductVideo;

class DeleteOldVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videos:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete videos older than one day from storage';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $disk = 'public';
        $threshold = Carbon::now()->subDay();

        $videos = Storage::disk($disk)->allFiles('videos'); // Replace with your directory path

        foreach ($videos as $video) {
            $lastModified = Storage::disk($disk)->lastModified($video);
            try {
                if (Carbon::parse($lastModified) < $threshold) {
                    // $this->info("Have To Deleted: " . $video);
                    try {
                    ProductVideo::where("video", $video)->delete();
                } catch (Exception $exception) {
                }
                Storage::disk($disk)->delete($video);
                }
            } catch (Exception $exception) {
                logger($exception->getMessage(), ['exception' => $exception]);
            }
        }
    }
}
