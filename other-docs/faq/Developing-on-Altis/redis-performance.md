# Optimising Object Cache performance at scale

Altis runs in a distributed, scalable architecture, allowing us to scale WordPress applications to any size. Solving one scaling problem can sometimes introduce new challenges further on, and so at large scale developers need to start thinking about Network IO as a potential bottleneck.

Primarily, managing the size of objects in a remote object cache can become critical as scaling network bandwidth isn't always possible.

## What does this mean?

A properly designed-for-scale system will leverage every available caching layer to keep processing down. The primary way of caching calls from the Database is using an Object Cache technology like [Redis](https://redis.io/) or [Valkey.](https://aws.amazon.com/elasticache/what-is-valkey/) Altis environments are currently backed by Redis or Valkey, (and further backed by [Afterburner](https://docs.altis-dxp.com/cloud/afterburner/)).

These technologies are very fast and, at scale, push a lot of bandwidth through network interfaces. If a large option is stored in the database (something like WordPress’ `alloptions` option has been seen to grow too large; `notoptions` can sometimes keep appending data without ever getting cleared, potentially infinitely inflating), this will get cached by the object cache. A key/value pair of just 3 MB, at scale, can saturate a network interface, causing packets to get queued, retried (further exacerbating the problem), and eventually dropped.

## Where to start?

Profiling your application is the only way to unblock this performance bottleneck. Replicating this kind of problem can be difficult in a local environment. Even non-production live environments may not exhibit problems. With the Altis CLI, available in the [Altis Dashboard](https://dashboard.altis-dxp.com/), is where you're best placed to start debugging your application.

[`wp shell`](https://developer.wordpress.org/cli/commands/shell/) will allow you to start interacting with a bootstrapped WordPress install. WordPress core and plugins are loaded, the object cache is initialised, and you can execute arbitrary PHP in that context. This makes `wp shell` an ideal tool for inspecting Redis behaviour and profiling cache usage.

## Inspecting the Object Cache in `wp shell`

Start by opening a shell on the environment you want to profile:

```bash
wp shell
```

Inside the shell, you can access the global object cache:

```php
global $wp_object_cache;
```

The important property here is:

```php
$wp_object_cache->cache;
```

This is an array of everything WordPress has loaded into the object cache for the current request, grouped by cache “group” (options, posts, notoptions, etc).

### Estimating Cache Size (Network Cost per Request)

PHP can’t directly tell you “how much memory this array uses”, but we can approximate its size by serialising it and checking the string length. That’s usually close enough to understand Redis → PHP network overhead.

Total object cache size on bootstrap:

```php
global $wp_object_cache;

$size_bytes = strlen( serialize( $wp_object_cache->cache ) );
$size_kb    = $size_bytes / 1024;
$size_mb    = $size_kb / 1024;

$size_mb;
```

This gives you a rough “how big is the cache payload this request pulled from Redis?” figure.

```php
$wp_object_cache->stats()
```

Gives the cache hits, and cache misses. Also prints every cached group, key and the data.

You can also inspect specific groups:

```php
// Size of options cache
strlen( serialize( $wp_object_cache->cache['options'] ?? [] ) );

// Size of notoptions (missing options cache)
strlen( serialize( $wp_object_cache->cache['options']['notoptions'] ?? [] ) );
```

Use this to identify “big offenders” like:

* Extremely large alloptions
* A bloated notoptions map
* Groups that are much larger than expected

### Profiling Custom Actions & Cavalcade Hooks

The above will catch most issues; but the problem might be in some action or cron job, not loaded in the WordPress bootstrap. This is where you will need to start getting creative. You can run things like `do_action( 'my_custom_hook', $arg1, $arg2 )` and afterwards re-run `$after_size = strlen( serialize( $wp_object_cache->stats() ) );` to review the key sizes. This applies to any function call in your application.

Altis support might be able to assist you in identifiyng windows in which network is more saturated than at other times, so please reachout for assistance; though Altis support will not be able to profile memory consumption or network saturation directly, we may be able to offer insights from the infrastructure.
