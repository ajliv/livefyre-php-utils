<?php
namespace Livefyre\Api;

use Livefyre\Core\Network;
use Livefyre\Routing\Client;
use Livefyre\Api\Entity\Topic;
use Livefyre\Api\Entity\Subscription;
use Livefyre\Api\Entity\SubscriptionType;

class PersonalizedStreams {

	const BASE_URL = "http://quill.%s/api/v4/";
	// const BASE_URL = "http://127.0.0.1:7722/api/v4/";
	const NETWORK_TOPICS_URL_PATH = ":topics/";
	const COLLECTION_TOPICS_URL_PATH = ":collection=%s:topics/";
	const SUBSCRIPTION_URL_PATH = ":subscriptions/";
	const SUBSCRIBER_URL_PATH = ":subscribers/";

	/* Topic API */
	public static function getTopic($obj, $id) {
		$url = self::getUrl($obj);
		$url = $url . Topic::generateUrn($obj, $id);

		$response = Client::GET($url, self::getHeaders($obj));
		
		$body = self::getData($response);
		if (!property_exists($body, "topic")) {
			return NULL;
		}

		return self::marshallJsonToTopic($body->{"topic"});
	}

	public static function postTopic($obj, $topic) {
		$topics = self::postTopics($obj, array($topic));

		if ($topics === NULL) {
			return NULL;
		}

		return array_shift(array_values($topics));
	}

	public static function patchTopic($obj, $topic) {
		return self::patchTopics($obj, array($topic)) == 1;
	}

	/* Multiple Topic API */
	public static function getTopics($obj, $limit = 100, $offset = 0) {
		$data = array("limit" => $limit, "offset" => $offset);
		$url = self::getTopicsUrl($obj);

		$response = Client::GET($url, self::getHeaders($obj), $data);
		
		$body = self::getData($response);
		if (!property_exists($body, "topics")) {
			return NULL;
		}

		$topics = array();
		foreach ($body->{"topics"} as &$topic) {
			$topics[] = self::marshallJsonToTopic($topic);
		}

		return $topics;
	}

	public static function postTopics($obj, $topics) {
		$json = array();
		foreach ($topics as &$topic) {
			$label = $topic->getLabel();
			if (strlen($label) > 128 || empty($label)) {
				throw new \InvalidArgumentException("topic label should be 128 char or under");
			}
			$json[] = $topic->jsonSerialize();
		}

		$data = json_encode(array("topics" => $json));
		$url = self::getTopicsUrl($obj);

		$response = Client::POST($url, self::getHeaders($obj), $data);
		return $topics;
	}

	public static function patchTopics($obj, $topics) {
		$data = json_encode(array("delete" => self::getTopicIds($topics)));
		$url =  self::getTopicsUrl($obj);

		$response = Client::PATCH($url, self::getHeaders($obj), $data);

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
			return NULL;
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
			return NULL;
		}

		$subscriptions = array();
		foreach ($body->{"subscriptions"} as &$sub) {
			$subscriptions[] = self::marshallJsontoSubscription($sub);
		}

		return $subscriptions;
	}

	public static function postSubscriptions($network, $userId, $topics) {
		$data = json_encode(array("subscriptions" => self::topicToSubscriptions($topics, $userId)));
		$url = self::getSubscriptionUrl($network, $userId);

		$response = Client::POST($url, self::getHeaders($network, $userId), $data);

		$body = self::getData($response);
		if (!property_exists($body, "added")) {
			return 0;
		}

		return $body->{"added"};
	}

	public static function putSubscriptions($network, $userId, $topics) {
		$data = json_encode(array("subscriptions" => self::topicToSubscriptions($topics, $userId)));
		$url = self::getSubscriptionUrl($network, $userId);

		$response = Client::PUT($url, self::getHeaders($network, $userId), $data);

		$body = self::getData($response);
		return (!((property_exists($body, "added") && $body->{"added"} > 0)
			|| (property_exists($body, "removed") && $body->{"removed"} > 0)));
	}

	public static function patchSubscriptions($network, $userId, $topics) {
		$data = json_encode(array("delete" => self::topicToSubscriptions($topics, $userId)));
		$url = self::getSubscriptionUrl($network, $userId);

		$response = Client::PATCH($url, self::getHeaders($network, $userId), $data);
		
		$body = self::getData($response);
		if (!property_exists($body, "removed")) {
			return 0;
		}

		return self::getData($response)->{"removed"};
	}

	public static function getSubscribers($network, $topic, $limit, $offset) {
		$url = self::getUrl($network) . $topic->getId() . self::SUBSCRIBER_URL_PATH;

		$response = Client::GET($url, self::getHeaders($network));
		
		$body = self::getData($response);
		if (!property_exists($body, "subscriptions")) {
			return NULL;
		}

		return self::getData($response)->{"subscriptions"};
	}

	/* Helper Methods */
	private static function getHeaders($obj, $userId = NULL) {
		$token = ($userId === NULL) ? $obj->buildLivefyreToken() : $obj->buildUserAuthToken($userId, "", Network::DEFAULT_EXPIRES);
		return array(
			"Authorization" => "lftoken " . $token,
			"Content-Type" => "application/json"
		);
	}

	private static function getUrl($obj) {
		return sprintf(self::BASE_URL, $obj->getNetworkName());
	}

	private static function getTopicsUrl($obj) {
		return self::getUrl($obj) . $obj->getUrn() . self::NETWORK_TOPICS_URL_PATH;
	}

	private static function getCollectionTopicsUrl($site, $collectionId) {
		return self::getUrl($site) . $site->getUrn() . sprintf(self::COLLECTION_TOPICS_URL_PATH, $collectionId);
	}

	private static function getSubscriptionUrl($network, $user) {
		return self::getUrl($network) . $network->getUserUrn($user) . self::SUBSCRIPTION_URL_PATH;
	}

	private static function getTopicIds($topics) {
		$topicIds = array();
		foreach ($topics as &$topic) {
			$topicIds[] = $topic->getId();
		}
		return $topicIds;
	}

	private static function topicToSubscriptions($topics, $userId) {
		$subscriptions = array();
		foreach($topics as &$topic) {
			$subscriptions[] = (new Subscription($topic->getId(), $userId, SubscriptionType::personalStream))->jsonSerialize();
		}
		return $subscriptions;
	}

	private static function getData($response) {
		return json_decode($response->body)->{"data"};
	}

	private static function marshallJsonToTopic($json) {
		$topic = Topic::copy(
			$json->{"id"},
			$json->{"label"},
			$json->{"createdAt"},
			$json->{"modifiedAt"});
		return $topic;
	}

	private static function marshallJsontoSubscription($json) {
		$subscription = new Subscription(
			$json->{"to"},
			$json->{"by"},
			$json->{"type"},
			$json->{"createdAt"});
		return $subscription;
	}
}