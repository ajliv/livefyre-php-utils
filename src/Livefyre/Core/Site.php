<?php
namespace Livefyre\Core;

use Livefyre\Utils\JWT;
use Livefyre\Utils\IDNA;
use Livefyre\Routing\Client;
use Livefyre\Api\PersonalizedStreams;
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

	public function buildCollectionMetaToken($title, $articleId, $url, $tags = "", $type = null) {
		if (filter_var($this->_IDNA->encode($url), FILTER_VALIDATE_URL) === false) {
			throw new \InvalidArgumentException("provided url is not a valid url");
		}
		if (strlen($title) > 255) {
			throw new \InvalidArgumentException("title length should be under 255 char");
		}

		$collectionMeta = array(
		    "url" => $url,
		    "tags" => $tags,
		    "title" => $title,
		    "articleId" => $articleId
		);

		if (!empty($type)) {
			if (in_array($type, self::$TYPE)) {
				$collectionMeta["type"] = $type;
			} else {
				throw new \InvalidArgumentException("type is not a recognized type. must be liveblog, livechat, livecomments, reviews, sidenotes, or an empty string");
			}
		}

		return JWT::encode($collectionMeta, $this->_key);
	}

	public function buildChecksum($title, $url, $tags = "") {
		if (filter_var($this->_IDNA->encode($url), FILTER_VALIDATE_URL) === false) {
			throw new \InvalidArgumentException("provided url is not a valid url");
		}
		if (strlen($title) > 255) {
			throw new \InvalidArgumentException("title length should be under 255 char");
		}

		$metaString = sprintf('{"url":"%s","tags":"%s","title":"%s"}', $url, $tags, $title);
		return md5($metaString);
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
		return PersonalizedStreams::getTopic($this, $id);
	}
	public function createOrUpdateTopic($id, $label) {
		$topic = Topic::generate($this, $id, $label);

		return PersonalizedStreams::postTopic($this, $topic);
	}
	public function deleteTopic($topic) {
		return PersonalizedStreams::patchTopic($this, $topic);
	}

	public function getTopics($limit = 100, $offset = 0) {
		return PersonalizedStreams::getTopics($this, $limit, $offset);
	}
	public function createOrUpdateTopics($topicMap) {
		$topics = array();
		foreach ($topicMap as $id => $label) {
		    array_push($topics, Topic::generate($this, $id, $label));
		}
		return PersonalizedStreams::postTopics($this, $topics);
	}
	public function deleteTopics($topics) {
		return PersonalizedStreams::patchTopics($this, $topics);
	}

	public function getCollectionTopics($collectionId) {
		return PersonalizedStreams::getCollectionTopics($this, $collectionId);
	}
	public function addCollectionTopics($collectionId, $topics) {
		return PersonalizedStreams::postCollectionTopics($this, $collectionId, $topics);
	}
	public function updateCollectionTopics($collectionId, $topics) {
		return PersonalizedStreams::putCollectionTopics($this, $collectionId, $topics);
	}
	public function removeCollectionTopics($collectionId, $topics) {
		return PersonalizedStreams::patchCollectionTopics($this, $collectionId, $topics);
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
