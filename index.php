<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
?>
<!DOCTYPE html>
<html>
<head>
	<title>Dicoding Tabin Web App</title>
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.14.2/dist/bootstrap-table.min.css">
	<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
	<!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script> -->
</head>
<body>
	<div class="col-md-12">
		<h1>Data User</h1>
		<?php
		date_default_timezone_set("Asia/Bangkok");

	    $host = "dicodingtabinserver.database.windows.net";
	    $user = "tabibintang";
	    $pass = "B1nt4ngTBP!";
	    $db = "dicodingtabindb2";

	    $connectionString = "DefaultEndpointsProtocol=https;AccountName=dicodingtabinstorage;AccountKey=RwfyAZUkMIefrjnka2F7a/tDi+Jpg3lekzpYIh2Ksjy46V62eQe/VdV+wp4U/rVGM7ejhzR+DZREqo0B7uiZ1w==;EndpointSuffix=core.windows.net";

	    try {
	        $conn = new PDO("sqlsrv:server = $host; Database = $db", $user, $pass);
	        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	        $blobClient = BlobRestProxy::createBlobService($connectionString);

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

				$nama = $_FILES['attachment']['name'];
				$ekstensi_diperbolehkan	= array('bmp','png','jpg','jpeg');
				$x = explode('.', $nama);
				$ekstensi = strtolower(end($x));
				$file_tmp = $_FILES['attachment']['tmp_name'];	

	            $fileToUpload = $nama;
	            if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
		            $createContainerOptions = new CreateContainerOptions();
		            $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
		            $createContainerOptions->addMetaData("key1", "value1");
	    			$createContainerOptions->addMetaData("key2", "value2");

	      			$containerName = "blockblobs".generateRandomString();
	      			
	      			$blobClient->createContainer($containerName, $createContainerOptions);

	      			$content = fopen($_FILES['attachment']['tmp_name'], "r");

	      			$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	      		}

	            // Insert data
	            $sql_insert = "INSERT INTO users (name, email, jobposition, birthdate, createddate, avatar, blockblob) 
	                        VALUES (?,?,?,?,?,?,?)";
	            $stmt = $conn->prepare($sql_insert);
	            $stmt->bindValue(1, $name);
	            $stmt->bindValue(2, $email);
	            $stmt->bindValue(3, $job);
	            $stmt->bindValue(4, $birthdate);
	            $stmt->bindValue(5, $date);
	            $stmt->bindValue(6, $nama);
	            $stmt->bindValue(7, $containerName);
	            $stmt->execute();

	            
	        } catch(Exception $e) {
	            echo "Failed: " . $e;
	        }

	        echo "<span class='alt alt-success'><h3>$name registered!</h3></span>";
	    }
	    ?>
		<form method="post" action="index.php" enctype="multipart/form-data">
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
					<td>Avatar</td><td><input class="form-control" type="file" name="attachment"></td>
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
	    		<th data-field="Name" data-sortable="true">Name</th>
				<th data-field="E-Mail" data-sortable="true">E-Mail</th>
				<th data-field="Birthdate" data-sortable="true">Birthdate</th>
				<th data-field="Job Position" data-sortable="true">Job Position</th>
				<!-- <th data-field="Added" data-sortable="true">Added</th>
				<th data-field="BlockBlob" data-sortable="true">BlockBlob</th> -->
				<th data-field="Avatar" data-sortable="true">Avatar</th>
				<th data-field="Analize" data-sortable="true">Analize</th>
	        </tr>
	      </thead>
	      <tbody>
	      	<?php 
	      	$sql_select = "SELECT * FROM users";
            $stmt = $conn->query($sql_select);
            $users = $stmt->fetchAll(); 
            if(count($users) > 0) {
	            foreach($users as $user) {
	            	$avatarurl = "https://dicodingtabinstorage.blob.core.windows.net/".$user['blockblob']."/".$user['avatar'];
	            	?>
			        <tr>
			          <td><?php echo $user['name'] ?></td>
			          <td><?php echo $user['email'] ?></td>
			          <td><?php echo $user['birthdate'] ?></td>
			          <td><?php echo $user['jobposition'] ?></td>
			          <!-- <td><?php echo $user['createddate'] ?></td>
			          <td><?php echo $user['blockblob'] ?></td> -->
			          <td><img style="width: 200px " src="<?php echo $avatarurl ?>"></td>
			          <td>
			          	<textarea id="responseTextArea<?php echo $user['id_user'] ?>" class="UIInput" style="width:580px; height:400px;"></textarea>

			          	<script type='text/javascript'>
			          		processImage("<?php echo $user['id_user'] ?>","<?php echo $avatarurl ?>");
			          	</script>
			          </td>
			        </tr>
		        <?php 
		    	}
		    } ?>
	      </tbody>
	    </table>
	</div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/bootstrap-table@1.14.2/dist/bootstrap-table.min.js"></script>

	<script type="text/javascript">
	function ngeAlert(_idUser, _Url){
		alert(_Url);
	}
    function processImage(_idUser, _Url) {
        // **********************************************
        // *** Update or verify the following values. ***
        // **********************************************
 
        // Replace <Subscription Key> with your valid subscription key.
        var subscriptionKey = "0727b71b292b44bbaeb76e4262577e57";
 
        // You must use the same Azure region in your REST API method as you used to
        // get your subscription keys. For example, if you got your subscription keys
        // from the West US region, replace "westcentralus" in the URL
        // below with "westus".
        //
        // Free trial subscription keys are generated in the "westus" region.
        // If you use a free trial subscription key, you shouldn't need to change
        // this region.
        var uriBase =
            "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
 
        // Request parameters.
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
 
        // Display the image.
        //var sourceImageUrl = document.getElementById("inputImage").value;
        var sourceImageUrl = _Url;
        //document.querySelector("#sourceImage").src = sourceImageUrl;
 
        // Make the REST API call.
        $.ajax({
            url: uriBase + "?" + $.param(params),
 
            // Request headers.
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },
 
            type: "POST",
 
            // Request body.
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })
 
        .done(function(data) {
            // Show formatted JSON on webpage.
            $("#responseTextArea"+_idUser).val(JSON.stringify(data, null, 2));
        })
 
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Display error message.
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
    };
</script>
</body>
</html>