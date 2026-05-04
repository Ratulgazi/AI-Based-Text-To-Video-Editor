<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    public function generate(Request $request)
    {
        set_time_limit(300);
        $text = (string) $request->input('text');

        if (trim($text) === '') {
            return redirect('/')->with('error', 'Please enter some text!');
        }

        $lines = preg_split('/[\.\?!]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $lines = array_values(array_filter(array_map('trim', $lines), function ($value) {
            return $value !== '';
        }));

        if (empty($lines)) {
            return redirect('/')->with('error', 'Please enter some text!');
        }

        $frameDir = public_path('frames');
        if (!file_exists($frameDir)) {
            mkdir($frameDir, 0777, true);
        }

        foreach (glob("$frameDir/*") as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        $colors = [
            [255, 255, 255],
            [255, 215, 0],
            [0, 255, 255],
            [255, 105, 180],
        ];

        $bgImages = [];
        if ($request->hasFile('background') && $request->file('background')->isValid()) {
            $bg = $request->file('background');
            $extension = strtolower($bg->extension() ?: 'jpg');
            $bgFolder = public_path('backgrounds');
            if (!file_exists($bgFolder)) {
                mkdir($bgFolder, 0777, true);
            }
            $bgPath = $bgFolder . '/user_bg.' . $extension;
            $bg->move($bgFolder, 'user_bg.' . $extension);
            $bgImages[] = $bgPath;
        }

        foreach (['jpg', 'jpeg', 'png', 'gif'] as $extension) {
            foreach (glob(public_path("backgrounds/*.$extension")) as $path) {
                $bgImages[] = $path;
            }
        }

        $bgImages = array_values(array_unique(array_filter($bgImages, 'file_exists')));

        $lineCount = count($lines);
        $durationSeconds = min(120, max(30, $lineCount * 3));
        $totalFrames = $durationSeconds * 10;
        $framesPerLine = max(1, (int) ceil($totalFrames / $lineCount));

        $font = public_path('fonts/Roboto-Regular.ttf');
        if (!file_exists($font)) {
            return redirect('/')->with('error', 'Font file is missing. Please add fonts/Roboto-Regular.ttf.');
        }

        $frameIndex = 0;
        foreach ($lines as $line) {
            $lineText = trim($line);
            if ($lineText === '') {
                continue;
            }

            $wrapped = wordwrap($lineText, 36, "\n", true);
            $textLines = explode("\n", $wrapped);

            for ($frame = 0; $frame < $framesPerLine; $frame++) {
                $imagePath = "$frameDir/frame$frameIndex.png";
                $img = $this->createImage($bgImages);

                [$r, $g, $b] = $colors[array_rand($colors)];
                $fontSize = 40;

                $centerX = imagesx($img) / 2;
                $centerY = imagesy($img) / 2;
                $offsetX = sin($frameIndex * 0.12) * 24;
                $offsetY = cos($frameIndex * 0.09) * 12;
                $scale = 1 + sin($frameIndex * 0.04) * 0.14;
                $rotation = sin($frameIndex * 0.02) * 5;

                $progress = $frame / max(1, $framesPerLine - 1);
                $alpha = 0;
                if ($progress < 0.25) {
                    $alpha = (int) (127 - ($progress / 0.25) * 127);
                } elseif ($progress > 0.75) {
                    $alpha = (int) ((($progress - 0.75) / 0.25) * 127);
                }
                $textColor = imagecolorallocatealpha($img, $r, $g, $b, $alpha);

                $lineHeight = $fontSize * 1.4 * $scale;
                $blockHeight = count($textLines) * $lineHeight;
                $y = $centerY - $blockHeight / 2 + $offsetY + $fontSize;

                foreach ($textLines as $textLineRow) {
                    $bbox = imagettfbbox($fontSize * $scale, 0, $font, $textLineRow);
                    $textWidth = $bbox[2] - $bbox[0];
                    $x = $centerX - ($textWidth / 2) + $offsetX;
                    imagettftext($img, $fontSize * $scale, $rotation, $x, $y, $textColor, $font, $textLineRow);
                    $y += $lineHeight;
                }

                imagepng($img, $imagePath);
                imagedestroy($img);
                $frameIndex++;
            }
        }

        $ffmpegPath = trim(shell_exec('command -v ffmpeg'));
        if ($ffmpegPath === '') {
            return redirect('/')->with('error', 'FFmpeg is not installed on the server.');
        }

        $audioFile = public_path('audio.mp3');
        $audioPath = escapeshellarg($audioFile);
        $silentCmd = sprintf('%s -y -f lavfi -i anullsrc=channel_layout=stereo:sample_rate=44100 -t %d -q:a 9 %s', $ffmpegPath, $durationSeconds, $audioPath);
        exec($silentCmd, $audioOutput, $audioStatus);

        if ($audioStatus !== 0) {
            Log::error('Audio generation failed', ['cmd' => $silentCmd, 'status' => $audioStatus, 'output' => $audioOutput]);
            return redirect('/')->with('error', 'Audio generation failed. Please try again.');
        }

        $output = public_path('output.mp4');
        $framePattern = escapeshellarg("$frameDir/frame%d.png");
        $outputPath = escapeshellarg($output);
        $ffmpegFilter = "edgedetect=low=0.1:high=0.4:mode=colormix,curves=all='0/0 0.2/0.4 0.8/0.9 1/1'";
        $videoCmd = sprintf('%s -y -framerate 10 -i %s -i %s -c:v libx264 -pix_fmt yuv420p -c:a aac -b:a 128k -vf "%s" -shortest %s', $ffmpegPath, $framePattern, $audioPath, $ffmpegFilter, $outputPath);
        exec($videoCmd, $videoOutput, $videoStatus);

        if ($videoStatus !== 0 || !file_exists($output) || filesize($output) < 1024) {
            Log::error('Video generation failed', ['cmd' => $videoCmd, 'status' => $videoStatus, 'output' => $videoOutput]);
            return redirect('/')->with('error', 'Video generation failed. Please try again with shorter text.');
        }

        return redirect('/')->with('video', 'output.mp4');
    }

    private function createImage(array $bgImages)
    {
        if (!empty($bgImages)) {
            $bgFile = $bgImages[array_rand($bgImages)];
            $type = @exif_imagetype($bgFile);

            if ($type === IMAGETYPE_JPEG) {
                $img = imagecreatefromjpeg($bgFile);
                imagesavealpha($img, true);
                return $img;
            }
            if ($type === IMAGETYPE_PNG) {
                $img = imagecreatefrompng($bgFile);
                imagesavealpha($img, true);
                return $img;
            }
            if ($type === IMAGETYPE_GIF) {
                $img = imagecreatefromgif($bgFile);
                imagesavealpha($img, true);
                return $img;
            }

            $content = @file_get_contents($bgFile);
            if ($content !== false) {
                $img = @imagecreatefromstring($content);
                if ($img !== false) {
                    imagesavealpha($img, true);
                    return $img;
                }
            }
        }

        $img = imagecreatetruecolor(1280, 720);
        $bg = imagecolorallocate($img, rand(0, 30), rand(0, 30), rand(0, 30));
        imagefill($img, 0, 0, $bg);
        imagesavealpha($img, true);

        return $img;
    }
}
