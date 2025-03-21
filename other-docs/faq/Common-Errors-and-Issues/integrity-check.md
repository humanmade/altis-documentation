#  Resolve SRI check failures 

Errors such as:

```
Failed to find a valid the 'integrity' attribute for resource...
```

Is a browser security error. The reason that these problems occur is due to a hash mismatch due to the SRI functionality within the [Altis Security module][https://github.com/humanmade/altis-security].
 
When Altis outputs scripts and styles onto the page, it includes a hash for the SRI, in the integrity attribute. This hash is generated within PHP by reading the file from disk, applying hashing algorithms, and then storing it in the object cache keyed against the file path and the version specified in the code. (In other words, it calls `wp_cache_set( '/content/your/file.css?1.2.3.4', 'sha384-...', 'altis_integrity' )`)

In addition, static files are cached by the CDN, where the file is cached for one year using the path and the query string as the cache key. 

In cases where the contents of a file are updated, the version within the 
codebase needs to be changed in order to invalidate both of these caches. If 
the version isn't updated, the CDN will continue to serve a stale version of 
the file (as the CDN's cache key won't have changed), and the backend 
will also continue to serve the old hash (as it pulls it from the cache). 
This causes the file to continue to be served correctly but without the latest updates.

If the CDN and the backend cache become out of sync, this can cause the file 
to not be loaded. This occurs only in the edge case where the file is 
updated without changing the version number, and either the object cache 
entry is evicted or cleared or the CDN cache entry is evicted. Either of 
these will cause the file contents as seen by the CDN and the backend to not
match, and the hashes to mismatch. (It's also possible for your browser to 
cache this, however, this only causes issues when you're doing things like 
CDN invalidation.)


For this reason, it's important that whenever the file changes, the version 
number must be changed too. This ensures that this edge case cannot be 
hit as well as making sure that the correct content is being served to users.

Using timestamps for the versions should be OK, but it's possible that this 
may cause problems with the multi-server architecture of Altis. Because 
there are multiple servers serving your files, the timestamps for each file 
could mismatch; this shouldn't normally cause an issue.

Our advice is to generate the version string within the build process: 
https://docs.altis-dxp.com/cloud/static-file-caching/

Read more about integrity checking:

https://docs.altis-dxp.com/security/browser/#subresource-integrity

https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity


Further steps on debugging:

https://docs.altis-dxp.com/guides/getting-help-with-altis/debugging/
