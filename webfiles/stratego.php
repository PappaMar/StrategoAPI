<?php
require_once "../library/dbconnect.php";
require_once "../library/board.php";
require_once "../library/game.php";
require_once "../library/users.php";

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

switch ($r=array_shift($request)) 
{
	case 'board':
		switch ($b=array_shift($request)) 
		{
			case null: handle_board($method);
				break;
			case 'piece': handle_piece($method, $request[0],$request[1],$input);
				break;
		}
		break;
	case 'status':
		if (sizeof($request)==0) 
		{
			handle_status($method);
		}
		else
		{
			header("HTTP/1.1 404 Not Found");
		}
		break;
	case 'players': handle_player($method, $request, $input);
		break;
	case 'exit': handle_end_game($method);
		break;
	default: header("HTTP/1.1 404 Not Found");
		exit;
}

function handle_board($method)
{
	if($method=='GET')
	{
		show_board();
	}
	else if($method=='POST')
	{
		reset_board();
	}
	else if($method=='PUT')
	{
		load_init_board();
	}
	else
	{
		header('HTTP/1.1 405 Method Not Allowed');
	}
}

function handle_piece($method, $x, $y, $input)
{
	//emfanisi pioniou me tis x,y syntetagmenes
	if($method=='GET')
	{
		show_piece($x,$y);
	}
	//topothetisi pioniou se x,y syntetagmeni
	else if($method=='PUT')
	{
		move_piece($x,$y,$input['x'],$input['y'],$input['token']);
	}
	else if($method=='POST')
	{
		set_piece($x,$y,$input['piece_color'],$input['piece'],$input['token']);
	}
}

function handle_player($method, $p, $input)
{
	switch($b=array_shift($p))
	{
		case null: 
				if($method=='GET')
				{
					show_users($method);
				}
				else
				{
					header("HTTP/1.1 400 Bad Request");
					print json_encode(['errormesg'=>"Method $method not allowed here."]);
				}
			break;				
		case 'B': handle_user($method,$b,$input);
			break;
		case 'R': handle_user($method,$b,$input);
			break;
		default: header("HTTP/1.1 404 Not Found");
			print json_encode(['errormesg'=>"Players $b not found."]);
			break;
	}
}


function handle_status($method)
{
	if($method=='GET')
	{
		show_status();
	}
	else
	{
		header('HTTP/1.1 405 Method Not Allowed');
	}
}

function handle_end_game($method)
{
	if($method=='POST')
	{
		end_game();
	}
	else
	{
		header('HTTP/1.1 405 Method Not Allowed');
	}
}
?>