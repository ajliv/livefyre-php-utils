<?php
namespace Livefyre\Test;

use Livefyre\Livefyre;
use Livefyre\Api\PersonalizedStream;
use Livefyre\Factory\CursorFactory;

class PersonalizedStreamTest extends \PHPUnit_Framework_TestCase {
    const NETWORK_NAME = "<NETWORK-NAME>";
    const NETWORK_KEY = "<NETWORK-KEY>";
    const SITE_ID = "<SITE-ID>";
    const SITE_KEY = "<SITE-KEY>";
    const COLLECTION_ID = "<COLLECTION-ID>";
    const USER = "<USER-ID>";
    const ARTICLE_ID = "<ARTICLE-ID>";

    private $_network;
    private $_site;

    protected function setUp() {
        $this->markTestSkipped(
              "can't make network calls to bad params."
            );

        $this->_network = Livefyre::getNetwork(self::NETWORK_NAME, self::NETWORK_KEY);
        $this->_site = $this->_network->getSite(self::SITE_ID, self::SITE_KEY);
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

        PersonalizedStream::getCollectionTopics($site, self::COLLECTION_ID);

        $topic2 = PersonalizedStream::createOrUpdateTopic($site, "2", "DUL");

        PersonalizedStream::addCollectionTopics($site, self::COLLECTION_ID, array($topic2));

        $topic1 = PersonalizedStream::createOrUpdateTopic($site, "1", "HANA");

        PersonalizedStream::replaceCollectionTopics($site, self::COLLECTION_ID, array($topic1));

        PersonalizedStream::removeCollectionTopics($site, self::COLLECTION_ID, array($topic1, $topic2));

        PersonalizedStream::deleteTopics($site, array($topic1, $topic2));
    }

    public function testSubscriptions() {
        $network = $this->_network;
        $userToken = $network->buildUserAuthToken(self::USER_ID, self::USER_ID . "@" . self::NETWORK_NAME, $network::DEFAULT_EXPIRES);

        $topic1 = PersonalizedStream::createOrUpdateTopic($network, "1", "HANA");
        $topic2 = PersonalizedStream::createOrUpdateTopic($network, "2", "DUL");

        PersonalizedStream::getSubscriptions($network, self::USER_ID);

        PersonalizedStream::addSubscriptions($network, $userToken, array($topic1, $topic2));

        PersonalizedStream::replaceSubscriptions($network, $userToken, array($topic2));

        PersonalizedStream::getSubscribers($network, $topic1);

        PersonalizedStream::removeSubscriptions($network, $userToken, array($topic2));

        PersonalizedStream::deleteTopic($network, $topic1);
    }

    public function testTimelineStream() {
        $network = $this->_network;

        $cursor = CursorFactory::getPersonalStreamCursor($network, self::USER_ID);

        $data = $cursor->next();
        $data = $cursor->previous();
    }
}
