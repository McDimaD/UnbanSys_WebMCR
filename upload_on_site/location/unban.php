<?php
if (!defined('MCR') || empty($user)) {exit;}
define('_STYLE', STYLE_URL.'Default/unban/');
define('_URL', BASE_URL.'unban/');
define('NAME', $player);

require_once(MCR_ROOT.'configs/unban.cfg.php');
require_once(MCR_ROOT.'instruments/unban.class.php'); 

$unban = new unban();

$menu->SetItemActive('unban');
$page = 'Платный разбан"';

if(isset($_SESSION['info'])){ define('_INFO', $unban->info()); }else{ define('_INFO', ''); }
$content_main = $unban->main();
if(isset($_SESSION['info'])){unset($_SESSION['info']); unset($_SESSION['info_t']);}
?>