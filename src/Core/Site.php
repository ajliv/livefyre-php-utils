<?php
namespace Livefyre\Core;

class Site {
	private $_networkName;
	private $_siteId;
	private $_siteKey;

	public function __construct($networkName, $siteId, $siteKey) {
		$this->_networkName = $networkName;
		$this->_siteId = $siteId;
		$this->_siteKey = $siteKey;
	}

	public function buildCollectionMetaToken($title, $articleId, $url, $tags, $stream = "") {
		if (filter_var($url, FILTER_VALIDATE_URL) === false) {
		    throw new \InvalidArgumentException("provided url is not a valid url");
		}
		if (strlen($title) > 255) {
			throw new \InvalidArgumentException("title length should be under 255 char");
		}

		$token = array(
		    "title" => $title,
		    "url" => $url,
		    "tags" => $tags,
		    "articleId" => $articleId,
		    "type" => $stream
		);

		return \JWT::encode($token, $this->_siteKey);
	}

	public function getCollectionContent($articleId) {
		$url = sprintf("http://bootstrap.%s/bs3/%s/%s/%s/init", $this->_networkName, $this->_networkName, $this->_siteId, base64_encode($articleId));
		$response = \Requests::get($url);

		return json_decode($response->body);
	}
}
