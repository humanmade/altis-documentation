## Automated Code Review Standards

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

Do not allow slow WordPress Database Queries. Queries against post meta can be very slow if queries are performed against `meta_value` or `meta_key`. In addition, user `orderby` in a query has poor performance.

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