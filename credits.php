<?php
require_once 'include/_universal.php';
$x = new universal('credits','credits',0);
$x->display_top(); ?>
<div align="center">
<br />
<span class="title"><strong><a href="http://www.nerdclub.net/alp/" target="TOP">autonomous LAN party</a></strong></span><br />
<?php spacer(1,4,1); ?>
<a href="http://www.nerdclub.net/" target="TOP"><img src="img/<?php echo $colors['image_text']; ?>_copyright.gif" width="248" height="7" border="0" alt="alp (c) 2004 the nerdclub programming team" /></a><br />
<br />
<span color="<?php echo $colors['blended_text']; ?>"><strong>ALP<?php if(ALP_TOURNAMENT_MODE) { echo '_tournaments'; } ?></strong> | <strong>version <?php echo $master['alpver']; ?></strong></span><br />
<br />
<span color="<?php echo $colors['blended_text']; ?>"><strong>ALP<?php if(ALP_TOURNAMENT_MODE) { echo '_tournaments'; } ?></strong> | <strong>report bugs <a href="http://sourceforge.net/tracker/?group_id=72308&atid=534081">at sourceforge</a></strong> | <strong>nerdclub ALP <a href="http://www.nerdclub.net/alp/forum/">forums</a></strong></span><br />
<br />
<span color="<?php echo $colors['blended_text']; ?>"><strong>IRC Channel: #ALP on irc.gamesurge.net</strong></span>
<br />
<br />
[<a href="license.php">released under the <strong>Q PUBLIC LICENSE version 1.0</strong></a>]<br />
<br />
</div>
<table border="0" cellspacing="1" cellpadding="8" width="100%" class="sm" align="center" bgcolor="<?php echo $colors['cell_title']; ?>">
<tr>
	<td colspan="3" bgcolor="<?php echo $colors['cell_background']; ?>" class="normal" valign="top">
	<span class="title"><strong>nerdclub programming team</strong></span><br />
	<br />
	<div align="center">
	<span color="<?php echo $colors['primary']; ?>" size="+1"><strong>shadow</strong></span><br />
	<span color="<?php echo $colors['secondary']; ?>">Rob Hruska</span><br />
	<br />
	<span color="<?php echo $colors['primary']; ?>" size="+1"><strong>Spam</strong></span><br />
	<span color="<?php echo $colors['secondary']; ?>">Tim Steiner</span><br />
	<br />
	<span color="<?php echo $colors['primary']; ?>" size="+1"><strong>xenophobia</strong></span><br />
	<span color="<?php echo $colors['secondary']; ?>">Zach Leatherman</span><br />
	<br />
	</div>
	</td>
</tr>
<tr>
	<td colspan="3" valign="top" bgcolor="<?php echo $colors['cell_background']; ?>" class="normal" >
	<span class="title"><strong>ALP developers</strong></span><br />
	<br />
	<div align="center">
    <span color="<?php echo $colors['primary']; ?>" size="+1"><strong>Curium</strong></span><br />
    <span color="<?php echo $colors['secondary']; ?>">Travis Kreikemeier</span><br />
    <span color="<?php echo $colors['secondary']; ?>">NETWAR LAN - <a href="http://www.netwar.org" target="_blank">www.netwar.org</a></span><br />
	<br />
    <span color="<?php echo $colors['primary']; ?>" size="+1"><strong>Havoc</strong></span><br />
    <span color="<?php echo $colors['secondary']; ?>">Charlie Croom</span><br />
    <br />
    <span color="<?php echo $colors['primary']; ?>" size="+1"><strong>sKuLLsHoT</strong></span><br />
    <span color="<?php echo $colors['secondary']; ?>">Jarrod Mast</span><br />
    <span color="<?php echo $colors['secondary']; ?>">Move or Bleed LANs - <a href="http://www.morb.ath.cx" target="_blank">www.morb.ath.cx</a></span><br />
    <br />
    <span color="<?php echo $colors['primary']; ?>" size="+1"><strong>zort</strong></span><br />
    <span color="<?php echo $colors['secondary']; ?>">Dean Hamstead</span><br />
    <br />
    <br />
	</div>
	</td>
</tr>
<tr>
	<td colspan="3" bgcolor="<?php echo $colors['cell_background']; ?>" class="normal">
	<br />
	<?php begitem('special thanks to...',0); ?>
	</td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><a href="http://www.squery.com/" target="TOP"><strong>SQuery</strong></a></td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">SQuery Team</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><a href="http://www.squery.com/" target="TOP">SQuery</a></td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><a href="http://www.skamp.net/" target="TOP"><strong>sk&atilde;mp</strong></a></td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">Guillaume Cocatre-Zilgien</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><a href="http://www.skamp.net/projects/gsqlib/" target="TOP">GSQlib</a> ( - 0.96.12)</td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><a href="http://sourceforge.net/projects/phpcast" target="TOP"><strong>PHPcast basemap</strong></a></td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">PHPcast Team</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">Streaming Music Base Code</td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><a href="http://getid3.sourceforge.net/" target="TOP"><strong>GetID3()</strong></a></td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">GetId3 Team</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">GetID3()</td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><strong>Steakeater</strong></td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">Andy Rutledge</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">original tournament code (0.90 - 0.95.x)</td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><strong>dufuz</strong></td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">Helgi Bormar</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">previous alp developer</td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><a href="http://hlmaps.sourceforge.net" target="TOP"><strong>HLMaps</strong></a></td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">Scott McCrory and Brian Porter</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">hlmaps_images -- compilation of Half-Life maps.</td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><a href="http://www.phpwcms.de/" target="TOP">phpwcms</a></td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">Oliver Georgi</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><?php get_arrow(); ?></td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><a href="http://dakrats.net/" target="TOP">dakrats.net</a></td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">Doctor_WHO</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">compilation of UT2004, COD, and miscellaneous HL maps.</td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>"><a href="http://www.ritfest.net/" target="TOP">RITFest</a></td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">Darthdurden</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">compilation of Far Cry maps.</td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">Dark Screen</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">&nbsp;</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">compilation of Halo maps.</td>
</tr>
<tr>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">Monkey</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">&nbsp;</td>
	<td style="background-color: <?php echo $colors['cell_background']; ?>">compilation of Starcraft maps.</td>
</tr>
</table>
<?php
$x->display_bottom(); ?>