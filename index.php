<!DOCTYPE html>
<html>
<head>
	<title>Dicoding Tabin Web App</title>
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.14.2/dist/bootstrap-table.min.css">
	<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</head>
<body>
	<div class="col-md-12">
		<h1>Data User</h1>
		<?php
	    $host = "dicodingtabinwebappserver.database.windows.net";
	    $user = "dicodingtabin";
	    $pass = "B1nt4ngTBP!";
	    $db = "dicodingtabindb";

	    try {
	        $conn = new PDO("sqlsrv:server = $host; Database = $db", $user, $pass);
	        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	    } catch(Exception $e) {
	        echo "Failed: " . $e;
	    }

	    if (isset($_POST['submit'])) {
	        try {
	            $name = $_POST['name'];
	            $email = $_POST['email'];
	            $job = $_POST['jobposition'];
	            $birthdate = $_POST['birthdate'];
	            $date = date("Y-m-d H:i:s");
	            // Insert data
	            $sql_insert = "INSERT INTO users (name, email, jobposition, birthdate, createddate) 
	                        VALUES (?,?,?,?,?)";
	            $stmt = $conn->prepare($sql_insert);
	            $stmt->bindValue(1, $name);
	            $stmt->bindValue(2, $email);
	            $stmt->bindValue(3, $job);
	            $stmt->bindValue(4, $birthdate);
	            $stmt->bindValue(5, $date);
	            $stmt->execute();
	        } catch(Exception $e) {
	            echo "Failed: " . $e;
	        }

	        echo "<span class='alt alt-success'><h3>Your're registered!</h3></span>";
	    }
	    ?>
		<form method="post" action="index.php" >
			<table>
				<tr>
					<td>Name</td><td><input class="form-control" type="text" name="name"></td>
				</tr>
				<tr>
					<td>E-Mail</td><td><input class="form-control" type="text" name="email"></td>
				</tr>
				<tr>
					<td>Birthdate</td><td><input class="form-control" type="date" name="birthdate"></td>
				</tr>
				<tr>
					<td>Job Position</td><td><input class="form-control" type="text" name="jobposition"></td>
				</tr>
				<tr>
					<td></td><td><input class="btn btn-primary" name="submit" type="submit" value="Save"> <input class="btn btn-danger" type="reset" value="Clear"></td>
				</tr>
			</table>
		</form>

		<hr>

		<table data-toggle="table" data-sort-class="table-active"
		  data-sortable="true">
	      <thead>
	        <tr>
	    		<th data-field="id" data-sortable="true">ID</th>
				<th data-field="name" data-sortable="true">Item Name</th>
				<th data-field="price" data-sortable="true">Item Price</th>
	        </tr>
	      </thead>
	      <tbody>
	        <tr>
	          <td>1</td>
	          <td>Item 1</td>
	          <td>$1</td>
	        </tr>
	        <tr>
	          <td>2</td>
	          <td>Item 2</td>
	          <td>$2</td>
	        </tr>
	      </tbody>
	    </table>
	</div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/bootstrap-table@1.14.2/dist/bootstrap-table.min.js"></script>
</body>
</html>