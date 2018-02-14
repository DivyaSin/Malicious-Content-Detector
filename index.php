<?php
	// This file creates a login page, start session and determine whether loggin in user is admin or not
	// After submitting the credientials we go to home page containing web applicaton for detecting whether the file contains infectious content or not.
	// inputs from web page are sanitized and validated (client side and php) before searching in database.

	require_once 'login.php';

	// connect to database
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);

	$username = $password = "";

	// take username and password and sanitize them before checking if they are present into database
	if(isset($_POST['username']) && isset($_POST['password'])) {
		$username = mysql_entities_fix_string($conn, $_POST['username']);
		$password = mysql_entities_fix_string($conn, $_POST['password']);
	}

	$fail = validate_username($username);
	$fail .= validate_password($password);

	// session starts
	session_start();

	if ($fail == "") {
		echo "Form data successfully validated and submitted: $username<br>";
 	
	 	if (isset($_POST['username']) && isset($_POST['password'])) {
			// hash and salt password
			$searchUserQuery = "SELECT * FROM Users WHERE username = '$username'";
			$result = $conn->query($searchUserQuery);
			if ($result) {
				$row = $result->fetch_array(MYSQLI_NUM);
				$result->close();
				$salt1 = "qm&h*";
				$salt2 = "pg!@";
				$token = hash('ripemd128', "$salt1$password$salt2");
				echo $token;
				echo "<br>";
				echo $row[1];
				if ($token == $row[1]) {
					echo " Hi $row[0], you are now logged in.<br>";
					

					// set session parameters
					$_SESSION["logged_in"] = true; 
	    			$_SESSION["username"] = $username; 		
					echo "<br>Username and password are found in database.<br>";

					// check if user is admin or not
					// go to web page for uploading the file 
					if ($row[3]=='1') {
						echo "User is admin<br>";
						$_SESSION['admin'] = '1';
					}
					else {
						$_SESSION['admin'] = '0';
					}
					header("Location:home.php");
				}
				else {
					echo "<br>Invalid username/password combination. Please LogIn again.<br>";
				}
			}
			else {
				// stay on login page
				echo "Not found in database. Please register or Login Again.<br>";
				echo "<br>";
				echo <<<_END
					<html>
					<body>
						<br/>
	        			<a href="Register.php">Register Here</a>
	        			<br/>
	        			<a href="index.php">Login Here</a>
	        		</body>
	        		</html>
_END;
				exit();
			}
		}
		else die("Username and password are not written.<br>");
	}

	echo <<<_END
	    <html>
	    <head>
	        <title>MaliciousFileDetector</title>
	        <script type="text/javascript" src="validation.js"></script>
	    </head>
	    <body>
	        <h3>
	            Welcome to Malicious Content Detector
	        </h3>
	        <label>username: </label>
	        <form name ="entrypage" method="post" action ="index.php" onsubmit="return validate()">
	            <input type="text" name="username" id="username" placeholder="Enter Username" onblur="validateUsername()"/><br/>
	            <p id="userNameError"></p>
	            <label>password:</label>
	            <input type="text" name="password" id ="pwd" placeholder="Enter Password" onblur="validatePassword()"/><br/>
	            <p id="passwordError"></p>
	            <input type="submit" name="Submit" value="Submit"/>
	            <p id="Validated"></p>
	        </form>
	        <br/>
	        <br/>
	        <a href="Register.php">Register Here</a>
	    </body>
	    </html>
_END;
	
	// php validation functions
	function validate_username($username) {
		if ($username == "") 
			return "No username is entered<br>";
		else if (strlen($username) < 5)
			return "Username must be atleast 5 characters<br>";
		else if (preg_match("/[^a-zA-Z0-9_-]/", $username))
			return "Only a-z, A-Z, 0-9, - and _ are allowed in Usernames<br>";
		return "";
	}
	function validate_password($password) {
		if ($password == "") 
			return "No password is entered<br>";
		else if (strlen($password) < 6)
			return "Password must be atleast 6 characters<br>";
		else if (!preg_match("/[a-z]/", $password) || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password))
			return "Password requires one each of a-z, A-Z and 0-9<br>";
		return "";
	}

	// sanitize input data
	function mysql_entities_fix_string($conn, $string) {
		return htmlentities(mysql_fix_string($conn, $string));
	}
	function mysql_fix_string($conn, $string) {
		if (get_magic_quotes_gpc()) $string = stripslashes($string);
		return $conn->real_escape_string($string);
	}

?>