<?php
/*
----------------------------------------
---- UnBan System by QPeach.org  -------
---- Version: 1.0                -------
---- Site: http://qpeach.org/    -------
---- Support: support@qpeach.org -------
----------------------------------------
*/

if (!defined('MCR')){ exit("Хацкер, да? Идем поговорим на QPeach.org"); }
class unban{
	private function notify($text, $url = '', $type = 4){
		$_SESSION['info'] = $text;
		$_SESSION['info_i'] = $type;
		header('Location: '._URL.$url); exit;
		return true;
	}

	public function info(){
		ob_start();
		
		switch($_SESSION['info_i']){
			case 1: $type = 'alert-success'; break;
			case 2: $type = 'alert-info'; break;
			case 3: $type = 'alert-error'; break;

			default: $type = ''; break;
		}

		include_once(_STYLE.'info.html');
		return ob_get_clean();
	}

	private function act(){
		global $cfg;
		ob_start();
		
		$login = NAME;
		$price = $cfg['price'];
		$name_val = $cfg['name_val'];
		
		$ban_bd = BD("SELECT COUNT(*) FROM `{$cfg['ultrabans_bd']}` WHERE name='$login' AND `type`='0'");
		$ban_mfa = mysql_fetch_array($ban_bd);
		$ban	= intval($ban_mfa[0]);

		if($ban <= 0){
			include_once(_STYLE."noban.html");
		} else {
			include_once(_STYLE."inban.html");
		}
		
		return ob_get_clean();
	}

	public function main(){
		global $cfg;
		ob_start();

		$login = NAME;
		$price = $cfg['price'];
		$act = self::act();
		
		$balance_bd = BD("SELECT {$cfg['balance']} FROM `{$cfg['iconomy_bd']}` WHERE `username`='$login'");
		$balance_mfa = mysql_fetch_array($balance_bd);
		$balance = intval($balance_mfa[0]);

		if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['unban'])){
			if($price <= $balance){
				$update = BD("UPDATE `{$cfg['iconomy_bd']}` SET {$cfg['balance']}={$cfg['balance']}-$price WHERE `username`='$login'");
				if(!$update){ 
					self::notify("Ошибка №1", "", 3); 
				} else {
					$delete = BD("DELETE FROM `{$cfg['ultrabans_bd']}` WHERE name='$login' AND (`type`='0' OR `type`='1' OR `type`='9')");
					if(!$delete){ 
						$error_r_5 = BD("UPDATE `{$cfg['iconomy_bd']}` SET {$cfg['balance']}={$cfg['balance']}+$price WHERE `username`='$login'");
						if(!$error_r_5){
							self::notify('Ошибка №2.1', '', 3); 
						} else {
							self::notify('Ошибка №2', '', 3); 
						}
					} else {
						self::notify('Поздравляем! Вы успешно оплатили разбан. Изменения вступят в течении 5 минут.', '', 1);
					}
				}
			} else {
				$nomoney = $price - $balance;
				$name_val = $cfg['name_val'];
				self::notify('Недостаточно средств! <br />Вам не хватает: '.$nomoney.' '.$name_val, '', 3); 
			}
		}

		include_once(_STYLE.'global.html');

		return ob_get_clean();
	}
}
?>