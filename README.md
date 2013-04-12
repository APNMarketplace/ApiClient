#ApiClient

Given a period separated path, ApiClient makes requests to the [ApnMarketplace API](https://api.apnmarketplace.co.nz/doc/) and returns the resource as a stdClass.

## Installation

1. Add ``apnmarketplace/apiclient`` as a dependency in your project's ``composer.json`` file:

        {
            "require": {
                "apnmarketplace/apiclient": "dev-master"
            }
        }

2. Install your dependencies using [composer](http://getcomposer.org):

        php composer.phar install

3. Require Composer's autoloader

    Composer also prepares an autoload file that's capable of autoloading all of the classes in any of the libraries that it downloads. To use it, just add the following line to your code's bootstrap process:

        $loader = require 'vendor/autoload.php';
        $loader->add('YourNamespace', '../your/path/'); // autoload other namepaces

## Usage

A client capable of making signed requests is required. A [Guzzle](https://github.com/guzzle/guzzle) based client is provided with the library, but may be replaced by any client implementing ``ApnMarketplace\ApiClient\Client\ClientInterface``.

    $loader = require_once '../vendor/autoload.php';

    use ApnMarketplace\ApiClient\App;
    use ApnMarketplace\ApiClient\Client\Guzzle\Client;
    use ApnMarketplace\ApiClient\Client\Guzzle\ApnMarketplacePlugin;
    use ApnMarketplace\ApiClient\Client\Guzzle\Session;

    $client = new Client();
    $client->addSubscriber(new ApnMarketplacePlugin('client_id', 'client_secret', 'Fri, 12 Apr 2013 09:40:29 +1200', new Session()));

    $api = new App($client);

    // GET request
    $resource = $api->get('search.all');

    // POST request
    $resource = $api->post('https://api.apnmarketplace.co.nz/foo', 'foo=bar&fiz=buz');

Guzzle is capable of transparent HTTP caching using a cache adapter for either Zend or Doctrine. See the [Guzzle docs](http://guzzlephp.org/guide/http/caching.html) for more info.

    use Doctrine\Common\Cache\ArrayCache;
    use Guzzle\Cache\DoctrineCacheAdapter;
    use Guzzle\Plugin\Cache\CachePlugin;

    $adapter = new DoctrineCacheAdapter(new ArrayCache());
    $cache = new CachePlugin($adapter, true);
    $client->addSubscriber($cache);