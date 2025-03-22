#  Is it possible to set CORS headers on the S3 bucket? 

No. Assets stored in S3 are served via the CDN (CloudFront). There is no mechanism to set custom headers at all when S3 objects are served via CloudFront.