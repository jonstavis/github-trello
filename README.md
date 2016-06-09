Github trello service hook
=============

This is a service hook for Trello that posts a comment to a card if you include a short link in your commit message.  To set up:

1. [Get an API key](https://trello.com/1/appKey/generate) for the account you want the comments to be posted under.  You may wish to create a separate account for this purpose (though you will have to add that user to your organization and any boards you want it to comment on).  This is the `$key` in config.php
2. Visit the above link and click on "Token" below your api key to generate a non-expiring user token with read and write access (so it can post comments).  This is the `$token` in config.php.
3. In Github, click "Settings", then "Service Hooks", then "Webhook URLs" and add the full URL of hook.php as a URL.

When commit code, if you include a Trello short link (looks like **https://trello.com/c/xxxxxxx**), a comment will be posted to the card referenced with a short message about how many files were added, removed or modified as well as a link to the commit on Github.

![Commit](https://raw.github.com/ehedaya/github-trello/master/commit.png)
