Each Space Merchant Realms round requires you to create a new Trader.<br />
To do this you must choose a name for yourself and select your race.<br />

<br /><?php
if ($Game->getDescription()) { ?>
	<h2>Round Description</h2>
	<p><?php echo bbifyMessage($Game->getDescription()); ?></p><?php
}?>

<table class="standard">
	<tr class="center">
		<th>Start Date</th>
		<th>Start Turns Date</th>
		<th>End Date</th>
		<th>Max Turns</th>
		<th>Start Turn Hours</th>
		<th>Max Players</th>
		<th>Alliance Max Players</th>
		<th>Alliance Max Vets</th>
	</tr>
	<tr class="center">
		<td width="12%"><?php echo date(DATE_FULL_SHORT_SPLIT,$Game->getStartDate()); ?></td>
		<td width="12%"><?php echo date(DATE_FULL_SHORT_SPLIT,$Game->getStartTurnsDate()); ?></td>
		<td width="12%"><?php echo date(DATE_FULL_SHORT_SPLIT,$Game->getEndDate()); ?></td>
		<td><?php echo $Game->getMaxTurns(); ?></td>
		<td><?php echo $Game->getStartTurnHours(); ?></td>
		<td><?php echo $Game->getMaxPlayers(); ?></td>
		<td><?php echo $Game->getAllianceMaxPlayers(); ?></td>
		<td><?php echo $Game->getAllianceMaxVets(); ?></td>
	</tr>
</table><br/>
<table class="standard">
	<tr class="center">
		<th>Type</th>
		<th>Game Speed</th>
		<th>Credits Required</th>
		<th>Stats Ignored</th>
		<th>Starting Credits</th>
	</tr>
	<tr class="center">
		<td><?php echo $Game->getGameType(); ?></td>
		<td><?php echo $Game->getGameSpeed(); ?></td>
		<td><?php echo $Game->getCreditsNeeded(); ?></td>
		<td><?php echo $Game->isIgnoreStats() ? 'Yes' : 'No'; ?></td>
		<td><?php echo number_format($Game->getStartingCredits()); ?></td>
	</tr>
</table><br />

<form<?php if(isset($JoinGameFormHref)){ ?> name="JoinGameForm" method="POST" action="<?php echo $JoinGameFormHref; ?>"<?php } ?>>

	<table class="nobord nohpad">
		<tr>
			<td>
				<h2>Rules</h2>
				<p>
					The following Trader names are <span class="red">prohibited</span>:<br />
					<ul>
						<li>Names that convey an attitude towards yourself or someone else - such as "Lamer" or "Shadow Sucks".</li>
						<li>Names that make excessive use of special characters, eg. "~-=[Daron]=-~" should be "Daron" instead.</li>
						<li>Names that look similar or identical to another player in an attempt to trick other players.</li>
						<li>Names with references to "out of character" information or that would make sense only to the player, not the character - such as "SpaceGamer" or "SMR Rules".</li>
					</ul>
					Names that violate these rules will be changed by the admins and, in extreme cases, abuse of the naming process will result in your account being banned.
					<br />
					For more information and a complete list of game rules, consult the Space Merchant Realms Wiki under <a href="<?php echo WIKI_URL; ?>/rules" target="_blank">Rules<img src="images/silk/help.png" width="16" height="16" alt="Wiki Link" title="Goto SMR Wiki: Rules"/></a>
				</p>

				<h2>Choosing Your Race</h2>
				<p>
					The race you select for your Trader affects the following:<br />
					<ul>
						<li>The <span class="yellow">galaxy</span> you will start the game in.</li>
						<li>The <span class="yellow">ports</span> where you can trade - you can only trade with your own race and races you have peace with.</li>
						<li>The <span class="yellow">ships</span> you can purchase - you cannot purchase a ship of another race.</li>
						<li>The <span class="yellow">weapons</span> you can arm your ship with - you can only buy weapons from your own race and races you have peace with.</li>
					</ul>
					Races that appear strongest may have certain disadvantages while races that appear weakest have special benefits. Listed below are some of the basic characteristics of each race.<br />
					<ul>
						<li><span class="yellow">Alskant</span> - Large variety of hardware but no dedicated warship. Trade bonuses with all races.</li>
						<li><span class="yellow">Creonti</span> - Cute and cuddly with lots of firepower. Reinforced ships with heavy armour plating.</li>
						<li><span class="yellow">Human</span> - Jump drive technology which enables fast inter-galactic movement.</li>
						<li><span class="yellow">Ik'Thorne</span> - Most overall defense, relying heavily on swarms of combat drones.</li>
						<li><span class="yellow">Salvene</span> - Illusion Generator technology which allows ships to mask their full strength.</li>
						<li><span class="yellow">Thevian</span> - Fastest racial ships in the universe.</li>
						<li><span class="yellow">WQ Human</span> - Cloaking Device technology which allows ships to hide from lower level traders.</li>
						<li><span class="yellow">Nijarin</span> - High firepower and Drone Communication Scrambler technology offsets lower defenses.</li>
					</ul>
					A full description and ship list for each race can be found on the SMR Wiki <a href="<?php echo WIKI_URL; ?>/game-guide/races" target="_blank">Races<img src="images/silk/help.png" width="16" height="16" alt="Wiki Link" title="Goto SMR Wiki: Races"/></a> page.<br />
				</p>

				<h2>Create Your Trader</h2>
				<p>Now it is time for you to create your Trader and begin your quest for riches, fame and glory! Where will your destiny take you?</p>
				<table>
					<tr>
						<td align="right"><b>Name:</b>&nbsp;</td>
						<td><input type="text" name="player_name" maxlength="32" class="InputFields"<?php if(!isset($JoinGameFormHref)){ ?>disabled="disabled"<?php } ?>></td>
						<td rowspan="4" class="standard"><img id="race_image" name="race_image" src="images/race/race1.gif" alt="Please select a race."></td>
					</tr>
					<tr>
						<td align="right"><b>Race:</b>&nbsp;</td>
						<td>
						<select name="race_id" id="InputFields" size="1" OnChange="go();">
							<?php /*<option value="1">[please select]</option> */
							foreach($Races as $Race) {
								?><option value="<?php echo $Race['ID']; if($Race['Selected']){ ?>" selected="selected<?php } ?>"><?php echo $Race['Name']; ?> (<?php echo $Race['NumberOfPlayers']; ?> Traders)<?php
							} ?>
						</select>
						</td>
					</tr>
					
					<tr>
						<td align="right">&nbsp;</td>
						<td><?php
						if(isset($JoinGameFormHref)) {
							?><input type="submit" name="action" value="Create Player" class="InputFields" /><?php
						}
						else {
							?><b>This game has not started yet.</b><?php
						} ?>
						</td>
					</tr>
					
					<tr>
						<td colspan="2">
							<div id="race_descr" class="InputFields" style="width:300px;height:275px;border:0;"></div>
						</td>
					</tr>
					
				</table>
			</td>
		</tr>

		<tr>
			<td align=center>
				<table>
					<tr>
						<td align=center colspan=4 class="center">Trading</td>
					</tr>
					<tr>
						<td align=left>Combat<br />
						Strength</td>
						<td align=center colspan=2>
							<img width="440" height="440" border="0" name="graph" id="graphframe" src="images/race/graph/graph1.gif" alt="Race overview" />
						</td>
						<td align=right>Hunting</td>
					</tr>
					<tr>
						<td align=center colspan=4 class="center">Utility</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>

<script type="text/javascript">
var	desc = new Array(<?php echo $RaceDescriptions; ?>);
function go() {
	var race_id = document.forms[0].race_id.options[document.forms[0].race_id.selectedIndex].value;
	document.getElementById('race_image').src = "images/race/race" + race_id + ".jpg";
	document.getElementById('graphframe').src = "images/race/graph/graph" + race_id + ".gif";
	document.getElementById('race_descr').innerHTML = desc[race_id - 1];
}
go();
</script>
