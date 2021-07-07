<?php

namespace App\Service;

use Exception;

class UrlToEmbedTransformer
{
    /**
     * Allows you to extract the ID of any video url
     * (youtube, dailymotion or vimeo)
     * in order to add it in an html embed video tag
     * 
     * @param string $url
     * @return string
     */
    public function urlToEmbed($url): string
    {
        $host = parse_url($url)["host"];

        if ($host === 'youtu.be' || strpos($host, 'youtube') == true) {

            if (!empty(parse_url($url)['fragment'])) {
                $partial_url = parse_url($url)['fragment'];
            } elseif (!empty(parse_url($url)['query'])) {
                $partial_url = parse_url($url)['query'];
            } else {
                $partial_url = parse_url($url)['path'];
            }

            $partial_url = explode('/', $partial_url);
            $partial_url = explode('=', $partial_url[count($partial_url) - 1]);
            $video_id = $partial_url[count($partial_url) - 1];

            $functionnal_link = 'https://www.youtube.com/embed/' . $video_id;

        } else if (is_int(strpos($host, 'vimeo', 0))) {

            $partial_url = parse_url($url)['path'];
            $partial_url = explode('/', $partial_url);
            $video_id = $partial_url[count($partial_url) - 1];

            $functionnal_link = "https://player.vimeo.com/video/" . $video_id;

        } else if ($host === 'dai.ly' || strpos($host, 'daily') == true) {

            $partial_url = parse_url($url)['path'];
            $partial_url = explode('/', $partial_url);
    
            if (strpos($host, 'daily') == true) {
                if (count($partial_url) == 4){
                    $video_id = $partial_url[3];
                } else {
                    $video_id = explode('_', $partial_url[2])[0];
                }
            } else if ($host === 'dai.ly') {
                $video_id = $partial_url[1];
            }

            $functionnal_link = 'https://www.dailymotion.com/embed/video/' . $video_id;

        // To do the same with facebook video
        // } else if (strpos($host, 'facebook') == true) {

        //     $partial_url = parse_url($url)['path'];
        //     $partial_url = explode('/', $partial_url);
        //     if (count($partial_url) === 4) {
        //         $video_id = $partial_url[count($partial_url) - 1];
        //     } else {
        //         $video_id = $partial_url[count($partial_url) - 2];
        //     }
            
        //     $functionnal_link = 'https://www.facebook.com/facebook/videos/' . $video_id . '/';
            
        } else {
            throw new Exception("Cette url n'est pas prise en charge par notre système.");
        }

        return $functionnal_link;
    }
}
