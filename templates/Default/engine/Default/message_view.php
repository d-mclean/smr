<?php
if (isset($MessageBoxes)) { ?>
	<p>Please choose your Message folder!</p>

	<table id="folders" class="standard">
		<thead>
			<tr>
				<th class="sort" data-sort="name">Folder</th>
				<th class="sort" data-sort="messages">Messages</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody class="list"><?php
			foreach ($MessageBoxes as $MessageBox) { ?>
				<tr id="<?php echo str_replace(' ','-',$MessageBox['Name']);?>" class="ajax<?php if($MessageBox['HasUnread']) { ?> bold<?php } ?>">
					<td class="name">
						<a href="<?php echo $MessageBox['ViewHref']; ?>"><?php echo $MessageBox['Name']; ?></a>
					</td>
					<td class="messages center yellow"><?php echo $MessageBox['MessageCount']; ?></td>
					<td><a href="<?php echo $MessageBox['DeleteHref']; ?>">Empty Read Messages</a></td>
				</tr><?php
			} ?>
		</tbody>
	</table>
	<p><a href="<?php echo $ManageBlacklistLink; ?>">Manage Player Blacklist</a></p>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>
	<script>
	var list = new List('folders', {
		valueNames: ['name', 'messages'],
		sortFunction: function(a, b, options) {
			return list.utils.naturalSort(a.values()[options.valueName].replace(/<.*?>|,/g,''), b.values()[options.valueName].replace(/<.*?>|,/g,''), options);
		}
	});
	</script><?php
}
else {
	if ($MessageBox['Type'] == MSG_GLOBAL) { ?>
		<form name="IgnoreGlobalsForm" method="POST" action="<?php echo $IgnoreGlobalsFormHref; ?>">
			<div align="center">Ignore global messages?&nbsp;&nbsp;
				<input type="submit" name="action" value="Yes" id="InputFields"<?php if ($ThisPlayer->isIgnoreGlobals()) { ?> style="background-color:green;"<?php } ?> />&nbsp;<input type="submit" name="action" value="No" id="InputFields"<?php if (!$ThisPlayer->isIgnoreGlobals()) { ?> style="background-color:green;"<?php } ?> />
			</div>
		</form><?php
	} ?>
	<br />
	<form name="MessageDeleteForm" method="POST" action="<?php echo $MessageBox['DeleteFormHref']; ?>">
		<table class="fullwidth center">
			<tr>
				<td style="width: 30%" valign="middle"><?php
					if(isset($PreviousPageHREF)) {
						?><a href="<?php echo $PreviousPageHREF; ?>"><img src="images/album/rew.jpg" alt="Previous Page" border="0"></a><?php
					} ?>
				</td>
				<td>
					<input type="submit" name="action" value="Delete" id="InputFields" />&nbsp;<select name="action" size="1" id="InputFields">
																						<option>Marked Messages</option>
																						<option>All Messages</option>
																					</select>
					<p>You have <span class="yellow"><?php echo $MessageBox['TotalMessages']; ?></span> <?php echo pluralise('message', $MessageBox['TotalMessages']); if($MessageBox['TotalMessages']!=$MessageBox['NumberMessages']){ ?> of which <span class="yellow"><?php echo $MessageBox['NumberMessages']; ?></span> <?php echo pluralise('is', $MessageBox['NumberMessages']); ?> being displayed<?php } ?>.</p>
				</td>
				<td style="width: 30%" valign="middle"><?php
					if(isset($NextPageHREF)) {
						?><a href="<?php echo $NextPageHREF; ?>"><img src="images/album/fwd.jpg" alt="Next Page" border="0"></a><?php
					} ?>
				</td>
			</tr>
		</table><?php
		
		if (isset($MessageBox['ShowAllHref'])) {
			?><div class="buttonA"><a class="buttonA" href="<?php echo $MessageBox['ShowAllHref'] ?>">&nbsp;Show all Messages&nbsp;</a></div><br /><br /><?php
		} ?>
		<table class="standard fullwidth"><?php
			if(isset($MessageBox['Messages'])) {
				foreach($MessageBox['Messages'] as &$Message) { ?>
					<tr>
						<td width="10"><input type="checkbox" name="message_id[]" value="<?php echo $Message['ID']; ?>" /><?php if($Message['Unread']) { ?>*<?php } ?></td>
						<td class="noWrap" width="100%"><?php
							if(isset($Message['ReceiverDisplayName'])) {
								?>To: <?php echo $Message['ReceiverDisplayName'];
							}
							else {
								?>From: <?php echo $Message['SenderDisplayName'];
							} ?>
						</td>
						<td class="noWrap"<?php if(!isset($Message['Sender'])) { ?> colspan="4"<?php } ?>>Date: <?php echo $Message['SendTime']; ?></td>
						<?php
						if (isset($Message['Sender'])) { ?>
							<td>
								<a href="<?php echo $Message['ReportHref']; ?>"><img src="images/report.png" width="16" height="16" border="0" align="right" title="Report this message to an admin" /></a>
							</td>
							<td>
								<a href="<?php echo $Message['BlacklistHref']; ?>">Blacklist Player</a>
							</td>
							<td>
								<a href="<?php echo $Message['ReplyHref']; ?>">Reply</a>
							</td><?php
						} ?>
					</tr>
					<tr>
						<td colspan="6"><?php echo bbifyMessage($Message['Text']); ?></td>
					</tr>
					<?php
				} unset($Message);
			} ?>
		</table>

		<table class="fullwidth center">
			<tr>
				<td style="width: 30%" valign="middle"><?php
					if(isset($PreviousPageHREF)) {
						?><a href="<?php echo $PreviousPageHREF; ?>"><img src="images/album/rew.jpg" alt="Previous Page" border="0"></a><?php
					} ?>
				</td>
				<td>
				</td>
				<td style="width: 30%" valign="middle"><?php
					if(isset($NextPageHREF)) {
						?><a href="<?php echo $NextPageHREF; ?>"><img src="images/album/fwd.jpg" alt="Next Page" border="0"></a><?php
					} ?>
				</td>
			</tr>
		</table>
	</form><?php
} ?>
