# Livefyre PHP Utility Classes
[![PHP version](https://badge.fury.io/ph/Livefyre%2Flivefyre-php-utils.png)](http://badge.fury.io/ph/Livefyre%2Flivefyre-php-utils)

Livefyre's official library for common server-side tasks necessary for getting Livefyre apps (comments, reviews, etc.) working on your website.

Works with PHP5.

## Installation

If using Composer, add this to your composer.json:

	"require": {
        "livefyre/livefyre-php-utils": "1.3.2"
    }

## Usage

Instantiating a network object:

```php
$network = Livefyre::getNetwork("networkName", "networkKey");
```

Building a Livefyre token:

```php
$network->buildLivefyreToken();
```

Building a user auth token:

```php
$network->buildUserAuthToken("userId", "displayName", expires);
```

To validate a Livefyre token:

```php
$network->validateLivefyreToken("lfToken");
```

To send Livefyre a user sync url and then have Livefyre pull user data from that url:

```php
$network->setUserSyncUrl("urlTemplate");
$network->syncUser("userId");
```

Instantiating a site object:

```php
$site = $network->getSite("siteId", "siteKey");
```

Building a collection meta token:
*The {options} argument is optional.*

```php
$site->buildCollectionMetaToken("title", "articleId", "url", {options});
```

Building a checksum:
*The 'tags' argument is optional.*

```php
$site->buildChecksum("title", "url", "tags");
```

To retrieve content collection data:

```php
$site->getCollectionContent("articleId");
```

To get a content collection's id:

```php
$site->getCollectionId("articleId");
```

## Additional Documentation

Located [here](http://answers.livefyre.com/developers/libraries).

## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request

Note: any feature update on any of Livefyre's libraries will need to be reflected on all libraries. We will try and accommodate when we find a request useful, but please be aware of the time it may take.

## License

MIT
