<?php

require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/db.inc.php");


$conn = dbConnect();
session_start();


if(count($_POST)>0) {
	if(isset($_POST["btnLogout"])) {
		
		$_SESSION = [];
		
		if(ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time()-86400,
				$params["path"],
				$params["domain"],
				$params["secure"],
				$params["httponly"],

			);
		}
		
		session_destroy();
        header("Location: einloggen.php");  

	}
}

function ketHetDatumai($induloDatum) {
    $datumok = [];
    $datum = new DateTime($induloDatum);

    for ($i = 0; $i < 14; $i++) {
        $datumok[] = $datum->format('Y-m-d');
        $datum->modify('+1 day');
    }

    return $datumok;
}





?>
<!doctype html>
<html lang="de">
	<head>
		<title>Gebuchte Termine</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
	</head>
	<body>
<h1>Gebuchte Termine</h1>
<table border="1" cellpadding="5" cellspacing="0">  
    <tr>
        <th>Datum</th>
        <th>Anfangszeit</th>
        <th>Endezeit</th>
        <th>Name</th>
        <th>Telefon</th>
        <th>Email</th>
    </tr>   
<?php

$gefragtedatum = $_SESSION["date"];
$alledatumok = ketHetDatumai($gefragtedatum);

foreach($alledatumok as $datum) {

$sql = "SELECT 
            gespeicherte_termin.datum, 
            gespeicherte_termin.anfang_zeit, 
            gespeicherte_termin.ende_zeit,
            kunden.name,
            kunden.telefon,
            kunden.email
        FROM gespeicherte_termin
        JOIN kunden ON gespeicherte_termin.kunden_id = kunden.id
        WHERE datum = '$datum'
        order by datum, anfang_zeit ASC
 
 ";
$result = dbQuery($conn, $sql);

while($data = dbFetch($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($data->datum) . "</td>";
    echo "<td>" . htmlspecialchars($data->anfang_zeit) . "</td>";
    echo "<td>" . htmlspecialchars($data->ende_zeit) . "</td>";
    echo "<td>" . htmlspecialchars($data->name) . "</td>";
    echo "<td>" . htmlspecialchars($data->telefon) . "</td>";
    echo "<td>" . htmlspecialchars($data->email) . "</td>";
    echo "</tr>";
}
}
?>
</table>

<h1>Logout Button</h1>
    <form method="post">
		<input type="submit" value="ausloggen" name="btnLogout">
	</form>

</body>
</html>
