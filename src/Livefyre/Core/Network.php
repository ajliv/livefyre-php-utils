<?php
namespace Livefyre\Core;

use Livefyre\Api\PersonalizedStreamsClient;
use Livefyre\Entity\Topic;
use Livefyre\Factory\CursorFactory;
use Livefyre\Routing\Client;
use Livefyre\Utils\JWT;

class Network {
	const DEFAULT_USER = "system";
	const DEFAULT_EXPIRES = 86400;

	private $_name;
	private $_key;

	public function __construct($name, $key) {
		$this->_name = $name;
		$this->_key = $key;
	}

	public function setUserSyncUrl($urlTemplate) {
		if (strpos($urlTemplate, "{id}") === false) {
			throw new \InvalidArgumentException("urlTemplate should contain {id}");
		}

		$url = sprintf("http://%s", $this->_name);
		$data = array("actor_token" => $this->buildLivefyreToken(), "pull_profile_url" => $urlTemplate);
		$response = Client::POST($url, array(), $data);
		
		return $response->status_code == 204;
	}

	public function syncUser($userId) {
		$data = array("lftoken" => $this->buildLivefyreToken());
		$url = sprintf("http://%s/api/v3_0/user/%s/refresh", $this->_name, $userId);

		$response = Client::POST($url, array(), $data);
		
		return $response->status_code == 200;
	}

	public function buildLivefyreToken() {
		return $this->buildUserAuthToken(self::DEFAULT_USER, self::DEFAULT_USER, self::DEFAULT_EXPIRES);
	}

	public function buildUserAuthToken($userId, $displayName, $expires) {
		if (!ctype_alnum($userId)) {
			throw new \InvalidArgumentException("userId must be alphanumeric");
		}

		$token = array(
		    "domain" => $this->_name,
		    "user_id" => $userId,
		    "display_name" => $displayName,
		    "expires" => time() + (int)$expires
		);

		return JWT::encode($token, $this->_key);
	}

	public function validateLivefyreToken($lfToken) {
		$tokenAttributes = JWT::decode($lfToken, $this->_key);

		return $tokenAttributes->domain == $this->_name
			&& $tokenAttributes->user_id == self::DEFAULT_USER
			&& $tokenAttributes->expires >= time();
	}

	public function getSite($siteId, $siteKey) {
		return new Site($this, $siteId, $siteKey);
	}

	/* Topics */
	public function getTopic($id) {
		return PersonalizedStreamsClient::getTopic($this, $id);
	}
	public function createOrUpdateTopic($id, $label) {
		$topic = Topic::create($this, $id, $label);

		return PersonalizedStreamsClient::postTopic($this, $topic);
	}
	public function deleteTopic($topic) {
		return PersonalizedStreamsClient::patchTopic($this, $topic);
	}

	public function getTopics($limit = 100, $offset = 0) {
		return PersonalizedStreamsClient::getTopics($this, $limit, $offset);
	}
	public function createOrUpdateTopics($topicMap) {
		$topics = array();
		foreach ($topicMap as $id => $label) {
		    array_push($topics, Topic::create($this, $id, $label));
		}

		return PersonalizedStreamsClient::postTopics($this, $topics);
	}
	public function deleteTopics($topics) {
		return PersonalizedStreamsClient::patchTopics($this, $topics);
	}

	/* User Subscriptions */
	public function getSubscriptions($userId) {
		return PersonalizedStreamsClient::getSubscriptions($this, $userId);
	}
	public function addSubscriptions($userId, $topics) {
		return PersonalizedStreamsClient::postSubscriptions($this, $userId, $topics);
	}
	public function updateSubscriptions($userId, $topics) {
		return PersonalizedStreamsClient::putSubscriptions($this, $userId, $topics);
	}
	public function removeSubscriptions($userId, $topics) {
		return PersonalizedStreamsClient::patchSubscriptions($this, $userId, $topics);
	}
	public function getSubscribers($topic, $limit = 100, $offset = 0) {
        return PersonalizedStreamsClient::getSubscribers($this, $topic, $limit, $offset);
    }

    /* Timeline Cursor */
    public function getTopicStreamCursor($topic, $limit = 50, $date = null) {
    	if (is_null($date)) {
    		$date = time();
    	}
    	return CursorFactory::getTopicStreamCursor($this, $topic, $limit, $date);
    }

    public function getPersonalStreamCursor($user, $limit = 50, $date = null) {
    	if (is_null($date)) {
    		$date = time();
    	}
    	return CursorFactory::getPersonalStreamCursor($this, $user, $limit, $date);
    }

	/* Getters */
	public function getUrn() {
		return "urn:livefyre:" . $this->_name;
	}
	public function getNetworkName() {
		return $this->getName();
	}
	public function getUserUrn($user) {
        return $this->getUrn().":user=".$user;
    }
    public function getName() {
    	return $this->_name;
    }
    public function getKey() {
    	return $this->_key;
    }
}
