#  Cavalcade jobs or stuck, or not running. 

If you've found your Cron/Cavalcade (we use these terms interchangeably) jobs have stopped running, it's likely the job as crashed, and is stuck in a 'running' state in the `wp_cavalcade_jobs` table.

You will need to manually update the status of the job to 'Failed'. This can be done via the Altis CLI.

Once a job has been marked as failed, your code will need to reschedule the job. Typically, this is done by calling wp_schedule_event() conditionally based on wp_next_scheduled().

## Why does this happen?

Situations where Cavalcade jobs get stuck are challenging to debug. Most commonly, it can occur when the Cavalcade Runner is unable to shut down the job correctly. Unfortunately, these cases are very hard to catch and debug, and while we've eliminated most places where this can occur it does appear to still happen.

Usually this happens if the Cavalcade container the job was running in gets killed for over memory consumption, or the container gets scaled in (terminated) by the system. In the latter case, jobs can only be guaranteed to run for 60 minutes. If your job run time exceeds 3,600 seconds, it's likely the container is culled, and the Cavalcade runner cannot shut down.

## Steps to resolve.

If a job or group of jobs are failing consistently, contact Altis Support, and we can assist you isolate the problem. Please provide us with a written architectural overview of the job that is failing. It may be required to create a CLI command to execute the job functions, to help isolate and debug the issue.