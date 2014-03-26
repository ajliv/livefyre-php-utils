<?php
namespace Livefyre\Core;

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
		$data = array("actor_token" => $this->buildLfToken(), "pull_profile_url" => $url);
		$response = \Requests::post($url, array(), $data);
		
		return $response->status_code == 204;
	}

	public function syncUser($userId) {
		$data = array("lftoken" => $this->buildLfToken());
		$url = sprintf("http://%s/api/v3_0/user/%s/refresh", $this->_networkName, $userId);

		$response = \Requests::post($url, array(), $data);

		return $response->status_code == 200;
	}

	public function buildLfToken() {
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

		return \JWT::encode($token, $this->_networkKey);
	}

	public function validateLivefyreToken($lfToken) {
		$tokenAttributes = \JWT::decode($lfToken, $this->_networkKey);

		return token_attributes.domain == $this->_network_name
			&& token_attributes.user_id == DEFAULT_USER
			&& token_attributes.expires >= time();
	}

	public function getSite($siteId, $siteKey) {
		return new Site($this->_networkName, $siteId, $siteKey);
	}
}
