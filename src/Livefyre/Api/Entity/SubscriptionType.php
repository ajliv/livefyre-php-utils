<?php
namespace Livefyre\Api\Entity;

use Livefyre\Utils\BasicEnum;

abstract class SubscriptionType extends BasicEnum {
    const personalStream = 1;
}
