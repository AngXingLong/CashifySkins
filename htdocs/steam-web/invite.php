<?php

$cookie = file_get_contents("steam_cookie.txt");

print_r($cookie);
//preg_match('/sessionid/', $cookie, $matches);
preg_split('/\s\s+/', $cookie);
print_r($cookie);
				
?>