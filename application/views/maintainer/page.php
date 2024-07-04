<!DOCTYPE html>
<html>
<head>
	<title>Maintener Page</title>
	<style type="text/css">
		table {
		  border-collapse: collapse;
		}

		table, th, td {
		  border: 1px solid black;
		}
		div .scroll-table{
			background-color: #333;
			overflow: auto;
			white-space: nowrap;
		}

	</style>
</head>
<body>
	<h1>Maintainer Page</h1>
	<hr>
	Excecute SQL
	<?= form_open(""); ?>
	<textarea name="myql" style="width: 50%"><?= !empty($query) ? $query : '' ?></textarea><br>
	<button name="submit" value="submit" type="submit">Excecute</button>
	<?= form_close(""); ?>
	<hr>
	<div class="scroll-table">
		<?= !empty($result) ? $result : '' ?>
	</div>
</body>
</html>
