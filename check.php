<?php
	$start = time();
	echo "Loading test passwords\n";
	$hashes = array();
	function addTestPassword($password) {
		global $hashes;
		$hash = sha1($password);
		$truncated = '00000' . substr($hash, 5);
		$hashes[$hash] = $password;
		$hashes[$truncated] = $password;
	}
	
	function pad($str) {
		return str_pad($str, 2, '0');
	}
	
	include('common.php');
	$common = array_merge($common, array('motdepasse', 'bonjour', 'allo', 'foreveralone', 'linkedin', 'linkedout', 'recruiter', 'googlerecruiter', 'toprecruiter', 'superrecruiter', 'humanresources', 'hiring'));
	foreach($common as $password) {
		addTestPassword($password);
	}
	
	for($year = 1960; $year < 2012; $year++) {
		for($month = 1; $month < 12; $month++) {
			for($day = 1; $day < 31; $day++) {
				addTestPassword($year . $month . $day);
				addTestPassword($year . pad($month) . pad($day));
				addTestPassword($day . $month . $year);
				addTestPassword($day . pad($month) . pad($year));
				addTestPassword($month . $day . $year);
				addTestPassword(pad($month) . pad($day) . $year);
			}
		}
	}
	
	echo "Loaded and hashed test passwords\n";
	
	$found = array();
	
	$handle = fopen('pw.txt', 'r');
	$count = 0;
	$total = 0;
	$cracked = 0;
	while($str = fread($handle, 42)) {
		$str = trim($str);
		if (isset($hashes[$str])) {
			$plain = $hashes[$str];
			//echo "Found password '$plain'\n";
			if (isset($found[$plain]))
				$found[$plain]++;
			else
				$found[$plain] = 1;
			$total++;
			if (substr($str, 0, 5) == '00000')
				$cracked++;
		}
		
		$count++;
		if ($count % 10000 == 0)
			echo "Tried $count passwords\n";
	}
	
	$end = time();
	$elapsed = $end - $start;
	$mins = floor($elapsed / 60);
	$seconds = $elapsed - ($mins * 60);
	
	$result = fopen('result.txt', 'w');
	fwrite($result, count($found) . "\r\n\r\n");
	foreach($found as $k => $v)
		fwrite($result, "$k\r\n");
	fclose($result);
	
	echo "Done! Compared $count passwords in $mins mins $seconds secs. Found $total passwords, $cracked of them have been cracked already\n";
	/*
		$hashes = fopen('hashes.php', 'w');
		fwrite($hashes, "'$str' => true,\r\n");
		fclose($hashes);
	*/
?>