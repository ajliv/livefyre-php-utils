<?php
namespace Livefyre\Api\Entity;

class Topic {

    const TOPIC_IDEN = ":topic=";
	private $id;
	private $label;
	private $createdAt;
	private $modifiedAt;

	public function __construct() { }

	/* new instances should use this method */
	public static function generate($obj, $id, $label) {
		return self::copy(self::generateUrn($obj, $id), $label);
	}

	public static function copy($id, $label, $createdAt = null, $modifiedAt = null) {
		$instance = new self();
    	$instance->setId($id);
    	$instance->setLabel($label);
    	$instance->setCreatedAt($createdAt);
    	$instance->setModifiedAt($modifiedAt);
    	return $instance;
	}

    public static function generateUrn($obj, $id) {
        return $obj->getUrn() . self::TOPIC_IDEN . $id;
    }

    public function getTruncatedId() {
    	$id = $this->id;
    	return substr($id, strrpos($id, "=") + strlen(self::TOPIC_IDEN));
    }
    public function getCreatedAtDate() {
    	return date('r', $this->createdAt);
    }
	public function getModifiedAtDate() {
    	return date('r', $this->modifiedAt);
    }
    public function jsonSerialize() {
    	return array_filter(get_object_vars($this));
	}

	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
	}
	public function getLabel() {
		return $this->label;
	}
	public function setLabel($label) {
		$this->label = $label;
	}
	public function getCreatedAt() {
		return $this->createdAt;
	}
	public function setCreatedAt($createdAt) {
		$this->createdAt = $createdAt;
	}
	public function getModifiedAt() {
		return $this->modifiedAt;
	}
	public function setModifiedAt($modifiedAt) {
		$this->modifiedAt = $modifiedAt;
	}
}
