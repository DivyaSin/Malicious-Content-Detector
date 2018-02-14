
Worked alone (no team)

Built a website that allows user to upload any text file to check if it contains malicious content or not. Also allows the admin to add signature of surely infected file in MySQL database. Inputs from webpage are sanitized, hashed, salted and validated before adding to or searching from database. Ensured a secure session mechanism. 

Technologies used: PHP, Javascript, HTML, SQL, XAMPP

This project contains the following files:

index.php == This is the login page where user or admin can login. (For admin, credentials are username = "admin", Password = "Pass4admin". For user not admin, credentials are username = "username", Password = "Pass4user")

validation.js == Javascript validation for login page.

login.php == It contains username, hostname, password and database name.

logout.php == It contains session destroy and lets the user logout from website.

home.php == It is the web application where user checks the malicious content of uploaded file.

