<?php
require_once "../library/game.php";

function show_users()
{
	global $mysqli;
	$sql = 'SELECT username, piece_color FROM players';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function show_user($b)
{
	global $mysqli;
	$sql = 'SELECT username, piece_color FROM players WHERE piece_color=?';
	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$b);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
	
}

function read_users()
{
	global $mysqli;
	$sql = 'SELECT * FROM players';
	$st = $mysqli -> prepare($sql);
	$st -> execute();
	$res = $st -> get_result();
	return($res -> fetch_all(MYSQLI_ASSOC));
}

function set_user($b,$input)
{ 
	if(!isset($input['username']) || $input['username']==' ')
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"No user name given."]);
		exit;
	}
	$username=$input['username'];
	global $mysqli;
	$sql = 'SELECT COUNT(*) AS c FROM players WHERE piece_color=? AND username IS NOT null';
	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$b);
	$st->execute();
	$res = $st->get_result();
	$r = $res->fetch_all(MYSQLI_ASSOC);
	if($r[0]['c']>0)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"Player $b is already set. Please SELECT another color."]);
		exit;
	}
	$sql = 'UPDATE players SET username=?, token=md5(CONCAT( ?,NOW())) WHERE piece_color=?';
	$st2 = $mysqli->prepare($sql);
	$st2->bind_param('sss',$username,$username,$b);
	$st2->execute();
	
	update_game_status();
	
	$sql = 'SELECT * FROM players WHERE piece_color=?';
	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$b);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);	
}

function handle_user($method,$b,$input)
{
	if($method=='GET')
	{
		show_user($b);
	}
	else if($method=='PUT')
	{
		set_user($b,$input);
	}
	else
	{
		header('HTTP/1.1 405 Method Not Allowed');
	}
}
	
function current_color($token)
{
	global $mysqli;
	if($token == null)
	{
		return(null);
	}
	$sql = 'SELECT * FROM players WHERE token=?';
	$st = $mysqli -> prepare($sql);
	$st -> bind_param('s',$token);
	$st -> execute();
	$res = $st -> get_result();
	if($row = $res -> fetch_assoc())
	{
		return($row['piece_color']);
	}
	return(null);
}

?>