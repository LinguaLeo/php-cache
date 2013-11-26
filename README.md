php-cache
=========
**PHP-Cache** is a library designed to simplify your work with the different cache storage engines. Currently library is under heavy development so API can change in the future.

Example using Redis:

```php
$redis = new \Redis();
$redis->connect('127.0.0.1');
$cache = new RedisCache($redis);

$cache->set('test', 'data'); //write 'data' by key 'test'
$cache->get('test'); //returns 'data'
```

Also atomic CAS-like operations supported (**create** and **update**). For example:

```php
$modifier = function (&$data) {
    $data = 'value';
};
$result = $this->cache->create('test', $modifier);
```

**Data** represents the reference to information that storage already contains. You can modify or completly replace it inside the callable.

If other client will change data before you call **create** you will get an **AtomicViolationException**. In your client code you can catch this exception and retry operation.

The main difference betweeen **create** and **update** methods is that **update** will do nothing in case storage does not contain specified key.

### Decorators
Currently library provides only one additional **HotCacheDecorator** which is very useful in a highloaded environment: it simply stores (and modifies) all cached data in a in-memory array. So if you call **get** once then all subsequent calls will be served only by hot cache without any requests to storage server.

To enable hot cache just wrap your creation of any **CacheProvider** object with the decorator:

```php
$redis = new \Redis();
$redis->connect('127.0.0.1');
$cache = new HotCacheDecorator(new RedisCache($redis));
```

### Cache key generation
Library provides simple mechanism to generate cache keys with **generateCacheKey** method of **CacheProvider**:
```php
$key = CacheProvider::generateCacheKey('arg1', 'arg2'); //cache:arg1:arg2
```
You can pass any number (but > 0) of arguments to this method to get a cache key. Also as the first parameter you can pass class name and if this class contains constant field **VERSION** then this constant will be mixed up with other arguments of **generateCacheKey**:

```php
class Test {
    const VERSION = 2;
}
...
$key = CacheProvider::generateCacheKey('Test', 'arg1', 'arg2'); //cache:Test:arg1:arg2:2
```
This behavior is very useful when you work with ORM models and want to cache different versions of objects without need to drop all cache data after making some changes in a small single model.