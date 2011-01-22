<?php
$expires = 1; //time in day.
$debug = 0;
$host = "www.best-signatures.com";
$cachefile = "armory_cache";
$filepath = "wowimg"; //no "/";
/*
1 -> sign save in Subdirectory that named by id, and sign using time to name. all sign not delete.
2 -> save in one directory, delete old sign before renew.
*/
$savemethod = '1';
/*
id: php?id= can be [a-zA-Z0-9] no other ascii char allowed.
char: this is you character name
relam: you character relam name, we accept almost chars like space, chinese and other unicode char.
style: the style of sign, this is not necessary item.
*/
$char[stxs] = array( "id" => "stxs",
						"char" => "使徒新生",
						"relam" => "shadowmoon",
						"style" => 'avatar=wotlk&mainstat=ilvl&stat1=health&stat2=armor&stat3=mastery&bg_color1=#008000&bg_color2=#008000&bg_color3=#000000&bg_image=450x80space_bk13-450x80.jpg&bg_type=image&effect=noeffect&color1=#ffffff&color2=#ffffff&color3=#ff9900&color4=#c0c0c0&color5=#000000&color7=#ff0000&color8=#b3b3b3&others[title]=1&others[guild]=1&others[profs]=1&');
						
$char[stgl] = array( "id" => "stgl",
						"char" => "使徒歸來",
						"relam" => "shadowmoon",
						"style" => 'avatar=wotlk&mainstat=ilvl&stat1=health&stat2=meleeattackpower&stat3=mastery&bg_color1=#008000&bg_color2=#008000&bg_color3=#000000&bg_image=450x80space_bk13-450x80.jpg&bg_type=image&effect=noeffect&color1=#ffffff&color2=#ffffff&color3=#ff9900&color4=#c0c0c0&color5=#000000&color7=#ff0000&color8=#b3b3b3&others[title]=1&others[guild]=1&others[profs]=1&');


//default style.
$style = 'avatar=wotlk&mainstat=ilvl&stat1=health&stat2=meleeattackpower&stat3=mastery&bg_color1=#008000&bg_color2=#008000&bg_color3=#000000&bg_image=450x80space_bk13-450x80.jpg&bg_type=image&effect=noeffect&color1=#ffffff&color2=#ffffff&color3=#ff9900&color4=#c0c0c0&color5=#000000&color7=#ff0000&color8=#b3b3b3&others[title]=1&others[guild]=1&others[profs]=1&';



?>