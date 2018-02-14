<?php
/**
 * Created by PhpStorm.
 * User: Divya Singh
 * Date: 12/3/17
 * Time: 3:41 PM
 */

?>
<html>
    <head>
        <title>Register New User</title>
        <script src="validation.js"></script>
    </head>
    <body>
        <form name="RegisterNewUser" onsubmit="return validateNewUser()" action = "addNewUser.php" method="post">
            <!--<label>First Name :</label> <input type="text" name="forename" id="forename" onblur="checkName('forename')"/>
            <br/><label>Last Name: </label> <input type="text" name="surname" id="surname" onblur="checkName('surname')"/>
            <p id="nameError"></p>
            <br/><label>age: </label> <input type="text" name="age" id="age" onblur="checkAge()"/>
            <p id="ageError"></p>
            -->
            <br/><label>Email ID: </label> <input type="text" name="EmailID" id="emailid" placeholder="Enter EmailID" onblur="validateEmail()"/>
            <p id="emailError"></p>
            <br/><label>User Name: </label> <input type="text" name="username" id="username" placeholder="Enter User Name" onblur="validateUsername()"/>
            <p id="userNameError"></p>
            <br/><label>Password: </label> <input type="text" name="password" id="pwd" placeholder="Enter Password" onblur="validatePassword()"/>
            <p id="passwordError"></p>

            <br/><br/>
            <input type="submit" name="Submit" value="Submit"/>
            <input type="reset" name="Reset" value="Reset"/>
        </form>
    </body>
</html>
