<?php
class DbQuery{
	private $conn;

	function __construct() {
		require_once(PHP_INCLUDES.'DbConnect.php');
		$db = new DbConnect();
		$this->conn = $db->Connect();
	}

	function __destruct() {
		$this->conn = null;
	}

	public function GetRecentChanges(){
		$Query = $this->conn->prepare("SELECT `Events`.`id`,date_format(`Events`.`start_time`,'%m/%d/%Y %H:%i') as `start_time`,
			`Events`.`summary`,`Events`.`change`,`Changer`.`full_name`,`Environments`.`env_name_short`,`Systems`.`system_name` 
			FROM `Events`
			INNER JOIN `Systems` ON `Events`.`system` = `Systems`.`id`
			INNER JOIN `Environments` ON `Events`.`environment` = `Environments`.`id`
			INNER JOIN `Changer` ON `Events`.`changer` = `Changer`.`id`
			WHERE `Events`.`start_time` >= (SELECT DATE_SUB(NOW(), INTERVAL 30 day)) ORDER BY `Events`.`start_time` DESC");
		$Query->execute();
		$result = $Query->fetchAll(PDO::FETCH_ASSOC);
		return $result;

	}
	public function GetChangers(){
		$Query = $this->conn->prepare("SELECT `Changer`.`full_name`,`Changer`.`id` FROM `Changer` WHERE `Changer`.`enabled` = 1 ORDER BY `Changer`.`username` ASC");
		$Query->execute();
		$result = $Query->fetchall(PDO::FETCH_ASSOC);
		return $result;
	}

	public function GetEnvironments(){
		$Query = $this->conn->prepare("SELECT `Environments`.`env_name_short`,`Environments`.`id` FROM `Environments` ORDER BY `Environments`.`env_name_short`");
		$Query->execute();
		$result = $Query->fetchall(PDO::FETCH_ASSOC);
		return $result;
	}

	public function GetSystems(){
		$Query = $this->conn->prepare("SELECT `Systems`.`system_name`,`Systems`.`id` FROM `Systems` ORDER BY `Systems`.`system_name` ASC");
		$Query->execute();
		$result = $Query->fetchall(PDO::FETCH_ASSOC);
		return $result;
	}

	public function GetSystemNamesArr(){
		$Query = $this->conn->prepare("SELECT `Systems`.`system_name` FROM `Systems` ORDER BY `Systems`.`system_name` ASC");
		$Query->execute();
		$result = $Query->fetchall(PDO::FETCH_ASSOC);
		return $result;
	}

	public function GetDurations(){
		$Query = $this->conn->prepare("SELECT `Event Durations`.`duration`,`Event Durations`.`id` FROM `Event Durations`");
		$Query->execute();
		$result = $Query->fetchall(PDO::FETCH_ASSOC);
		return $result;
	}

	public function GetChangeByID($change_id){
		$Query = $this->conn->prepare("SELECT `Events`.`id`,date_format(`Events`.`start_time`,'%m/%d/%Y %H:%i') as `start_time`, `Event Durations`.`duration`,
			`Events`.`summary`,`Events`.`change`,`Changer`.`full_name`,`Environments`.`env_name_short`,`Systems`.`system_name` 
			FROM `Events`
			INNER JOIN `Systems` ON `Events`.`system` = `Systems`.`id`
			INNER JOIN `Environments` ON `Events`.`environment` = `Environments`.`id`
			INNER JOIN `Changer` ON `Events`.`changer` = `Changer`.`id`
			INNER JOIN `Event Durations` ON `Events`.`duration` = `Event Durations`.`id`
			WHERE `Events`.`id` = :change_instance");
		$Query->bindParam(':change_instance', $change_id, PDO::PARAM_INT);
		$Query->execute();
		$result = $Query->fetchall(PDO::FETCH_ASSOC);
		return $result;
	}

	public function GetChangeBySystem($system){
		$Query = $this->conn->prepare("SELECT `Events`.`id`,date_format(`Events`.`start_time`,'%m/%d/%Y %H:%i') as `start_time`,
			`Events`.`summary`,`Events`.`change`,`Changer`.`full_name`,`Environments`.`env_name_short`,`Systems`.`system_name` 
			FROM `Events`
			INNER JOIN `Systems` ON `Events`.`system` = `Systems`.`id`
			INNER JOIN `Environments` ON `Events`.`environment` = `Environments`.`id`
			INNER JOIN `Changer` ON `Events`.`changer` = `Changer`.`id` 
			WHERE `Systems`.`SYSTEM_name` = :system
			ORDER BY `Events`.`start_time` DESC");
		$Query->bindParam(':system', $system, PDO::PARAM_STR);
		$Query->execute();
		$result = $Query->fetchall(PDO::FETCH_ASSOC);
		return $result;
	}

	public function GetChangeBySearch($search_criteria){
		$search_criteria = "%".$search_criteria."%";
		$Query = $this->conn->prepare("SELECT `Events`.`id`,date_format(`Events`.`start_time`,'%m/%d/%Y %h:%i') as `start_time`,
				`Events`.`summary`,`Events`.`change`,`Changer`.`full_name`,`Environments`.`env_name_short`,`Systems`.`system_name` 
				FROM `Events`
				INNER JOIN `Systems` ON `Events`.`system` = `Systems`.`id`
				INNER JOIN `Environments` ON `Events`.`environment` = `Environments`.`id`
				INNER JOIN `Changer` ON `Events`.`changer` = `Changer`.`id`
				WHERE (`Environments`.`env_name_short` LIKE :criteria1 OR `Changer`.`full_name` LIKE :criteria2
				OR `Events`.`summary` LIKE :criteria3 OR `Events`.`change` LIKE :criteria4)
				GROUP BY `Events`.`id`
				ORDER BY `Events`.`start_time` DESC");
		$Query->bindParam(':criteria1', $search_criteria, PDO::PARAM_STR);
		$Query->bindParam(':criteria2', $search_criteria, PDO::PARAM_STR);
		$Query->bindParam(':criteria3', $search_criteria, PDO::PARAM_STR);
		$Query->bindParam(':criteria4', $search_criteria, PDO::PARAM_STR);
		$Query->execute();
		$result = $Query->fetchall(PDO::FETCH_ASSOC);
		return $result;
	}

	public function GetCalendarChanges($from,$to){
		$output_array = array();
		$datetime_to = date('Y-m-d H:i:s', $to / 1000 );
		$datetime_from = date('Y-m-d H:i:s', $from / 1000 );
		$Query = $this->conn->prepare("SELECT `Events`.`id`, `Events`.`summary` AS `title`,`Environment Classifications`.`name_short` AS `class`, `Events`.`start_time` AS `start`,`Event Durations`.`duration_milliseconds` AS `duration` 
										FROM `Events` 
										JOIN `Environments` ON `Events`.`environment` = `Environments`.`id`
										JOIN `Environment Classifications` ON `Environments`.`class` = `Environment Classifications`.`class_id` 
										JOIN `Event Durations` ON `Events`.`duration` = `Event Durations`.`id`
										WHERE `start_time` BETWEEN :datetime_from AND :datetime_to");
		$Query->bindParam(':datetime_from', $datetime_from, PDO::PARAM_STR);
		$Query->bindParam(':datetime_to', $datetime_to, PDO::PARAM_STR);
		$Query->execute();
		//$Result = $Query->fetchAll(PDO::FETCH_OBJ);
		$result = $Query->fetchAll();
		foreach ($result as $row) {
			$output_array[] = array(
				'id' => $row['id'],
				'class' => "event-".$row['class'],
				'title' => $row['title'],
				'url' => "javascript:ViewChange(".$row['id'].")",
				'start' => strtotime($row['start'])."000",
				'end' => strtotime($row['start'])."000"+$row['duration']
				);
		}
		return $output_array;
	}

	

	public function CreateChange($start,$duration,$summary,$change,$owner,$environment,$system){
		$Query = $this->conn->prepare("INSERT INTO `ChangeLog_PRD`.`Events` (`id`,`start_time`,`duration`,`change`,`changer`,`environment`,`summary`,`system`) 
										VALUES (NULL, :start_time, :duration, :change, :owner, :environemnt, :summary, :system)");
		$Query->bindParam(':start_time', $start, PDO::PARAM_STR);
		$Query->bindParam(':duration', $duration, PDO::PARAM_INT);
		$Query->bindParam(':change', $change, PDO::PARAM_STR);
		$Query->bindParam(':owner', $owner, PDO::PARAM_INT);
		$Query->bindParam(':environemnt', $environment, PDO::PARAM_INT);
		$Query->bindParam(':summary', $summary, PDO::PARAM_STR);
		$Query->bindParam(':system', $system, PDO::PARAM_INT);
		$Query->execute();
		$result = $Query->errorCode();
		return $result;
	}
}
?>