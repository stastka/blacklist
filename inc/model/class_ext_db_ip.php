<?php
require_once APP_ROOT . "/inc/model/class_db.php";
class ipModel extends Db
{
    public function getEntrys($limit)
    {
        return $this->select("SELECT id_ip AS id, ip, fqdn, dt  FROM blacklist ORDER BY ip ASC LIMIT ?", ["i", $limit]);
    }
    public function getRawList($limit)
    {
        //$now = date('Y-m-d H:i:s');
        return $this->select("SELECT ip FROM blacklist WHERE dt < NOW() ORDER BY ip ASC LIMIT ?", ["i", $limit]);
    }
    public function cEntry($ip,$fqdn="none")
    {
        return $this->select("SELECT Count(ip) AS cCount FROM blacklist WHERE ip='".$ip."' OR fqdn='".$fqdn."';");
    }
    public function addEntry($ip,$fqdn,$dt)
    {
        return $this->cmd("INSERT INTO `blacklist` (`id_ip`, `ip`, `fqdn`, `dt`) VALUES (NULL, '".$ip."', '".$fqdn."', '".$dt."');");
    }
    public function updateEntryDate($ip,$fqdn,$dt)
    {
        if($fqdn == ""){ $fqdn="none";}
        return $this->cmd("UPDATE `blacklist` SET `dt` = '".$dt."' WHERE `blacklist`.`ip` = '".$ip."' OR  fqdn='".$fqdn."' LIMIT 1;");
    }
    public function updateEntry($ip,$fqdn,$dt)
    {
        if($fqdn == ""){ $fqdn="none";}
        return $this->cmd("UPDATE `blacklist` SET `dt` = '".$dt."',`fqdn` = '".$fqdn."' WHERE `blacklist`.`ip` = '".$ip."' LIMIT 1;");
    }
    public function deleteEntry($ip)
    {
        return $this->cmd("DELETE FROM blacklist WHERE `blacklist`.`ip` = '".$ip."' LIMIT 1;");
    }
    public function getEntry($filter,$fqdn="none")
    {
        return $this->select("SELECT id_ip AS id, ip, fqdn, dt FROM blacklist WHERE ip='".$filter."' OR fqdn LIKE '$fqdn%'");
    }
	public function addLog($action,$ip,$fqdn,$dt,$user,$remoteip)
    {
        return $this->cmd("INSERT INTO `blacklist_log` (`id_log`, `action`, `ip`, `fqdn`, `dt`, `user`, `remoteip`,`dt_log`) VALUES (NULL, '".$action."', '".$ip."', '".$fqdn."', '".$dt."', '".$user."',  '".$remoteip."', NOW());");
    }
}
?>