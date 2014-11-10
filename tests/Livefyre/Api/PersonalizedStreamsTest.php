<?php
namespace Livefyre;

use Livefyre\Api\PersonalizedStream;
use Livefyre\Factory\CursorFactory;

class PersonalizedStreamTest extends \PHPUnit_Framework_TestCase {
    private $network;
    private $site;
    private $config;

    protected function setUp() {
        $this->config = new LfTest();
        $this->config->setPropValues("prod");
        $this->network = Livefyre::getNetwork($this->config->NETWORK_NAME, $this->config->NETWORK_KEY);
        $this->site = $this->network->getSite($this->config->SITE_ID, $this->config->SITE_KEY);
    }

    public function testNetworkTopicApi() {
        $network = $this->network;
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
        $site = $this->site;
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
        $site = $this->site;
        $name = "PHP PSSTREAM TEST " . time();
        $collection = $this->site->buildLiveCommentsCollection($name, $name, $this->config->URL);
        $collection->createOrUpdate();

        PersonalizedStream::getCollectionTopics($collection);

        $topic2 = PersonalizedStream::createOrUpdateTopic($site, "2", "DUL");

        PersonalizedStream::addCollectionTopics($collection, array($topic2));

        $topic1 = PersonalizedStream::createOrUpdateTopic($site, "1", "HANA");

        PersonalizedStream::replaceCollectionTopics($collection, array($topic1));

        $name = "PHP PSSTREAM TEST " . time();
        $collection2 = $this->site->buildLiveCommentsCollection($name, name, $this->config->URL);
        $collection2->getData()->setTopics(array($topic1, $topic2));
        $collection2->createOrUpdate();

        PersonalizedStream::removeCollectionTopics($collection, array($topic1, $topic2));

        PersonalizedStream::deleteTopics($site, array($topic1, $topic2));
    }

    public function testSubscriptions() {
        $network = $this->network;
        $userToken = $network->buildUserAuthToken($this->config->USER_ID, $this->config->USER_ID . "@" . $this->config->NETWORK_NAME, $network::DEFAULT_EXPIRES);

        $topic1 = PersonalizedStream::createOrUpdateTopic($network, "1", "HANA");
        $topic2 = PersonalizedStream::createOrUpdateTopic($network, "2", "DUL");

        PersonalizedStream::getSubscriptions($network, $this->config->USER_ID);

        PersonalizedStream::addSubscriptions($network, $userToken, array($topic1, $topic2));

        PersonalizedStream::replaceSubscriptions($network, $userToken, array($topic2));

        PersonalizedStream::getSubscribers($network, $topic1);

        PersonalizedStream::removeSubscriptions($network, $userToken, array($topic2));

        PersonalizedStream::deleteTopic($network, $topic1);
    }

    public function testTimelineStream() {
        $network = $this->network;

        $cursor = CursorFactory::getPersonalStreamCursor($network, $this->config->USER_ID);

        $data = $cursor->next();
        $data = $cursor->previous();
    }
}
