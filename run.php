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
		
			global $compliments;
			
			// uncomment following line while debugging
			echo "{$message->author->username}: {$message->content}", PHP_EOL;

			if ( !preg_match("/(!)?224008638858133504/", "{$message->author->id}") ) { // don't respond to yourself.
			
				/*
				if ( "{$message->channel->id}" != "223895430079971329" ) { // only post in channels(s) of your choice
					echo "Can't post in channel #{$message->channel->id}.", PHP_EOL;
					return;
				}
				else {
				*/
				
					$thismessage = "{$message->content}";
					$thisuser = "{$message->author->id}";
					
					//********** BEGIN COMMANDS
					
					//***** hello nicebot
					if ( preg_match("/^(((hello|hi|hey)(\s+there)?)|greetings|(ay\s+)?yo|good\s+(morning|afternoon|day|evening))([,!])?(\s+)<@(!)?224008638858133504>/i",$thismessage) ) {
						$message->channel->sendMessage("Hello <@!$thisuser>!");
					}
					
					//***** doom for fimion
					if ( preg_match("/doo+m/i",$thismessage) ) {
						$message->channel->sendMessage("Doomed. *Doooooooomed!* <@!$thisuser>");
					}
					
					//**** dice roll for grey <3
					
					// syntax: !roll (4)d20(+3)( for initiative) ( -v)
					// we can't roll more than 9999 9999-sided dice +- 9999 to prevent overflow
					if (preg_match("/^!roll\s+(\d*)d(\d+)([+-])?(\d*)?(\s+for initiative)?(\s+-v)?/",$thismessage,$roll)) {
						if (isset($roll[1]) && strlen($roll[1]) < 5) { // checking how many dice
							if (isset($roll[2]) && strlen($roll[2]) < 5) {// checking how many sides
								if ($roll[2] > 1) { // you can't roll less than a 2
									if ( strlen($roll[4]) < 5) { // our additions must also be less than 5 digits
										$results = array(); // for later...
										$modifier = 0;
										$output = "";
										$grandtotal = 0;
										
															
										/* planning:
										 * "user rolled (these dice) <for initiative>! <Showing all rolls.>"
										 * reset array
										 * roll dice, store in array
										 * check verbose mode
										 * if on: print each roll, then modifier, then total, then grand total
										 * if off: add each (roll+modifier) and just print total
										 */
										
										// count number of dice and how many sides, then roll it and stick the result in an array
										// if number of dice is not specified, make it 0
										if ($roll[1] != "") {
											$numdice = $roll[1];
										}
										else {
											$numdice = 1;
										}
										
										for ($d = 0; $d < $numdice; $d++) {
											$result = mt_rand(1,$roll[2]);
											$results[$d] = $result;
										}
										
										// look at the modifier
										if (isset($roll[3]) && isset($roll[4])) {
											if ($roll[3] === "+") {
												$modifier = $roll[4];
											}
											if ($roll[3] === "-") {
												$modifier = ($roll[4]*-1);
											}				
										}
										
										// check for initiative
										if (isset($roll[5]) && $roll[5] != "") {
											$forin = " for initiative!";
										}
										else {
											$forin = "!";
										}

										// check for verbose mode, HOWEVER you cannot use it if you're only rolling one die
										if (isset($roll[6]) && $roll[6] != "" && $roll[1] > 1) { // VERBOSE IS ON
											
											// go through array, display dice result, then modifier, then total result
											foreach ($results as $r) {
												
												// check if modifier is 0
												if ($roll[4] > 0 && $roll[4] != "") {												
													$output = $output."Rolled $r, and with $modifier it's **".($r+$modifier)."**!\n";
												}
												else {
													$output = $output."Rolled **$r**!\n";
												}
												
												$grandtotal += $r+$modifier;
											}
											
											$output .= "\n<@!$thisuser> rolled **$grandtotal**$forin";
										}
										else { // VERBOSE IS OFF
											foreach ($results as $r) {
												$grandtotal += $r+$modifier;
											}
											$output .= "\n<@!$thisuser> rolled **$grandtotal**$forin";
										}
										
										// output EVERYTHING to channel here
										$message->channel->sendMessage($output);		
										
										// dump array contents
										//var_dump($results);
										//var_dump($roll);						
									}
									else {
										$message->channel->sendMessage("I don't know what to do with these long numbers, <@!$thisuser>! :confounded: ");
									} // end addition/subtraction checker
								}
								else {
									$message->channel->sendMessage("But <@!$thisuser> you can't roll a ".$roll[2]."-sided die! :fearful:");
								} // end if die is less than 2 sides
							}
							else {
								$message->channel->sendMessage("But <@!$thisuser> I can't count that high! :sob:");
							} // end # of sides
						}
						else {
							$message->channel->sendMessage("But <@!$thisuser> I can't roll *that* many dice! :sweat:");
						} // end # of dice
					}
					
					//***** random compliments
					if ( preg_match("/^!compliment(\s+<@(!)?\d+>)?/i",$thismessage,$user) ){
						
						/* i am not creative
						 * some compliments adapted from:
						 * peoplearenice.blogspot.com/p/compliment-list.html
						 * happier.com/blog/nice-things-to-say-100-compliments
						 */
						 
						 // if the array does not exist or if the array has been reduced to zero length, regenerate it
						 if ( !isset($compliments) || (isset($compliments) && count($compliments)==0) ) {
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
								11 => "On a scale of 1 to 10, {NAME} is [OVERFLOW ERROR]"
							);
						}
						
						// randomize the compliments
						shuffle($compliments);
						
						// construct our compliment. we'll take the last one on the list
						$which = count($compliments)-1;
						
						if (isset($user[1])) {
							$output = str_replace("{NAME}",$user[1],$compliments[$which]);
						}
						else {
							$output = str_replace("{NAME}","<@!$thisuser>",$compliments[$which]);
						}
						
						// display compliment
						$message->channel->sendMessage($output);
						
						// remove that item from the the array
						array_pop($compliments);
						//echo "array length now: ".count($compliments);

					}
					
					//***** COIN FLIP
					// syntax: !coinflip< number>
					// if no number specified, it'll flip only one.
					// limit 9999 coins
					if ( preg_match("/^!coinflip(\s+\d*)?/",$thismessage,$flip) ) {
							
							$heads = 0;
							$tails = 0;
							
							// # coins not set or set to one coin
							if (!isset($flip[1]) || $flip[1] == 1) {
								
								$r = mt_rand(0,1);
								
								if ($r == 0) { // heads
									$result = "heads";
								}
								else {
									$result = "tails";
								}
								
								$output = "<@!$thisuser> flipped **$result**!";
							}
							
							// more than one coin
							if (isset($flip[1]) && $flip[1] > 1 && strlen($flip[1]) < 5) {
							
								for ($r = 0; $r < $flip[1]; $r++) {
									$thisflip = mt_rand(0,1);
									
									if ($thisflip == 0) {
										$heads++;
									}
									else {
										$tails++;
									}
								}
							
							
								$output = "<@!$thisuser>'s results:\n\n";
								$output .= "Heads: **$heads**\n";
								$output .= "Tails: **$tails**\n";
							}
							
							// too many coins
							if (isset($flip[1]) && strlen($flip[1]) > 4) {
								$output = "But <@!$thisuser> I don't even *have* that many coins! :scream:";
							}
							// not enough coins
							if (isset($flip[1]) && $flip[1] < 1) {
								$output = "I have to flip at least one coin, <@!$thisuser>... :sweat:";
							}
							
							// output
							$message->channel->sendMessage($output);

					} // end coin flip
					
					//***** HELP FILE
					if ( preg_match("/^!help/",$thismessage) ) {
						
						// here comes some fugly formatting...
						$output .= "Hello <@!$thisuser>! I'm nicebot. I'm made by alleZSoyez.\n";
						$output .= "Here's a list of what I can do:\n\n";
						$output .= "**(some greeting) (ping me)** I'm a nice bot who loves to say hello to humans.\n\n";
						$output .= "**Doom** is a magic word.\n\n";
						$output .= "**!compliment <user>** I'll compliment whoever you want. If no one is specified, I'll compliment *you!*\n\n";
						$output .= "**!roll <#>d<#><+ or -#> <for initiative> <-v>** I'll roll dice for you.\n - Example: **!roll 2d20+5** to roll two d20 dice and add 5 to each result.\n - If you don't tell me how many to roll, I'll assume you wanted only one.\n - If you'd like to **view** each individual roll, add **-v** to the end.\n\n";
						$output .= "**!coinflip <#>** I'll flip a coin for you.\n - You can tell me how many coins you want, otherwise I'll just flip one.";

						$message->channel->sendMessage($output);						
					}
					
					//********* END COMMANDS
			//	} // end channel restriction
			} // end user id check
		});
	});

	$discord->run();
