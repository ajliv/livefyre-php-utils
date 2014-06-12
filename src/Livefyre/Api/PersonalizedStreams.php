<?php

use Requests;
use Livefyre\Api\Dto\Topic;

class PersonalizedStreams {

	private $_BASE_URL = "https://%1$s/api/v4/%1$s";
	private $_NETWORK_TOPIC_URL_PATH = "/topic/%1$s/";
	private $_SITE_TOPIC_URL_PATH = "/site/%1$s/topic/%2$s/";
	private $_NETWORK_TOPICS_URL_PATH = "/topics/";
	private $_SITE_TOPICS_URL_PATH = "/site/%1$s/topics/";
	private $_COLLECTION_TOPICS_URL_PATH = "/site/%1$s/collection/%2$s/topics/";
	private $_USER_SUBSCRIPTION_URL_PATH = "/user/%1$s/subscriptions/";

	/* Topic API */
	public static function getTopic($obj, $id) {
		$headers = array("Authorization" => "lftoken " + $obj->buildLivefyreToken());
		$response = Requests::get($this::getUrl($obj, $id), $headers);
		
		return $this::marshallJsonToTopic(json_decode($response->body)->{"topic"});
	}

	public static function addOrUpdateTopic($obj, $topic) {
		if (strlen($topic->getLabel()) > 128 || empty($topic->getLabel())) {
			throw new \InvalidArgumentException("topic label should be 128 char or under");
		}

		$data = json_encode(array("topic" => $topic));
		$headers = array(
			"Authorization" => "lftoken " + $obj->buildLivefyreToken(),
			"Content-Type" => "application/json"
		);
		$response = Requests::post($this::getUrl($obj, $id), $headers, $data);
		
		$date = json_decode($response->body)->{"updated"};

		$topic->setModifiedAt($date);

		if (!isset($topic->getCreatedAt())) {
			$topic->setCreatedAt($date);
		}

		return json_decode($response->body);
	}

	public static function deleteTopic($obj, $id) {
		$headers = array("Authorization" => "lftoken " + $obj->buildLivefyreToken());
		$response = Requests::delete($this::getUrl($obj, $id), $headers, $data);
		
		return json_decode($response->body)->{"deleted"} == 1;
	}

	/* Multiple Topic API */
	public static function getTopics($obj, $limit = 100, $offset = 0) {
		$data = array("limit" => $limit, "offset" => $offset);
		$headers = array("Authorization" => "lftoken " + $obj->buildLivefyreToken());
		$response = Requests::get($this::getUrl($obj, NULL), $headers, $data);
		
		$topics = array();
		foreach (json_decode($response->body)->{"topics"} as $topic) {
			$topics[] = $this::marshallJsonToTopic($topic);
		}
		return $topics;
	}

	public static function addOrUpdateTopics($obj, $topics) {
		foreach ($topics as $topic) {
			if (strlen($topic->getLabel()) > 128 || empty($topic->getLabel()) {
				throw new \InvalidArgumentException("topic label should be 128 char or under");
			}
		}
		$data = json_encode(array("topics" => $topics));
		$headers = array(
			"Authorization" => "lftoken " + $obj->buildLivefyreToken(),
			"Content-Type" => "application/json"
		);
		$response = Requests::post($this::getUrl($obj, NULL), $headers, $data);

		$date = json_decode($response->body)->{"updated"};
		foreach ($topics as $topic) {
			$topic->setModifiedAt($date);

			if (!isset($topic->getCreatedAt())) {
				$topic->setCreatedAt($date);
			}
		}
		return $topics;
	}

	public static function deleteTopics($obj, $ids) {
		$data = json_encode(array("topicIds" => $ids));
		$headers = array(
			"Authorization" => "lftoken " + $obj->buildLivefyreToken(),
			"Content-Type" => "application/json"
		);
		$response = Requests::delete($this::getUrl($obj, NULL), $headers, $data);

		return json_decode($response->body);
	}

	/* Collection Topic API */
	public static function getCollectionTopics($site, $collectionId) {
		$headers = array("Authorization" => "lftoken " + $site->buildLivefyreToken());
		$response = Requests::get($this::getCollectionUrl($site, $collectionId), $headers, $data);
		
		return json_decode($response->body)->{"topicIds"};
	}

	public static function addCollectionTopics($site, $collectionId, $topics) {
		$data = json_encode(array("topicIds" => $this::getTopicIds($topics)));
		$headers = array(
			"Authorization" => "lftoken " + $this->buildLivefyreToken(),
			"Content-Type" => "application/json"
		);
		$response = Requests::post($this::getCollectionUrl($site, $collectionId), $headers, $data);

		return json_decode($response->body);
	}

	public static function updateCollectionTopics($site, $collectionId, $topics) {
		$data = json_encode(array("topicIds" => $this::getTopicIds($topics)));
		$headers = array(
			"Authorization" => "lftoken " + $this->buildLivefyreToken(),
			"Content-Type" => "application/json"
		);
		$response = Requests::post($this::getCollectionUrl($site, $collectionId), $headers, $data);

		return json_decode($response->body);
	}

	public static function deleteCollectionTopics($site, $collectionId, $topics) {
		$data = json_encode(array("topicIds" => $this::getTopicIds($topics)));
		$headers = array(
			"Authorization" => "lftoken " + $this->buildLivefyreToken(),
			"Content-Type" => "application/json"
		);
		$response = Requests::delete($this::getCollectionUrl($site, $collectionId), $headers, $data);
		
		return json_decode($response->body);
	}

	/* UserSubscription API */
	public static function getUserSubscriptions($network, $userId) {
		$headers = array("Authorization" => "lftoken " + $network->buildLivefyreToken());
		$response = Requests::get($this::getUserSubscriptionUrl($network, $userId), $headers, $data);
		
		return json_decode($response->body)->{"objectIds"};
	}

	public static function addUserSubscriptions($network, $userId, $topics) {
		$data = json_encode(array("objectIds" => $this::getTopicIds($topics)));
		$headers = array(
			"Authorization" => "lftoken " + $this->buildLivefyreToken(),
			"Content-Type" => "application/json"
		);
		$response = Requests::post($this::getUserSubscriptionUrl($network, $userId), $headers, $data);

		return json_decode($response->body)->{"added"};
	}

	public static function updateUserSubscriptions($network, $userId, $topics) {
		$data = json_encode(array("objectIds" => $this::getTopicIds($topics)));
		$headers = array(
			"Authorization" => "lftoken " + $this->buildLivefyreToken(),
			"Content-Type" => "application/json"
		);
		$response = Requests::post($this::getUserSubscriptionUrl($network, $userId), $headers, $data);

		return json_decode($response->body);
	}

	public static function deleteUserSubscriptions($network, $userId, $topics) {
		$data = json_encode(array("objectIds" => $this::getTopicIds($topics)));
		$headers = array(
			"Authorization" => "lftoken " + $this->buildLivefyreToken(),
			"Content-Type" => "application/json"
		);

		$response = Requests::delete($this::getUserSubscriptionUrl($network, $userId), $headers, $data);
		
		return json_decode($response->body)->{"removed"};
	}

	/* Helper Methods */
	private static function getUrl($obj, $id) {
		$base = "";
		$path = "";
		if (get_class($obj) == "Network") {
			$base = sprintf($this->_BASE_URL, $obj->getName());

			if (isset($id)) {
				$path = sprintf($this->_NETWORK_TOPIC_URL_PATH, $id);
			} else {
				$path = $this->_NETWORK_TOPICs_URL_PATH;
			}
		} elseif (get_class($obj) == "Site") {
			$base = sprintf($this->_BASE_URL, $obj->getNetworkName());

			if (isset($id)) {
				$path = sprintf($this->_SITE_TOPIC_URL_PATH, $obj->getId(), $id);
			} else {
				$path = sprintf($this->_SITE_TOPICS_URL_PATH, $obj->getId());
			}
		}
		return $base+$path;
	}

	private static function getCollectionUrl($site, $collectionId) {
		$base = sprintf($this->_BASE_URL, $site->getNetworkName());
		$path = sprintf($this->_COLLECTION_TOPICS_URL_PATH, $site->getId(), $collectionId);
		return $base+$path;
	}

	private static function getUserSubscriptionUrl($network, $userId) {
		$base = sprintf($this->_BASE_URL, $network->getName());
		$path = sprintf($this->_USER_SUBSCRIPTION_URL_PATH, $userId);
		return $base+$path;
	}

	private static function getTopicIds($topics) {
		$topicIds = array();
		foreach ($topics as $topic) {
			$topicIds[] = $topic->getId();
		}
		return $topicIds;
	}

	private static function marshallJsonToTopic($json) {
		$topic = new Topic(
			$json->{"id"},
			$json->{"label"},
			$json->{"createdAt"},
			$json->{"modifiedAt"});
		return $topic;
	}
}