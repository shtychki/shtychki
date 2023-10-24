<?php

$data = json_decode(trim(file_get_contents('php://input')), true);

if (stripos($data['title'], 'Bitrix24') === false) {
	die;
}

sleep(3);

$roistatData = array(
    'roistat' => $data['visit_id'],
    'key'     => 'OWM3NDdjYjgwOWQwMjc3NWNhY2FmYWQyMzMyMDVmNTU6MTk1MzIw',
    'title'   => 'Заявка с виджета Битрикс',
    'phone'   => $data['phone'],
    'name'    => $data['name'],
    'email'   => $data['email'],
    'fields'  => array(
    ),
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://cloud.roistat.com/api/proxy/1.0/leads/add?" . http_build_query($roistatData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);

// $output = curl_exec($ch);

curl_close($ch);
