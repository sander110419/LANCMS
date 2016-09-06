<?php
// this file is not needed if !ALP_TOURNAMENT_MODE_COMPUTER_GAMES
include "include/_universal.php";
$x = new universal("tournaments maps","map",1);
if($x->is_secure()) {
	if(empty($_POST)||empty($_POST["tourneyid"])) {
		$x->display_slim("incorrect usage.","disp_tournament.php");
	} else {
		$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$_POST["tourneyid"]."'"));
		if(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])) {
			include "include/cl_validation.php";
			$valid = new validate();
			if($tournament["ttype"]==1) {
				$bracket = "w";
			} else {
				$bracket = "l";
			}
			$rounds = $dbc->database_num_rows($dbc->database_query("SELECT DISTINCT rnd FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='".$bracket."'"));
			$allgood = true;
			if($tournament["ttype"]==5) $startd = 0;
			else $startd = 1;
			for($i=$startd;$i<=$rounds;$i++) {
				if(!$dbc->database_query("UPDATE tournament_matches SET map='".$valid->get_value("map_".$i)."' WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$i."' AND bracket='".$bracket."'")) {
					$allgood = false;
				}
				if(!$dbc->database_query("UPDATE tournament_matches SET map=NULL WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$i."' AND bracket='".($bracket=="w"?"l":"w")."'")) {
					$allgood = false;
				}
			}
			if($allgood) {
				$str = "map update successful.";
			} else {
				$str = "error! maps not updated.";
			}
			$x->display_slim($str,"disp_tournament.php?id=".$tournament["tourneyid"]);
		} else {
			$x->display_slim("unauthorized.","disp_tournament.php?id=".$tournament["tourneyid"]);
		}
	}
} else {
	$x->display_slim("you are not authorized to view this page.");
} ?>