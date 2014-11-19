<?php

namespace Livefyre;


use Livefyre\Dto\Topic;
use Livefyre\Factory\CursorFactory;

class CursorFactoryTest extends \PHPUnit_Framework_TestCase {
    private $config;
    private $network;

    const LIMIT = 10;

    protected function setUp() {
        $this->config = new LfTest();
        $this->config->setPropValues("prod");
        $this->network = Livefyre::getNetwork($this->config->NETWORK_NAME, $this->config->NETWORK_KEY);
    }

    public function testPersonalStreamCursor() {
        $psResource = sprintf("urn:livefyre:%s.fyre.co:user=%s:personalStream", $this->network->getNetworkName(), $this->config->USER_ID);

        $cursor = CursorFactory::getPersonalStreamCursor($this->network, $this->config->USER_ID);
        $this->assertEquals($psResource, $cursor->getData()->getResource());

        $cursor = CursorFactory::getPersonalStreamCursor($this->network, $this->config->USER_ID, $this::LIMIT, time());
        $this->assertEquals($psResource, $cursor->getData()->getResource());
        $this->assertEquals($this::LIMIT, $cursor->getData()->getLimit());
    }

    public function testTopicStreamCursor() {
        $topicId = "topic";
        $label = "label";
        $tsResource = sprintf("urn:livefyre:%s.fyre.co:topic=%s:topicStream", $this->network->getNetworkName(), $topicId);

        $topic = Topic::create($this->network, $topicId, $label);
        $cursor = CursorFactory::getTopicStreamCursor($this->network, $topic);
        $this->assertEquals($tsResource, $cursor->getData()->getResource());

        $cursor = CursorFactory::getTopicStreamCursor($this->network, $topic, $this::LIMIT, time());
        $this->assertEquals($tsResource, $cursor->getData()->getResource());
        $this->assertEquals($this::LIMIT, $cursor->getData()->getLimit());
    }
}