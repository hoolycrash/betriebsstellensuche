<!DOCTYPE html>
<html lang="de">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="styles.css">
	<link rel="stylesheet" href="darkstyles.css">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>RIL100 Search</title>
</head>
<body>
	
	<div class="topbar">
		<a href="#" class="tab active">RIL100-Code</a>&nbsp;&nbsp;&nbsp;<a href="name.php?input=<?php echo($_GET['input']) ?>" class="tab">Name</a>
	</div>

	<div class="secondbar">
		<form method="GET" action="index.php">
			<input type="text" name="input" autocomplete="off" id="search" value="<?php echo($_GET['input']) ?>" placeholder="RIL100-Code..." required>
		</form>
	</div>
	
		<?php
		// search csv
		function searchCSV($input) {
		$csvFile = 'data.csv';

		if (($handle = fopen($csvFile, 'r')) !== FALSE) {
			// look for matching ril100 code
			while (($row = fgetcsv($handle, 1000, ';')) !== FALSE) {
				if ($row[1] == $input) {
					fclose($handle);
					return $row;
 				}
			}

			// if no matching ril100 code search all
			fseek($handle, 0); 
			while (($row = fgetcsv($handle, 1000, ';')) !== FALSE) {
				// is search input matching with ril100 code or ril100 Langname?
				if (strcasecmp($row[1], $input) === 0) {

					fclose($handle);
					return $row;
				}
			}
			fclose($handle);
		}

		return NULL; 
	}
	
		// is there input
		if (isset($_GET['input'])) {
			$userInput = trim($_GET['input']);
			if (!empty($userInput)) {
				$result = searchCSV($userInput);
				if ($result !== NULL) {
					//output
					 echo '
						<div class="box">
                       		<b>RIL100-Code:</b> ' . htmlspecialchars($result[1], ENT_QUOTES, 'UTF-8') . '<hr>
							<b>RIL100-Langname:</b> ' . htmlspecialchars($result[2], ENT_QUOTES, 'UTF-8') . '<hr>
							<b>Typ</b>: ' . htmlspecialchars($result[5], ENT_QUOTES, 'UTF-8') . '<hr>
							<b>Betriebszustand</b>: <span class="zustand-' . htmlspecialchars($result[6], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($result[6], ENT_QUOTES, 'UTF-8') . '</span><hr>
                        	<b>Regionalbereich</b>: '. htmlspecialchars($result[10], ENT_QUOTES, 'UTF-8') .'
                        </div>
						';
	                
				} else {
					echo '
					<div class="box">
						<p>Keine Ergbenisse</p>
						<p><i>Versuche die Suche nach Stationsname</i></p>
					</div>';
				}
			} else {
				echo '<p>Bitte geben Sie einen Suchbegriff ein.</p>';
			}
		}

		// look up hafas station
				
				$stationinput = $result[3];
				// url to hafas station blabla
				$url = "https://v6.db.transport.rest/stations?query=" . urlencode($stationinput);
				
				// curl the server
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				
				// is error?
				if (curl_errno($ch)) {
				    die("Can't load data: " . curl_error($ch));
				}
				
				// json data convert
				$data = json_decode($response, true);
				
				// valid?
				if ($data === null) {
				    die("Error");
				}
				
				// answer no content?
				
				if ($result === 1) {
                echo 'Mehrere';
                
            } else if (empty($_GET['input'])) {
				    
				} else
				if (empty($data)) {
				} else {
				    // prioriese hbf / hauptbahnhof entries
				    $selectedStation = null;
				    foreach ($data as $station) {
				        if (strpos($station['name'], 'Hbf') !== false || strpos($station['name'], 'Hauptbahnhof') !== false) {
				            $selectedStation = $station;
				            break;
				        }
				    }
				
				    // if not show first entry
				    if ($selectedStation === null) {
				        $selectedStation = reset($data);
				    }
					
					echo '
					<br>
					<br>
					<b>Ergebnisse von db-stations:</b>
					<div class="box">
						<b>Name:</b> ' . $selectedStation['name'] . '<hr>
						<b>Typ:</b> ' . $selectedStation['productLine']['segment'] . '<hr>
						<b>Adresse:</b><br>
						' . $selectedStation['address']['street'] . '<br>
						' . $selectedStation['address']['zipcode'] . '<br>
						' . $selectedStation['address']['city'] . '<hr>
						
						<a href="https://www.bahnhof.de/' . $selectedStation['name'] . '">bahnhof.de</a>
						
					</div>';
		
			}
				
				// cURL close
				curl_close($ch);
			?>
			
			<br>
			
			<a href="https://www.buymeacoffee.com/felixnietzold" target="_blank">Buy me a coffe ☕</a><br>
			<a href="https://github.com/hoolycrash/betriebsstellensuche" target="_blank">Github 😺</a>
	
</body>
</html>