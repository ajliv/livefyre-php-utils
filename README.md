# Livefyre PHP Utility Classes
[![PHP version](https://badge.fury.io/ph/Livefyre%2Flivefyre-php-utils.png)](http://badge.fury.io/ph/Livefyre%2Flivefyre-php-utils)

Livefyre's official library for common server-side tasks necessary for getting Livefyre apps (comments, reviews, etc.) working on your website.

Works with PHP5.

## Installation

If using Composer (a highly-recommended PHP dependency manager), add this to your composer.json:

	"require": {
        "livefyre/livefyre-php-utils": "1.0.0"
    }

Otherwise you can clone the repo from http://github.com/livefyre/livefyre-php-utils and copy the project into your application.


## Import

Either add - 
```php
use Livefyre\Livefyre;
```
Or call it explicitly -
```php
Livefyre\Livefyre::getNetwork("networkName", "networkKey");
```

## Usage

Creating tokens:

**Livefyre token:**

```php
$network = Livefyre::getNetwork("networkName", "networkKey");
$network->buildLfToken();
```

**User auth token:**

```php
$network = Livefyre::getNetwork("networkName", "networkKey");
$network->buildUserAuthToken("userId", "displayName", double timeTillExpire);
```

**Collection meta token:**

```php
$network = Livefyre::getNetwork("networkName", "networkKey");
$site = $network->getSite("siteId", "siteKey")
$site->buildCollectionMetaToken("title", "articleId", "url", "tags", "stream");
```

To validate a Livefyre token:

```php
$network = Livefyre::getNetwork("networkName", "networkKey");
$network->validateLivefyreToken("lfToken");
```

To send Livefyre a user sync url and then have Livefyre pull user data from that url:

```php
$network = Livefyre::getNetwork("networkName", "networkKey");
$network->setUserSyncUrl("url");
$network->syncUser("userId");
```
        
To retrieve content collection data as a json object from Livefyre:

```php
$site = Livefyre::getNetwork("networkName", "networkKey")->getSite("siteId", "siteSecret");
content = $site->getCollectionContent("articleId");
```

## Documentation

Located [here](answers.livefyre.com/libraries).

## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request

Note: any feature update on any of Livefyre's libraries will need to be reflected on all libraries. We will try and accommodate when we find a request useful, but please be aware of the time it may take.

## License

MIT