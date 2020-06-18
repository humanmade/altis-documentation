# Automated Code Review Standards

Many of the rules in the ACR ruleset (HM-Required) have a comment that outline what the rule checks however the following will give a high level overview of what is checked.

## Certain directories (and subdirectories) will be excluded and not checked by ACR
- `node_modules`
- `vendor`

## PHP specific checks and best practices
- Require At Least PHP 7.1
- PHP's timezone should not be changed.
- Only longhand php tags (`<?php`) should be used. Do not use shorthand tags (`<?`) or legacy tags ASP-style tags (`<%`, `%>`, `<%=`, script tag `<script language="php">`).
- There is no reference sign on a function call. The reference sign should only be used in function definitions.
- The backtick operator is not allowed as PHP will attempt to execute the contents as a shell command.
- When using `in_array()` specify the strict parameter `bool $strict` is also specified.

## Maintain query best practices and check against slow database queries
- Queries against post meta can be very slow if queries are performed against `meta_value` or `meta_key`. In addition, user `orderby` in a query has poor performance.
- To maintain performance, queries should only return less than or equal to a 100 posts.
- Raw SQL statements should be wrapped in a prepare, `$wpdb->prepare()`.

## Require input/output is checked
- When outputting data from PHP, Use sanitization functions or WordPress output functions.
- Inputs should be both sanitized and validated to ensure the input is expected.

## Protect filesystem's data
- Do not allow using `__FILE__` in menu slugs.

## Use WordPress core functions when possible
- Preferred instead of the PHP alternative.
- Deprecated functions should not be used.
- Certain WordPress functions and constants have alternatives, prefer the better alternative. For example, use `wp_safe_redirect()` instead of `wp_redirect()`. This validates the redirect is in the whitelist (site by default).
- Ensure nonce verification is used when making requests to ensure the request is allowed.

## Deprecated functions should not be used and use alternative WordPress functions and constants if a better alternative exists

## Some PHP functions/operations are not allowed
- `eval()` is not allowed as it allows execution of arbitrary PHP code and thus a security risk.
- `create_function()` should not be used, it is deprecated as of PHP 7.2.
- Do not allow the usage of the `goto` operator, it allows jumping to another section of code which creates for less readable code.

## Cron restrictions
- Only allow cron intervals 15 minutes (default value) and longer.

## File specific checks
- Byte-order mark unicode character encoding is not allowed.
- Check for empty statements; `for`, `switch`, `if`, ect should contain logic.

## Ensure translation functions are used correctly

## Ensure there are no functions that output data which are used during development
- Do not leave `var_dump/print_r/phpinfo`.