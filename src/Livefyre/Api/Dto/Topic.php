<?php
namespace Livefyre\Api\Dto;

class Topic {

	private $_id;
	private $_label;
	private $_createdAt;
	private $_modifiedAt;

	public function __construct($id, $label) {
		$this->_id = $id;
		$this->_label = $label;
	}

	public function __construct($id, $label, $createdAt, $modifiedAt) {
		$this->_id = $id;
		$this->_label = $label;
		$this->_createdAt = $createdAt;
		$this->_modifiedAt = $modifiedAt;
	}

	public function getId() {
		return $this->_id;
	}

	public function setId($id) {
		$this->_id = $id;
	}

	public function getLabel() {
		return $this->_label;
	}

	public function setLabel($label) {
		$this->_label = $label;
	}

	public function getCreatedAt() {
		return $this->_createdAt;
	}

	public function setCreatedAt($createdAt) {
		$this->_createdAt = $createdAt;
	}

	public function getModifiedAt() {
		return $this->_modifiedAt;
	}

	public function setModifiedAt($modifiedAt) {
		$this->_modifiedAt = $modifiedAt;
	}
}