<?php
namespace Livefyre\Test;

use Livefyre\Livefyre;
use Livefyre\Api\PersonalizedStreamsClient;
use Livefyre\Factory\CursorFactory;

class PersonalizedStreamsClientTest extends \PHPUnit_Framework_TestCase {
    const NETWORK_NAME = "<NETWORK-NAME>";
    const NETWORK_KEY = "<NETWORK-KEY>";
    const SITE_ID = "<SITE-ID>";
    const SITE_KEY = "<SITE-KEY>";
    const COLLECTION_ID = "<COLLECTION-ID>";
    const USER_ID = "<USER-ID>";
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
        $topic = PersonalizedStreamsClient::createOrUpdateTopic($network, "1", "UNO");
        $this->assertFalse($topic === null);

        $topic = PersonalizedStreamsClient::getTopic($network, "1");
        $this->assertFalse($topic->getCreatedAt() === null);

        $this->assertTrue(PersonalizedStreamsClient::deleteTopic($network, $topic));


        $topics = PersonalizedStreamsClient::createOrUpdateTopics($network, array("1", "UNO"));
        $this->assertFalse($topics === null);

        $topics = PersonalizedStreamsClient::getTopics($network);
        $this->assertFalse(sizeof($topics) === 1);

        PersonalizedStreamsClient::deleteTopics($network, $topics);
    }

    public function testSiteTopicApi() {
        $site = $this->_site;
        $topic = PersonalizedStreamsClient::createOrUpdateTopic($site, "2", "DUL");
        $this->assertFalse($topic === null);

        $topic = PersonalizedStreamsClient::getTopic($site, "2");
        $this->assertFalse($topic->getCreatedAt() === null);

        $this->assertTrue(PersonalizedStreamsClient::deleteTopic($site, $topic));


        $topics = PersonalizedStreamsClient::createOrUpdateTopics($site, array("2", "DUL"));
        $this->assertFalse($topics === null);

        $topics = PersonalizedStreamsClient::getTopics($site);
        $this->assertFalse(sizeof($topics) === 1);

        PersonalizedStreamsClient::deleteTopics($site, $topics);
    }
    
    public function testCollectionTopicApi() {
        $site = $this->_site;

        PersonalizedStreamsClient::getCollectionTopics($site, self::COLLECTION_ID);

        $topic2 = PersonalizedStreamsClient::createOrUpdateTopic($site, "2", "DUL");

        PersonalizedStreamsClient::addCollectionTopics($site, self::COLLECTION_ID, array($topic2));

        $topic1 = PersonalizedStreamsClient::createOrUpdateTopic($site, "1", "HANA");

        PersonalizedStreamsClient::replaceCollectionTopics($site, self::COLLECTION_ID, array($topic1));

        PersonalizedStreamsClient::removeCollectionTopics($site, self::COLLECTION_ID, array($topic1, $topic2));

        PersonalizedStreamsClient::deleteTopics($site, array($topic1, $topic2));
    }

    public function testSubscriptions() {
        $network = $this->_network;
        $userToken = $network->buildUserAuthToken(self::USER_ID, self::USER_ID . "@" . self::NETWORK_NAME, $network::DEFAULT_EXPIRES);

        $topic1 = PersonalizedStreamsClient::createOrUpdateTopic($network, "1", "HANA");
        $topic2 = PersonalizedStreamsClient::createOrUpdateTopic($network, "2", "DUL");

        PersonalizedStreamsClient::getSubscriptions($network, self::USER_ID);

        PersonalizedStreamsClient::addSubscriptions($network, $userToken, array($topic1, $topic2));

        PersonalizedStreamsClient::replaceSubscriptions($network, $userToken, array($topic2));

        PersonalizedStreamsClient::getSubscribers($network, $topic1);

        PersonalizedStreamsClient::removeSubscriptions($network, $userToken, array($topic2));

        PersonalizedStreamsClient::deleteTopic($network, $topic1);
    }

    public function testTimelineStream() {
        $network = $this->_network;

        $cursor = CursorFactory::getPersonalStreamCursor($network, self::USER_ID);

        $data = $cursor->next();
        $data = $cursor->previous();
    }
}
