<?php
require_once 'include/_universal.php';
$x = new universal('tech support','',2);
if ($x->is_secure() && $toggle['techsupport']) {
	$x->display_top();
    echo "<strong>admininstrator</strong>: technical support<br /><br />";
	$colours = array('009999','3333cc','009900','66cc00','99cc00','ffff00','ffcc00','ff6600','ff0000','990000');
	if (empty($_POST)) {
	   $data = $dbc->database_query('SELECT * FROM techsupport ORDER BY itemtime DESC');
		if ($dbc->database_num_rows($data)) {
			begitem('tech support admin cp'); ?>
            list of jobs:
			<table border="0" cellpadding="3" cellspacing="0" width="100%">
			<tr><td><?php spacer(1,28); ?></td><td><b>username</b><br /><?php spacer(0,1); ?></td><td><b>details</b><br /><?php spacer(0,1); ?></td><td><b>time</b><br /><?php spacer(100); ?></td><td><div align="center"><b>severity</b></div></td><td width="100%"><b>problem</b></td><td><b>status</b></td><td>&nbsp;</td></tr>
			<?php
			$counter = 0;
			while($row = $dbc->database_fetch_assoc($data)) {
				$temprow = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM users WHERE userid='.(int)$row['userid'])); ?>
				<tr <?php echo ($counter%2 == 0?'bgcolor="'.$colors['cell_alternate'].'"':''); ?>>
				<td><?php spacer(1,28); ?></td>
				<td>#<?php echo $row['itemid']; ?> <a href="disp_users.php?id=<?php echo $row['userid']; ?>"<?php echo ($row['fixed']==0?"style=\"color: #".$colours[($row['severity']*2) -1]."\"><b>":">"); ?><?php echo $temprow['username']; ?><?php echo ($row['fixed']==0?'</b>':''); ?></a></td>
                <td><a href="techsupport_details.php?sid=<?php echo $row["itemid"];?>"><img src="img/goto.gif" border=0 alt="view details"></a></td>
				<?php $time = round((date('U')-date('U',strtotime($row['itemtime'])))/3600,1); ?>
				<td><?php echo ($time!=0?$time.' hours ago':'now'); ?></td>
				<td bgcolor="#<?php echo $colours[($row['severity']*2)-1]; ?>"><div align="center"><font color="#000000">&nbsp;<b><?php //
										switch($row['severity']){
											case(1):
												echo "1 [annoying]";
												break;
											case(2):
												echo "2 [minor]";
												break;
											case(3):
												echo "3 [important]";
												break;
											case(4):
												echo "4 [major]";
												break;
											case(5):
												echo "5 [critical]";
												break;
										}
											//echo $row['severity']; ?></b>&nbsp;</font></div></td>
				<td><?php   if(strlen($row['info']) > 50)
                                echo substr(str_replace("\n", "\n<br />", $row['info']), 0, 47)."...";
                            else
                                echo str_replace("\n", "\n<br />", $row['info'])
                            ?></td>
				<td><?php if (empty($row['assigned_to']) && empty($row['fixed'])){
                                //Unassigned and unfixed.
                                echo 'Unassigned.';
                            }elseif(!empty($row['assigned_to']) && empty($row['fixed'])){
                                //Assigned and unfixed
                                echo 'Assigned.';
                                $user_row = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM  `users` WHERE `userid`='.$row['assigned_to']));
                                echo ' ['.$user_row["username"].']';
                            }elseif(!empty($row['assigned_to']) && !empty($row['fixed'])){
                                //assigned and fixed.
                                echo '<a href="techsupport_details.php?sid='.$row["itemid"]. '" title="View result of tech support">Fixed.</a>';
                                $user_row = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM  `users` WHERE `userid`='.$row['assigned_to']));
                                echo ' ['.$user_row["username"].']';
                            }?>
                </td>
				<td>
                    <form action="<?php echo get_script_name();?>" method="POST">
                        <input type="hidden" name="jid" value="<?php echo $row['itemid'];?>" />
                        <input type="hidden" name="type" value="frommenu" />
                    <?php
                    if ($row['fixed'] == 0){ ?>
                            <input class="formcolours" type="submit" name="go" value="<?php
                        if (empty($row['assigned_to'])){
                            ?>assign<?php
                        }else{
                            ?>reassign<?php
                        }
                        ?>">
                        
                        <?php
                    }
                    if ($row['fixed'] == 0){?>
                        <input class="formcolours" type="submit" name="go" value="solve"/>
                        <?php
                    }?>
                        <input class="formcolours" type="submit" name="go" value="edit" />
                    </form>
				</td>
            </tr>
				<?php
				$counter++;
			} ?>
            </table>
			<br /><b><a href="techsupport.php">back to tech support</a></b><br />
			<?php
            enditem('tech support admin cp');
        }

	} else {
		require_once 'include/cl_validation.php';
		$valid = new validate();
		if ($valid->get_value('type') == 'frommenu'){
            if ($valid->get_value('go') == "assign" || $valid->get_value('go') == "reassign"){
                $valid->is_empty('jid', 'i need a job id, twat');
                if(!$valid->is_error()){
                    if($job = $dbc->database_query('SELECT * FROM `techsupport` WHERE `itemid`='.$valid->get_value('jid'))){
                        $row = $dbc->database_fetch_assoc($job);
                        $user = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM `users` WHERE `userid`='.$row['userid']));
                        if (!empty($row['assigned_to'])){
                            $assigned_to = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM `users` WHERE `userid`='.$row['assigned_to']));
                        }
                        begitem('assign job');
                        ?>
                        <form action="<?php echo get_script_name(); ?>" method="POST">
                        <input type="hidden" name="type" value="stage2" />
                        <input type="hidden" name="jid" value="<?php echo $row['itemid'];?>" />
                        <table border="0" cellpadding="3" cellspacing="0" width="100%">
                        
                        <tr>
                            <td width="25%"><b>assigning job</b></td>
                            <td>job #<?php echo $row['itemid'];?>, by <?php echo $user['username'];?></td>
                        </tr>
                        <?php
                        if (!empty($row['assigned_to'])){
                            $assigned_to = $dbc->database_fetch_assoc($dbc->database_query('SELECT * FROM `users` WHERE `userid`='.$row['assigned_to']));
                        ?>
                        <tr>
                            <td><b>assigned to</b></td>
                            <td><?php echo $assigned_to['username']; ?></td>
                        </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <td><b>severity</b></td>
                            <td><span style="color: #<?php echo $colours[($row['severity'] * 2) - 1]; ?>; font-weight:bold;"><?php //
										switch($row['severity']){
											case(1):
												echo "1 [annoying]";
												break;
											case(2):
												echo "2 [minor]";
												break;
											case(3):
												echo "3 [important]";
												break;
											case(4):
												echo "4 [major]";
												break;
											case(5):
												echo "5 [critical]";
												break;
										}
											//echo $row['severity']; ?></span></td>
                        </tr>
                        <tr>
                            <td><b>assign to</b></td>
                            <td><select name="assign_to">
                                <option value="0"> </option>
                                <?php
                                    $users = $dbc->database_query('SELECT * FROM `users` WHERE `priv_level` > 1');
                                    while($row = $dbc->database_fetch_assoc($users)){
                                        ?><option value="<?php echo $row['userid'];?>"><?php echo $row['username'];?></option>
                                <?php
                                }?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" name="go" value="assign" /></td>
                        </tr>
                        
                        </table>
                        </form>
                        

                        <?php
                        //assigning job.
                        enditem('assign job');
                    }else{
                        //cant assign job.
                    ?> sorry i cant get this one.<br /><a href="techsupport_admin.php">back</a><?php
                    }
                }else{
                    $valid->display_errors();
                }// END if (!$valid->is_error())
            
            }elseif ($valid->get_value('go') == "solve"){// END if ($valid->get_value('go') == "assign" || $valid->get_value('go') == "reassign")
                if ($row = $dbc->database_query('SELECT * FROM techsupport WHERE `itemid`='.$valid->get_value('jid'))){
                    $row = $dbc->database_fetch_assoc($row);
                    $user = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM `users` WHERE `userid`=".$row['userid']));
                    begitem('solve problem');
                    ?>
                        <form action="admin_techsupport.php" method="POST">
                          <input type="hidden" name="jid" value="<?php echo $row['itemid'];?>" />
                          <input type="hidden" name="type" value="stage2" />
                        <table border="0" cellpadding="3" cellspacing="0" width="100%">
                        <tr>
                            <td width="25%"><b>assigning job</b></td>
                            <td>job #<?php echo $row['itemid'];?>, by <b><a href="disp_users.php?id=<?php echo $user['userid'];?>"><?php echo $user['username'];?></a></b></td>
                        </tr>
                        <tr>
                            <td><b>info given</b></td>
                            <td><?php echo str_replace("\n", "\n<br />", $row['info']);?></td>
                        </tr>
                        <tr>
                            <td><b>solution</b></td>
                            <td><textarea name="result"></textarea></td>
                        <tr>
                            <td></td>
                            <td><input type="submit" name="go" value="solve" /></td>
                        </tr>
                        </table>
                    <?php
                }else{
                    ?>
                    that jid didnt work n00bish man!
                    <?php
                    
                }
            }elseif ($valid->get_value('go') == "edit"){
                if ($row = $dbc->database_query('SELECT * FROM techsupport WHERE `itemid`='.$valid->get_value('jid'))){
                    $row = $dbc->database_fetch_assoc($row);
                    $user = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM `users` WHERE `userid`=".$row['userid']));
                    begitem('edit');
                    ?>
                    <form action="admin_techsupport.php" method="POST">
                        <input type="hidden" name="go" value="edit" />
                        <input type="hidden" name="jid" value="<?php echo $row['itemid'];?>" />
                        <input type="hidden" name="type" value="stage2" />
                    <table border="0" cellpadding="3" cellspacing="0" width="100%">
                    <tr>
                        <td width="25%"><b>editing job</b></td>
                        <td>job #<?php echo $row['itemid'];?>, by <b><a href="disp_users.php?id=<?php echo $user['userid'];?>"><?php echo $user['username'];?></a></b></td>
                    </tr>
                    <tr>
                        <td><b>severity</b></td>
                        <td><select name='severity'>
                                <option value="<?php echo $row['severity'];?>"><?php
                                	   switch($row['severity']){
											case(1):
												echo "1 [annoying]";
												break;
											case(2):
												echo "2 [minor]";
												break;
											case(3):
												echo "3 [important]";
												break;
											case(4):
												echo "4 [major]";
												break;
											case(5):
												echo "5 [critical]";
												break;
										}?></option>
                                <option value="1">1 [annoying]</option>
                                <option value="2">2 [minor]</option>
                                <option value="3">3 [important]</option>
                                <option value="4">4 [major]</option>
                                <option value="5">5 [critical]</option>
                            </select></td>
					</tr>
					<tr>
                        <td><b>fixed</b></td>
                        <td><input type="checkbox" name="fixed" <?php
                            if ($row['fixed'])
                                echo "checked";
                            ?> /></td>
                    </tr>
                    <tr>
                        <td><b>info</b></td>
                        <td><textarea name="info"><?php echo htmlentities($row['info']);?></textarea></td>
                    </tr>
                    <tr>
                        <td><b>solution</b></td>
                        <td><textarea name="solution"><?php echo htmlentities($row['solution']);?></textarea></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" value="edit!" /></td>
                    </tr>
                    </table>
                    <b><a href="admin_techsupport.php">back</a></b><br />
                    <?php
                    enditem('edit');
                }else{
                    ?>
                    that jid didnt work n00bish man!
                    <?php

                }
            }
            
        }elseif ($valid->get_value('type') == "stage2"){// END if( $valid->get_value('type') == 'frommenu')
            if($valid->get_value('go') == "assign"){
                $valid->is_empty('jid', 'cmon i need a jid to do this!');
                if (!$valid->is_error()){
                    if ($dbc->database_query("UPDATE techsupport SET `assigned_to`=".$valid->get_value('assign_to')." WHERE `itemid`=".$valid->get_value('jid'))){
                        ?>job has been assigned.<br />
                            <b><a href="admin_techsupport.php">back.</a></b>
                        <?php
                    }else{
                        ?>there has been an error assigning job.<br />
                            <b><a href="admin_techsupport.php">back.</a></b>
                        <?php
                    }
                }else{
                    $valid->diplay_errors();
                }
            }elseif($valid->get_value('go') == "solve"){
                $valid->is_empty('jid', 'cmon i need a jid to do this!');
                $valid->is_empty('result', 'cmon i need a reason for success!');
                if (!$valid->is_error()){

                    if ($dbc->database_query("UPDATE techsupport set fixed=1, fixer='".$_COOKIE['userid']."', result='".$valid->get_value('result')."', assigned_to='".intval($_COOKIE['userid'])."' WHERE itemid='".$valid->get_value('jid')."'")){
                        ?>job has been solved.<br />
                            <b><a href="admin_techsupport.php">back.</a></b>
                        <?php
                    }else{
                        ?>there has been an error updating job.<br />
                            <b><a href="admin_techsupport.php">back.</a></b>
                        <?php
                    }
                }else{
                    $valid->diplay_errors();
                }
            }elseif($valid->get_value('go') == "edit"){
                $valid->is_empty('jid', 'cmon i need a jid to do this!');
                if (!$valid->is_error()){
                    if ($valid->get_value('fixed') == "on"){
                        $fixed = 1;
                    }else{
                        $fixed = 0;
                    }
                    if ($dbc->database_query("UPDATE techsupport SET `fixed`=$fixed, `info`='".$valid->get_value('info')."', `result`='".$valid->get_value('solution')."', `severity`=".$valid->get_value('severity')." WHERE itemid='".$valid->get_value('jid')."'")){
                        ?>job has been edited.<br />
                            <b><a href="admin_techsupport.php">back.</a></b>
                        <?php
                    }else{
                        ?>there has been an error editing job.<br />
                            <b><a href="admin_techsupport.php">back.</a></b>
                        <?php
                    }
                }else{
                    $valid->diplay_errors();
                }
            }
        } //END chooseage from 'frommenu' and 'stage2'
    }// END if (empty($_POST))
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}