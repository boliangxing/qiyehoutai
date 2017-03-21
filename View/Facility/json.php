<?php

	for($i=0;$i<100;$i++){
		$data[0][$i]['name'] = date("Y-m-d H:i:s",time()+$i);
		$data[0][$i]['value'] = array(date("Y-m-d H:i:s",time()+$i),date("s",time()+$i));

		$data[1][$i]['name'] = date("Y-m-d H:i:s",time()+$i+1);
		$data[1][$i]['value'] = array(date("Y-m-d H:i:s",time()+$i),date("s",time()+$i+1));
	}

	// var_dump($data);
	echo json_encode($data);

