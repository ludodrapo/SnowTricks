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

            $functionnal_link = 'https://www.youtube.com/embed/' . $this->getYouTubeVideoId($url);
            
        } else if (is_int(strpos($host, 'vimeo', 0))) {

            $functionnal_link = "https://player.vimeo.com/video/" . $this->getVimeoVideoId($url);

        } else if ($host === 'dai.ly' || strpos($host, 'daily') == true) {

            $functionnal_link = 'https://www.dailymotion.com/embed/video/' . $this->getDailyMotionVideoId($url, $host);

            // To do the same with facebook video but the aspect ratio is 1:1 so it does not match with others
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

    /**
     * To extract video id from youtube urls
     *
     * @param string $url
     * @return string
     */
    public function getYouTubeVideoId($url): string
    {

        if (!empty(parse_url($url)['fragment'])) {
            $partial_url = parse_url($url)['fragment'];
        } elseif (!empty(parse_url($url)['query'])) {
            $partial_url = parse_url($url)['query'];
        } else {
            $partial_url = parse_url($url)['path'];
        }

        $partial_url = explode('/', $partial_url);
        $partial_url = explode('=', $partial_url[count($partial_url) - 1]);
        return $partial_url[count($partial_url) - 1];
    }

    /**
     * To extract video id from vimeo urls
     *
     * @param string $url
     * @return string
     */
    public function getVimeoVideoId($url): string
    {
        $partial_url = parse_url($url)['path'];
        $partial_url = explode('/', $partial_url);
        return $partial_url[count($partial_url) - 1];
    }

    /**
     * To extract video id from dailymotion urls
     *
     * @param string $url
     * @param string $host
     * @return string
     */
    public function getDailyMotionVideoId($url, $host): string
    {
        $partial_url = parse_url($url)['path'];
        $partial_url = explode('/', $partial_url);

        if (strpos($host, 'daily') == true) {
            if (count($partial_url) == 4) {
                return $partial_url[3];
            } else {
                return explode('_', $partial_url[2])[0];
            }
        } else if ($host === 'dai.ly') {
            return $partial_url[1];
        }
    }
}
