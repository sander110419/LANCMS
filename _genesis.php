<?php
//General Genesis Settings
$this->settings['email_allow']		= 0;						//Allow this program to send email.
$this->settings['email_override']	= '';		//Put an email address here to override all emails to be delivered to it.
$this->settings['email_errorsTo']	= '';		//Email to email errors to.
$this->settings['email_errorsFrom']	= $lan['email'];		//Where should the errors come from.
$this->settings['app_name'] = 'ALP';
$this->database['debug'] = 1;  							//Put Genesis database engine into debug mode.
?>