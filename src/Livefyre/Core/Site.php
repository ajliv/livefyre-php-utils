<?php
namespace Livefyre\Core;

use Livefyre\Utils\JWT;
use Livefyre\Utils\IDNA;
use Livefyre\Routing\Client;
use Livefyre\Api\Domain;

class Site {
	private $_network;
    private $_data;

	private static $TYPE = array(
		"reviews", "sidenotes", "ratings", "counting", "liveblog", "livechat", "livecomments");

	public function __construct($network, $id, $key) {
		$this->_network = $network;
		$this->_id = $id;
		$this->_key = $key;
	}
}
