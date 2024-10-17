<?php
require_once "../library/users.php";
require_once "../library/game.php";

function show_board()
{
	global $mysqli;
	$sql = 'SELECT * FROM board';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC),JSON_PRETTY_PRINT);
}

function reset_board()
{
	global $mysqli;
	$sql = 'CALL clean_board()';
	$mysqli->query($sql);
	show_board();
}

function load_init_board()
{
	global $mysqli;
	$sql = 'CALL fill_board()';
	$mysqli->query($sql);
	load_init_status();
	show_board();
	print json_encode(read_status(), JSON_PRETTY_PRINT);
}

function read_board()
{
	global $mysqli;
	$sql = 'SELECT * FROM board';
	$st = $mysqli -> prepare($sql);
	$st -> execute();
	$res = $st -> get_result();
	return($res -> fetch_all(MYSQLI_ASSOC));
}

function show_piece($x,$y)
{
	global $mysqli;
	$sql = 'SELECT * FROM board WHERE x=? AND y=?';
	$st = $mysqli -> prepare($sql);
	$st -> bind_param('ii',$x,$y);
	$st -> execute();
	$res = $st -> get_result();
	header('Content-type: application/json');
	print json_encode($res -> fetch_all(MYSQLI_ASSOC),JSON_PRETTY_PRINT);
}


function set_piece($x,$y,$pcolor,$p,$token)
{
	if($token == null || $token == '')
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"Token is not set."]);
		exit;
	}
	
	$color = current_color($token);
	if($color == null)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"You are not a player of this game."]);
		exit;
	}
	
	$status = read_status();
	if($status['status'] != 'piece_positioning')
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"It is not time for piece positioning."]);
		exit;
	}
	
	if($status['p_turn'] != $color)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"It is not your turn."]);
		exit;
	}
	
	if($color == 'R' && $x > 4)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"!!!! The $color player is ONLY allowed to place his-hers pieces in lines 1-4 !!!!"]);
		exit;
	}
	
	if($color == 'B' && $x < 7)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"!!!! The $color player is ONLY allowed to place his/hers pieces in lines 7-10 !!!!"]);
		exit;
	}
	
	if($x > 11 || $y > 11)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"!!!! The value of x or y is outside the board !!!!"]);
		exit;
	}
	
	if($x < 1 || $y < 1)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"!!!! The value of x or y is outside the board !!!!"]);
		exit;
	}
	
	$orig_board = read_board();
	$board = convert_board($orig_board);
	
	if($board[$x][$y]['piece_color'] != null || $board[$x][$y]['piece'] != null)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"This cell is already taken. Try another cell."]);
		exit;
	}
	
	global $mysqli;
	$sql3 = 'SELECT COUNT(*) AS c FROM board WHERE piece_color=? AND piece=?';
	$st3 = $mysqli->prepare($sql3);
	$st3->bind_param('ss',$pcolor,$p);
	$st3->execute();
	$res3 = $st3->get_result();
	$pieces_on_board = $res3->fetch_assoc()['c'];
	
	if($pcolor == $color)
	{
		if($p == 'F' && $pieces_on_board == 1)
		{
			print json_encode(["You can't place more than 1 FLAG (F) in your board."]);
		}
		else if($p == 'B' && $pieces_on_board == 6)
		{
			print json_encode(["You can't place more than 6 BOMBS (B) in your board."]);
		}
		else if($p == 'S' && $pieces_on_board == 1)
		{
			print json_encode(["You can't place more than 1 SPY (S) in your board."]);
		}
		else if($p == '1' && $pieces_on_board == 1)
		{
			print json_encode(["You can't place more than 1 MARSHAL (1) in your board."]);
		}
		else if($p == '2' && $pieces_on_board == 1)
		{
			print json_encode(["You can't place more than 1 GENERAL (2) in your board."]);
		}
		else if($p == '3' && $pieces_on_board == 2)
		{
			print json_encode(["You can't place more than 2 COLONELS (3) in your board."]);
		}
		else if($p == '4' && $pieces_on_board == 3)
		{
			print json_encode(["You can't place more than 3 MAJORS (4) in your board."]);
		}
		else if($p == '5' && $pieces_on_board == 4)
		{
			print json_encode(["You can't place more than 4 CAPTAINS (5) in your board."]);
		}
		else if($p == '6' && $pieces_on_board == 4)
		{
			print json_encode(["You can't place more than 4 LIEUTENANTS (6) in your board."]);
		}
		else if($p == '7' && $pieces_on_board == 4)
		{
			print json_encode(["You can't place more than 4 SERGEANTS (7) in your board."]);
		}
		else if($p == '8' && $pieces_on_board == 5)
		{
			print json_encode(["You can't place more than 5 MINERS (8) in your board."]);
		}
		else if($p == '9' && $pieces_on_board == 8)
		{
			print json_encode(["You can't place more than 8 SCOUTS (9) in your board."]);
		}
		else
		{
			switch($p)
			{
				case 'F': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (F). You have to add 1 FLAG in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				case 'B': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (B). You have to add 6 BOMBS in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				case 'S': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (S). You have to add 1 SPY in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				case '1': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (1). You have to add 1 MARSHAL in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				case '2': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (2). You have to add 1 GENERAL in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				case '3': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (3). You have to add 2 COLONELS in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				case '4': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (4). You have to add 3 MAJORS in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				case '5': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (5). You have to add 4 CAPTAINS in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				case '6': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (6). You have to add 4 LIEUTENANTS in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				case '7': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (7). You have to add 4 SERGEANTS in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				case '8': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (8). You have to add 5 MINERS in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				case '9': $pieces_on_board++;
					print json_encode(["You added $pieces_on_board (9). You have to add 8 SCOUTS in total."]);
					do_place($x,$y,$pcolor,$p);
					update_pturn_game_status($token);
					break;
				default: header("HTTP/1.1 404 Not Found");
					exit;
			}
		}
	}	
}

function do_place($x,$y,$pcolor,$p)
{
	global $mysqli;
	$sql = 'CALL place_piece(?,?,?,?);';
	$st = $mysqli -> prepare($sql);
	$st -> bind_param('iiss',$x,$y,$pcolor,$p);
	$st -> execute();
	
	header('Content-type: application/json');
	print json_encode(read_board(), JSON_PRETTY_PRINT);
}


function move_piece($x,$y,$x2,$y2,$token)
{
	if($token == null || $token == '')
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"token is not set."]);
		exit;
	}
	
	$color = current_color($token);
	if($color == null)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"You are not a player of this game."]);
		exit;
	}
	
	$status = read_status();
	if($status['status'] != 'started')
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"Game has not started yet."]);
		exit;
	}
	
	if($status['p_turn'] != $color)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"It is not your turn."]);
		exit;
	}
	
	if($x > 11 || $y > 11)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"!!!! The value of x or y is outside the board !!!!"]);
		exit;
	}
	
	if($x < 1 || $y < 1)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"!!!! The value of x or y is outside the board !!!!"]);
		exit;
	}
	
	if($x2 > 11 || $y2 > 11)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"!!!! The value of x or y is outside the board !!!!"]);
		exit;
	}
	
	if($x2 < 1 || $y2 < 1)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"!!!! The value of x or y is outside the board !!!!"]);
		exit;
	}
	
	$orig_board = read_board();
	$board = convert_board($orig_board);
	
	if($board[$x][$y]['piece'] == null)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode("You haven't selected a piece.");
		exit;
	}
	
	if($board[$x2][$y2]['b_color'] == 'GY')
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode("You cannot jump in the lakes!");
		exit;
	}
	
	$n = add_valid_moves_to_piece($board,$color,$x,$y);
	$m = add_valid_moves_to_board($board,$color);
	
	if($board[$x][$y]['piece'] == 'B')
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode("The BOMB piece cannot be moved !!!");
		exit;
	}
	
	if($board[$x][$y]['piece'] == 'F')
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode("The FLAG piece cannot be moved !!!");
		exit;
	}
	
	if($m == 0)
	{
		print json_encode(["You have no moves left. YOU LOST THE GAME !!!"]);
		if ($color = 'R')
		{
			$color = 'B';
		}
		else
		{
			$color = 'R';
		}
		update_result($color);
		exit;
	}
	
	if($n == 0)
	{
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"This piece cannot move."]);
		exit;
	}
	
	foreach($board[$x][$y]['moves'] as $i => $move)
	{
		if($x2 == $move['x'] && $y2 == $move['y'])
		{	
			if($board[$x2][$y2]['piece'] == null)
			{
				do_move($x,$y,$x2,$y2);
				exit;
			}
			else if($board[$x][$y]['piece'] == $board[$x2][$y2]['piece'])
			{
				print json_encode("Your piece was the same with the opponent's. You defeat each other.");
				attack_on_same_piece($x,$y,$x2,$y2);
				exit;
			}
			else if($board[$x][$y]['piece'] < $board[$x2][$y2]['piece'])
			{
				if($board[$x][$y]['piece'] != '8' && $board[$x2][$y2]['piece'] == 'B')
				{
					print json_encode("!!! BOMB !!! You lost your piece.");
					attack_on_stronger_piece($x,$y,$x2,$y2);
					exit;
				}
				else if($board[$x][$y]['piece'] == '8' && $board[$x2][$y2]['piece'] == 'B')
				{
					print json_encode("1 BOMB has been defused.");
					do_move($x,$y,$x2,$y2);
					exit;
				}
				else if($board[$x2][$y2]['piece'] == 'F')
				{
					print json_encode("You captured the enemy's FLAG. YOU WON THE GAME !!!");
					do_move($x,$y,$x2,$y2);
					update_result($color);
					exit;
				}
				else
				{
					print json_encode("Your piece won the enemy's one.");
					do_move($x,$y,$x2,$y2);
					exit;
				} 
			}
			else if($board[$x][$y]['piece'] > $board[$x2][$y2]['piece'])
			{
				if($board[$x][$y]['piece'] == 'S' && $board[$x2][$y2]['piece'] == '1')
				{
					print json_encode("Your SPY has been successfully revealed the MARSHAL's position. Well done, you destroyed it.");
					do_move($x,$y,$x2,$y2);
					exit;
				}
				else if($board[$x2][$y2]['piece'] == 'F')
				{
					print json_encode("You captured the enemy's FLAG. YOU WON THE GAME !!!");
					do_move($x,$y,$x2,$y2);
					update_result($color);
					exit;
				}
				else
				{
					print json_encode("You attacked a stronger piece. You lost your piece.");
					attack_on_stronger_piece($x,$y,$x2,$y2);
					exit;
				}
			}
		}
	}
	
	header("HTTP/1.1 400 Bad Request");
	print json_encode(['errormesg'=>"This move is legal."]);
	exit;
	
}

function do_move($x,$y,$x2,$y2)
{
	global $mysqli;
	$sql = 'CALL move_piece(?,?,?,?);';
	$st = $mysqli -> prepare($sql);
	$st -> bind_param('iiii',$x,$y,$x2,$y2);
	$st -> execute();
	
	header('Content-type: application/json');
	print json_encode(read_board(), JSON_PRETTY_PRINT);
}


function attack_on_same_piece($x,$y,$x2,$y2)
{
	global $mysqli;
	$sql = 'CALL attack_on_same_piece(?,?,?,?);';
	$st = $mysqli -> prepare($sql);
	$st -> bind_param('iiii',$x,$y,$x2,$y2);
	$st -> execute();
	
	header('Content-type: application/json');
	print json_encode(read_board(), JSON_PRETTY_PRINT);
}

function attack_on_stronger_piece($x,$y,$x2,$y2)
{
	global $mysqli;
	$sql = 'CALL attack_on_stronger_piece(?,?,?,?);';
	$st = $mysqli -> prepare($sql);
	$st -> bind_param('iiii',$x,$y,$x2,$y2);
	$st -> execute();
	
	header('Content-type: application/json');
	print json_encode(read_board(), JSON_PRETTY_PRINT);
}

function update_result($color)
{
	global $mysqli;
	$sql = 'CALL update_result(?);';
	$st = $mysqli -> prepare($sql);
	$st -> bind_param('s',$color);
	$st -> execute();
	
	print json_encode(read_status(), JSON_PRETTY_PRINT);
}

function add_valid_moves_to_board(&$board,$b)
{
	$number_of_moves = 0;
	for($x=1; $x<11; $x++)
	{
		for($y=1; $y<11; $y++)
		{
			$number_of_moves += add_valid_moves_to_piece($board, $b, $x, $y);
		}
	}
	return($number_of_moves);
}

function add_valid_moves_to_piece(&$board, $b, $x, $y)
{
	$number_of_moves = 0;
	if($board[$x][$y]['piece_color'] == $b)
	{
		switch($board[$x][$y]['piece'])
		{
			case 'F': $number_of_moves += flag_moves($board, $b, $x, $y);
				break;
			case 'B': $number_of_moves += bomb_moves($board, $b, $x, $y);
				break;
			case 'S': $number_of_moves += spy_moves($board, $b, $x, $y);
				break;
			case '1': $number_of_moves += marshal_moves($board, $b, $x, $y);
				break;
			case '2': $number_of_moves += general_moves($board, $b, $x, $y);
				break;
			case '3': $number_of_moves += colonel_moves($board, $b, $x, $y);
				break;
			case '4': $number_of_moves += major_moves($board, $b, $x, $y);
				break;
			case '5': $number_of_moves += captain_moves($board, $b, $x, $y);
				break;
			case '6': $number_of_moves += lieutenant_moves($board, $b, $x, $y);
				break;
			case '7': $number_of_moves += sergeant_moves($board, $b, $x, $y);
				break;
			case '8': $number_of_moves += miner_moves($board, $b, $x, $y);
				break;
			case '9': $number_of_moves += scout_moves($board, $b, $x, $y);
				break;
		}
	}
	return($number_of_moves);
}

function flag_moves(&$board, $b, $x, $y)
{
	return(0);
}

function bomb_moves(&$board, $b, $x, $y)
{
	return(0);
}

function spy_moves(&$board, $b, $x, $y)
{
	$m = [ [1,0], [-1,0], [0,1], [0,-1] ];
	$moves = [];
	
	foreach($m as $k => $t)
	{
		$x2 = $x + $t[0];
		$y2 = $y + $t[1];
		if($x2>=1 && $x2<=10 && $y2>=1 && $y2<=10 && $board[$x2][$y2]['piece_color']!=$b && $board[$x2][$y2]['b_color']!='GY')
		{
			//if the destination cell is inside the board, the cell is not occupied by a piece of the same color and the cell is not grey color (a lake)
			$move=['x'=>$x2, 'y'=>$y2];
			$moves[] = $move;
		}
	}
	
	$board[$x][$y]['moves'] = $moves;
	return (sizeof($moves));
}

function marshal_moves(&$board, $b, $x, $y)
{
	$m = [ [1,0], [-1,0], [0,1], [0,-1] ];
	$moves = [];
	
	foreach($m as $k => $t)
	{
		$x2 = $x + $t[0];
		$y2 = $y + $t[1];
		if($x2>=1 && $x2<=10 && $y2>=1 && $y2<=10 && $board[$x2][$y2]['piece_color']!=$b && $board[$x2][$y2]['b_color']!='GY')
		{
			//if the destination cell is inside the board, the cell is not occupied by a piece of the same color and the cell is not grey color (a lake)
			$move=['x'=>$x2, 'y'=>$y2];
			$moves[] = $move;
		}
	}
	
	$board[$x][$y]['moves'] = $moves;
	return (sizeof($moves));
}

function general_moves(&$board, $b, $x, $y)
{
	$m = [ [1,0], [-1,0], [0,1], [0,-1] ];
	$moves = [];
	
	foreach($m as $k => $t)
	{
		$x2 = $x + $t[0];
		$y2 = $y + $t[1];
		if($x2>=1 && $x2<=10 && $y2>=1 && $y2<=10 && $board[$x2][$y2]['piece_color']!=$b && $board[$x2][$y2]['b_color']!='GY')
		{
			//if the destination cell is inside the board, the cell is not occupied by a piece of the same color and the cell is not grey color (a lake)
			$move=['x'=>$x2, 'y'=>$y2];
			$moves[] = $move;
		}
	}
	
	$board[$x][$y]['moves'] = $moves;
	return (sizeof($moves));
}

function colonel_moves(&$board, $b, $x, $y)
{
	$m = [ [1,0], [-1,0], [0,1], [0,-1] ];
	$moves = [];
	
	foreach($m as $k => $t)
	{
		$x2 = $x + $t[0];
		$y2 = $y + $t[1];
		if($x2>=1 && $x2<=10 && $y2>=1 && $y2<=10 && $board[$x2][$y2]['piece_color']!=$b && $board[$x2][$y2]['b_color']!='GY')
		{
			//if the destination cell is inside the board, the cell is not occupied by a piece of the same color and the cell is not grey color (a lake)
			$move=['x'=>$x2, 'y'=>$y2];
			$moves[] = $move;
		}
	}
	
	$board[$x][$y]['moves'] = $moves;
	return (sizeof($moves));
}

function major_moves(&$board, $b, $x, $y)
{
	$m = [ [1,0], [-1,0], [0,1], [0,-1] ];
	$moves = [];
	
	foreach($m as $k => $t)
	{
		$x2 = $x + $t[0];
		$y2 = $y + $t[1];
		if($x2>=1 && $x2<=10 && $y2>=1 && $y2<=10 && $board[$x2][$y2]['piece_color']!=$b && $board[$x2][$y2]['b_color']!='GY')
		{
			//if the destination cell is inside the board, the cell is not occupied by a piece of the same color and the cell is not grey color (a lake)
			$move=['x'=>$x2, 'y'=>$y2];
			$moves[] = $move;
		}
	}
	
	$board[$x][$y]['moves'] = $moves;
	return (sizeof($moves));
}

function captain_moves(&$board, $b, $x, $y)
{
	$m = [ [1,0], [-1,0], [0,1], [0,-1] ];
	$moves = [];
	
	foreach($m as $k => $t)
	{
		$x2 = $x + $t[0];
		$y2 = $y + $t[1];
		if($x2>=1 && $x2<=10 && $y2>=1 && $y2<=10 && $board[$x2][$y2]['piece_color']!=$b && $board[$x2][$y2]['b_color']!='GY')
		{
			//if the destination cell is inside the board, the cell is not occupied by a piece of the same color and the cell is not grey color (a lake)
			$move=['x'=>$x2, 'y'=>$y2];
			$moves[] = $move;
		}
	}
	
	$board[$x][$y]['moves'] = $moves;
	return (sizeof($moves));
}

function lieutenant_moves(&$board, $b, $x, $y)
{
	$m = [ [1,0], [-1,0], [0,1], [0,-1] ];
	$moves = [];
	
	foreach($m as $k => $t)
	{
		$x2 = $x + $t[0];
		$y2 = $y + $t[1];
		if($x2>=1 && $x2<=10 && $y2>=1 && $y2<=10 && $board[$x2][$y2]['piece_color']!=$b && $board[$x2][$y2]['b_color']!='GY')
		{
			//if the destination cell is inside the board, the cell is not occupied by a piece of the same color and the cell is not grey color (a lake)
			$move=['x'=>$x2, 'y'=>$y2];
			$moves[] = $move;
		}
	}
	
	$board[$x][$y]['moves'] = $moves;
	return (sizeof($moves));
}

function sergeant_moves(&$board, $b, $x, $y)
{
	$m = [ [1,0], [-1,0], [0,1], [0,-1] ];
	$moves = [];
	
	foreach($m as $k => $t)
	{
		$x2 = $x + $t[0];
		$y2 = $y + $t[1];
		if($x2>=1 && $x2<=10 && $y2>=1 && $y2<=10 && $board[$x2][$y2]['piece_color']!=$b && $board[$x2][$y2]['b_color']!='GY')
		{
			//if the destination cell is inside the board, the cell is not occupied by a piece of the same color and the cell is not grey color (a lake)
			$move=['x'=>$x2, 'y'=>$y2];
			$moves[] = $move;
		}
	}
	
	$board[$x][$y]['moves'] = $moves;
	return (sizeof($moves));
}

function miner_moves(&$board, $b, $x, $y)
{
	$m = [ [1,0], [-1,0], [0,1], [0,-1] ];
	$moves = [];
	
	foreach($m as $k => $t)
	{
		$x2 = $x + $t[0];
		$y2 = $y + $t[1];
		if($x2>=1 && $x2<=10 && $y2>=1 && $y2<=10 && $board[$x2][$y2]['piece_color']!=$b && $board[$x2][$y2]['b_color']!='GY')
		{
			//if the destination cell is inside the board, the cell is not occupied by a piece of the same color and the cell is not grey color (a lake)
			$move=['x'=>$x2, 'y'=>$y2];
			$moves[] = $move;
		}
	}
	
	$board[$x][$y]['moves'] = $moves;
	return (sizeof($moves));
}

function scout_moves(&$board, $b, $x, $y)
{
	$m = [ [1,0], [-1,0], [0,1], [0,-1] ];
	$moves = [];
	
	foreach($m as $k=>$t)
	{
		for($i=$x+$t[0],$j=$y+$t[1]; $i>=1 && $i<=10 && $j>=1 && $j<=10; $i+=$t[0], $j+=$t[1])
		{
			if($board[$i][$j]['b_color'] == 'GY')
			{
				break;
			}
			else
			{
				if($board[$i][$j]['piece_color'] == null)
				{ 
					$move=['x'=>$i, 'y'=>$j];
					$moves[]=$move;
				} 
				else if($board[$i][$j]['piece_color'] != $b) 
				{
					$move=['x'=>$i, 'y'=>$j];
					$moves[]=$move;
					break;
				}
				else if($board[$i][$j]['piece_color'] == $b)
				{
					break;
				}
			}
		}

	}
	$board[$x][$y]['moves'] = $moves;
	return(sizeof($moves));
}

function convert_board(&$orig_board)
{
	$board = [];
	foreach($orig_board as $i => &$row)
	{
		$board[$row['x']][$row['y']] = &$row;
	}
	return($board);
}
?>