<?php
//
//  TorrentTrader v2.x
//	$LastChangedDate: 2012-08-18 19:52:54 +0100 (Sat, 18 Aug 2012) $
//      $LastChangedBy: arcticwolf44 $
//	
//	http://www.torrenttrader.org
//
//

// VERY BASIC ADMINCP

require_once("backend/functions.php");
require_once("backend/bbcode.php");
dbconn();
loggedinonly();

function navmenu(){
global $site_config;

function print_body($html) {
$html = print();
return $html;
}
print('<td align="center"><a href="admincp.php?action=censor"><img src="images/admin/word_censor.png" border="0" width="32" height="32" alt="" /><br /><?php echo T_("WORD_CENSOR"); ?></a><br /></td>';
}

$action = $_REQUEST["action"];
$do = $_REQUEST["do"];
#======================================================================#
# Word Censor Filter
#======================================================================#
if($action == "censor") {
stdhead("Censor");
navmenu();
if($site_config["OLD_CENSOR"])
{
//Output
if ($_POST['submit'] == 'Add Censor'){
$query = "INSERT INTO censor (word, censor) VALUES (" . sqlesc($_POST['word']) . "," . sqlesc($_POST['censor']) . ");";
             SQL_Query_exec($query);
             }
if ($_POST['submit'] == 'Delete Censor'){
  $aquery = "DELETE FROM censor WHERE word = " . sqlesc($_POST['censor']) . " LIMIT 1";
  SQL_Query_exec($aquery);
  }

begin_frame(T_("WORD_CENSOR"));  
/*------------------
|HTML form for Word Censor
------------------*/
?>

<form method="post" action="admincp.php?action=censor">  
<table width='100%' cellspacing='3' cellpadding='3' align='center'>
<tr>
<td bgcolor='#eeeeee'><font face="verdana" size="1">Word:  <input type="text" name="word" id="word" size="50" maxlength="255" value="" /></font></td></tr>
<tr><td bgcolor='#eeeeee'><font face="verdana" size="1">Censor With:  <input type="text" name="censor" id="censor" size="50" maxlength="255" value="" /></font></td></tr>
<tr><td bgcolor='#eeeeee' align='left'>
<font size="1" face="verdana"><input type="submit" name="submit" value="Add Censor" /></font></td>
</tr>
</table>
</form>

<form method="post" action="admincp.php?action=censor">
<table>
<tr>
<td bgcolor='#eeeeee'><font face="verdana" size="1">Remove Censor For: <select name="censor">
<?php
/*-------------
|Get the words currently censored
-------------*/

$select = "SELECT word FROM censor ORDER BY word";
$sres = SQL_Query_exec($select);
while ($srow = mysql_fetch_array($sres))
{
        echo "<option>" . $srow[0] . "</option>\n";
        }
echo'</select></font></td></tr><tr><td bgcolor="#eeeeee" align="left">
<font size="1" face="verdana"><input type="submit" name="submit" value="Delete Censor" /></font></td>
</tr></table></form>';
}
else
{
$to=isset($_GET["to"])?htmlentities($_GET["to"]):$to='';
switch ($to)
  {
    case 'write':
         begin_frame($LANG['ACP_CENSORED']);
         if (isset($_POST["badwords"]))
            {
            $f=fopen("censor.txt","w+");
            @fwrite($f,$_POST["badwords"]);
            fclose($f);
            }
			show_error_msg(T_("SUCCESS"),"Censor Updated!",0);
         break;


    case '':
    case 'read':
    default:
      $f=@fopen("censor.txt","r");
      $badwords=@fread($f,filesize("censor.txt"));
      @fclose($f);
	  begin_frame($LANG['ACP_CENSORED']);
      echo'<form action="admincp.php?action=censor&to=write" method="post" enctype="multipart/form-data">
  <table width="100%" align="center">
    <tr>
      <td align="center">'.$LANG['ACP_CENSORED_NOTE'].'</td>
    </tr>
    <tr>
      <td align="center"><textarea name="badwords" rows="20" cols="60">'.$badwords.'</textarea></td>
    </tr>
    <tr>
      <td align="center">
        <input type="submit" name="write" value="'.T_("CONFIRM").'" />&nbsp;&nbsp;
        <input type="submit" name="write" value="'.T_("CANCEL").'" />
      </td>
    </tr>
  </table>
</form><br />';
break;
}
}
end_frame();
stdfoot();
}
// End forum Censored Words
