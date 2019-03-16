<?php

$dataFile = "./data.json";
$adminSecret = "some super secret secret here";


function error($code, $msg)
{
	echo($msg);
	http_response_code($code);
	exit(1);
}
function readData()
{
	global $dataFile;
	$text = file_get_contents($dataFile);
	return json_decode($text, true);
}
function writeData($data)
{
	global $dataFile;
	$text = json_encode($data);
	file_put_contents($dataFile, $text);
}



if(!isset($_POST["action"]))
	error(400, "No action defined");

$action = $_POST["action"];

if($action === "status")
{
	$data = readData();

	$confirms = 0;
	$rejects = 0;
	foreach($data as $token => $entry)
	{
		if($entry["participation"])
			$confirms++;
		else
			$rejects++;
	}

	$response = [
		"confirms" => $confirms,
		"rejects" => $rejects,
	];

	if(isset($_POST["token"]))
		$response["user"] = $data[$_POST["token"]];

	echo(json_encode($response));
}
else if($action === "participate")
{
	if(!isset($_POST["token"])
		|| !isset($_POST["participation"])
		|| !isset($_POST["name"]))
		error(400, "Missing parameters");

	if(!preg_match("/^\w+$/", $_POST["name"]))
		error(400, "Invalid name");

	$token = $_POST["token"];
	$data = readData();

	$data[$token] = [
		"participation" => $_POST["participation"] === "true",
		"name" => $_POST["name"],
		"timestamp" => $_SERVER["REQUEST_TIME"],
		"ip" => $_SERVER["REMOTE_ADDR"],
	];

	writeData($data);
	echo("{\"status\":\"ok\"}");
}
else if($action === "list")
{
	if(!isset($_POST["secret"])
		|| $_POST["secret"] !== $adminSecret)
		error(401, "Missing or invalid secret");

	$data = readData();
	echo(json_encode($data));
}
else if($action === "clear")
{
	if(!isset($_POST["secret"])
		|| $_POST["secret"] !== $adminSecret)
		error(401, "Missing or invalid secret");

	writeData([]);
	echo("{\"status\":\"ok\"}");
}
else
{
	error(400, "Invalid action");
}

?>
