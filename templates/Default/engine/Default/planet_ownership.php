<?php

if (!$Planet->hasOwner()) { ?>
	<p>
		This planet is unclaimed.<?php
		if (isset($PlayerPlanet)) { ?>
			<br />If you claim it, you will lose ownership of the planet in Sector #<?php echo $PlayerPlanet; ?>!<?php
		} ?>
	</p>
	<form method="POST" action="<?php echo $ProcessingHREF; ?>">
		<input type="submit" name="action" value="Take Ownership" id="InputFields" />
	</form><?php
}
else {
	if ($Planet->getOwnerID() != $Player->getAccountID()) { ?>
		<p><?php echo SmrPlayer::getPlayer($Planet->getOwnerID(), $Planet->getGameID())->getLinkedDisplayName(false); ?> owns this planet.</p>
		<p>
			You can claim the planet when you enter the correct password.<?php
			if (isset($PlayerPlanet)) { ?>
				<br />If you do, you will lose ownership of the planet in Sector #<?php echo $PlayerPlanet; ?>!<?php
			} ?>
		</p>
		<form method="POST" action="<?php echo $ProcessingHREF; ?>">
			<input type="text" name="password" id="InputFields">&nbsp;&nbsp;&nbsp;
			<input type="submit" name="action" value="Take Ownership" id="InputFields" />
		</form><?php
	}
	else { ?>
		<p>You own this planet!</p>
		<form method="POST" action="<?php echo $ProcessingHREF; ?>">
			<input type="text" name="password" value="<?php echo htmlspecialchars($Planet->getPassword()); ?>" id="InputFields" />&nbsp;&nbsp;&nbsp;
			<input type="submit" name="action" value="Set Password" id="InputFields" />
		</form>
		<br />

		<form method="POST" action="<?php echo $ProcessingHREF; ?>">
			<input type="text" name="name" value="<?php echo htmlspecialchars($Planet->getName()); ?>" id="InputFields" />&nbsp;&nbsp;&nbsp;
			<input type="submit" name="action" value="Rename" id="InputFields" />
		</form><?php
	}
}

?>
