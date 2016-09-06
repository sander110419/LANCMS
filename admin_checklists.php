<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('plural'),0);
if($x->is_secure()) {
	$x->display_top(); ?>
	<b><?php echo get_lang('administrator'); ?></b>: <?php echo get_lang('plural'); ?><br />
	<br />
	<b><?php echo get_lang('tournaments'); ?></b>:
	<?php
	$checklist = array(
		"<a href=\"admin_tournament.php\">".get_lang('link_admin_tournament')."</a>: ".get_lang('tourney_step1'),
		"<a href=\"admin_teams.php\">".get_lang('link_admin_teams')."</a>: ".get_lang('tourney_step2'),
		"<a href=\"disp_teams.php\">".get_lang('link_disp_teams')."</a>: ".get_lang('tourney_step3'),
		"<a href=\"admin_teams_delete.php\">".get_lang('link_admin_teams_delete')."</a>: ".get_lang('tourney_step4'),
		"<a href=\"admin_seeding.php\">".get_lang('link_admin_seeding')."</a>: ".get_lang('tourney_step5')."<a href=\"admin_seeding_erase.php\">".get_lang('link_admin_seeding_erase')."</a>",
		"<a href=\"admin_tournament_start.php\">".get_lang('link_admin_tournament_start')."</a>: ".get_lang('tourney_step6'),
		"<a href=\"tournaments.php\">".get_lang('link_tournaments')."</a>: ".get_lang('tourney_step7'),
		"<a href=\"admin_tournament_unstart.php\">".get_lang('link_admin_tournament_unstart')."</a>: ".get_lang('tourney_step8'),
		);
	?>
	<table class="sm">
		<?php
		for($i=0;$i<sizeof($checklist);$i++) { ?>
			<tr>
				<td valign="top"><?php echo ($i+1); ?>.) </td>
				<td><?php echo $checklist[$i]; ?></td>
			</tr>
			<?php
		} ?>
	</table>
	<br />
	<b><?php echo get_lang('prizes'); ?></b>:
	<?php
	$checklist = array(
		"<a href=\"admin_prizes.php\">".get_lang('link_admin_prizes')."</a>: ".get_lang('prizes_step1'),
		get_lang('prizes_step2'),
		"<a href=\"admin_prize_control.php\">".get_lang('link_admin_prize_control')."</a>: ".get_lang('prizes_step3'),
		get_lang('prinzes_step4')." - <a href=\"admin_prizes_print.php\">".get_lang('link_admin_prizes_print')."</a> / <a href=\"admin_prizes_draw.php\">".get_lang('link_admin_prizes_draw')."</a>",
		"<a href=\"admin_prize_control.php\">".get_lang('link_admin_prize_control')."</a>: ".get_lang('prizes_step5'),
		"<a href=\"admin_prize_control.php\">".get_lang('link_admin_prize_control')."</a>: ".get_lang('prizes_step6'),
		);
	?>
	<table class="sm">
		<?php
		for($i=0;$i<sizeof($checklist);$i++) { ?>
			<tr>
				<td valign="top"><?php echo ($i+1); ?>.) </td>
				<td><?php echo $checklist[$i]; ?></td>
			</tr>
			<?php
		} ?>
	</table>
	<?php
	$x->display_bottom();
} else {
	$x->display_slim(get_lang('noauth'));
}