<?php
namespace Livefyre\Core;

use Livefyre\Utils\JWT;
use Livefyre\Api\PersonalizedStreams;
use Requests;

class Network {
	const DEFAULT_USER = "system";
	const DEFAULT_EXPIRES = 86400;

	private $_networkName;
	private $_networkKey;

	public function __construct($networkName, $networkKey) {
		$this->_networkName = $networkName;
		$this->_networkKey = $networkKey;
	}

	public function setUserSyncUrl($urlTemplate) {
		if (strpos($urlTemplate, "{id}") === false) {
			throw new \InvalidArgumentException("urlTemplate should contain {id}");
		}

		$url = sprintf("http://%s", $this->_networkName);
		$data = array("actor_token" => $this->buildLivefyreToken(), "pull_profile_url" => $urlTemplate);
		$response = Requests::post($url, array(), $data);
		
		return $response->status_code == 204;
	}

	public function syncUser($userId) {
		$data = array("lftoken" => $this->buildLivefyreToken());
		$url = sprintf("http://%s/api/v3_0/user/%s/refresh", $this->_networkName, $userId);

		$response = Requests::post($url, array(), $data);
		
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
		    "domain" => $this->_networkName,
		    "user_id" => $userId,
		    "display_name" => $displayName,
		    "expires" => time() + $expires
		);

		return JWT::encode($token, $this->_networkKey);
	}

	public function validateLivefyreToken($lfToken) {
		$tokenAttributes = JWT::decode($lfToken, $this->_networkKey);

		return $tokenAttributes->domain == $this->_networkName
			&& $tokenAttributes->user_id == self::DEFAULT_USER
			&& $tokenAttributes->expires >= time();
	}

	/* Topics */
	public function getTopic($id) {
		return PersonalizedStreams::getTopic($this, $id);
	}
	public function addOrUpdateTopic($topic) {
		return PersonalizedStreams::addOrUpdateTopic($this, $topic);
	}
	public function deleteTopic($id) {
		return PersonalizedStreams::deleteTopic($this, $id);
	}

	public function getTopics($limit = 100, $offset = 0) {
		return PersonalizedStreams::getTopics($this, $limit, $offset);
	}
	public function addOrUpdateTopics($topics) {
		return PersonalizedStreams::addOrUpdateTopics($this, $topics);
	}
	public function deleteTopics($ids) {
		return PersonalizedStreams::deleteTopics($this, $ids);
	}

	/* User Subscriptions */
	public static function getUserSubscriptions($userId) {
		return PersonalizedStreams::getUserSubscriptions($this, $userId);
	}
	public static function addUserSubscriptions($userId, $topics) {
		return PersonalizedStreams::addUserSubscriptions($this, $userId, $topics);
	}
	public static function updateUserSubscriptions($userId, $topics) {
		return PersonalizedStreams::updateUserSubscriptions($this, $userId, $topics);
	}
	public static function deleteUserSubscriptions($userId, $topics) {
		return PersonalizedStreams::deleteUserSubscriptions($this, $userId, $topics);
	}

	/* Getters */
	public function getSite($siteId, $siteKey) {
		return new Site($this, $siteId, $siteKey);
	}

	public function getName() {
		return $this->_networkName;
	}
}
