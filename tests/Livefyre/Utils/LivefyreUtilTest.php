<?php

namespace Livefyre;


use Livefyre\Utils\LivefyreUtils;

class LivefyreUtilTest extends \PHPUnit_Framework_TestCase {
    private $config;

    protected function setUp() {
        $this->config = new LfTest();
        $this->config->setPropValues("prod");
    }

    public function testSiteValidUrls() {
        $this->assertFalse(LivefyreUtils::isValidUrl("aawef"));
        $this->assertTrue(LivefyreUtils::isValidUrl("http://test.com:8000"));
        $this->assertTrue(LivefyreUtils::isValidUrl("http://test.com"));
        $this->assertTrue(LivefyreUtils::isValidUrl("https://test.com/"));
        $this->assertTrue(LivefyreUtils::isValidUrl("ftp://test.com/"));
        $this->assertTrue(LivefyreUtils::isValidUrl("http://清华大学.cn"));
        $this->assertTrue(LivefyreUtils::isValidUrl("http://www.mysite.com/myresumé.html"));
        $this->assertTrue(LivefyreUtils::isValidUrl("https://test.com/path/test.-_~!$&'()*+,=:@/dash"));
    }
}