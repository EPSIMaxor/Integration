<?php

	try {
		$bdd = new PDO('mysql:host=localhost;dbname=inte', $_SEREVR['bdd_username'], $_SERVER['bdd_password']);
	}
	catch( PDOException $Exception ) {
		header('HTTP/1.1 500 Internal Server Error');
	    	echo 'Erreur de connexion : ' . $Exception->getMessage() . ' ; code : ' . $Exception->getCode();
		exit();
	}

	$method = $_SERVER['REQUEST_METHOD'];
	$path = $_SERVER['REQUEST_URI'];
	
	$output = array();

	$output['info'] = array(
		'method' => $method,
		'path' => $path
	);


	if($path == '/rest/users/') {
		
		if($method == 'GET') {

			$output['users'] = array();

			$req = $bdd->prepare('SELECT * FROM users;');
			$req->execute();
			while($row = $req->fetch()) {
				$output['users'][] = array(
					'username' => $row['username'],
					'token' => $row['token'],
					'link' => '/rest/users/' . $row['id']
				);
			}
		}
		else {

			header("HTTP/1.1 400 Bad Request");
			$output['error'] = array(
				'code' => 5,
				'message' => 'Invalid method for this path'
			);
		}

	}
	elseif(strpos($path, '/rest/users/') === 0) {
		$id = substr($path, strlen('/rest/users/'));
		
		if($method == 'GET') {


			$output['users'] = array();

			$req = $bdd->prepare('SELECT * FROM users where id = :id;');
			$req->bindParam(':id', $id, PDO::PARAM_INT);
			$req->execute();
			while($row = $req->fetch()) {
				$output['users'][] = array(
					'username' => $row['username'],
					'token' => $row['token'],
					'link' => '/rest/users/' . $row['id']
				);
			}
		}
		else {

			header("HTTP/1.1 400 Bad Request");
			$output['error'] = array(
				'code' => 5,
				'message' => 'Invalid method for this path'
			);
		}

	}


	header('Content-Type: application/json');
	echo json_encode($output);

?>
