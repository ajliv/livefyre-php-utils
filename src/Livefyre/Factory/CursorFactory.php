<?php
namespace Livefyre\Factory;

use Livefyre\Entity\TimelineCursor;

class CursorFactory {
	public static function getTopicStreamCursor($core, $topic, $limit, $date) {
		$resource = $topic->getId() . ":topicStream";
		return new TimelineCursor($core, $resource, $limit, $date);
	}

	public static function getPersonalStreamCursor($network, $user, $limit, $date) {
		$resource = $network->getUserUrn($user) . ":personalStream";
		return new TimelineCursor($network, $resource, $limit, $date);
	}
}
