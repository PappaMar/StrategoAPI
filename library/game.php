<?php
require_once "../library/users.php";

function show_status() 
{
	global $mysqli;
	$sql = 'SELECT * FROM game_status';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function update_game_status()
{
	global $mysqli;
	$sql = 'SELECT * FROM game_status';
	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	$status = $res->fetch_assoc();
	
	$new_status=null;
	$new_turn=null;
	
	$sql3 = 'SELECT COUNT(*) AS aborted FROM players WHERE last_action< (NOW() - INTERVAL 5 MINUTE)';
	$st3=$mysqli->prepare($sql3);
	$st3->execute();
	$res3 = $st3->get_result();
	$aborted = $res3->fetch_assoc()['aborted'];
	if($aborted>0)
	{
		$sql2 = 'UPDATE players SET username=NULL, token=NULL WHERE last_action< (NOW() - INTERVAL 5 MINUTE)';
		$st2 = $mysqli->prepare($sql2);
		$st2->execute();
		if($status['status']=='piece_positioning')
		{
			$new_status='aborted';
		}
		else if($status['status']=='started')
		{
			$new_status='aborted';
		}
	}
	
	$sql4 = 'SELECT COUNT(*) AS c FROM players WHERE username IS NOT NULL';
	$st4 = $mysqli->prepare($sql4);
	$st4->execute();
	$res4 = $st4->get_result();
	$active_players = $res4->fetch_assoc()['c'];
	
	switch($active_players)
	{
		case 0: $new_status='not_active';
			break;
		case 1: $new_status='initialized';
			print json_encode(["The 1st player has joined the game."]);
			break;
		case 2: $new_status='piece_positioning';
			print json_encode(["The 2nd player has joined the game. Get ready to place your pieces into the board!"]);
			if($status['p_turn']==null)
			{
				$new_turn='R';
			}
			break;
	}
	
	$sql6 = 'UPDATE game_status SET status=?, p_turn=?';
	$st6 = $mysqli->prepare($sql6);
	$st6->bind_param('ss',$new_status,$new_turn);
	$st6->execute();	
}

function update_pturn_game_status($token)
{
	
	$new_status=null;
	$new_turn=null;
	
	$color = current_color($token);
	
	global $mysqli;
	$sql = 'SELECT COUNT(*) AS p FROM board WHERE piece_color=?';
	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$color);
	$st->execute();
	$res = $st->get_result();
	$pieces_placed = $res->fetch_assoc()['p'];
	
	if($pieces_placed == 40)
	{
		if($color == 'R')
		{
			$new_turn = 'B';
			print json_encode(["The $color player has placed his-her pieces."]);
			$sql1 = 'UPDATE game_status SET p_turn=?';
			$st1 = $mysqli->prepare($sql1);
			$st1->bind_param('s',$new_turn);
			$st1->execute();
		}
		else if($color == 'B')
		{
			$new_status = 'started';
			$new_turn = 'R';
			print json_encode(["The $color player has placed his-her pieces. The game is about to start. HAVE FUN!"]);
			$sql2 = 'UPDATE game_status SET status=?, p_turn=?';
			$st2 = $mysqli->prepare($sql2);
			$st2->bind_param('ss',$new_status,$new_turn);
			$st2->execute();
		}
	}
}

function read_status()
{
	global $mysqli;
	$sql = 'SELECT * FROM game_status';
	$st = $mysqli -> prepare($sql);
	$st -> execute();
	$res = $st -> get_result();
	$status = $res -> fetch_assoc();
	return($status);
}

function load_init_status()
{
	global $mysqli;
	$new_status = 'started';
	$new_turn = 'R';
	$sql = 'UPDATE game_status SET status=?, p_turn=?';
	$st = $mysqli->prepare($sql);
	$st->bind_param('ss',$new_status,$new_turn);
	$st->execute();
}

function end_game()
{
	print json_encode("The game is about to end..\r\n");
	global $mysqli;
	$sql = 'CALL clean_board()';
	$mysqli->query($sql);
	print json_encode("board info:", JSON_PRETTY_PRINT);
	header('Content-type: application/json');
	print json_encode(read_board(),JSON_PRETTY_PRINT);
	
	$status='not active';
	$p_turn=null;
	$result=null;
	$sql1 = 'UPDATE game_status SET status=?, p_turn=?, result=?';
	$st1 = $mysqli->prepare($sql1);
	$st1->bind_param('sss',$status,$p_turn,$result);
	$st1->execute();
	print json_encode("status info:");
	print json_encode(read_status(),JSON_PRETTY_PRINT);
	
	$username=null;
	$token=null;
	$sql2 = 'UPDATE players SET username=?, token=?';
	$st2 = $mysqli->prepare($sql2);
	$st2->bind_param('ss',$username,$token);
	$st2->execute();
	print json_encode("players info:");
	print json_encode(read_users(),JSON_PRETTY_PRINT);	
}
?>