<?php
require_once('MySqlDatabase.class.inc');
require_once(CONFIG . 'SmrMySqlSecrets.inc');

class SmrSessionMySqlDatabase extends MySqlDatabase {
	// add static members via traits
	use SmrMySqlSecrets;
	public function SmrSessionMySqlDatabase() {
		parent::__construct(self::$host, self::$user, self::$password, self::$databaseName,
		                    self::$port, self::$socket);
	}
}
?>
