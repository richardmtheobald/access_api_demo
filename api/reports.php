<?php
	$loginCheckRequired = false;
	
	require('includeInAll.php');
	
	$query = "select * from rtUsers where 0 = 1"; // default query
	$params = array(':x'=>'0'); // default params; we do not allow empty params
	$result = array('title'=>'Generic Report',
		'headers'=>array(),
		'rows'=>array());
	
	if(isset($_REQUEST['report'])){
		if($_REQUEST['report'] == 'loginReport'){
			$query = "select `username`, sum(`successful`) as 'successful', count(`id`) as 'attempts' from `rtLoginAttempts` where createDateTime >= NOW() - INTERVAL 1 WEEK group by `username`";
			$result['title'] = 'Logins in the Past Week';
		}
		if($_REQUEST['report'] == 'highReport'){
			$query = "select distinct `rtTemps`.`postalCode`, IFNULL(a.`high`,'Unknown') as 'Today', IFNULL(b.`high`,'Unknown') as 'Yesterday', IFNULL(c.`high`,'Unknown') as '-2 Days', IFNULL(d.`high`,'Unknown') as '-3 Days', IFNULL(d.`high`,'Unknown') as '-4 Days' from `rtTemps` left join (select `postalCode`, max(`tempInF`) as 'high' from `rtTemps` where `createDateTime` >= DATE_FORMAT(NOW(),'%Y-%m-%d') and `createDateTime` < DATE_FORMAT((NOW() + INTERVAL 1 DAY),'%Y-%m-%d') group by `postalCode`) a on `rtTemps`.`postalCode` = a.`postalCode` left join (select `postalCode`, max(`tempInF`) as 'high' from `rtTemps` where `createDateTime` >= DATE_FORMAT((NOW()-INTERVAL 1 DAY),'%Y-%m-%d') and `createDateTime` < DATE_FORMAT(NOW(),'%Y-%m-%d') group by `postalCode`) b on `rtTemps`.`postalCode` = b.`postalCode` left join (select `postalCode`, max(`tempInF`) as 'high' from `rtTemps` where `createDateTime` >= DATE_FORMAT((NOW()-INTERVAL 2 DAY),'%Y-%m-%d') and `createDateTime` < DATE_FORMAT(NOW()-INTERVAL 1 DAY,'%Y-%m-%d') group by `postalCode`) c on `rtTemps`.`postalCode` = c.`postalCode` left join (select `postalCode`, max(`tempInF`) as 'high' from `rtTemps` where `createDateTime` >= DATE_FORMAT((NOW()-INTERVAL 3 DAY),'%Y-%m-%d') and `createDateTime` < DATE_FORMAT(NOW()-INTERVAL 2 DAY,'%Y-%m-%d') group by `postalCode`) d on `rtTemps`.`postalCode` = d.`postalCode` left join (select `postalCode`, max(`tempInF`) as 'high' from `rtTemps` where `createDateTime` >= DATE_FORMAT((NOW()-INTERVAL 4 DAY),'%Y-%m-%d') and `createDateTime` < DATE_FORMAT(NOW()-INTERVAL 3 DAY,'%Y-%m-%d') group by `postalCode`) e on `rtTemps`.`postalCode` = e.`postalCode`";
			$result['title'] = 'Highs; Past 5 Days';
		}
	}
	
	$dbQuery = $dbCon->r($query,$params);
	$dbQuery->setFetchMode(PDO::FETCH_ASSOC);
	$headers = array();
	for ($i = 0; $i < $dbQuery->columnCount(); $i++) {
		$col = $dbQuery->getColumnMeta($i);
		$headers[] = $col['name'];
	}
	$result['headers'] = $headers;
	$result['rows'] = array();
	while($dbResult = $dbQuery->fetch()){
		$result['rows'][] = $dbResult;
	}
	
	echo json_encode($result);
?>