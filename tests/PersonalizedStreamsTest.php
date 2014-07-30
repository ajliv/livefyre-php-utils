<?php
namespace Livefyre\Test;

use Livefyre\Livefyre;

class PersonalizedStreamsClientTest extends \PHPUnit_Framework_TestCase {
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
        $topic = $network->createOrUpdateTopic("1", "UNO");
        $this->assertFalse($topic === null);

        $topic = $network->getTopic("1");
        $this->assertFalse($topic->getCreatedAt() === null);

        $this->assertTrue($network->deleteTopic($topic));


        $topics = $network->createOrUpdateTopics(array("1", "UNO"));
        $this->assertFalse($topics === null);

        $topics = $network->getTopics();
        $this->assertFalse(sizeof($topics) === 1);

        $network->deleteTopics($topics);
    }

    public function testNetworkSubscriptionApi() {
        $network = $this->_network;

        $subs = $network->getSubscriptions(self::USER);

        $topics = $network->createOrUpdateTopics(array("2","DOS","3","TROIS"));

        $network->addSubscriptions(self::USER, $topics);
        $network->updateSubscriptions(self::USER, $topics);

        $topic = $network->getTopic("2");
        $network->getSubscribers($topic);

        $network->removeSubscriptions(self::USER, $topics);

        $network->deleteTopics($topics);
    }

    public function testSiteTopicApi() {
        $site = $this->_site;
        $topic = $site->createOrUpdateTopic("2", "DUL");
        $this->assertFalse($topic === null);

        $topic = $site->getTopic("2");
        $this->assertFalse($topic->getCreatedAt() === null);

        $this->assertTrue($site->deleteTopic($topic));


        $topics = $site->createOrUpdateTopics(array("2", "DUL"));
        $this->assertFalse($topics === null);

        $topics = $site->getTopics();
        $this->assertFalse(sizeof($topics) === 1);

        $site->deleteTopics($topics);
    }
    
    public function testCollectionTopicApi() {
        $site = $this->_site;

        $site->getCollectionTopics(self::COLLECTION_ID);

        $topic2 = $site->createOrUpdateTopic("2", "DUL");

        $site->addCollectionTopics(self::COLLECTION_ID, array($topic2));

        $topic1 = $site->createOrUpdateTopic("1", "HANA");

        $site->updateCollectionTopics(self::COLLECTION_ID, array($topic1));

        $site->removeCollectionTopics(self::COLLECTION_ID, array($topic1, $topic2));

        $site->deleteTopics(array($topic1, $topic2));
    }

    public function testSubscriptions() {
        $network = $this->_network;

        $topic1 = $network->createOrUpdateTopic("1", "HANA");
        $topic2 = $network->createOrUpdateTopic("2", "DUL");

        $network->getSubscriptions(self::USER);

        $network->addSubscriptions(self::USER, array($topic1, $topic2));

        $network->updateSubscriptions(self::USER, array($topic2));

        $network->getSubscribers($topic1);

        $network->removeSubscriptions(self::USER, array($topic2));

        $network->deleteTopic($topic1);
    }

    public function testTimelineStream() {
        $network = $this->_network;

        $cursor = $network->getPersonalStreamCursor(self::USER);

        $data = $cursor->next();
        $data = $cursor->previous();
    }
}
