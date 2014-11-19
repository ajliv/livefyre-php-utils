# Livefyre PHP Utility Classes
[![PHP version](https://badge.fury.io/ph/Livefyre%2Flivefyre-php-utils.png)](http://badge.fury.io/ph/Livefyre%2Flivefyre-php-utils)
[![Circle CI](https://circleci.com/gh/Livefyre/livefyre-php-utils.png?style=badge)](https://circleci.com/gh/Livefyre/livefyre-php-utils)
[![Coverage Status](https://coveralls.io/repos/Livefyre/livefyre-php-utils/badge.png)](https://coveralls.io/r/Livefyre/livefyre-php-utils)

Livefyre's official library for common server-side tasks necessary for getting Livefyre apps (comments, reviews, etc.) working on your website.

Works with PHP 5.3+.

## Installation with Composer

You can install the library via Composer[http://getcomposer.org/]. Add this to your +composer.json+:

    {
      "require": {
        "livefyre/livefyre-php-utils": "2.*"
      }
    }

Then install via:

    composer.phar install

To use the library, either user Composer's autoload[https://getcomposer.org/doc/00-intro.md#autoloading]:

    require_once('vendor/autoload.php');

Or manually:

    require_once('/path/to/vendor/livefyre/livefyre-php-utils/src/Livefyre.php');

## Installation without Composer

Obtain the latest version of the Livefyre PHP library with:

	git clone https://github.com/Livefyre/livefyre-php-utils

To use the library, add the following to your PHP script:

	require_once("/path/to/livefyre-php-utils/src/Livefyre.php");

## Documentation

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
