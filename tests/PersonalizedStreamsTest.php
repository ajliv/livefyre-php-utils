<?php
namespace Livefyre\Test;

use Livefyre\Livefyre;

class PersonalizedStreamsClientImplTest extends \PHPUnit_Framework_TestCase {
   const NETWORK_NAME = "<NETWORK-NAME>";
   const NETWORK_KEY = "<NETWORK-KEY>";
   const SITE_ID = "<SITE-ID>";
   const SITE_KEY = "<SITE-KEY>";
   const COLLECTION_ID = "<COLLECTION-ID>";
    
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
        $this->assertFalse($topic === NULL);

        $topic = $network->getTopic("1");
        $this->assertFalse($topic->getCreatedAt() === NULL);

        $this->assertTrue($network->deleteTopic($topic));


        $topics = $network->createOrUpdateTopics(array("1", "UNO"));
        $this->assertFalse($topics === NULL);

        $topics = $network->getTopics();
        $this->assertFalse(sizeof($topics) === 1);

        $network->deleteTopics($topics);
    }

    public function testNetworkSubscriptionApi() {
        $network = $this->_network;
        $user = '539f362185889e79f5000000';

        $subs = $network->getSubscriptions($user);

        $topics = $network->createOrUpdateTopics(array("2","DOS","3","TROIS"));

        $network->addSubscriptions($user, $topics);
        $network->updateSubscriptions($user, $topics);

        $topic = $network->getTopic("2");
        $network->getSubscribers($topic);

        $network->removeSubscriptions($user, $topics);

        $network->deleteTopics($topics);
    }

    public function testSiteTopicApi() {
        $site = $this->_site;
        $topic = $site->createOrUpdateTopic("2", "DUL");
        $this->assertFalse($topic === NULL);

        $topic = $site->getTopic("2");
        $this->assertFalse($topic->getCreatedAt() === NULL);

        $this->assertTrue($site->deleteTopic($topic));


        $topics = $site->createOrUpdateTopics(array("2", "DUL"));
        $this->assertFalse($topics === NULL);

        $topics = $site->getTopics();
        $this->assertFalse(sizeof($topics) === 1);

        $site->deleteTopics($topics);
    }
    
    public function testCollectionTopicApi() {
        $site = $this->_site;

        $site->getCollectionTopics(self::COLLECTION_ID);

        $topic = $site->createOrUpdateTopic("2", "DUL");

        $site->addCollectionTopics(self::COLLECTION_ID, array($topic));

        $topic = $site->createOrUpdateTopic("1", "HANA");

        $site->updateCollectionTopics(self::COLLECTION_ID, array($topic));

        $site->removeCollectionTopics(self::COLLECTION_ID, array($topic));
    }
}
