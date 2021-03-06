CREATE TABLE alliance_has_op_response (
	alliance_id SMALLINT(5) unsigned NOT NULL,
	game_id TINYINT(3) unsigned NOT NULL,
	account_id SMALLINT(5) unsigned NOT NULL,
	response ENUM('YES','NO','MAYBE') NOT NULL,
	PRIMARY KEY (alliance_id, game_id, account_id)
) ENGINE=InnoDB;

ALTER TABLE alliance_has_op
  DROP yes,
  DROP no,
  DROP maybe;