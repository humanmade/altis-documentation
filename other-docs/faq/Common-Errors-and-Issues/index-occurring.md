# ElasticSearch Index: Error: An index is already occurring. Try again later.

If you receive the error `ElasticSearch Index: Error: An index is already occurring. Try again later. ` when performing an
ElasticSearch reindex, it's possible the indexing flag for an in-progress index has got stuck. The resulting error will occur:

```
Error: An index is already occurring. Try again later.
```

In such a case, you can clear the flag by running the following:

```
wp elasticpress clear-sync 
```

Alternatively, if the above doesn't resolve the issue, it can also be stuck
in a [WordPress transient](https://developer.wordpress.org/apis/transients/). If so, you can try the following:

```
wp transient delete ep_wpcli_sync --network
```

Occasionally this value can also be stored in the object cache. Flushing the object cache should be performed with caution when
working on live environments. You can flush your object cache via a [new CLI Session](docs://cloud/dashboard/cli/). Run the
following:

```bash
wp cache flush
```

**Note that flushing the object cache can have a significant impact on performance, so we recommend you only do this when necessary.
** 
