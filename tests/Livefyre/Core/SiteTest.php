<?php

namespace Livefyre\Core;


use Livefyre\LfTest;
use Livefyre\Livefyre;
use Livefyre\Type\CollectionType;

class SiteTest extends \PHPUnit_Framework_TestCase {
    private $config;
    private $network;
    private $site;

    protected function setUp() {
        $this->config = new LfTest();
        $this->config->setPropValues("prod");
        $this->network = Livefyre::getNetwork($this->config->NETWORK_NAME, $this->config->NETWORK_KEY);
        $this->site = $this->network->getSite($this->config->SITE_ID, $this->config->SITE_KEY);
    }

    public function testBuildCollections() {
        $collection = $this->site->buildLiveCommentsCollection($this->config->TITLE, $this->config->ARTICLE_ID, $this->config->URL);
        $this->assertEquals(CollectionType::LIVECOMMENTS, $collection->getData()->getType());

        $collection = $this->site->buildLiveBlogCollection($this->config->TITLE, $this->config->ARTICLE_ID, $this->config->URL);
        $this->assertEquals(CollectionType::LIVEBLOG, $collection->getData()->getType());

        $collection = $this->site->buildLiveChatCollection($this->config->TITLE, $this->config->ARTICLE_ID, $this->config->URL);
        $this->assertEquals(CollectionType::LIVECHAT, $collection->getData()->getType());

        $collection = $this->site->buildCountingCollection($this->config->TITLE, $this->config->ARTICLE_ID, $this->config->URL);
        $this->assertEquals(CollectionType::COUNTING, $collection->getData()->getType());

        $collection = $this->site->buildRatingsCollection($this->config->TITLE, $this->config->ARTICLE_ID, $this->config->URL);
        $this->assertEquals(CollectionType::RATINGS, $collection->getData()->getType());

        $collection = $this->site->buildReviewsCollection($this->config->TITLE, $this->config->ARTICLE_ID, $this->config->URL);
        $this->assertEquals(CollectionType::REVIEWS, $collection->getData()->getType());

        $collection = $this->site->buildSidenotesCollection($this->config->TITLE, $this->config->ARTICLE_ID, $this->config->URL);
        $this->assertEquals(CollectionType::SIDENOTES, $collection->getData()->getType());

        $collection = $this->site->buildCollection(CollectionType::COUNTING, $this->config->TITLE, $this->config->ARTICLE_ID, $this->config->URL);
        $this->assertEquals(CollectionType::COUNTING, $collection->getData()->getType());
    }

    /**
     * @covers Livefyre\Validator\SiteValidator::validate
     * @expectedException InvalidArgumentException
     */
    public function testInit_badID() {
        $this->network->getSite(NULL, $this->config->SITE_KEY);
    }

    /**
     * @covers Livefyre\Validator\SiteValidator::validate
     * @expectedException InvalidArgumentException
     */
    public function testInit_badKey() {
        $this->network->getSite($this->config->SITE_ID, "");
    }

    public function testGetUrn() {
        $urn = $this->site->getUrn();
        $this->assertEquals($this->site->getNetwork()->getUrn() . ":site=" . $this->config->SITE_ID, $urn);
    }
}
