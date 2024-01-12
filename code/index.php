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
	    // Funktion zum Durchsuchen der CSV-Datei
	    function searchCSV($input) {
        $csvFile = 'data.csv';

        if (($handle = fopen($csvFile, 'r')) !== FALSE) {
            // Zuerst nach Übereinstimmung mit RL100-Code suchen
            while (($row = fgetcsv($handle, 1000, ';')) !== FALSE) {
                // Überprüfe, ob der Suchbegriff genau mit dem RL100-Code übereinstimmt
                if ($row[1] == $input) {
                    fclose($handle);
                    return $row;
                }
            }

            // Wenn keine genaue Übereinstimmung gefunden wurde, suche den Rest der CSV-Datei
            fseek($handle, 0); // Zurücksetzen des Dateizeigers auf den Anfang der Datei
            while (($row = fgetcsv($handle, 1000, ';')) !== FALSE) {
                // Überprüfe, ob der Suchbegriff im RL100-Code oder RL100-Langname enthalten ist
                if (stripos($row[1], $input) !== FALSE || stripos($row[2], $input) !== FALSE) {
                    fclose($handle);
                    return $row;
                }
            }
            fclose($handle);
        }

        return NULL; // Rückgabe, wenn kein Ergebnis gefunden wurde
    }
	
	    // Überprüfe, ob ein Suchbegriff übergeben wurde
	    if (isset($_GET['input'])) {
	        $userInput = trim($_GET['input']); // Entferne Leerzeichen am Anfang und Ende des Eingabewerts
	        if (!empty($userInput)) {
	            $result = searchCSV($userInput);
	
	            // Zeige individuelle Werte basierend auf dem Suchergebnis
	            if ($result !== NULL) {
	                // Beispiel: Gib den RL100-Code und den RL100-Langname aus
	                echo '
					<div class="box">
						<b>RIL100-Code:</b> ' . $result[1] . '<hr>
						<b>Langname:</b> ' . $result[2] . '<hr>
						<b>Typ:</b> ' . $result[4] . '<hr>
						<b>Niederlassung:</b> ' . $result[10] . '
					</div>';
	                // Füge weitere Ausgaben hinzu, wie gewünscht
					
					
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
	
		
		// Die Station, die du übergeben hast
				
				$stationinput = $result[3];
				// Die URL für die Datenabfrage
				$url = "https://v6.db.transport.rest/stations?query=" . urlencode($stationinput);
				
				// cURL verwenden, um die Daten abzurufen
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				
				// Überprüfen, ob es einen Fehler beim Abrufen der Daten gab
				if (curl_errno($ch)) {
				    die("Can't load data: " . curl_error($ch));
				}
				
				// Die JSON-Daten in ein assoziatives Array konvertieren
				$data = json_decode($response, true);
				
				// Überprüfen, ob die Daten gültig sind
				if ($data === null) {
				    die("Error");
				}
				
				// Überprüfen, ob die Antwort leer ist
				
				if (empty($_GET['input'])) {
				    
				} else
				if (empty($data)) {
				} else {
				    // Den Eintrag mit "Hbf" oder "Hauptbahnhof" im Namen auswählen, falls vorhanden
				    $selectedStation = null;
				    foreach ($data as $station) {
				        if (strpos($station['name'], 'Hbf') !== false || strpos($station['name'], 'Hauptbahnhof') !== false) {
				            $selectedStation = $station;
				            break;
				        }
				    }
				
				    // Wenn kein passender Eintrag gefunden wurde, den ersten Eintrag verwenden
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
				
				// cURL schließen
				curl_close($ch);
			?>
	
</body>
</html>
