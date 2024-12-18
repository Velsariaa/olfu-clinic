<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function upload_cloudinary($filename, &$response, &$error, &$secure_url, $type="image")
{
	$cloudinary = ci()->config->item('cloudinary');

	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL 			=> 'https://api.cloudinary.com/v1_1/'.$cloudinary['name'].'/'.$type.'/upload',
		CURLOPT_RETURNTRANSFER 	=> true,
		CURLOPT_ENCODING 		=> '',
		CURLOPT_MAXREDIRS 		=> 10,
		CURLOPT_TIMEOUT 		=> 0,
		CURLOPT_FOLLOWLOCATION 	=> true,
		CURLOPT_HTTP_VERSION 	=> CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST 	=> 'POST',
		CURLOPT_SSL_VERIFYPEER 	=> false,
		CURLOPT_POSTFIELDS => [
			'public_id' 	=> time().rand(10000, 99999), 
			'api_key' 		=> $cloudinary['api_key'],
			'file'			=> new CURLFILE($filename),
			'upload_preset' => $cloudinary['upload_preset'],
			'folder' 		=> $cloudinary['folder']
		]
	));

	$response = curl_exec($curl);

	$error = curl_error($curl);

	curl_close($curl);

	if ("" != $error)
		return false;

	if (!$response)
		return false;

	$response_js = json_decode($response, true);

	if (!find($response_js, 'asset_id'))
	{
		$error = 'Invalid response';
		
		return false;
	}

	$secure_url = find($response_js, 'secure_url');

	return true;
}