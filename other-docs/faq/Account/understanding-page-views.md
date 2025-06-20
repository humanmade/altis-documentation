# Understanding Page View Billing on Altis Cloud

## What Is an Altis Page View?

An Altis page view is any response from the Altis Cloud platform that results in an HTML page. This includes users loading pages, as
well as automated traffic such as search engines indexing your site, AI bots scraping content, and other automated requests.

## Why Is There a Discrepancy Between Altis Page Views and My Analytics Data?

The discrepancy arises because Altis counts every request that results in an HTML page, including automated traffic and users with
JavaScript disabled. In contrast, analytics tools like Google Analytics rely on JavaScript running in the user’s browser, which may
not capture all visits, especially from bots, users with ad blockers, or those who disable JavaScript. This leads to higher page
view counts on Altis compared to traditional analytics platforms.

## Best Practices for Managing Automated Traffic

1. Review Your Data: Regularly analyze your traffic data to understand common user agents, response codes, URLs, and the geographic
   origin of your traffic. This helps you ensure that the traffic aligns with your expected audience and identify any anomalies.
2. Utilize robots.txt: Configure your robots.txt file to control which crawlers can access your site and how frequently they can do
   so. This can help manage the load from search engines and other automated agents.
3. Request Block Rules: If you identify problematic automated traffic, contact your Altis account manager to request specific block
   rules.
4. Analyze Access Logs: Download and review access logs from the Altis dashboard to get detailed insights into traffic patterns,
   sources, and the type of content being accessed. This can help you identify and manage unusual spikes.

## Common Misconceptions

1. All Traffic is the Same: A common misconception is that every recorded page view represents a real user. In reality, it includes
   automated traffic such as bots and crawlers, which inflate the numbers.
2. Page Views Match Analytics Data: Many users assume that page views in Altis should match their analytics data. However, due to
   differences in counting methods (e.g., JavaScript-based analytics vs. server-side counting), these numbers will significantly
   differ.
3. Only Human Visits Count: Another misconception is that only human visits are counted. Altis page views include all HTML page
   responses, whether from humans, bots, or automated processes.
