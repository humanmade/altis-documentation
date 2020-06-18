# Coding Standards

All code deployed on the Altis platform must pass a minimum set of coding standards. These standards ensure that your code is performant, secure, and stable by checking for known issues.

Each standard has one or more corresponding sniff codes, which are also shown in the ACR output. These sniffs can be used to [ignore the error](README.md#ignore) if they are false positives, but note that this will then require manual code review by the Altis Cloud team.


## Database

### Avoid meta value queries {#meta-queries}

*Sniff: `HM.Performance.SlowMetaQuery`, `HM.Performance.SlowOrderBy`*

Any `meta_query` that uses the `meta_value` column should not be used. Additionally, ordering results by meta value should not be used.

The meta tables do not have MySQL indexes on the `meta_value` column, so query times can be very long for larger tables. While these may have decent performance with smaller sets of data, the meta tables tend to grow rapidly, and quickly become a significant scaling issue.

Consider storing lookup values in the meta key itself. This allows checking for the existence of a meta key (by using `EXISTS` in `meta_compare`) instead, which uses the index directly.


### Limit queries to 100 items {#bounded-queries}

*Sniff: `WordPress.WP.PostsPerPage`*

When writing database queries or post queries, always include an upper limit. If you need to get every item, reconsider the need to do so first, and whether you can change the process altogether. Unbounded queries are fine when you’re only working with a few items in the database, but present problems as the site scales.

If you can do so, rethink the way you’re storing or presenting data in the first place. You may be able to present pagination to the user instead, or use an infinite scroll technique to load items in batches instead.

The typical unbounded query in WordPress is using `WP_Query` with `'posts_per_page' => -1`. The Altis standards limit `posts_per_page` to 100.

Likewise, raw database queries without a `LIMIT` clause should not be used.

If you really do need to touch every post in the database, consider whether you can move your code into an offline solution; that is, into a wp-cli command or a cron task. This ensures that end users are shielded from potential scaling issues, and makes the operation fail-safe. These queries must still be bounded, but you can work in batches.


### Prepare all database queries {#prepare-queries}

*Sniff: `WordPress.DB.PreparedSQL`, `WordPress.DB.PreparedSQLPlaceholders`*

All dynamic parameters in database queries must be escaped for the SQL query to avoid SQL injection bugs.

Ensure all SQL statements are wrapped in a call to [`$wpdb->prepare()`](https://developer.wordpress.org/reference/classes/wpdb/prepare/).


## Check input and output

### Escape all output {#escape-output}

*Sniff: `HM.Security.EscapeOutput`*

Escaping is used to ensure that data is safe to be output to the browser. Data must be escaped using one of Altis' built-in escaping functions before output. The type of escaping function to use depends on the context in which the data is output.

This is because data might be safe or unsafe depending on where it is output. Data that is perfectly safe to output between two HTML tags might not be safe to output inside of a piece of inline JavaScript.

When writing code, always escape immediately before output. This is referred to as **late escaping**. This makes it clear when and how data is escaped, making the code easy to review and to understand.

It also avoids introducing security issues by accident. If the contents of a variable are escaped first, and then output later in the code, then this code is secure at the time it's written. However if at a later time, then escaping is removed from the variable, then all the instances in which the variable is output are now vulnerable. Late escaping avoids this problem.


### Validate and sanitize input {#validate-sanitize-input}

*Sniff: `HM.Security.ValidatedSanitizedInput`*

All user input must be validated and sanitized before being used in the codebase.

Validation and sanitization are two separate but related concepts.

When validating data, you are looking for certain criteria in the data. Or simply put, you’re saying "I want the data to have this, this, and this". Sanitisation on the other hand is about removing all the harmful elements from the data. In essence you’re saying "I don’t want the data to have this, this, and this".

But the difference is more than just conceptual. With validation, we store the data once we have verified it’s valid. If not, we discard it.

With sanitization, we take the data, and remove everything we don’t want. This means that we might change the data during the sanitization process. So in the case of user input, it is not guaranteed that all the input is kept. So it’s important that you choose the right sanitization functions, to keep the data intact.


## Check CSRF tokens (nonces) on destructive requests {#nonce}

*Sniff: `HM.Security.NonceVerification`*

Altis provides a system called [nonces](https://developer.wordpress.org/plugins/security/nonces/) which prevent [cross-site request forgery (CSRF)](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html) vulnerabilities.

Nonces should be checked on every request which performs a destructive action, as they check the user intended to take the action. This should be paired with checking user capabilities, so that you are checking both user authorization and user intent.


## Hide debugging data

### Do not expose filenames {#no-expose-files}

*Sniff: `WordPress.Security.PluginMenuSlug`*

`__FILE__` should not be used in admin menu slugs, as it exposes information to users about the underlying filesystem. While this is not a security issue directly, it can make privilege escalation much easier, so is to be avoided as part of a security-in-depth mentality.


### Do not output debugging information {#development-functions}

*Sniff: `WordPress.PHP.DevelopmentFunctions`*

Debugging functions such as `var_dump()` and `print_r()` must not be used, as they can output debug information directly to unauthorized users, and can expose internal implementation data of the application. Additionally, errors must not be set to be output directly to users for the same reason.

Altis provides full error logging functionality via the [Altis Dashboard](docs://cloud/dashboard.md). Custom logging information can be output to these logs via the `error_log()` and `trigger_error()` functions.

Additionally, the [Altis Dev Tools](docs://dev-tools/) can be used for inspecting data and logging detailed information. The Dev Tools module is enabled by default on development and staging environments, and can be enabled via your configuration on production environments if desired.


## Use Altis functionality

### Use built-in Altis functions where possible {#alternative-functions}

*Sniff: `WordPress.WP.AlternativeFunctions`*

When built-in functions in Altis exist, they should be preferred over using PHP or third-party code.

This includes functionality like making remote requests via cURL, which should instead use the [HTTP API](https://developer.wordpress.org/apis/handbook/http/). This ensures that remote requests appear in debugging tooling and logging.


### Do not use deprecated functions {#no-deprecated}

*Sniff: `WordPress.WP.DeprecatedFunctions`, `WordPress.WP.DeprecatedClasses`, `WordPress.WP.DeprecatedParameters`, `WordPress.WP.DeprecatedParameterValues`*

Deprecated functions should not be used, as they often contain out-moded functionality, and may be removed at a later date.


### Use better alternatives where possible {#no-discouraged}

*Sniff: `WordPress.WP.DiscouragedConstants`, `WordPress.WP.DiscouragedFunctions`*

Certain Altis functions and constants have alternatives, prefer the better alternative.

For example, use `wp_safe_redirect()` instead of `wp_redirect()`. This validates the redirect is to an allowed domain (current site by default), and avoids the [open redirect vulnerability class](https://cwe.mitre.org/data/definitions/601.html).



## Best practices

### Only use long-hand PHP tags {#php-tags}

*Sniff: `Generic.PHP.DisallowShortOpenTag`*

Only long-hand php tags (`<?php`) should be used. Do not use shorthand tags (`<?`) or legacy tags ASP-style tags (`<%`, `%>`, `<%=`, and `<script language="php">`).

Generally, all data must be escaped before being output to the browser. All escaping functions in Altis have echoing equivalents with the `_e` suffix, so the short-echo syntax (`<?=`) is not recommended (but is allowed).


### Do not change the timezone {#timezone}

*Sniff: `WordPress.DateTime.RestrictedFunctions`*

PHP's timezone should not be changed via `date_default_timezone_set()`. Many parts of both Altis and third-party code assume that the timezone is set to UTC, and changing this will break those systems.

Instead, use [`wp_date()`](https://developer.wordpress.org/reference/functions/wp_date/) for any date operations which require the site's timezone. You can also use [DateTime objects](https://php.net/datetime) with [DateTimeZone objects](https://php.net/datetimezone) for manual timezone operations.


### Do not use the reference operator with function calls {#call-time-pass-by-reference}

*Sniff: `Generic.Functions.CallTimePassByReference`*

Call-time pass-by-reference calls [are prohibited](https://www.php.net/manual/en/language.references.pass.php), and the reference sign cannot be specified with function calls. The reference sign should only be used in function definitions.


### Do not use the backtick operator {#no-backtick}

*Sniff: `Generic.PHP.BacktickOperator`*

The backtick operator is not allowed as PHP will attempt to execute the contents as a shell command.

Running shell commands is generally not permitted, but exceptions can be made for very advanced use-cases. Contact the Altis Cloud team if you have a use case for this.


### Do not use dynamic code constructs (eval) {#no-eval}

*Sniff: `Squiz.PHP.Eval`, `WordPress.PHP.RestrictedPHPFunctions`*

Dynamic code constructs (such as `eval()` and `create_function()`) allow execution of arbitrary PHP code, and must not be used.

These represent a significant security risk, as they could be combined with input injection vulnerabilities to form a [code injection attack](https://owasp.org/www-community/attacks/Code_Injection) (also called a Remote Code Execution (RCE) vulnerability).


### Do not use `goto` {#no-goto}

*Sniff: `Generic.PHP.DiscourageGoto`*

The `goto` operator should not be used, as it significantly impacts readability of code. This in turn makes reviewing code much harder, as well as making development of sites harder.


### Do not use Unicode byte-order marks (BOM) {#no-bom}

*Sniff: `Generic.Files.ByteOrderMark`*

Unicode byte-order marks (BOMs) are not allowed. Due to the way that PHP works when including files, BOMs will cause unexpected behaviour and trigger warnings about unexpected output.


### Do not have empty statements {#no-empty-statements}

*Sniff: `WordPress.CodeAnalysis.EmptyStatement`*

Empty statements (including `for`, `switch`, `if`, etc) should not be used, and should contain logic within them.

These are usually signs that your code is not doing what you expect, and indicate that your logic is incorrect.


### Ensure translation functions are used correctly {#correct-translations}

*Sniff: `WordPress.WP.I18n`*

In order to work correctly with static analysis tools and Altis' translation system, translated text must follow specific rules. Notably, strings must be static, and must use `sprintf()` after translation when variable strings are required.

Consult the [internationalization documentation](https://developer.wordpress.org/themes/functionality/internationalization/) for more information.
