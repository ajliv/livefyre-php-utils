<?php

namespace Livefyre\Type;


use Livefyre\Utils\BasicEnum;

abstract class CollectionType extends BasicEnum {
    const REVIEWS = "reviews";
    const SIDENOTES = "sidenotes";
    const RATINGS = "ratings";
    const COUNTING = "counting";
    const LIVEBLOG = "liveblog";
    const LIVECHAT = "livechat";
    const LIVECOMMENTS = "livecomments";
}
