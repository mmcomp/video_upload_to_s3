<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFProbe;
use Thumbnail;

/*
 * Main Logic of the projct is here
 */
class VideoController extends Controller
{
    /*
     * Redirects user to the first page to upload the video
     */
    public function create(){
        return view('videos.create');
    }

    /*
     * Stores the video to the s3 storage
     * creates 4 frames from the video and save it to public
     */
    public function store(Request $request){
        // Check if the video duration is more than 4 secs
        $video_local_path = $request->file('video')->getPathname();
        $ffprobe = FFProbe::create();
        $duration = (int)$ffprobe->format($video_local_path)->get('duration');
        if($duration <= 4)
            return "Video is too small!!!";

        // Upload the file to the s3 storage in video subfolder
        $request->file('video')->store('videos', 's3');

        // Generating 4 frames of the video
        $frame1 = "frame1_". strtotime(date("Y-m-d H:i:s")) . ".jpg";
        $frame2 = "frame2_". strtotime(date("Y-m-d H:i:s")) . ".jpg";
        $frame3 = "frame3_". strtotime(date("Y-m-d H:i:s")) . ".jpg";
        $frame4 = "frame4_". strtotime(date("Y-m-d H:i:s")) . ".jpg";
        $this->generateThumbnail($video_local_path, public_path(), $frame1, (int)($duration/4));
        $this->generateThumbnail($video_local_path, public_path(), $frame2, (int)($duration/2));
        $this->generateThumbnail($video_local_path, public_path(), $frame3, (int)($duration*3/4));
        $this->generateThumbnail($video_local_path, public_path(), $frame4, $duration);

        // Redirecting to show view to show the frames
        return view('videos.show', [
            "frame1" => $frame1,
            "frame2" => $frame2,
            "frame3" => $frame3,
            "frame4" => $frame4,
        ]);
    }

    /*
     * This function generate a frame from a specific second of it
     */
    public function generateThumbnail($video, $thumb_path, $thumb_name, $second){
        $thumbnail_status =  Thumbnail::getThumbnail($video, $thumb_path, $thumb_name, $second);
        return $thumbnail_status;
    }
}
