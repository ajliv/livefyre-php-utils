<?php
namespace Livefyre\Api;

use Livefyre\Core\Network;
use Livefyre\Routing\Client;
use Livefyre\Entity\Topic;
use Livefyre\Entity\Subscription;
use Livefyre\Entity\SubscriptionType;

class PersonalizedStreamsClient {

	const BASE_URL = "http://quill.%s/api/v4/";
	const STREAM_URL = "http://bootstrap.%s/api/v4/";

	const NETWORK_TOPICS_URL_PATH = ":topics/";
	const COLLECTION_TOPICS_URL_PATH = ":collection=%s:topics/";
	const SUBSCRIPTION_URL_PATH = ":subscriptions/";
	const SUBSCRIBER_URL_PATH = ":subscribers/";
	const TIMELINE_PATH = "timeline/";

	/* Topic API */
	public static function getTopic($core, $id) {
		$url = self::getUrl($core);
		$url = $url . Topic::generateUrn($core, $id);

		$response = Client::GET($url, self::getHeaders($core));
		
		$body = self::getData($response);
		if (!property_exists($body, "topic")) {
			return null;
		}

		return Topic::serializeFromJson($body->{"topic"});
	}

	public static function postTopic($core, $topic) {
		$topics = self::postTopics($core, array($topic));

		if (is_null($topics)) {
			return null;
		}

		return array_shift(array_values($topics));
	}

	public static function patchTopic($core, $topic) {
		return self::patchTopics($core, array($topic)) == 1;
	}

	/* Multiple Topic API */
	public static function getTopics($core, $limit = 100, $offset = 0) {
		$url = self::getTopicsUrl($core) . "?limit=" . $limit . "&offset=" . $offset; 

		$response = Client::GET($url, self::getHeaders($core));
		
		$body = self::getData($response);
		if (!property_exists($body, "topics")) {
			return null;
		}

		$topics = array();
		foreach ($body->{"topics"} as &$topic) {
			$topics[] = Topic::serializeFromJson($topic);
		}

		return $topics;
	}

	public static function postTopics($core, $topics) {
		$json = array();
		foreach ($topics as &$topic) {
			$label = $topic->getLabel();
			if (empty($label) || strlen($label) > 128) {
				throw new \InvalidArgumentException("topic label should be 128 char or under and not empty");
			}
			$json[] = $topic->serializeToJson();
		}

		$data = json_encode(array("topics" => $json));
		$url = self::getTopicsUrl($core);

		$response = Client::POST($url, self::getHeaders($core), $data);
		return $topics;
	}

	public static function patchTopics($core, $topics) {
		$data = json_encode(array("delete" => self::getTopicIds($topics)));
		$url =  self::getTopicsUrl($core);

		$response = Client::PATCH($url, self::getHeaders($core), $data);

		$body = self::getData($response);
		if (!property_exists($body, "deleted")) {
			return 0;
		}

		return $body->{"deleted"};
	}

	/* Collection Topic API */
	public static function getCollectionTopics($site, $collectionId) {
		$url = self::getCollectionTopicsUrl($site, $collectionId);

		$response = Client::GET($url, self::getHeaders($site));

		$body = self::getData($response);
		if (!property_exists($body, "topicIds")) {
			return null;
		}

		return $body->{"topicIds"};
	}

	public static function postCollectionTopics($site, $collectionId, $topics) {
		$data = json_encode(array("topicIds" => self::getTopicIds($topics)));
		$url = self::getCollectionTopicsUrl($site, $collectionId);

		$response = Client::POST($url, self::getHeaders($site), $data);

		$body = self::getData($response);
		if (!property_exists($body, "added")) {
			return 0;
		}

		return $body->{"added"};
	}

	public static function putCollectionTopics($site, $collectionId, $topics) {
		$data = json_encode(array("topicIds" => self::getTopicIds($topics)));
		$url = self::getCollectionTopicsUrl($site, $collectionId);

		$response = Client::PUT($url, self::getHeaders($site), $data);

		$body = self::getData($response);
		return (!((property_exists($body, "added") && $body->{"added"} > 0)
			|| (property_exists($body, "removed") && $body->{"removed"} > 0)));
	}

	public static function patchCollectionTopics($site, $collectionId, $topics) {
		$data = json_encode(array("delete" => self::getTopicIds($topics)));
		$url = self::getCollectionTopicsUrl($site, $collectionId);

		$response = Client::PATCH($url, self::getHeaders($site), $data);
		
		$body = self::getData($response);
		if (!property_exists($body, "removed")) {
			return 0;
		}

		return $body->{"removed"};
	}

	/* UserSubscription API */
	public static function getSubscriptions($network, $userId) {
		$url = self::getSubscriptionUrl($network, $userId);

		$response = Client::GET($url, self::getHeaders($network));

		$body = self::getData($response);
		if (!property_exists($body, "subscriptions")) {
			return null;
		}

		$subscriptions = array();
		foreach ($body->{"subscriptions"} as &$sub) {
			$subscriptions[] = Subscription::serializeFromJson($sub);
		}

		return $subscriptions;
	}

	public static function postSubscriptions($network, $userId, $topics) {
		$data = json_encode(array("subscriptions" => self::buildSubscriptions($topics, $userId)));
		$url = self::getSubscriptionUrl($network, $userId);

		$response = Client::POST($url, self::getHeaders($network, $userId), $data);

		$body = self::getData($response);
		if (!property_exists($body, "added")) {
			return 0;
		}

		return $body->{"added"};
	}

	public static function putSubscriptions($network, $userId, $topics) {
		$data = json_encode(array("subscriptions" => self::buildSubscriptions($topics, $userId)));
		$url = self::getSubscriptionUrl($network, $userId);

		$response = Client::PUT($url, self::getHeaders($network, $userId), $data);

		$body = self::getData($response);
		return (!((property_exists($body, "added") && $body->{"added"} > 0)
			|| (property_exists($body, "removed") && $body->{"removed"} > 0)));
	}

	public static function patchSubscriptions($network, $userId, $topics) {
		$data = json_encode(array("delete" => self::buildSubscriptions($topics, $userId)));
		$url = self::getSubscriptionUrl($network, $userId);

		$response = Client::PATCH($url, self::getHeaders($network, $userId), $data);
		
		$body = self::getData($response);
		if (!property_exists($body, "removed")) {
			return 0;
		}

		return self::getData($response)->{"removed"};
	}

	public static function getSubscribers($network, $topic, $limit = 100, $offset = 0) {
		$url = self::getUrl($network) . $topic->getId() . self::SUBSCRIBER_URL_PATH;

		$response = Client::GET($url, self::getHeaders($network));
		
		$body = self::getData($response);
		if (!property_exists($body, "subscriptions")) {
			return null;
		}

		return self::getData($response)->{"subscriptions"};
	}

	public static function getTimelineStream($core, $resource, $limit = 50, $until = null, $since = null) {
		$url = self::getTimelineUrl($core) . "?resource=" . $resource . "&limit=" . $limit;

		if (isset($until)) {
			$url .= "&until=" . $until;
		} elseif (isset($since)) {
			$url .= "&since=" . $since;
		}

		$response = Client::GET($url, self::getHeaders($core));

		return json_decode($response->body);
	}

	/* Helper Methods */
	private static function getHeaders($core, $userId = null) {
		$token = ($userId === null) ? $core->buildLivefyreToken() : $core->buildUserAuthToken($userId, "", Network::DEFAULT_EXPIRES);
		return array(
			"Authorization" => "lftoken " . $token,
			"Content-Type" => "application/json"
		);
	}

	private static function getUrl($core) {
		return sprintf(self::BASE_URL, $core->getNetworkName());
	}

	private static function getTopicsUrl($core) {
		return self::getUrl($core) . $core->getUrn() . self::NETWORK_TOPICS_URL_PATH;
	}

	private static function getCollectionTopicsUrl($site, $collectionId) {
		return self::getUrl($site) . $site->getUrn() . sprintf(self::COLLECTION_TOPICS_URL_PATH, $collectionId);
	}

	private static function getSubscriptionUrl($network, $user) {
		return self::getUrl($network) . $network->getUserUrn($user) . self::SUBSCRIPTION_URL_PATH;
	}

	private static function getTimelineUrl($core) {
		return sprintf(self::STREAM_URL, $core->getNetworkName()) . self::TIMELINE_PATH;
	}

	private static function getTopicIds($topics) {
		$topicIds = array();
		foreach ($topics as &$topic) {
			$topicIds[] = $topic->getId();
		}
		return $topicIds;
	}

	private static function buildSubscriptions($topics, $userId) {
		$subscriptions = array();
		foreach($topics as &$topic) {
			$subscriptions[] = (new Subscription($topic->getId(), $userId, SubscriptionType::personalStream))->serializeToJson();
		}
		return $subscriptions;
	}

	private static function getData($response) {
		return json_decode($response->body)->{"data"};
	}
}
