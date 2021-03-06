<?php
require_once(get_file_loc('menu.inc'));

if (!isset($var['race_id'])) {
	SmrSession::updateVar('race_id', $player->getRaceID());
}
$raceID = $var['race_id'];

$template->assign('PageTopic','Ruling Council Of ' . Globals::getRaceName($raceID));

// echo menu
create_council_menu($raceID);

$RACES =& Globals::getRaces();
$raceRelations =& Globals::getRaceRelations($player->getGameID(),$raceID);

$peaceRaces = array();
$neutralRaces = array();
$warRaces = array();
foreach ($RACES as $otherRaceID => $raceInfo) {
	if($otherRaceID != RACE_NEUTRAL && $raceID != $otherRaceID) {
		if($raceRelations[$otherRaceID] >= RELATIONS_PEACE) {
			$peaceRaces[$otherRaceID] = $raceInfo;
		}
		else if($raceRelations[$otherRaceID] <= RELATIONS_WAR) {
			$warRaces[$otherRaceID] = $raceInfo;
		}
		else {
			$neutralRaces[$otherRaceID] = $raceInfo;
		}
	}
}

$template->assignByRef('PeaceRaces', $peaceRaces);
$template->assignByRef('NeutralRaces', $neutralRaces);
$template->assignByRef('WarRaces', $warRaces);

?>
