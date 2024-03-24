


API Timeout Retry

Occasionally API requests to fishbowl will time out. Fishbowl dev team has indicated this is sometimes due to a limitation on their end. Can also sometimes be due to current TWB hosting setup.

This plugin includes a built-in retry function for submissions that timeout during the API post. This runs on an hourly cron job, that looks for all GF entries that have a Fishbowl status of "timeout error" and attempt to re-send them.