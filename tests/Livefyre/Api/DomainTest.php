<?php

namespace Livefyre;


use Livefyre\Api\Domain;

class DomainTest extends \PHPUnit_Framework_TestCase {
    private $network;
    private $site;
    private $collection;
    private $config;

    protected function setUp() {
        $this->config = new LfTest();
        $this->config->setPropValues("prod");
        $this->network = Livefyre::getNetwork($this->config->NETWORK_NAME, $this->config->NETWORK_KEY);
        $this->site = $this->network->getSite($this->config->SITE_ID, $this->config->SITE_KEY);
        $this->collection = $this->site->buildLiveCommentsCollection(
            $this->config->TITLE, $this->config->ARTICLE_ID, $this->config->URL);
    }

    public function testBootstrap() {
        $bootstrapUrlSsl = "https://" . $this->network->getNetworkName() . ".bootstrap.fyre.co";
        $this->assertEquals($bootstrapUrlSsl, Domain::bootstrap($this->network));
        $this->assertEquals($bootstrapUrlSsl, Domain::bootstrap($this->site));
        $this->assertEquals($bootstrapUrlSsl, Domain::bootstrap($this->collection));

        $this->network->setSsl(false);
        $bootstrapUrl = "http://bootstrap." . $this->network->getNetworkName() . ".fyre.co";
        $this->assertEquals($bootstrapUrl, Domain::bootstrap($this->network));
        $this->assertEquals($bootstrapUrl, Domain::bootstrap($this->site));
        $this->assertEquals($bootstrapUrl, Domain::bootstrap($this->collection));
    }

    public function testQuill() {
        $quillUrlSsl = "https://" . $this->network->getNetworkName() . ".quill.fyre.co";
        $this->assertEquals($quillUrlSsl, Domain::quill($this->network));
        $this->assertEquals($quillUrlSsl, Domain::quill($this->site));
        $this->assertEquals($quillUrlSsl, Domain::quill($this->collection));

        $this->network->setSsl(false);
        $quillUrl = "http://quill." . $this->network->getNetworkName() . ".fyre.co";
        $this->assertEquals($quillUrl, Domain::quill($this->network));
        $this->assertEquals($quillUrl, Domain::quill($this->site));
        $this->assertEquals($quillUrl, Domain::quill($this->collection));
    }
}