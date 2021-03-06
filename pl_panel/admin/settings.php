<?php

	include("../../core.php");

	$System = new System;
    $System->check_admin();
    $con = $System->conDB("../../config.json");
    $lang = $System->parse_lang("../../src/lang/".$System->load_locale().".json");
    
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title><?php echo $lang["settings"]; ?> | Teeach</title>
	<link rel="stylesheet" href="../../src/css/main.css" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'/>
	<?php $System->set_head(); ?>
    <!-- Tabs JS -->
    <script src="../../src/js/tabs.js"></script>
    <!-- Check All JS -->
    <script src="../../src/js/check-all.js"></script>
</head>
<body onload="javascript:cambiarPestanna(pestanas,pestana1);">

	<?php
		$action = $_GET['action'];

		if ($action == "save") {
			
			$centername = $_POST['centername'];
			$logo = $_POST['logo'];
			$accesspass = $_POST['accesspass'];
			$lang_val = $_POST['lang'];
			@$showgroups = $_POST['showgroups'];
            $JP = $_POST['JP'];

			if(isset($showgroups)) {
				$showgroups = "true";
			} else {
				$showgroups = "false";
			}

			$query = $con->query("UPDATE pl_settings SET value='$centername' WHERE property='centername'")or die("Query error 1!");
			$query = $con->query("UPDATE pl_settings SET value='$logo' WHERE property='logo'")or die("Query error 2!");
			$query = $con->query("UPDATE pl_settings SET value='$accesspass' WHERE property='accesspass'")or die("Query error 3!");
			$query = $con->query("UPDATE pl_settings SET value='$showgroups' WHERE property='showgroups'")or die("Query error 4!");
            $query = $con->query("UPDATE pl_settings SET value=$JP WHERE property='JP'")or die("Query error 5!");
            $query = $con->query("UPDATE pl_settings SET value='$lang_val' WHERE property='lang'")or die("Query error 6!");
		
			if($_FILES["up_lang"]["size"] != 0){
				$target_dir = "../../src/lang/";
				$target_file = $target_dir . basename($_FILES["up_lang"]["name"]);
				$uploadOk = 1;
				$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
				// Allow certain file formats
				if($fileType != "json") {
					echo "Sorry, only json files are allowed.";
					$uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					echo "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file
				} else {
					$fp_langs = fopen("../../src/lang/langs.json", "r+");
					$rfile_langs = fread($fp_langs, filesize("../../src/lang/langs.json"));
					fclose($fp_langs);
					$json_langs = json_decode($rfile_langs);
					$filename = explode(".", basename( $_FILES["up_lang"]["name"]))[0];
					if(!in_array($filename,$json_langs->{"langs"})){
						$json_langs->{"langs"}[] = $filename;
					}
					
					if (move_uploaded_file($_FILES["up_lang"]["tmp_name"], $target_file)) {
						$fp_langs = fopen("../../src/lang/langs.json", "w");
						fwrite($fp_langs, json_encode($json_langs));
						fclose($fp_langs);
						
					} else {
						echo "Sorry, there was an error uploading your file.";
					}
				}
			}
			
			echo '<a href="settings.php?action">Accept</a>';

		} else {
			
			//Queries
			$query_centername = $con->query("SELECT * FROM pl_settings WHERE property='centername'");
			$query_logo = $con->query("SELECT * FROM pl_settings WHERE property='logo'");
			$query_accesspass = $con->query("SELECT * FROM pl_settings WHERE property='accesspass'");
			$query_sg = $con->query("SELECT * FROM pl_settings WHERE property='showgroups'");
            $query_JP = $con->query("SELECT * FROM pl_settings WHERE property='JP'");
            $query_lang = $con->query("SELECT * FROM pl_settings WHERE property='lang'");

			//Arrays
			$row_centername = mysqli_fetch_array($query_centername);
			$row_logo = mysqli_fetch_array($query_logo);
			$row_accesspass = mysqli_fetch_array($query_accesspass);
			$row_sg = mysqli_fetch_array($query_sg);
            $row_JP = mysqli_fetch_array($query_JP);
            $row_lang = mysqli_fetch_array($query_lang);

			//Values
			$centername = $row_centername['value'];
			$logo = $row_logo['value'];
			$accesspass = $row_accesspass['value'];
			$sg = $row_sg['value'];
            $JP = $row_JP['value'];
            $lang_val = $row_lang['value'];


			echo '
            <div class="admin_header">
                <div class="admin_hmenu">
                    <a href="index.php"><img src="../../src/ico/back.svg" alt="Atrás" class="btn_back"></a><h2><a href="index.php">Admin</a> >> <a href="settings.php?action">'.$lang["settings"].'</a></h2>
			    </div>
            </div>
            	<center>
					<form action="settings.php?action=save" method="post" enctype="multipart/form-data">			
						<div class="contenedor">


						<nav class="ui_tabs">
                            <ul>
                                <li class="active"><a href="#tab_01">'.$lang["basic"].'</a></li>
                                <li><a href="#tab_02">'.$lang["privacy"].'</a></li>
                                <li><a href="#tab_03">'.$lang["advanced"].'</a></li>
                                <li><a href="#tab_04">'.$lang["about"].'</a></li>
                            </ul>        
                        </nav>


                        <div class="ui_tabs_content">
                            <form class="ui_form">
                                <div id="tab_01" class="ui_tab_content">
                                    <table>
                                        <tr><td><label for="centername">'.$lang["centername"].': </label></td><td><input type="text" name="centername" value="'.$centername.'"></td></tr>
                                        <tr><td><label for="logo">'.$lang["logo"].': </label></td><td><input type="text" name="logo" value="'.$logo.'"></td></tr>
                                        <tr><td></td><td><img src="'.$logo.'" alt="logo"></td></tr>
                                        <tr><td><label for="accesspass">'.$lang["accesspass"].': </label></td><td><input type="text" name="accesspass" value="'.$accesspass.'"></td></tr>
                                    </table>    
                                </div>
                                
                                <div id="tab_02" class="ui_tab_content">';
                                    if ($sg == "true") {
                                        echo '<input type="checkbox" name="showgroups" checked>';
                                    } else {
                                        echo '<input type="checkbox" name="showgroups">';
                                    }
                                        echo '                          
                                            <label for="showgroups">'.$lang["show_groups_prf"].'</label>
                                        </div>
                                <div id="tab_03" class="ui_tab_content">
                                    <label for="JP">'.$lang["join_group"].': </label>
                                    <select name="JP">';
                                    switch($JP) {
                                        case 1:
                                            echo '
                                                <option value="1" selected>'._("Direct").'</option>
                                                <option value="2">'._("Request").'</option>
                                                <option value="3">'._("Disabled").'</option>
                                            ';
                                            break;
                                        case 2:
                                            echo '
                                                <option value="1">'._("Direct").'</option>
                                                <option value="2" selected>'._("Request").'</option>
                                                <option value="3">'._("Disabled").'</option>
                                            ';
                                            break;
                                        case 3:
                                            echo '
                                                <option value="1">'._("Direct").'</option>
                                                <option value="2">'._("Request").'</option>
                                                <option value="3" selected>'._("Disabled").'</option>
                                            ';
                                            break;
                                        default:
                                            echo '
                                                <option value="1" selected>'._("Direct").'</option>
                                                <option value="2">'._("Request").'</option>
                                                <option value="3">'._("Disabled").'</option>
                                            ';
                                    }
                                    echo '
                                        </select> 
									<br>
									<label for="lang">'.$lang["language"].': </label>
									<select name="lang">';
										$fp_langs = fopen("../../src/lang/langs.json", "r");
										$rfile_langs = fread($fp_langs, filesize("../../src/lang/langs.json"));
										$json_langs = json_decode($rfile_langs);
										foreach ($json_langs->{"langs"} as $index => $row_langs) {
											echo '<option value="'.$row_langs.'"';if($lang_val == $row_langs) echo "selected";echo'>'.$row_langs.'</option>';
										}
										echo'
									</select>
									<br>
									<label for="up_lang">'.$lang["upload_lang"].': </label>
									<input type="file" name="up_lang">
									
                                    </form>
                                </div>
                                <div id="tab_04" class="ui_tab_content">
                                <span style="font-weight: bold">Teeach</span><br>
                                <p>Version 0.1 Pre-Alpha</p><br>
                                '._("Server time: ").' '.date("d-m-Y H:i:s").'            				
            				</div>
   						</div>

   						<input type="submit" value="'.$lang["save"].'">

   					</form>
    			</center>
			';
		}
	?>
</body>
</html>
