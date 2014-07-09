<?php
namespace Livefyre\Core;

use Livefyre\Utils\JWT;
use Livefyre\Utils\IDNA;
use Livefyre\Routing\Client;
use Livefyre\Api\PersonalizedStreamsClient;
use Livefyre\Api\Entity\Topic;

class Site {
	private $_network;
	private $_id;
	private $_key;
	private $_IDNA;

	private static $TYPE = array(
		"reviews", "sidenotes", "ratings", "counting", "liveblog", "livechat", "livecomments");

	public function __construct($network, $id, $key) {
		$this->_network = $network;
		$this->_id = $id;
		$this->_key = $key;
		$this->_IDNA = new IDNA(array('idn_version' => 2008));
	}

	public function buildCollectionMetaToken($title, $articleId, $url, $options = array()) {
		if (filter_var($this->_IDNA->encode($url), FILTER_VALIDATE_URL) === false) {
			throw new \InvalidArgumentException("provided url is not a valid url");
		}
		if (strlen($title) > 255) {
			throw new \InvalidArgumentException("title length should be under 255 char");
		}

		$collectionMeta = array(
		    "url" => $url,
		    "title" => $title,
		    "articleId" => $articleId
		);

		if (array_key_exists("type", $options) AND !in_array($options["type"], self::$TYPE)) {
			throw new \InvalidArgumentException("type is not a recognized type. must be in " . implode(",", self::$TYPE));
		}

		return JWT::encode(array_merge($collectionMeta, $options), $this->_key);
	}

	public function buildChecksum($title, $url, $tags = "") {
		if (filter_var($this->_IDNA->encode($url), FILTER_VALIDATE_URL) === false) {
			throw new \InvalidArgumentException("provided url is not a valid url");
		}
		if (strlen($title) > 255) {
			throw new \InvalidArgumentException("title length should be under 255 char");
		}

		$checksum = array("tags" => $tags, "title" => $title, "url" => $url);
		return md5(json_encode($checksum, JSON_UNESCAPED_SLASHES));
	}

	public function getCollectionContent($articleId) {
		$url = sprintf("http://bootstrap.%s/bs3/%s/%s/%s/init", $this->_network->_networkName, $this->_network->_networkName, $this->_id, base64_encode($articleId));
		$response = Client::GET($url);

		return json_decode($response->body);
	}

	public function getCollectionId($articleId) {
		$content = $this->getCollectionContent($articleId);
		return $content->{"collectionSettings"}->{"collectionId"};
	}

	/* Topics */
	public function getTopic($id) {
		return PersonalizedStreamsClient::getTopic($this, $id);
	}
	public function createOrUpdateTopic($id, $label) {
		$topic = Topic::generate($this, $id, $label);

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
		    array_push($topics, Topic::generate($this, $id, $label));
		}
		return PersonalizedStreamsClient::postTopics($this, $topics);
	}
	public function deleteTopics($topics) {
		return PersonalizedStreamsClient::patchTopics($this, $topics);
	}

	public function getCollectionTopics($collectionId) {
		return PersonalizedStreamsClient::getCollectionTopics($this, $collectionId);
	}
	public function addCollectionTopics($collectionId, $topics) {
		return PersonalizedStreamsClient::postCollectionTopics($this, $collectionId, $topics);
	}
	public function updateCollectionTopics($collectionId, $topics) {
		return PersonalizedStreamsClient::putCollectionTopics($this, $collectionId, $topics);
	}
	public function removeCollectionTopics($collectionId, $topics) {
		return PersonalizedStreamsClient::patchCollectionTopics($this, $collectionId, $topics);
	}

	/* Timeline Cursor */
    public function getTopicStreamCursor($topic, $limit = 50, $date = null) {
    	if (is_null($date)) {
    		$date = time();
    	}
    	return CursorFactory::getTopicStreamCursor($this, $topic, $limit, $date);
    }

	/* Getters */
	public function getUrn() {
		return $this->_network->getUrn() . ":site=" . $this->_id;
	}
	public function getNetworkName() {
		return $this->_network->getNetworkName();
	}
	public function buildLivefyreToken() {
		return $this->getNetwork()->buildLivefyreToken();
	}
	public function getNetwork() {
		return $this->_network;
	}
	public function getId() {
		return $this->_id;
	}
	public function getKey() {
		return $this->_key;
	}
}
