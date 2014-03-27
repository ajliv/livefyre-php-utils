<?php
namespace Livefyre\Core;

use JWT;
use Requests;

/**
 * Object that encapsulates methods that are related to livefyre networks.
 */
class Network {
	const DEFAULT_USER = "system";
	const DEFAULT_EXPIRES = 86400;

	private $_networkName;
	private $_networkKey;

	public function __construct($networkName, $networkKey) {
		$this->_networkName = $networkName;
		$this->_networkKey = $networkKey;
	}

    /**
     * Set the URL that Livefyre will use to fetch user profile info from your user management
     * system. Be sure to set urlTemplate with a working endpoint (see Remote Profiles) before
     * making calls to updateRemoteUser().
     * The registered “urlTemplate” must contain the string "{id}" which will be replaced with
     * the ID of the user that’s being updated.
     * ex. urlTemplate = “http://example.com/users/get_remote_profile?id={id}”
     *
     * @param string $urlTemplate template that Livefyre will use to fetch user profile info. must not be
     *            null
     * @return true if successful
     * @see <a href="http://docs.livefyre.com/developers/user-auth/remote-profiles/#ping-for-pull">documentation</a>
     * @throws InvalidArgumentException if $urlTemplate does not contain {id}
     */
	public function setUserSyncUrl($urlTemplate) {
		if (strpos($urlTemplate, "{id}") === false) {
			throw new \InvalidArgumentException("urlTemplate should contain {id}");
		}

		$url = sprintf("http://%s", $this->_networkName);
		$data = array("actor_token" => $this->buildLfToken(), "pull_profile_url" => $url);
		$response = Requests::post($url, array(), $data);
		
		return $response->status_code == 204;
	}

	/**
     * Pings Livefyre with a user id stored in your user management system, prompting Livefyre to
     * pull the latest user profile data from the customer user management system. See the
     * setUserSyncUrl() method to add your pull URL to Livefyre.
     * 
     * @param string $userId user id for the user
     * @return true if Livefyre was successfully pinged. false otherwise
     * @see <a href="http://docs.livefyre.com/developers/user-auth/remote-profiles/#ping-for-pull">documentation</a>
     */
	public function syncUser($userId) {
		$data = array("lftoken" => $this->buildLfToken());
		$url = sprintf("http://%s/api/v3_0/user/%s/refresh", $this->_networkName, $userId);

		$response = Requests::post($url, array(), $data);

		return $response->status_code == 200;
	}

	/**
     * Creates a Livefyre token. It is needed for interacting with a lot of Livefyre API endpoints.
     *
     * @return a Livefyre token
     */
	public function buildLfToken() {
		return $this->buildUserAuthToken(self::DEFAULT_USER, self::DEFAULT_USER, self::DEFAULT_EXPIRES);
	}

	/**
     * Creates a Livefyre user token. It is recommended that this is called after the user
     * is authenticated.
     *
     * @param string $userId user id for the user. must be alphanumeric
     * @param string $displayName display name for the user
     * @param double $expires seconds until this token is to expire
     * @return String containing the user token
     * @throws InvalidArgumentException if $userId is not alphanumeric
     */
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

	/**
     * Validates a Livefyre token as a valid token for this Network.
     * 
     * @param string $lfToken token to be validated
     * @return true if $lfToken is a valid and current Livefyre token, false otherwise
     */
	public function validateLivefyreToken($lfToken) {
		$tokenAttributes = JWT::decode($lfToken, $this->_networkKey);

		return token_attributes.domain == $this->_network_name
			&& token_attributes.user_id == DEFAULT_USER
			&& token_attributes.expires >= time();
	}

	/**
	 * Returns an instance of a livefyre site object.
	 *
	 * @param string $siteId Livefyre-provided site id
     * @param string $siteKey The Livefyre-provided key for this particular site.
     * @return a Site object
	 */
	public function getSite($siteId, $siteKey) {
		return new Site($this->_networkName, $siteId, $siteKey);
	}
}
