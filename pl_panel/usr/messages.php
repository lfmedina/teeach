<?php
	include("../../core.php");
	include("../../usr.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title><?php echo _("Messages");?> | Teeach </title>
	<link rel="stylesheet" href="../../src/css/main.css">
	<?php
		$System = new System();
		$con = $System->conDB("../../config.json");
		$User = $System->get_user_by_id($_SESSION['h'], $con);
		$System->set_head();
	?>
</head>
<body>
	<?php
		$System = new System();
		$System->set_header();
		$System->set_usr_menu($usr_h,$usr_privilege);

		if (@$_GET['action'] == "new") {
			echo '
				<table>

					<form action="messages.php?action=send" method="POST">
						<tr><td><label for="to">'._("To: (username)").'</label></td><td><input type="text" name="to"></td></tr>
						<tr><td><label for="subject">'._("Subject: ").'</label></td><td><input type="text" name="subject"></td></tr>
						<tr><td></td><td><textarea name="body" cols="50" rows="8"></textarea></td></tr>
						<tr><td><input type="submit" value="Enviar"></td></tr>
					</form>
			</table>
			';

		} elseif(@$_GET['action'] == "send") {

			$to_username = $_POST['to'];

			$query = $con->query("select * from pl_users where username='$to_username'");
			$row = mysqli_fetch_array($query);

			$to = $row['id'];

			$subject = $_POST['subject'];
			$body = $_POST['body'];
			$from = $User->id;
			$h = substr( md5(microtime()), 1, 18);

			$date = date("Y-m-d H:i:s");

			$query2 = $con->query("INSERT INTO pl_messages(from_id,to_id,subject,body,h,date) VALUES($from,$to,'$subject','$body','$h','$date')")or die("Query error!");

			echo '<a href="messages.php">Aceptar</a>';

		} elseif(@$_GET['action'] == "sent") {
			echo '
				<ul class="submenu">
					<a href="messages.php?action=new"><li>'._("New").'</li></a>
				</ul>
				<aside>
					<h3>'._("Mailbox").'</h3>
					<ul>
						<li><a href="messages.php">'._("Received").'</a></li>
						<li><div class="actual_select"><a href="#">'._("Sent").'</a></div></li>
					</ul>
				</aside>
				<table class="table">
					<thead>
						<th></th>
						<th>'._("To").'</th>
						<th>'._("Subject").'</th>
						<th>'._("Date").'</th>
					</thead>
					<tbody>
			';

				$query = $con->query("SELECT * FROM pl_messages WHERE from_id=$User->id ORDER BY id DESC")or die("Query error!");

				while ($row = mysqli_fetch_array($query)) {

					$to_id = $row['to_id'];
					$subject = $row['subject'];
					$date = $row['date'];

					$query2 = $con->query("SELECT * FROM pl_users WHERE id=$to_id")or die("Query error!");
					$row2 = mysqli_fetch_array($query2);
					$name = $row2['name'];
					$surname1 = $row2['subname1'];

					$to = $name." ".$surname1;

					$h = $row['h'];


					echo '
					<tr>
						<td><input type="checkbox"></td>
						<td>'.$to.'</td>
						<td><a href="messages.php?action=view&h='.$h.'">'.$subject.'</a></td>
						<td>'.$date.'</td>
					</tr>
					';
				}

			echo '
					</tbody>
				</table>
			';

		} elseif(@$_GET['action'] == "view") {

			$h = $_GET['h'];

			$query = $con->query("SELECT * FROM pl_messages WHERE h='$h'")or die("Query error!");
			$row = mysqli_fetch_array($query);

			$subject = $row['subject'];
			$body = $row['body'];

			echo '
				<h1>'.$subject.'</h1>
				<p>'.$body.'</p>
			';

		} else {
			echo '
				<ul class="submenu">
					<a href="messages.php?action=new"><li>'._("New").'</li></a>
				</ul>
				<aside>
					<h3>'._("Mailbox").'</h3>
					<ul>
						<li><div class="actual_select"><a href="#">'._("Received").'</a></div></li>
						<li><a href="messages.php?action=sent">'._("Sent").'</a></li>
					</ul>
				</aside>
				<table class="table">
					<thead>
						<th></th>
						<th>'._("From").'</th>
						<th>'._("Subject").'</th>
						<th>'._("Date").'</th>
					</thead>
					<tbody>
			';

				$query = $con->query("SELECT * FROM pl_messages WHERE to_id=$User->id ORDER BY id DESC")or die("Query error!");

				while ($row = mysqli_fetch_array($query)) {

					$from_id = $row['from_id'];
					$subject = $row['subject'];
					$date = $row['date'];

					$query2 = $con->query("SELECT * FROM pl_users WHERE id=$from_id")or die("Query error!");
					$row2 = mysqli_fetch_array($query2);
					$name = $row2['name'];
					$surname1 = $row2['subname1'];

					$from = $name." ".$surname1;

					$h = $row['h'];


					echo '					
						<tr>
							<td><input type="checkbox"></td>
							<td>'.$from.'</td>
							<td><a href="messages.php?action=view&h='.$h.'">'.$subject.'</a></td>
							<td>'.$date.'</td>
						</tr>					
					';
				}

			echo '
					</tbody>
				</table>
			';
		}
	?>
</body>
</html>