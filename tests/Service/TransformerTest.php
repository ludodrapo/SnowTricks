<?php

namespace tests\Service;

use App\Service\UrlToEmbedTransformer;
use PHPUnit\Framework\TestCase;

/**
 * class TransformerTest
 * @package tests\Service
 */
class TransformerTest extends TestCase
{
    /**
     * @dataProvider urlsToBeTested
     * @return void
     */
    public function testExpectedUrlReturningEmbedLink($url, $expectedLink): void
    {
        $transformer = new UrlToEmbedTransformer;

        $this->assertSame($transformer->urlToEmbed($url), $expectedLink);
    }

    /**
     * @return void
     */
    public function testUnexpectedUrlReturningException(): void
    {
        $transformer = new UrlToEmbedTransformer;

        $this->expectExceptionMessage("L'url de cette vidéo n'est pas prise en charge par notre système.");

        $transformer->urlToEmbed('https://www.facebook.com/youtube/videos/1547601798759401/?extid=CL-UNK-UNK-UNK-AN_GK0T-GK1C');
    }

    /**
     * @return void
     */
    public function urlsToBeTested()
    {
        return [
            'youTube1' => ['http://www.youtube.com/sandalsResorts#p/c/54B8C800269D7C1B/0/dQw4w9WgXcQ', "https://www.youtube.com/embed/dQw4w9WgXcQ"],
            'youTube2' => ["https://www.youtube.com/watch?v=yoWJfsk0gvk", "https://www.youtube.com/embed/yoWJfsk0gvk"],
            'youtu.be' => ['https://youtu.be/dQw4w9WgXcQ', "https://www.youtube.com/embed/dQw4w9WgXcQ"],
            'dailyMotion1' => ['http://www.dailymotion.com/video/x2jvvep_hakan-yukur-klip_sport', "https://www.dailymotion.com/embed/video/x2jvvep"],
            'dailyMotion2' => ['http://www.dailymotion.com/hub/x2jvvep_Galatasaray#video=x2jvvep', "https://www.dailymotion.com/embed/video/x2jvvep"],
            'dailyMotion3' => ['https://www.dailymotion.com/embed/video/x2jvvep', "https://www.dailymotion.com/embed/video/x2jvvep"],
            'dailyMotion4' => ['http://www.dailymotion.com/hub/x2jvvep_Galatasaray', "https://www.dailymotion.com/embed/video/x2jvvep"],
            'dailyMotion5' => ['http://www.dailymotion.com/video/x2jvvep_coup-incroyable-pendant-un-match-de-ping-pong_tv', "https://www.dailymotion.com/embed/video/x2jvvep"],
            'dailyMotion6' => ['http://www.dailymotion.com/video/x2jvvep_rates-of-exchange-like-a-renegade_music', "https://www.dailymotion.com/embed/video/x2jvvep"],
            'dailyMotion7' => ['http://www.dailymotion.com/video/x2jvvep', "https://www.dailymotion.com/embed/video/x2jvvep"],
            'dailyMotion8' => ['http://www.dailymotion.com/hub/x2jvvep_Galatasaray', "https://www.dailymotion.com/embed/video/x2jvvep"],
            'dail.ly' => ['http://dai.ly/x2jvvep', "https://www.dailymotion.com/embed/video/x2jvvep"],
            'vimeo' => ['http://player.vimeo.com/video/87973054?title=0&amp;byline=0&amp;portrait=0', "https://player.vimeo.com/video/87973054"]
        ];
    }
}
