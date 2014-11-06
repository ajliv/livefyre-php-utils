<?php

namespace Livefyre\Validator;


use Livefyre\Model\CollectionData;
use Livefyre\Type\CollectionType;
use Livefyre\Utils\IDNA;

class CollectionValidator {
    public static function validate(CollectionData $data) {
        $IDNA = new IDNA(array('idn_version' => 2008));

        $reason = "";

        $articleId = $data->getArticleId();
        if (empty($articleId)) {
            $reason .= "\n Article id is null or blank.";
        }

        $title = $data->getTitle();
        if (empty($title)) {
            $reason .= "\n Title is null or blank.";
        } elseif (strlen($title) > 255) {
            $reason .= "\n Title is longer than 255 characters.";
        }

        $url = $data->getUrl();
        if (empty($url)) {
            $reason .= "\n URL is null or blank.";
        } elseif (filter_var($IDNA->encode($data->getUrl()), FILTER_VALIDATE_URL) === false) {
            $reason .= "\n URL is not a valid url. see http://www.ietf.org/rfc/rfc2396.txt.";
        }

        $type = $data->getType();
        if (empty($type)) {
            $reason .= "\n Type is null or blank.";
        } elseif (!CollectionType::isValidValue($type)) {
            $reason .= "\n Type is not of a valid type.";
        }

        if (count($reason) > 0) {
            throw new \InvalidArgumentException("Problems with your collection input:" . $reason);
        }

        return $data;
    }
}