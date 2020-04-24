# Code Review

All Code Review for Altis is done via GitHub in the pre set-up GitHub repository for your project. Specific development flow process varies project to project, this document only covers the required Human Made Code Review. Specifically where in the "code development to running in production" flow the Human Made Code Review needs to be performed can be discussed separately.

It is required that all code review be performed on Git branches, via a GitHub Pull Request. When a given Git branch is ready for review, the following process should be followed:

1. Open a Pull Request against the "mainline" branch (typically `master` or `staging`)
1. Assign the Pull Request to the `@humanmade-cloud` GitHub user.
1. Make sure your Pull Request passes any status checks and WordPress Coding Standards.

Once the Pull Request is submitted, it's possible an automated code review will be performed by the HM Linter bot. In this case, the Pull Request will be marked as "Changes Requested" and you should fix up any errors to do with formatting for the manual review to continue.

Upon receiving a new Pull Request, Human Made will perform the manual code review. This will result in one of three outcomes:

1. "Changes Requested" - We asked for some things to be changed in the code, they should be rectified before a second review pass.
1. "Commented" - We asked a question of for more information for the Pull Request. Get back to us, and we'll be able to continue.
1. "Approved" - The code is ready to be merged into the mainline branch, and will not need to be reviewed again on it's way to production!

Once a Pull Request is reviewed, the next steps depend on the process of the project. You may communicate on the Pull Request for Human Made to merge and deploy the Pull Request on Approval. This can be done via a comment, or a label of "Review Merge" or similar.

The Pull Request will be assigned back to the developer, if the Pull Request requires changes or comment. Once the changes have been made, the developer should assign the Pull Request back to the `@humanmade-cloud` GitHub user.

If you only want code reviewed, but not yet merged and deployed then you don't need to do anything. This is useful if you want to control when code is merged to the mainline branch, as you can perform the merge yourself.

Note: If you have requested Code Reviews from other users, we will not merge and deploy a Pull Request until those users have also Approved the Pull Request.

## Automatic Code Review
Automatic Code Review (commonly referred to as ACR) is used to check a code base for errors to ensure a website and overall stack’s security and performance is not compromised. This tool is used to perform a preliminary check for potential problem areas and ensure a better consistency of the standards we apply to the code we review rather than being dependent on the reviewer. 

### When is it performed?
The ACR process is kicked off when a Pull Request (PR) is created. Every commit pushed to a branch after the PR is created will have the ACR process executed against it.

### How will I know if there are issues with my code?
Errors are highlighted within GitHub, under the review section.

TODO: Add screenshots of GitHub

### What types of code are checked?
The types of code that are reviewed are JavaScript (unbuilt) and PHP. CSS files can also be reviewed but are not at this point.

### What is looked for?
The ACR focus on security and performance issues. At a high level, the following are checked:
- SQL Injection
- XSS and XSRF attacks
- Escaping Output and Sanitising Input
- Composer and NPM Dependency vulnerabilities
- Meta Value queries, Database related queries, Disk Access, Object Caching, WP_Query Specifics, Differently named cookies 

#### Detailed Breakdown of Checks
Many of the rules in the ACR ruleset (HM-Required) have a comment that outline what the rule checks for however the following will breakdown what each rule in the ACR ruleset:

The following directories will be excluded and not checked by ACR:

	<exclude-pattern>node_modules/*</exclude-pattern>
	<exclude-pattern>vendor/*</exclude-pattern>

PHP Specific Checks.

	<!-- Check for PHP cross-version compatibility. -->
	<config name="testVersion" value="7.1-" />
	<rule ref="PHPCompatibilityWP">
		<exclude name="PHPCompatibility.Miscellaneous.RemovedAlternativePHPTags.MaybeASPOpenTagFound" />
	</rule>

Do not allow slow WordPress Database Queries. TODO: needs more explanation.

	<!-- Disallow slow database queries -->
	<rule ref="HM.Performance.SlowMetaQuery" />
	<rule ref="HM.Performance.SlowOrderBy" />

Require output is escaped, using sanitization functions or WordPress output functions.

	<!-- Require output is escaped. -->
	<!-- Note: we use our version of EscapeOutput to ignore error-reporting functions. -->
	<!-- <rule ref="WordPress.Security.EscapeOutput"> -->
	<rule ref="HM.Security.EscapeOutput">
		<properties>
			<property name="customEscapingFunctions" type="array">
				<element value="whitelist_html" />
			</property>
			<property name="customAutoEscapedFunctions" type="array">
				<!-- Allow all the built-in URL functions -->
				<element value="home_url" />
				<element value="get_home_url" />
				<element value="site_url" />
				<element value="get_site_url" />
				<element value="admin_url" />
				<element value="get_admin_url" />
				<element value="includes_url" />
				<element value="content_url" />
				<element value="plugins_url" />
				<element value="network_site_url" />
				<element value="network_home_url" />
				<element value="network_admin_url" />
				<element value="user_admin_url" />
				<element value="self_admin_url" />

				<!-- Other URL functions -->
				<element value="get_template_directory_uri" />
				<element value="get_theme_file_uri" />
				<element value="get_term_link" />
				<element value="wp_nonce_url" />

				<!--
					For the minimum standard, we're not treating translations
					as an attack vector.
				-->
				<element value="__" />
				<element value="_x" />
				<element value="_n" />

				<!-- Other templating tags. -->
				<element value="paginate_links" />
			</property>
		</properties>
	</rule>

Protect filesystem's data, do not allow using `__FILE__` in menu slugs.

	<!-- Disallow use of __FILE__ in menu slugs, which exposes the filesystem's data. -->
	<rule ref="WordPress.Security.PluginMenuSlug" />
	<rule ref="WordPress.Security.PluginMenuSlug.Using__FILE__">
		<type>error</type>
	</rule>

Use WordPress core functions instead of the PHP alternative.

	<!-- Disallow functions where WordPress has an alternative. -->
	<rule ref="WordPress.WP.AlternativeFunctions">
		<!-- ...but, allow some back in. -->
		<properties>
			<property name="exclude" type="array">
				<element value="file_get_contents" />
				<element value="file_system_read" />
				<element value="json_encode" />
				<element value="json_decode" />

				<!-- wp_parse_url() only exists for inconsistency in PHP <5.4 -->
				<element value="parse_url" />
			</property>
		</properties>
	</rule>
	<rule ref="WordPress.DB.RestrictedFunctions" />
	<rule ref="WordPress.DB.RestrictedClasses" />

Using `eval()` is not allowed as is it allows execution of arbitrary PHP code and thus a security risk.

	<!-- Disallow eval(). (From WordPress-Core) -->
	<rule ref="Squiz.PHP.Eval"/>
	<rule ref="Squiz.PHP.Eval.Discouraged">
		<type>error</type>
		<message>eval() is a security risk so not allowed.</message>
	</rule>

Do not allow usage of `create_function()` which is deprecated as of PHP 7.2.

	<!-- Disallow create_function() -->
	<rule ref="WordPress.PHP.RestrictedPHPFunctions"/>

The `goto` operator allows jumping to another section of code which creates for less readable code.

	<!-- Disallow goto function. -->
	<!-- Remove after https://github.com/squizlabs/PHP_CodeSniffer/pull/1664 -->
	<rule ref="WordPress.PHP.DiscourageGoto"/>
	<rule ref="WordPress.PHP.DiscourageGoto.Found">
		<type>error</type>
		<message>The "goto" language construct should not be used.</message>
	</rule>

Only allow cron intervals 15 minutes (default value) and longer.

	<!-- Disallow cron checks which are too frequent. -->
	<rule ref="WordPress.WP.CronInterval" />
	<rule ref="WordPress.WP.CronInterval.CronSchedulesInterval">
		<type>error</type>
		<message>Scheduling crons at %s sec ( less than %s minutes ) is prohibited.</message>
	</rule>

To maintain performance, queries should only return less than or equal to a 100 posts.

	<!-- Disallow querying more than 100 posts at once. -->
	<rule ref="WordPress.WP.PostsPerPage" />
	<rule ref="WordPress.WP.PostsPerPage.posts_per_page_numberposts">
		<type>error</type>
	</rule>
	<rule ref="WordPress.WP.PostsPerPage.posts_per_page_posts_per_page">
		<type>error</type>
	</rule>

PHP's timezone should not be changed.

	<!-- Disallow changing PHP's timezone. -->
	<rule ref="WordPress.WP.TimezoneChange" />

Only longhand php tags (`<?php`) should be used. Do not use shorthand tags (`<?`) or legacy tags ASP-style tags (`<%`, `%>`, `<%=`, script tag `<script language="php">`).

	<!-- Disallow short PHP tags. (From WordPress-Core) -->
	<rule ref="Generic.PHP.DisallowShortOpenTag">
		<!-- But, allow short echo, which is now standard. -->
		<exclude name="Generic.PHP.DisallowShortOpenTag.EchoFound" />
	</rule>

	<!-- Disallow old-style PHP tags (e.g. ASP-style) -->
	<rule ref="Generic.PHP.DisallowAlternativePHPTags">
		<!-- Allow ASP-style tags that aren't tokenised. -->
		<exclude name="Generic.PHP.DisallowAlternativePHPTags.MaybeASPShortOpenTagFound" />
		<exclude name="Generic.PHP.DisallowAlternativePHPTags.MaybeASPOpenTagFound" />
	</rule>

Raw SQL statements should be wrapped in a prepare, `$wpdb->prepare()`.

	<!-- Require prepared SQL statements. -->
	<rule ref="WordPress.DB.PreparedSQL" />
	<rule ref="WordPress.DB.PreparedSQLPlaceholders" />

Byte-order mark unicode character encoding is not allowed.

	<!-- Disallow BOM, which causes issues with headers being sent. (From WordPress-Core) -->
	<rule ref="Generic.Files.ByteOrderMark" />

Check for empty statements; `for`, `switch`, `if`, ect should contain logic.

	<!-- Disallow empty statements. -->
	<rule ref="WordPress.CodeAnalysis.EmptyStatement" />

Ensure translation functions are used correctly.

	<!-- Require correct usage of WP's i18n functions. -->
	<rule ref="WordPress.WP.I18n">
		<properties>
			<property name="check_translator_comments" value="false" />
		</properties>

		<!-- Allow empty strings to be translated (e.g. space character) -->
		<exclude name="WordPress.WP.I18n.NoEmptyStrings" />

		<!--
			Allow unordered placeholders. It's not good style, but strictly
			speaking it's not a problem.
		-->
		<exclude name="WordPress.WP.I18n.UnorderedPlaceholdersText" />
		<exclude name="WordPress.WP.I18n.MixedOrderedPlaceholdersText" />
	</rule>

There is no reference sign on a function call. The reference sign should only be used in function definitions.

	<!-- Disallow parts of PHP which may cause compatibility issues. -->
	<rule ref="Generic.Functions.CallTimePassByReference" />

Ensure there are no functions that output data which are used during development.

	<!-- Disallow "development" functions like var_dump/print_r/phpinfo -->
	<rule ref="WordPress.PHP.DevelopmentFunctions">
		<!-- Allow triggering errors for reporting purposes. -->
		<exclude name="WordPress.PHP.DevelopmentFunctions.error_log_error_log" />
		<exclude name="WordPress.PHP.DevelopmentFunctions.error_log_trigger_error" />

		<!-- Allow overriding the error handler. -->
		<exclude name="WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler" />

		<!-- Allow changing error level. -->
		<exclude name="WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting" />

		<!-- Allow backtraces. -->
		<exclude name="WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace" />
		<exclude name="WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary" />

		<!-- Set remaining to errors. -->
		<type>error</type>
	</rule>
	<!-- Override message for clarity. -->
	<rule ref="WordPress.PHP.DevelopmentFunctions.error_log_var_dump">
		<message>%s() found. Errors should be logged via error_log() or trigger_error().</message>
	</rule>
	<rule ref="WordPress.PHP.DevelopmentFunctions.error_log_var_export">
		<message>%s() found. Errors should be logged via error_log() or trigger_error().</message>
	</rule>
	<rule ref="WordPress.PHP.DevelopmentFunctions.error_log_print_r">
		<message>%s() found. Errors should be logged via error_log() or trigger_error().</message>
	</rule>
	<rule ref="WordPress.PHP.DevelopmentFunctions.error_log_debug_print_backtrace">
		<message>%s() found. Use error_log( wp_debug_backtrace_summary() ) instead.</message>
	</rule>

Deprecated functions should not be used and use alternative WordPress functions and constants if a better alternative exists. 

	<!-- Disallow parts of WP which have been deprecated. -->
	<rule ref="WordPress.WP.DeprecatedFunctions" />
	<rule ref="WordPress.WP.DeprecatedClasses" />
	<rule ref="WordPress.WP.DeprecatedParameters" />
	<rule ref="WordPress.WP.DeprecatedParameterValues" />

	<!-- Disallow parts of WP which have better alternatives. -->
	<rule ref="WordPress.WP.DiscouragedConstants" />
	<rule ref="WordPress.WP.DiscouragedFunctions">
		<properties>
			<property name="exclude" type="array">
				<!--
					wp_reset_query() does a different thing to
					wp_reset_postdata() and should not be discouraged.
				-->
				<element value="wp_reset_query" />
			</property>
		</properties>
	</rule>

The backtick operator is not allowed as PHP will attempt to execute the contents as a shell command.

	<!-- Disallow the backtick operator (which calls out to the system). -->
	<rule ref="Generic.PHP.BacktickOperator" />

	<!-- Require valid syntax. -->
	<rule ref="Generic.PHP.Syntax" />

	<!-- Disallow silencing errors. -->
	<rule ref="WordPress.PHP.NoSilencedErrors" />
	<rule ref="WordPress.PHP.NoSilencedErrors.Discouraged">
		<message>Errors should not be silenced. Found: %s</message>
	</rule>

Ensure nonce verification is used when making requests to ensure the request is allowed.

	<!-- Require nonce verification. -->
	<rule ref="HM.Security.NonceVerification">
		<properties>
			<property name="allowQueryVariables" value="true" />
		</properties>
	</rule>

Inputs should be both sanitized and validated to ensure the input is expected.

	<!-- Validate sanitised input. -->
	<rule ref="HM.Security.ValidatedSanitizedInput">
		<properties>
			<property name="customUnslashingSanitizingFunctions" type="array">
				<!-- Allow checking nonces without sanitization. -->
				<element value="wp_verify_nonce" />
			</property>

			<property name="customSanitizingFunctions" type="array">
				<!--
					Decoding isn't technically a sanitisation function,
					however you can't really sanitize JSON input.
				-->
				<element value="json_decode" />

				<!-- wp_trim_words() uses wp_strip_all_tags() internally. -->
				<element value="wp_trim_words" />
			</property>
		</properties>
	</rule>

When using `in_array()` specify the strict parameter `bool $strict` is also specified.

	<!-- Require strict types for in_array() calls. -->
	<!-- <rule ref="WordPress.PHP.StrictInArray"/> -->

Use `wp_safe_redirect()` instead of `wp_redirect()`. This validates the redirect is in the whitelist (site by default).

	<!-- Require wp_safe_redirect() instead of wp_redirect -->
	<!-- <rule ref="WordPress.Security.SafeRedirect"/> -->


### Can I run this process locally?
An Altis command is still in development, however, since the ACR is based on a custom PHPCS standard the process executed locally by running the following command `TODO: vendor/bin/phpcs -e --standard=HM-Required`

TODO: Add screenshot of output

### Is there a way I can have the ACR ignored?
Lines of code can be ignored however entire files and folders can not.

### What if an issue found in ACR is a false positive?
A false positive can be ignored using the method in "Is there a way I can have the ACR ignored?". Include a note why this line was ignored.
