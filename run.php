<?php
include __DIR__.'/vendor/autoload.php';
require("token.php");
$discord = new \Discord\Discord([
    'token' => $token
]);
$discord->on('ready', function ($discord) {
    echo "Bot is ready.", PHP_EOL;
  
    // Listen for events here
    $discord->on('message', function ($message) {
		
		echo "{$message->author->username}: {$message->content}", PHP_EOL;
		
		if ( "{$message->channel->id}" != 223895430079971329 ) { // only post in botgames
			//echo "Can't post in channel #{$message->channel->id}.", PHP_EOL;
			return;
		}
		else {
			
			$thismessage = "{$message->content}";
			$thisuser = "{$message->author->id}";
			
			//********** BEGIN COMMANDS
			
			//***** hello nicebot
			if ( preg_match("/^(((hello|hi|hey)(\s+there)?)|greetings|(ay\s+)?yo|good\s(morning|afternoon|day|evening))([,!])?(\s+)<@224008638858133504>/i",$thismessage) ) {
				$message->channel->sendMessage("Hello <@!$thisuser>!");
			}
			
			//***** doom for fimion
			if ( preg_match("/doo+m/i",$thismessage) ) {
				$message->channel->sendMessage("Doomed. *Doooooooomed!* <@!$thisuser>");
			}
			
			//**** dice roll for grey <3
			
			// syntax: !roll (4)d20(+3)( for initiative)
			// we can't roll more than 9999 9999-sided dice +- 9999 to prevent overflow
			if (preg_match("/^!roll\s+(\d*)d(\d+)([+-])?(\d*)?(\s+for initiative)?/",$thismessage,$roll)) {
				if (isset($roll[1]) && strlen($roll[1]) < 5) { // checking how many dice
					if (isset($roll[2]) && strlen($roll[2]) < 5) {// checking how many sides
						if ($roll[2] > 1) { // you can't roll less than a 2
							if ( strlen($roll[4]) < 5) { // our additions must also be less than 5 digits
								$result = 0;
								
								// see how many dice we have, and roll however many
								if ( $roll[1] > 0 ) {
									for ($d = 0; $d < $roll[1]; $d++) {
										$result = $result + mt_rand(1,$roll[2]);
									}
								}
								else {
									$result = mt_rand(1,$roll[2]);
								}
								
								// add or subtract from result
								if (isset($roll[3]) && isset($roll[4])) {
									if ($roll[3] === "+") {
										$result += $roll[4];
									}
									if ($roll[3] === "-") {
										$result -= $roll[4];
									}
								}
								
								// for initiative
								if (isset($roll[5])) {
									$output = "<@!$thisuser> rolled **$result** for initiative!";
								}
								else {
									$output = "<@!$thisuser> rolled **$result!**";
								}
								
								// let's be lulzy
								if ($result <= 1) {
									$output .= " Ouch.";
								}
								
								// output to channel here
								$message->channel->sendMessage($output);
								
							}
							else {
								$message->channel->sendMessage("I don't know what to do with these big numbers, <@!$thisuser>. 😓");
							} // end addition/subtraction checker
						}
						else {
							$message->channel->sendMessage("But <@!$thisuser> you can't roll a ".$roll[2]."-sided die!");
						} // end if die is less than 2 sides
					}
					else {
						$message->channel->sendMessage("But <@!$thisuser> I can't count that high! 😭");
					} // end # of sides
				}
				else {
					$message->channel->sendMessage("But <@!$thisuser> I can't roll *that* many dice! 😨");
				} // end # of dice
			}
			
			//***** random compliments
			if ( preg_match("/^!compliment(\s+<@!\d+>)?/i",$thismessage,$user) ){
				
				/* i am not creative
				 * some compliments adapted from:
				 * peoplearenice.blogspot.com/p/compliment-list.html
				 * happier.com/blog/nice-things-to-say-100-compliments
				 */
				 
				 $compliments = array(
					0 => "{NAME}, you're cooler than ice on the rocks.",
					1 => "{NAME} makes me smile.",
					2 => "{NAME} is my sunshine on a rainy day.",
					3 => "{NAME} looks really good today!",
					4 => "I like {NAME}'s style.",
					5 => "Hey {NAME}, your mouse told me that you have very soft hands.",
					6 => "Is it hot in here or is it just {NAME}?",
					7 => "{NAME}'s every thought and motion contributes to the beauty of the universe.",
					8 => "Hey {NAME}, can you teach me how to be as awesome as you?",
					9 => "I'm having trouble coming up with a compliment worthy of {NAME}.",
					10 => "{NAME}'s more fun than bubble wrap.",
					11 => "On a scale of 1 to 10, {NAME} is [OVERFLOW ERROR]",
				);
				
				// generate a compliment
				$which = mt_rand(0, count($compliments)-1);
				
				// construct our compliment
				if (isset($user[1])) {
					$output = str_replace("{NAME}",$user[1],$compliments[$which]);
				}
				else {
					$output = str_replace("{NAME}","<@!$thisuser>",$compliments[$which]);
				}
				
				// display compliment
				$message->channel->sendMessage($output);
				
			}
			//********* END COMMANDS
		}
    });
});

$discord->run();
