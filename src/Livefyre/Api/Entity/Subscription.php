<?php
namespace Livefyre\Api\Entity;

use Livefyre\Api\Entity\SubscriptionType;

class Subscription {

	private $to;
	private $by;
	private $type;
	private $createdAt;

	public function __construct($to, $by, $type, $createdAt = null) {
		$this->by = $by;
		$this->type = $type;
		$this->to = $to;
		$this->createdAt = $createdAt;
	}

    public function jsonSerialize() {
    	return array_filter(get_object_vars($this));
	}

	public function getTo() {
		return $this->to;
	}
	public function setTo($to) {
		$this->to = $to;
	}
	public function getBy() {
		return $this->by;
	}
	public function setBy($by) {
		$this->by = $by;
	}
	public function getType() {
		return $this->type;
	}
	public function setType($type) {
		$this->type = $type;
	}
	public function getCreatedAt() {
		return $this->createdAt;
	}
	public function setCreatedAt($createdAt) {
		$this->createdAt = $createdAt;
	}
}
