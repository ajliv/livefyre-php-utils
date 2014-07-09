<?php
namespace Livefyre\Api\Entity;

use Livefyre\Api\PersonalizedStreamsClient;

class TimelineCursor {

	const DATE_FORMAT = "Y-m-d\TH:i:s.z\Z";

	private $_core;
	private $_resource;
	private $_currentTime;
	private $_next = FALSE;
	private $_previous = FALSE;
	private $_limit;

	public function __construct($core, $resource, $limit, $startTime) {
		$this->_core = $core;
		$this->_resource = $resource;
		$this->_limit = $limit;
		$this->_currentTime = $startTime;
	}

	public function next($limit = null) {
		$limit = (is_null($limit)) ? $this->_limit : $limit;

		$time = gmdate(self::DATE_FORMAT, $this->_currentTime);
		$data = PersonalizedStreamsClient::getTimelineStream($this->_core, $this->_resource, $limit, null, $time);
		$cursor = $data->{"meta"}->{"cursor"};
		
		$this->_next = $cursor->{"hasNext"};
		$this->_previous = $cursor->{"next"} !== null;

		$this->_currentTime = $this->_previous ? date("U", strtotime($cursor->{"prev"})) : $this->_currentTime;

		return $data;
	}

	public function previous($limit = null) {
		$limit = (is_null($limit)) ? $this->_limit : $limit;

		$time = gmdate(self::DATE_FORMAT, $this->_currentTime);
		$data = PersonalizedStreamsClient::getTimelineStream($this->_core, $this->_resource, $limit, $time, null);
		
		$cursor = $data->{"meta"}->{"cursor"};
		
		$this->_previous = $cursor->{"hasPrev"};
		$this->_next = $cursor->{"prev"} !== null;

		$this->_currentTime = $this->_next ? date("U", strtotime($cursor->{"prev"})) : $this->_currentTime;

		return $data;
	}

	public function getResource() {
		return $this->_resource;
	}
	public function getCursorTime() {
		return $this->_currentTime;
	}
	public function setCursorTime($newTime) {
		$this->_currentTime = $newTime;
	}
	public function hasPrevious() {
		return $this->_previous;
	}
	public function hasNext() {
		return $this->_next;
	}
	public function getLimit() {
		return $this->_limit;
	}
	public function setLimit($limit) {
		$this->_limit = $limit;
	}
}