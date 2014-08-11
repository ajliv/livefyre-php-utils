<?php
namespace Livefyre;

use Livefyre\Livefyre;
use Livefyre\Api\PersonalizedStream;
use Livefyre\Factory\CursorFactory;

use Livefyre\LfTest;

class PersonalizedStreamTest extends \PHPUnit_Framework_TestCase {
    private $_network;
    private $_site;
    private $_config;

    protected function setUp() {
        $this->markTestSkipped("can't make network calls to bad params.");

        $this->_config = new LfTest();
        $this->_config->setPropValues("prod");
        $this->_network = Livefyre::getNetwork($this->_config->NETWORK_NAME, $this->_config->NETWORK_KEY);
        $this->_site = $this->_network->getSite($this->_config->SITE_ID, $this->_config->SITE_KEY);
    }
    
    public function testNetworkTopicApi() {
        $network = $this->_network;
        $topic = PersonalizedStream::createOrUpdateTopic($network, "1", "UNO");
        $this->assertFalse($topic === null);

        $topic = PersonalizedStream::getTopic($network, "1");
        $this->assertFalse($topic->getCreatedAt() === null);

        $this->assertTrue(PersonalizedStream::deleteTopic($network, $topic));


        $topics = PersonalizedStream::createOrUpdateTopics($network, array("1", "UNO"));
        $this->assertFalse($topics === null);

        $topics = PersonalizedStream::getTopics($network);
        $this->assertFalse(sizeof($topics) === 1);

        PersonalizedStream::deleteTopics($network, $topics);
    }

    public function testSiteTopicApi() {
        $site = $this->_site;
        $topic = PersonalizedStream::createOrUpdateTopic($site, "2", "DUL");
        $this->assertFalse($topic === null);

        $topic = PersonalizedStream::getTopic($site, "2");
        $this->assertFalse($topic->getCreatedAt() === null);

        $this->assertTrue(PersonalizedStream::deleteTopic($site, $topic));


        $topics = PersonalizedStream::createOrUpdateTopics($site, array("2", "DUL"));
        $this->assertFalse($topics === null);

        $topics = PersonalizedStream::getTopics($site);
        $this->assertFalse(sizeof($topics) === 1);

        PersonalizedStream::deleteTopics($site, $topics);
    }
    
    public function testCollectionTopicApi() {
        $site = $this->_site;

        PersonalizedStream::getCollectionTopics($site, $this->_config->COLLECTION_ID);

        $topic2 = PersonalizedStream::createOrUpdateTopic($site, "2", "DUL");

        PersonalizedStream::addCollectionTopics($site, $this->_config->COLLECTION_ID, array($topic2));

        $topic1 = PersonalizedStream::createOrUpdateTopic($site, "1", "HANA");

        PersonalizedStream::replaceCollectionTopics($site, $this->_config->COLLECTION_ID, array($topic1));

        PersonalizedStream::removeCollectionTopics($site, $this->_config->COLLECTION_ID, array($topic1, $topic2));

        PersonalizedStream::deleteTopics($site, array($topic1, $topic2));
    }

    public function testSubscriptions() {
        $network = $this->_network;
        $userToken = $network->buildUserAuthToken($this->_config->USER_ID, $this->_config->USER_ID . "@" . $this->_config->NETWORK_NAME, $network::DEFAULT_EXPIRES);

        $topic1 = PersonalizedStream::createOrUpdateTopic($network, "1", "HANA");
        $topic2 = PersonalizedStream::createOrUpdateTopic($network, "2", "DUL");

        PersonalizedStream::getSubscriptions($network, $this->_config->USER_ID);

        PersonalizedStream::addSubscriptions($network, $userToken, array($topic1, $topic2));

        PersonalizedStream::replaceSubscriptions($network, $userToken, array($topic2));

        PersonalizedStream::getSubscribers($network, $topic1);

        PersonalizedStream::removeSubscriptions($network, $userToken, array($topic2));

        PersonalizedStream::deleteTopic($network, $topic1);
    }

    public function testTimelineStream() {
        $network = $this->_network;

        $cursor = CursorFactory::getPersonalStreamCursor($network, $this->_config->USER_ID);

        $data = $cursor->next();
        $data = $cursor->previous();
    }
}
