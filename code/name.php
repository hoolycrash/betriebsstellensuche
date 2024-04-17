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
		<a href="index.php?input=<?php echo($_GET['input']) ?>" class="tab">RIL100-Code</a>&nbsp;&nbsp;&nbsp;<a href="#" class="tab active">Name</a>
	</div>
	
	<div class="secondbar">
		<form method="GET" action="name.php">
			<input type="text" name="input" autocomplete="off" id="search" value="<?php echo($_GET['input']) ?>" placeholder="Betriebsstellenname..." required>
		</form>
	</div>
	
		
		
	
		<?php
		// search csv
		function searchCSV($input) {
		$csvFile = 'data.csv';
		
		
		

		if (($handle = fopen($csvFile, 'r')) !== FALSE) {
			$results = array(); // Single array for all results
			// look for matching ril100 code
			while (($row = fgetcsv($handle, 1000, ';')) !== FALSE) {
				if ($row[1] == $input || stripos($row[1], $input) !== FALSE || stripos($row[2], $input) !== FALSE) {
					$results[] = $row; // add element to results array
 				}
			}
			
			

			fclose($handle);
			return $results; // return results
		}

		return NULL; 
	}
	
	
	
		// is there input
		if (isset($_GET['input'])) {
			$userInput = trim($_GET['input']);
			if (!empty($userInput)) {
				$results = searchCSV($userInput);
				
				
				
				if (!empty($results)) { // are there results?
                    // schow list if >1 result
                    foreach ($results as $result) {
                        echo '
						<a href="index.php?input=' . htmlspecialchars($result[1], ENT_QUOTES, 'UTF-8') . '" class="link-blank">
						<div class="box">
                       		<b>' . htmlspecialchars($result[1], ENT_QUOTES, 'UTF-8') . ':</b> ' . htmlspecialchars($result[2], ENT_QUOTES, 'UTF-8') . '<hr>
							<b>Typ</b>: ' . htmlspecialchars($result[5], ENT_QUOTES, 'UTF-8') . '
                        </div>
						</a>
						';
                    }
                } else {
					echo '
					<div class="box">
						Keine Ergebnisse
					</div>';
				}
			} else {
				echo '<p>Bitte geben Sie einen Suchbegriff ein.</p>';
			}
		}

		
			?>
			
			<br>
			
			<a href="https://www.buymeacoffee.com/felixnietzold" target="_blank">Buy me a coffe ☕</a><br>
			<a href="https://github.com/hoolycrash/betriebsstellensuche" target="_blank">Github 😺</a>
	
</body>
</html>
