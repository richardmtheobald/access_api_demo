<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>The Weather</title>
		<link rel="stylesheet" href="css/styles.css?v=1.0">
	</head>
	<body>
		<div id="main">
			<div id="logIn" class="form">
				<span class="title"><span class="titleText">Log In</span></span>
				<div class="container">
					<div class="colspan2"><label for="logInUsername">Username</label></div>
					<div class="colspan2"><input id="logInUsername" type="text" class="req" name="username" /></div>
					<div class="colspan2"><label for="logInPassword">Password</label></div>
					<div class="colspan2"><input id="logInPassword" type="password" class="req" name="password" /></div>
					<div class="colspan4"><span class="button logIn">Log In</span></div>
					<div class="colspan4"><span class="button editUser">Sign Up</span></div>
					<div class="colspan4"><span class="button showReports">Reporting</span></div>
				</div>
			</div>
			
			<div id="editUser" class="form hidden">
				<span class="title"><span class="titleText">Edit User</span></span>
				<div class="container">
					<div class="colspan4 hidden"><input type="hidden" name="hash" value="0" readonly /></div>
					<div class="colspan2"><label for="editUserFirstName">First Name</label></div>
					<div class="colspan2"><input id="editUserFirstName" type="text" class="req" name="firstName" /></div>
					<div class="colspan2"><label for="editUserLastName">Last Name</label></div>
					<div class="colspan2"><input id="editUserLastName" type="text" class="req" name="lastName" /></div>
					<div class="colspan2"><label for="editUserEmailAddress">Email</label></div>
					<div class="colspan2"><input id="editUserEmailAddress" type="text" class="req" name="emailAddress" /></div>
					<div class="colspan2"><label for="editUserPostalCode">Postal Code</label></div>
					<div class="colspan2"><input id="editUserPostalCode" type="text" class="req" name="postalCode" /></div>
					<div class="colspan2"><label for="editUserUsername">Username</label></div>
					<div class="colspan2"><input id="editUserUsername" type="text" class="req" name="username" /></div>
					<div class="colspan2"><label for="editUserPassword">Password</label></div>
					<div class="colspan2"><input id="editUserPassword" type="password" class="req" name="password" /></div>
					<div class="colspan2"><label for="editUserConfirmPassword">Confirm Password</label></div>
					<div class="colspan2"><input id="editUserConfirmPassword" type="password" class="req" name="confirmPassword" /></div>
					<div class="colspan4"><span class="button saveUser">Save</span></div>
					<div class="colspan4"><span class="button cancelEditUser">Cancel</span></div>
				</div>
			</div>
			
			<div id="weather" class="form hidden">
				<span class="title"><span class="titleText">Current Weather</span></span>
				<div class="container">
					<div class="colspan4">Hello <span class='userFirstName'>User</span>,</div>
					<div class="colspan4">Current Temp: <span class='currentTemp'>0</span>F</div>
					<div class="colspan4">Weather: <span class='weatherReport'>Unknown</span></div>
					<div class="colspan4"><span class="button editUser">Edit User</span></div>
					<div class="colspan4"><span class="button showReports">Show Reports</span></div>
					<div class="colspan4"><span class="button signOut">Sign Out</span></div>
				</div>
			</div>
			
			
			
			<div id="reports" class="form hidden">
				<span class="title"><span class="titleText">Reports</span></span>
				<div class="container">
					<div class="colspan4"><span class="link" onclick="getReport('loginReport');">Login Report</span></div>
					<div class="colspan4"><span class="link" onclick="getReport('highReport');">Highs</span></div>
					<div class="colspan4"><span class="button backToMain">Back</span></div>
				</div>
			</div>
		</div>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="js/scripts.js?v=1.0"></script>
	</body>
</html>