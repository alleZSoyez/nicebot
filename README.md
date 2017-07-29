# nicebot
a nice bot named nicebot for discord

just create a file in the same directory called "token.php" with this format:

```
<?php
	$token = "YOUR_TOKEN_HERE";
```

In addition, please change line 14 in run.php to include your bot's client ID so it does not respond to itself.

```
$clientID = "YOUR_CLIENT_ID_HERE";
```

Optionally, you can also restrict it by channel starting on line 22 of run.php should the built-in permissions management in discord not be enough.
