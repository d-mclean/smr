Announcements are displayed to all users the next time they log in.<br /><br />
<?php if(isset($Preview)) { ?><table class="standard"><tr><td><?php echo bbifyMessage($Preview); ?></td></tr></table><br /><?php } ?>
<form name="AnnouncementCreateForm" method="POST" action="<?php echo $AnnouncementCreateFormHref; ?>">
	<textarea spellcheck="true" name="message" spellcheck="true" id="InputFields"><?php if(isset($Preview)) { echo $Preview; } ?></textarea><br />
	<input type="submit" name="action" value="Create announcement" id="InputFields" />&nbsp;<input type="submit" name="action" value="Preview announcement" id="InputFields" />
</form>
