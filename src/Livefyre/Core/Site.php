<?php
namespace Livefyre\Core;

use JWT;
use Requests;

/**
 * Object that encapsulates methods that are related to livefyre sites.
 */
class Site {
	private $_networkName;
	private $_siteId;
	private $_siteKey;

	public function __construct($networkName, $siteId, $siteKey) {
		$this->_networkName = $networkName;
		$this->_siteId = $siteId;
		$this->_siteKey = $siteKey;
	}

	/**
	 * Creates a Livefyre collection meta token. Pass this token to any page on your site that displays a Livefyre app 
	 * (comment, blog, reviews, etc.). In particular, Livefyre uses the token to instantiate a new collection on your pages.
	 * If the collection exists already, Livefyre will update the collection with the latest values in the token.
	 * 
	 * @param string $title title for collection. cannot be longer than 255 characters. should be html-encoded
	 * @param string $articleId article id for collection
	 * @param string $url url for collection. must be valid domain and start with a valid scheme (http:// or https://)
	 * @param string $tags tags for collection
	 * @param string $stream stream string for collection (only NONE or reviews are acceptable at this point)
	 * @return String containing the collection meta token
     * @throws InvalidArgumentException if $url is not a properly formed url
     * @throws InvalidArgumentException if $title is longer than 255 char
	 */
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

		return JWT::encode($token, $this->_siteKey);
	}

	/**
	 * Gets collection content (SEO) for this Site. siteKey must be set before calling this method.
	 * Get user generated content for a pre-existing collection. Returns user generated content (UGC) as an HTML fragment.
	 * Customers can embed the UGC on the page that’s returned in the initial response so crawlers can index the UGC
	 * content without executing javascript.
	 * 
	 * Note, only use getContent if you want to make UGC available to crawlers that don’t execute javascript.
	 * livefyre.js handles displaying collection content on article pages otherwise.
	 * 
	 * GET http://bootstrap.{network}/bs3/{network}/{siteId}/{b64articleId}/init
	 * 
	 * @param int $articleId articleId for the content to be retrieved
	 * @return php variable containing collection content represented as JSON
	 * @see <a href="http://docs.livefyre.com/developers/reference/http-reference/#section-22">documentation</a>
	 */
	public function getCollectionContent($articleId) {
		$url = sprintf("http://bootstrap.%s/bs3/%s/%s/%s/init", $this->_networkName, $this->_networkName, $this->_siteId, base64_encode($articleId));
		$response = Requests::get($url);

		return json_decode($response->body);
	}
}
