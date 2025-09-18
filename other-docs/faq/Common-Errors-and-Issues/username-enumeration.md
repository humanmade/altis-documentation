# WordPress Usernames/IDs are Publically Available

Security teams sometimes flag that WordPress usernames and user IDs are publicly accessible. This behavior is intentional and part of WordPress’s security model.

## Why WordPress Usernames and IDs Are Public
WordPress treats usernames and IDs as public information by design. Rather than relying on secrecy, it emphasizes stronger defenses such as robust password policies and two-factor authentication. This approach is documented in the [WordPress Security Handbook](https://make.wordpress.org/core/handbook/testing/reporting-security-vulnerabilities/#why-are-disclosures-of-usernames-or-user-ids-not-a-security-issue).

Making usernames visible does not by itself create a vulnerability. Even if hidden, usernames are typically easy to guess from display names or email addresses, and exposure does not grant access without the correct password. While public usernames can make brute force attacks slightly easier, the real protection comes from requiring strong credentials and layered defenses.

To address genuine risks, we recommend enforcing strong passwords, enabling two-factor authentication wherever possible, and relying on additional safeguards such as Altis’s built-in brute force protection. Together, these measures ensure that the availability of usernames and IDs does not weaken overall security.