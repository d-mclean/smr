<?php
try {

	// ********************************
	// *
	// * I n c l u d e s   h e r e
	// *
	// ********************************

	require_once('config.inc');
	require_once(LIB . 'Default/smr.inc');
	require_once(get_file_loc('SmrAccount.class.inc'));


	// get this user from db
	$login = $_REQUEST['login'];
	// creates a new user account object
	$account =& SmrAccount::getAccountByName($login);
	$email = $_REQUEST['email'];
	if ($account==null || $account->getEmail() != $email) {
		// unknown user
		header('Location: '.URL.'/error.php?msg=' . rawurlencode('User does not exist'));
		exit;

	}

	$account->generatePasswordReset();

	$resetURL = URL.'/reset_password.php?login='.$account->getLogin().'&resetcode='.$account->getPasswordReset();
	// send email with password to user
	mail($email, 'Space Merchant Realms Password',
		 'A user from ' . getIpAddress() . ' requested to reset your password!'.EOL.EOL.
		 '   Your password reset code is: ' . $account->getPasswordReset().EOL.
		 '   You can use this url: '.$resetURL .EOL.EOL.
		 'The Space Merchant Realms server is on the web at '.URL.'/',
		 'From: support@smrealms.de');

	header('Location: '.URL.'/reset_password.php');
	exit;

}
catch(Exception $e) {
	handleException($e);
}
?>
