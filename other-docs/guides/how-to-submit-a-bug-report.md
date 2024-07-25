# How to Submit a Bug Report

Bugs happen, and we’re here to support you. If you find a bug in Altis, we'll handle fixing it.

To help our product team understand your issue, there are some requirements for a bug report that must be met. This allows our team
to know how to both identify the problem, and whether it has been resolved.

When [filing a bug report](./getting-help-with-altis.md) please provide all of the following:

## Test Results

Please write a brief description of the bug, including what you expect to happen and what is currently happening.

## Reduced Test Case

Bugs should be isolated to a [reduced test case](https://css-tricks.com/reduced-test-cases/), as this helps us to ensure that the
bug exists in Altis, rather than your project. Reduced test cases should be created against a standard Altis local environment with
just the minimal changes needed to reproduce the bug.

Please ensure you provide the reduced test case as the basis of the bug report. If we cannot confirm that the test case is within
Altis, we may ask you to reduce the test case further to ensure it isn't caused by your custom code.

The reduced test case can be provided as a code snippet in the report itself or as a link to
a [GitHub gist](https://gist.github.com/) if there are multiple files involved.

Customers on Enterprise support plans may wish to use their dedicated technical engineering support to handle bug isolation.

## Steps to Reproduce

If possible, please also provide detailed step-by-step instructions to reproduce the issue. This will ensure that we can replicate
the problem, and avoids back-and-forth conversations.

## Environment Setup and Configuration

Please let us know what environments the bug is occurring in, what configuration you have in `composer.json` under the `extra.altis`
property (making sure to redact any sensitive information like API keys), and whether you were able to reproduce the bug locally or
not.
