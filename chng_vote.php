<?php
require_once 'include/_universal.php';
$x = new universal('','',1);
if ($x->is_secure()) { 
	if (empty($_POST)) {
		$x->display_top(); ?>
		you must submit a vote to participate in the poll.  go back to <a href="polls.php">polls</a>.<br /><br />
		<?php
		$x->display_bottom();
	} else {
		include 'include/cl_validation.php';
		$valid = new validate();
		
		if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM poll_votes WHERE userid='".$_COOKIE['userid']."' AND pollid='".$valid->get_value('pollid')."'"))!=0) {
			$valid->add_error("you have already voted in this poll.  you cannot vote twice.");
		}
		$valid->is_empty('pollid','could not ascertain which poll you voted in, please try again.');
		$valid->is_empty('vote','you didn\'t vote!  please go back and check one of the options.');
		if (!$valid->is_error()) {
			if ($dbc->database_query("INSERT INTO poll_votes (userid, pollid, choiceid) VALUES ('".$_COOKIE['userid']."','".$valid->get_value('pollid')."','".$valid->get_value('vote')."');")) {
				$x->display_slim('vote successfully cast.','polls.php');
			} else {
				$x->display_top(); ?>
				there has been an error voting.  your vote has _not_ been cast.<br /><br />
				<?php
				$x->display_bottom();
			}
		} else {
			$x->display_top();
			$valid->display_errors();
			$x->display_bottom();
		}
	}
} else {
	$x->display_slim('you are not authorized to view this page.<br /><br />');
} ?>