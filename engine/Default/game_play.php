<?php
$template->assign('PageTopic','Play Game');

if(isset($var['errorMsg'])) {
	$template->assign('ErrorMessage',$var['errorMsg']);
}
if (isset($var['msg'])) {
	$template->assign('Message',$var['msg']);
}

$template->assign('UserRankingLink',SmrSession::getNewHREF(create_container('skeleton.php', 'rankings_view.php')));
$template->assign('UserRankName',$account->getRankName());

$games = array();
$games['Play'] = array();
$game_id_list = array();
$db->query('SELECT end_date, game_id, game_name, game_speed, game_type
			FROM game JOIN player USING (game_id)
			WHERE account_id = '.$db->escapeNumber(SmrSession::$account_id).'
				AND end_date >= ' . $db->escapeNumber(TIME) . '
			ORDER BY start_date DESC');
if ($db->getNumRows() > 0) {
	while ($db->nextRecord()) {
		$game_id = $db->getField('game_id');
		$games['Play'][$game_id]['ID'] = $game_id;
		$games['Play'][$game_id]['Name'] = $db->getField('game_name');
		$games['Play'][$game_id]['Type'] = $db->getField('game_type');
		$games['Play'][$game_id]['EndDate'] = date(DATE_FULL_SHORT_SPLIT,$db->getField('end_date'));
		$games['Play'][$game_id]['Speed'] = $db->getField('game_speed');
		$games['Play'][$game_id]['Type'] = $db->getField('game_type');

		$container = create_container('game_play_processing.php');
		$container['game_id'] = $game_id;
		if($games['Play'][$game_id]['Type'] == '1.2')
			$games['Play'][$game_id]['PlayGameLink'] = 'loader2.php?sn=' . SmrSession::addLink($container);
		else
			$games['Play'][$game_id]['PlayGameLink'] = SmrSession::getNewHREF($container);

		// creates a new player object
		$curr_player =& SmrPlayer::getPlayer(SmrSession::$account_id, $game_id);
		$curr_ship =& $curr_player->getShip();

		// update turns for this game
		$curr_player->updateTurns();

		// generate list of game_id that this player is joined
		$game_id_list[] = $game_id;

		$db2 = new SmrMySqlDatabase();
		$db2->query('SELECT count(*) as num_playing
					FROM player
					WHERE last_cpl_action >= ' . $db->escapeNumber(TIME - 600) . '
						AND game_id = '.$db->escapeNumber($game_id));
		$db2->nextRecord();
		$games['Play'][$game_id]['NumberPlaying'] = $db2->getField('num_playing');

		// create a container that will hold next url and additional variables.

		$container_game = array();
		$container_game['url'] = 'skeleton.php';
		$container_game['body'] = 'game_stats.php';
		$container_game['game_id'] = $game_id;
		$games['Play'][$game_id]['GameStatsLink'] = SmrSession::getNewHREF($container_game);
		$games['Play'][$game_id]['Maintenance'] = $curr_player->getTurns();
		$games['Play'][$game_id]['LastActive'] = format_time(TIME-$curr_player->getLastCPLAction(),TRUE);
		$games['Play'][$game_id]['LastMovement'] = format_time(TIME-$curr_player->getLastActive(),TRUE);

	}
}

// CLASSIC COMPATIBILITY
if(false&&USE_COMPATIBILITY) {
	foreach(Globals::getCompatibilityDatabases('Game') as $databaseClassName => $databaseInfo) {
		require_once(get_file_loc($databaseClassName.'.class.inc'));
		$db = new $databaseClassName();
		$db->query('SELECT DATE_FORMAT(end_date, \'%c/%e/%Y\') as end_date, game.game_id as game_id, game_name, game_speed, game_type
					FROM game JOIN player USING (game_id)
					WHERE account_id = '.$db->escapeNumber(SmrSession::$old_account_id).'
						AND end_date >= ' . $db->escapeNumber(TIME) . '
					ORDER BY start_date DESC');
		if ($db->getNumRows() > 0) {
			require_once(get_file_loc('smr_player.inc',$databaseInfo['GameType']));
			require_once(get_file_loc('smr_ship.inc',$databaseInfo['GameType']));
			while ($db->nextRecord()) {
				$game_id = $db->getField('game_id');
				$index = $databaseClassName.$game_id;
				$games['Play'][$index]['ID'] = $game_id;
				$games['Play'][$index]['Name'] = $db->getField('game_name');
				$games['Play'][$index]['Type'] = $db->getField('game_type');
				$games['Play'][$index]['EndDate'] = $db->getField('end_date');
				$games['Play'][$index]['Speed'] = $db->getField('game_speed');
				$games['Play'][$index]['Type'] = $db->getField('game_type');

				$container = array();
				$container['game_id'] = $game_id;
				$container['url'] = 'game_play_processing.php';
				$games['Play'][$index]['PlayGameLink'] = 'loader2.php?sn=' . SmrSession::addLink($container);

				// creates a new player object
				$curr_player = new SMR_PLAYER(SmrSession::$old_account_id, $game_id);
				$curr_ship = new SMR_SHIP(SmrSession::$old_account_id, $game_id);

				// update turns for this game
				$curr_player->update_turns($curr_ship->speed);

				// generate list of game_id that this player is joined
				$game_id_list[] = $game_id;

				$db2 = new $databaseClassName();
				$db2->query('SELECT count(*) as num_playing FROM player
							WHERE last_active >= ' . $db->escapeNumber(TIME - 600) . '
								AND game_id = '.$db->escapeNumber($game_id));
				$db2->nextRecord();
				$games['Play'][$index]['NumberPlaying'] = $db2->getField('num_playing');

				// create a container that will hold next url and additional variables.

				$container_game = array();
				$container_game['url'] = 'skeleton.php';
				$container_game['body'] = 'game_stats.php';
				$container_game['game_id'] = $game_id;
				$games['Play'][$index]['GameStatsLink'] = SmrSession::getNewHREF($container_game);
				$games['Play'][$index]['Maintenance'] = $curr_player->turns;
				$games['Play'][$index]['LastActive'] = format_time(TIME-$curr_player->last_active,TRUE);
				$games['Play'][$index]['LastMovement'] = format_time(TIME-$curr_player->last_active,TRUE);

			}
		}
	}
	$db = new SmrMySqlDatabase();
}
if(empty($games['Play']))
	unset($games['Play']);
//End Compat

if (count($game_id_list) > 0) {
	$db->query('SELECT start_date, end_date, game.game_id as game_id, game_name, max_players, game_type, credits_needed, game_speed
				FROM game
				WHERE game_id NOT IN (' . $db->escapeArray($game_id_list) . ')
					AND end_date >= ' . $db->escapeNumber(TIME) . '
					AND enabled = ' . $db->escapeBoolean(true) . '
				ORDER BY start_date DESC');
}
else {
	$db->query('SELECT start_date, end_date, game.game_id as game_id, game_name, max_players, game_type, credits_needed, game_speed
				FROM game
				WHERE end_date >= ' . $db->escapeNumber(TIME) . '
					AND enabled = ' . $db->escapeBoolean(true) . '
				ORDER BY start_date DESC');
}

// ***************************************
// ** Join Games
// ***************************************

// are there any results?
if ($db->getNumRows() > 0) {
	$games['Join'] = array();
	// iterate over the resultset
	while ($db->nextRecord()) {
		$game_id = $db->getField('game_id');
		$games['Join'][$game_id]['ID'] = $game_id;
		$games['Join'][$game_id]['Name'] = $db->getField('game_name');
		$games['Join'][$game_id]['StartDate'] = date(DATE_FULL_SHORT_SPLIT,$db->getField('start_date'));
		$games['Join'][$game_id]['EndDate'] = date(DATE_FULL_SHORT_SPLIT,$db->getField('end_date'));
		$games['Join'][$game_id]['MaxPlayers'] = $db->getField('max_players');
		$games['Join'][$game_id]['Type'] = $db->getField('game_type');
		$games['Join'][$game_id]['Speed'] = $db->getField('game_speed');
		$games['Join'][$game_id]['Credits'] = $db->getField('credits_needed');
		// create a container that will hold next url and additional variables.
		$container = array();
		$container['game_id'] = $game_id;
		$container['url'] = 'skeleton.php';
		$container['body'] = 'game_join.php';

		$games['Join'][$game_id]['JoinGameLink'] = SmrSession::getNewHREF($container);
	}
}

// ***************************************
// ** Previous Games
// ***************************************

$games['Previous'] = array();

//New previous games
$db->query('SELECT start_date, end_date, game_name, game_speed, game_id ' .
		'FROM game WHERE end_date < '.$db->escapeNumber(TIME).' ORDER BY game_id DESC');
if ($db->getNumRows()) {
	while ($db->nextRecord()) {
		$game_id = $db->getField('game_id');
		$games['Previous'][$game_id]['ID'] = $game_id;
		$games['Previous'][$game_id]['Name'] = $db->getField('game_name');
		$games['Previous'][$game_id]['StartDate'] = date(DATE_DATE_SHORT,$db->getField('start_date'));
		$games['Previous'][$game_id]['EndDate'] = date(DATE_DATE_SHORT,$db->getField('end_date'));
		$games['Previous'][$game_id]['Speed'] = $db->getField('game_speed');
		// create a container that will hold next url and additional variables.
		$container = create_container('skeleton.php');
		$container['game_id'] = $container['GameID'] = $game_id;
		$container['game_name'] = $games['Previous'][$game_id]['Name'];

		$container['body'] = 'hall_of_fame_new.php';
		$games['Previous'][$game_id]['PreviousGameHOFLink'] = SmrSession::getNewHREF($container);
		$container['body'] = 'news_read.php';
		$games['Previous'][$game_id]['PreviousGameNewsLink'] = SmrSession::getNewHREF($container);
		$container['body'] = 'game_stats.php';
		$games['Previous'][$game_id]['PreviousGameStatsLink'] = SmrSession::getNewHREF($container);
	}
}

if(USE_COMPATIBILITY) {
	foreach(Globals::getCompatibilityDatabases('History') as $databaseClassName => $databaseInfo) {
		require_once(get_file_loc($databaseClassName.'.class.inc'));
		//Old previous games
		$historyDB = new $databaseClassName();
		$historyDB->query('SELECT start_date, end_date, game_name, speed, game_id
							FROM game ORDER BY game_id DESC');
		if ($historyDB->getNumRows()) {
			while ($historyDB->nextRecord()) {
				$game_id = $historyDB->getField('game_id');
				$index = $databaseClassName.$game_id;
				$games['Previous'][$index]['ID'] = $game_id;
				$games['Previous'][$index]['Name'] = $historyDB->getField('game_name');
				$games['Previous'][$index]['StartDate'] = date(DATE_DATE_SHORT,$historyDB->getField('start_date'));
				$games['Previous'][$index]['EndDate'] = date(DATE_DATE_SHORT,$historyDB->getField('end_date'));
				$games['Previous'][$index]['Speed'] = $historyDB->getField('speed');
				// create a container that will hold next url and additional variables.
				$container = array();
				$container['url'] = 'skeleton.php';
				$container['game_id'] = $game_id;
				$container['HistoryDatabase'] = $databaseClassName;
				$container['game_name'] = $games['Previous'][$index]['Name'];
				$container['body'] = 'games_previous.php';

				$games['Previous'][$index]['PreviousGameLink'] = SmrSession::getNewHREF($container);
				$container['body'] = 'games_previous_hof.php';
				$games['Previous'][$index]['PreviousGameHOFLink'] = SmrSession::getNewHREF($container);
				$container['body'] = 'games_previous_news.php';
				$games['Previous'][$index]['PreviousGameNewsLink'] = SmrSession::getNewHREF($container);
				$container['body'] = 'games_previous_detail.php';
				$games['Previous'][$index]['PreviousGameStatsLink'] = SmrSession::getNewHREF($container);
			}
		}
	}
	$db = new SmrMySqlDatabase(); // restore database
}

$template->assign('Games',$games);

// ***************************************
// ** Voting
// ***************************************
$container = create_container('skeleton.php','vote.php');
$template->assign('VotingHref',SmrSession::getNewHREF($container));

$db->query('SELECT * FROM voting WHERE end > ' . $db->escapeNumber(TIME) . ' ORDER BY end DESC');
if($db->getNumRows()>0) {
	$db2 = new SmrMySqlDatabase();
	$votedFor=array();
	$db2->query('SELECT * FROM voting_results WHERE account_id = ' . $db->escapeNumber($account->getAccountID()));
	while ($db2->nextRecord()) {
		$votedFor[$db2->getField('vote_id')] = $db2->getField('option_id');
	}
	$voting = array();
	while ($db->nextRecord()) {
		$voteID = $db->getField('vote_id');
		$voting[$voteID]['ID'] = $voteID;
		$container = array();
		$container['body'] = 'game_play.php';
		$container['url'] = 'vote_processing.php';
		$container['vote_id'] = $voteID;
		$voting[$voteID]['HREF'] = SmrSession::getNewHREF($container);
		$voting[$voteID]['Question'] = $db->getField('question');
		$voting[$voteID]['TimeRemaining'] = format_time($db->getField('end') - TIME, true);
		$voting[$voteID]['Options'] = array();
		$db2->query('SELECT option_id,text,count(account_id) FROM voting_options LEFT OUTER JOIN voting_results USING(vote_id,option_id) WHERE vote_id = ' . $db->escapeNumber($db->getInt('vote_id')).' GROUP BY option_id');
		while ($db2->nextRecord()) {
			$voting[$voteID]['Options'][$db2->getField('option_id')]['ID'] = $db2->getField('option_id');
			$voting[$voteID]['Options'][$db2->getField('option_id')]['Text'] = $db2->getField('text');
			$voting[$voteID]['Options'][$db2->getField('option_id')]['Chosen'] = isset($votedFor[$db->getField('vote_id')]) && $votedFor[$voteID] == $db2->getField('option_id');
			$voting[$voteID]['Options'][$db2->getField('option_id')]['Votes'] = $db2->getField('count(account_id)');
		}
	}
	$template->assign('Voting',$voting);
}

// ***************************************
// ** Donation Link
// ***************************************

$template->assign('DonateLink', SmrSession::getNewHREF(create_container('skeleton.php', 'donation.php')));

// ***************************************
// ** Announcements View
// ***************************************
$container = array();
$container['url'] = 'skeleton.php';
$container['body'] = 'announcements.php';
$container['view_all'] = 'yes';
$template->assign('OldAnnouncementsLink',SmrSession::getNewHREF($container));

?>
