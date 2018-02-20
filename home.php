<?php
	// This file checks if file is infected if user is not admin else if user is admin, it will add the signature (first 20 bytes of file) of malware in database with malware name given by admin.
	// If the user is not admin, it searches for the signature obtained from database in file. If found, the file upoaded by user is infected else not.
	// inputs from web page are sanitized and validated (client side and php) before adding to database.
	// client side validation and php side validation is done on malware name
	// file signature is sanitized before adding it to database.

	require_once 'login.php';
	// This file allows the user to upload the file and check if it is malicious

	// session starts
	session_start();

	echo "Welcome ".$_SESSION['username']."<br> Please upload the text file only to check if it is malicious or not.";

	// connect to database
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);

	echo "<br>";
	echo "<br>";
	echo "<br>";

	// javascript validation for form
	echo <<<_END
		<html>
		<head>
		<title> Upload File </title>
		<script> 
		function validate(form) {
			fail = validateMalwareName(form.malware_name.value)
			if (fail == "") return true
			else {
				alert(fail)
				return false
			}
		}
		function validateMalwareName(malware_name) {
			if (malware_name == "") 
				return "No malware name is entered.\n";
			else if (/[^a-zA-Z0-9]/.test(malware_name))
				return "Only a-z, A-Z, and 0-9 are allowed in malware name<br>";
			return "";
		}
		</script>
		</head>
		<body>
		<form method="post" action="home.php" enctype="multipart/form-data" onsubmit = "return validate(this)">
			Select File: <input type="file" name="file_upload">
_END;

	if ($_SESSION["admin"] == '1') {
		echo <<<_END
			Malware Name: <input type="text" name="malware_name">
			<input type="submit" value="Upload">
		</form>
		<br/>
        <br/>
        <a href="logout.php" style="float:right">Logout</a>
    	</body>
    	</html>
_END;
		$malware_name = "";
		if (isset($_POST['malware_name'])) {
			$malware_name = mysql_entities_fix_string($conn, $_POST['malware_name']);
		}
		$fail = validate_malwareName($malware_name);
		if ($fail == "") {
			echo "Malware name is ".$malware_name. "<br>" ;
		}
		else {
			echo $fail;
			exit();
		}
	}
	else {
		echo <<<_END
			<input type="submit" value="Upload">
		</form>
		<br/>
        <br/>
        <a href="logout.php" style="float:right">Logout</a>
    	</body>
    	</html>
_END;
	}

	if ($_FILES) {

		// sanitize and validate the filename
		$name = strtolower(preg_replace("/[^A-Za-z0-9.]/", "", $_FILES['file_upload']['name']));

		// check for errors

		// check if there are any errors in uploading
		if($_FILES['file_upload']['error'] > 0) {
			die('An error has ocurred while uploading. Try again. Please upload the file again.');
		}

		// check if the file type is text
		if($_FILES['file_upload']['type'] != 'text/plain') {
			die('File can not be uploaded. Unsupported filetype. Please upload text file only.');
		}

		// // check if the file with that name already exists 
		// if(file_exists('files/' . $name)){
		// 	die('File with that name already exists.');
		// }

		// move the file to permanent location 
		if(!move_uploaded_file($_FILES['file_upload']['tmp_name'], './files/' . $name)) {
			die('Error uploading file. Check if the destination is writeable.');
		}

		echo "File uploaded successfully: ".$name."<br>" ;
		$file = file_get_contents( "./files/". $name);


		// check if file is empty 
		if (''== $file) {
			echo "\nFile uploaded is empty.";
		}
		// else{
		// // display the contents of file on webpage
		// 	echo "Contents of file are:"."\n";
		// 	echo $file;
		// }

		// if the user is admin then user is allowed to add surely infected file
    	// add it to database with first 20 bytes
    	// if user is not admin,then check if the file is infected by searching a string from the database
		if ($_SESSION["admin"] == '1') {
			// user is admin
			// file is surely infected 
			// store the sequence of first 20 bytes in database
			echo "The user is admin.<br>";
			echo "The file is surely infected.";
			echo "<br>";
			echo "Store the first 20 bytes in database.";
			echo "<br>";

			$file = file_get_contents( "./files/". $name, NULL, NULL, 0, 20);
			// sanitize contents before adding it to database MaliciousDb in table Malware
			$file = mysql_entities_fix_string($conn, $file);
			addInfectedFile($malware_name, $file);
		}
		elseif ($_SESSION["admin"] == '0'){
			// user is not an admin
			// file is putatively infected file
			// search within file for one of the malware strings in database
			echo "The user is not admin.<br>";
			echo "The file is putatively infected.";
			echo "<br>";
			echo "Search file for one of the strings in database.<br>";

			$searchMalwareQuery = "SELECT * from Malware";
			// print_r($searchMalwareQuery);
			$getString = $conn->query($searchMalwareQuery);
			echo "<br>";
			if (!$getString) die ("Malware cannot be added: " . $conn->error);
			$rows = $getString->num_rows;
			for ($j = 0; $j < $rows; ++$j) {
				$getString->data_seek($j);
				$row = $getString->fetch_array(MYSQLI_NUM);
				echo "Checking if any malware from database is present in file uploaded by user.<br>";
				echo "<br>";
				$isFileInfected = checkInfectedFile($row[1], "./files/".$name);
				if ($isFileInfected) {
					echo "Malware = ".$row[0]." found in file. So, file is surely infected.<br>";
					break;
				}
			}
			if(!$isFileInfected) {
				echo "Malware not found in file<br>";
			}
		}
	}
	else {
		die("<br>No file has been uploaded. Please upload the file.<br>");
	}

	// check if file added by user (not admin) is infected
	function checkInfectedFile($string, $filepath) {
		if (exec('grep '.escapeshellarg($string).' '.$filepath)) {
			return true;
			// if found then file is surely infected, can store it in database and update it
			// addInfectedFile($file);
		}
		return false;
	}

	function addInfectedFile($malware_name, $bytesequence) {
		global $conn;
		// add to table Malware in database MaliciousDb
		$addMalwareQuery = "INSERT INTO Malware VALUES ('$malware_name', '$bytesequence')";
		print_r($addMalwareQuery);
		$addMalware = $conn->query($addMalwareQuery);
		echo "<br>";
		if (!$addMalware) die ("Malware cannot be added: " . $conn->error);
	}

	//php validation functions for form elements
	function validate_malwareName($malware_name) {
		if ($malware_name == "") 
			return "No malware name is entered<br>";
		else if (preg_match("/[^a-zA-Z0-9]/", $malware_name))
			return "Only a-z, A-Z, and 0-9 are allowed in malware name<br>";
		return "";
	}
	// sanitization for inputs
	function mysql_entities_fix_string($conn, $string) {
		return htmlentities(mysql_fix_string($conn, $string));
	}

	function mysql_fix_string($conn, $string) {
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
		return $conn->real_escape_string($string);
	}
?>