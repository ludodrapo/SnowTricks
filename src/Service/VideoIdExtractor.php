<?php

namespace App\Service;

class VideoIdExtractor
{
    /**
     * Allows you to extract the ID of any YouTube video
     * in order to add it in a html embed video tag
     * 
     * @param string $url
     * @return string
     */
    public function extractId($url): string
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
        
            $video_id = substr($partial_url, -11);
        }

        return $video_id;
    }
}
