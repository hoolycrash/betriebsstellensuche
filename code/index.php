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
	
	   <span class="title">Betriebsstellensuche</span>
	
	    <form method="GET" action="index.php">
	        <input type="text" name="input" autocomplete="off" id="search" value="<?php echo($_GET['input']) ?>" placeholder="RIL100, Name..." required>
	    </form>
	
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
                // Überprüfe, ob der Suchbegriff im RL100-Code oder RL100-Langname enthalten ist
                if (stripos($row[1], $input) !== FALSE || stripos($row[2], $input) !== FALSE) {
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
						<b>RIL100-Code:</b> ' . $result[1] . '<hr>
						<b>Langname:</b> ' . $result[2] . '<hr>
						<b>Typ:</b> ' . $result[4] . '<hr>
						<b>Niederlassung:</b> ' . $result[10] . '
					</div>';
	                
					
					
	            } else {
	                echo '
				<div class="box">
					Keine Ergbenisse
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
				
				if (empty($_GET['input'])) {
				    
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
	
</body>
</html>
