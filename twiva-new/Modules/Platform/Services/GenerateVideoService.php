<?php


namespace Modules\Platform\Services;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateVideoService {

    protected $ffmpeg;
    protected $image_files;
    protected array $images;
    protected $list_validate_images;
    protected array $validate_extensions = ['.jpg', '.jpeg', '.png', '.webp'];
    protected string $output_name;
    protected string $output_name_with_subtitle;
    protected $logo_path;
    protected $output_path;
    protected $output_name_with_subtitle_path;
    protected $output_absolute_path;
    protected $output_absolute_path_with_subtitle;
    protected $subtitle;
    protected string $command;
    protected string $command_with_subtile;

    /**
     *
     */
    public function __construct(string $image_files, $ffmpeg = null)
    {
        $this->image_files = $image_files;
        $this->checkFfmpegDrive($ffmpeg);
        $this->validateFiles();
        $this->generateName();
        $this->generatePaths();
        $this->logo();
    }

    public function checkFfmpegDrive($ffmpeg)
    {
        if (is_null($ffmpeg))
        {
            $this->ffmpeg = env('FFMPEG_BINARIES', 'ffmpeg');
        }
        else
        {
            $this->ffmpeg = $ffmpeg;
        }
    }

    public function validateFiles()
    {
        $arr_files = explode(' ', $this->image_files);
        foreach ($arr_files as $file)
        {
            $exists = Storage::disk('public')->exists($file);
            if ($exists and Str::contains($file, $this->validate_extensions))
            {
                $this->images[] = storage_path('app/public/' . $file);
            }
        }

        if (isset($this->images))
        {
            $this->list_validate_images = Arr::join($this->images, ' ');
        }
    }

    public function generateName()
    {
        if (isset($this->images))
        {
            $uuid_name = Str::uuid();
            $this->output_name = $uuid_name . '.mp4';
            $this->output_name_with_subtitle = $uuid_name . '_with_subtitle.mp4';
        }
    }

    public function generatePaths($root_video_path = 'videos')
    {
        if (isset($this->images))
        {
            $this->output_path = "{$root_video_path}/{$this->output_name}";
            $this->output_name_with_subtitle_path = "{$root_video_path}/{$this->output_name_with_subtitle}";
            $this->output_absolute_path = storage_path("app/public/{$root_video_path}/{$this->output_name}");
            $this->output_absolute_path_with_subtitle = storage_path("app/public/{$root_video_path}/{$this->output_name_with_subtitle}");
        }
        else
        {
            $this->output_path = "";
            $this->output_name_with_subtitle_path = "";
            $this->output_absolute_path = "";
            $this->output_absolute_path_with_subtitle = "";
        }
    }

    public function addSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    public function logo($logo = 'front/images/logos/Logo.png')
    {
        $this->logo_path = public_path($logo);
    }

    public function generateVideoCommand()
    {
        Storage::disk('public')->put($this->output_path, "");
        $this->command = "cat {$this->list_validate_images} | {$this->ffmpeg} -y -framerate 0.5 -f image2pipe -i - -c:v libx264 -r 30 -t 30 -pix_fmt yuv420p {$this->output_absolute_path}";
        if (env('LOGGER_COMMAND')) {
            logger('command generate video:', ['$this->command' => $this->command]);
        }
    }

    public function generateVideoWithSubtitleCommand()
    {
        if (is_null($this->subtitle))
        {
            return throw new Exception('Please, enter subtile before');
        }
        $escapedUrl = Str::replace(['https://', 'http://'], '', $this->subtitle);
        $this->command_with_subtile = "{$this->ffmpeg} -y -i {$this->output_absolute_path} -i {$this->logo_path} -filter_complex \"[0:v]drawtext=text='$escapedUrl':x=(w-text_w)/2:y=(h-text_h)-100:fontsize=36:fontcolor=white:box=1:boxborderw=100:boxcolor=gray@0.7[outv];[outv][1:v]overlay=10:10[outv]\" -map \"[outv]\" -c:a copy {$this->output_absolute_path_with_subtitle}";

        if (env('LOGGER_COMMAND'))
        {
            logger('command generate video with subtitle:', ['command_with_subtile' => $this->command_with_subtile]);
        }
    }

    public function runCommand($with_subtile = false)
    {
        if ($with_subtile)
        {
            $output = shell_exec($this->command_with_subtile);
        }
        else
        {
            $output = shell_exec($this->command);
        }
        if (env('LOGGER_COMMAND'))
        {
            logger('output command: ', ['output' => $output]);
        }
        return $output;
    }

    public function getOutput($with_subtile = false, $return_absolute = false)
    {
        $output = null;
        if ($with_subtile)
        {
            if (Storage::disk('public')->exists($this->output_name_with_subtitle_path))
            {
                if ($return_absolute)
                {
                    $output = $this->output_name_with_subtitle_path;
                }
                else
                {
                    $output = asset('storage/' . $this->output_name_with_subtitle_path);
                }
            }
        }
        else
        {
            if (Storage::disk('public')->exists($this->output_path))
            {
                if ($return_absolute)
                {
                    $output = $this->output_path;
                }
                else
                {
                    $output = asset('storage/' . $this->output_path);
                }
            }
        }

        // logger('GenerateVideoService.php => ', ['output' => $output]);
        return $output;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getCommandWithSubtile()
    {
        return $this->command_with_subtile;
    }


    public function saveProductVideo($product, $with_subtile)
    {
        if ($with_subtile)
        {
            $data = [
                // 'video' => $this->output_path,
                'video_with_subtitle' => $this->output_name_with_subtitle_path,
            ];
        }
        else
        {
            $data = [
                'video' => $this->output_path,
                // 'video_with_subtitle' => $this->output_name_with_subtitle_path,
            ];
        }
        $result = $product->video()->updateOrCreate([], $data);
    }
}
