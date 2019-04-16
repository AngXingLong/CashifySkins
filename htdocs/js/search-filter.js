function create_drop_down(value,parameters){
	var output = "<select";
	if(parameters["id"]){
		output += " id='"+parameters["id"]+"'";
	}
	if(parameters["class"]){
		output += " class='"+parameters["class"]+"'";
	}
	if(parameters["name"]){
		output += " name='"+parameters["name"]+"'";
	}
	if(parameters["onchange"]){
		output += " onchange='"+parameters["onchange"]+"'";
	}
	
	output += ">";
	
	if(parameters["identifier"]){
		output += "<option value=''>"+parameters["identifier"]+"</option>";
	}

	for(k in value){
		var v = value[k];
		output += "<option value='"+v+"'>"+v+"</option>";
	}
	
	output += "</select>";
	
	return output;
}


function steam_filter(){
	var output = "";
	var card = ["Normal","Foil"];
	var rarity = ["Common","Uncommon","Rare","Extraordinary","Precious","Unparalleled"];
	var type = ["Emoticon","Profile Background","Booster Pack"];
	
	output += create_drop_down(card,{"id":"card","class":"filter_small","identifier":"Card:"});
	output += create_drop_down(rarity,{"id":"rarity","class":"filter_small","identifier":"Rarity:"});
	output += create_drop_down(type,{"id":"type","class":"filter_small","identifier":"Type:"});
	
	return output;
	
}

function tf_filter(){
	var output  = "";
	
	var tf_class = ["Soldier","Pyro","Scout","Sniper","Medic","Spy","Engineer","Demoman"];
	var exterior = ["Factory New","Minimal Wear","Field-Tested","Well-Worn","Battle-Scarred"];
	var quality = ["Decorated","Strange","Genuine","Vintage"];
	var grade = ["Civilian","Freelance","Mercenary","Commando","Assassin","Elite"];
	
	output += create_drop_down(tf_class,{"id":"class","class":"filter_medium","identifier":"Class:"});
	output += "<br>";
	output += create_drop_down(exterior,{"id":"exterior","class":"filter_small","identifier":"Exterior:"});
	output += create_drop_down(quality,{"id":"quality","class":"filter_small","identifier":"Quality:"});
	output += create_drop_down(grade,{"id":"grade","class":"filter_small","identifier":"Grade:"});

	return output;
}

function dota_filter(){
	var output = "";
	
	var heroes = ["Ancient Apparition","Anti-Mage","Axe","Bane","Batrider","Beastmaster","Bloodseeker","Bounty Hunter","Brewmaster","Bristleback","Broodmother","Centaur Warrunner","Chaos Knight","Chen","Clinkz","Clockwerk","Crystal Maiden","Dark Seer","Dazzle","Death Prophet","Disruptor","Doom","Dragon Knight","Drow Ranger","Earth Spirit","Earthshaker","Elder Titan","Ember Spirit","Enchantress","Enigma","Faceless Void","Gyrocopter","Huskar","Invoker","Io","Jakiro","Juggernaut","Keeper of the Light","Kunkka","Legion Commander","Leshrac","Lich","Lifestealer","Lina","Lion","Lone Druid","Luna","Lycan","Magnus","Medusa","Meepo","Mirana","Morphling","Naga Siren","Nature's Prophet","Necrophos","Night Stalker","Nyx Assassin","Ogre Magi","Omniknight","Oracle","Outworld Devourer","Phantom Assassin","Phantom Lancer","Phoenix","Puck","Pudge","Pugna","Queen of Pain","Razor","Riki","Rubick","Sand King","Shadow Demon","Shadow Fiend","Shadow Shaman","Silencer","Skywrath Mage","Slardar","Slark","Sniper","Spectre","Spirit Breaker","Storm Spirit","Sven","Techies","Templar Assassin","Terrorblade","Tidehunter","Timbersaw","Tinker","Tiny","Treant Protector","Troll Warlord","Tusk","Undying","Ursa","Vengeful Spirit","Venomancer","Viper","Visage","Warlock","Weaver","Windranger","Winter Wyvern","Witch Doctor","Wraith King","Zeus"];
	var quality = ["Auspicious","Genuine","Cursed","Frozen","Unusual","Corrupted","Elder","Exalted"];
	var rarity = ["Uncommon","Rare","Mythical","Immortal","Legendary","Arcana"];
	var type = ["Loading Screen","Courier","Bundle","Taunt","Ward","Treasure","Cursor Pack","Announcer","HUD Skin","Tool","Music"];
	
	output += create_drop_down(heroes,{"id":"hero","class":"filter_medium","identifier":"Hero:"});
	output += "<br>";
	output += create_drop_down(quality,{"id":"quality","class":"filter_small","identifier":"Quality:"});
	output += create_drop_down(rarity,{"id":"rarity","class":"filter_small","identifier":"Rarity:"});
	output += create_drop_down(type,{"id":"type","class":"filter_small","identifier":"Type:"});
	
	return output;
}

function csgo_filter(){
	
	var menu = {
	"Pistols":["CZ75-Auto","Desert Eagle","Dual Berettas","Glock-18","USP-S","P2000","P250","Five-Seven","Tec-9"],
	"Rifles":["AWP","AK-47","M4A1-S","M4A4","G3SG1","AUG","Galil AR","FAMAS","SG553","SCAR-20","Schmidt Scout"],
	"Smg":["MAC-10","PP-Bizon","MP7","MP9","UMP-7","P90"],
	"Heavy":["Nova","XM1014","MAG-7","Sawed-Off","M249","Negev"],
	"Knives":["Gut Knife","Flip Knife","Huntsman Knife","Bayonet","M9 Bayonet","Butterfly Knife","Karambit"],
	"Others":["Key","Cases","Music Kit"]
	};
	
	var output = "<div id ='filtermenu'><ul>";
	
	for(cat in menu){
		output += "<li>"+cat+"<ul>";
		for(k in menu[cat]){
			var value = menu[cat][k];
			output += "<li onClick=\"addtosearch('"+value+"') \">"+value+"</li>";
		}
		output += "</ul></li>";
	}

	output += "</ul></div>";
	
	var exterior = ["Factory New","Minimal Wear","Field-Tested","Well-Worn","Battle-Scarred"];
	output += create_drop_down(exterior,{"id":"exterior","class":"filter_small","identifier":"Exterior:"});
	
	var quality =["Mil-Spec Grade","Industrial Grade","Restricted","Classified","Covert","Base Grade","High Grade","Exotic","Remarkable","Contraband"];
	output += create_drop_down(quality,{"id":"quality","class":"filter_small","identifier":"Quality:"});

	output += "<select id='stattrack' class='filter_small'>";
	output += "<option value=''>Stattrack</option>";
	output += "<option value='StatTrak™'>Stattrack Only</option>";
	output += "</select>";

	return output;

}

