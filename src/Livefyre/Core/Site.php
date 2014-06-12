<?php
namespace Livefyre\Core;

use Livefyre\Utils\JWT;
use Livefyre\Utils\IDNA;
use Livefyre\Api\PersonalizedStreams;
use Requests;

class Site {
	private $_network;
	private $_siteId;
	private $_siteKey;
	private $_IDNA;

	private static $TYPE = array(
		"reviews", "sidenotes", "ratings", "counting", "liveblog", "livechat", "livecomments");

	public function __construct($network, $siteId, $siteKey) {
		$this->_network = $network;
		$this->_siteId = $siteId;
		$this->_siteKey = $siteKey;
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

		return JWT::encode($collectionMeta, $this->_siteKey);
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
		$url = sprintf("http://bootstrap.%s/bs3/%s/%s/%s/init", $this->_network->_networkName, $this->_network->_networkName, $this->_siteId, base64_encode($articleId));
		$response = Requests::get($url);

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

	public function getCollectionTopics($collectionId) {
		return PersonalizedStreams::getCollectionTopics($this, $collectionId);
	}
	public function addCollectionTopics($collectionId, $topics) {
		return PersonalizedStreams::addCollectionTopics($this, $collectionId, $topics);
	}
	public function updateCollectionTopics($collectionId, $topics) {
		return PersonalizedStreams::updateCollectionTopics($this, $collectionId, $topics);
	}
	public function deleteCollectionTopics($collectionId, $topics) {
		return PersonalizedStreams::deleteCollectionTopics($this, $collectionId, $topics);
	}

	/* Getters */
	public function getId() {
		return $this->_siteId;
	}

	public function getNetwork() {
		return $this->_network;
	}

	public function getNetworkName() {
		return $this->getNetwork()->getName();
	}

	public function buildLivefyreToken() {
		return $this->getNetwork()->buildLivefyreToken();
	}
}
