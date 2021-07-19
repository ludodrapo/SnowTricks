<?php

namespace App\tests\Service;

use App\Service\UrlToEmbedTransformer;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{

    /**
     * @dataProvider urlsToBeTested
     * @return void
     */
    public function testExpectedUrlReturningEmbedLink($url, $expectedLink)
    {
        $transformer = new UrlToEmbedTransformer;

        $this->assertSame($transformer->urlToEmbed($url), $expectedLink);
    }

    public function testUnexpectedUrlReturningException()
    {
        $transformer = new UrlToEmbedTransformer;

        $this->expectExceptionMessage("Cette url n'est pas prise en charge par notre système.");

        $transformer->urlToEmbed('https://www.facebook.com/youtube/videos/1547601798759401/?extid=CL-UNK-UNK-UNK-AN_GK0T-GK1C');
    }

    public function urlsToBeTested()
    {
        return [
            'youTube' => ['http://www.youtube.com/sandalsResorts#p/c/54B8C800269D7C1B/0/dQw4w9WgXcQ', "https://www.youtube.com/embed/dQw4w9WgXcQ"],
            'youtu.be' => ['https://youtu.be/dQw4w9WgXcQ', "https://www.youtube.com/embed/dQw4w9WgXcQ"],
            'dailyMotion' => ['http://www.dailymotion.com/video/x2jvvep_hakan-yukur-klip_sport', "https://www.dailymotion.com/embed/video/x2jvvep"],
            'dail.ly' => ['http://dai.ly/x2jvvep', "https://www.dailymotion.com/embed/video/x2jvvep"],
            'vimeo' => ['http://player.vimeo.com/video/87973054?title=0&amp;byline=0&amp;portrait=0', "https://player.vimeo.com/video/87973054"]
        ];
    }
}
