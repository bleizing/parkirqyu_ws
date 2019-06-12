<?php

return [

	'status_codes' => [
		'status_code_success' => 200,
		'status_code_created' => 201,
		'status_code_updated' => 202,
		'status_code_deleted' => 203,
		'status_code_no_content' => 204,
		'status_code_partial_content' => 206,
		'status_code_bad_request' => 400,
		'status_code_unauthorized' => 401,
		'status_code_forbidden' => 403,
		'status_code_not_found' => 404,
		'status_code_internal_server_error' => 500,
		'status_code_service_unavailable' => 503
	],

	'error_codes' => [
		'error_code_success' => 0,
		'error_code_validation' => 1,
		'error_code_data_exist' => 2,
		'error_code_data_not_exist' => 3,
		'error_code_data_not_match' => 4,
		'error_code_create_data_failed' => 5,
		'error_code_unauthorized' => 6,
		'error_code_forbidden' => 7,
		'error_code_token_invalid' => 8,
		'error_code_token_expired' => 9,
		'error_code_token_not_exist' => 10
	],

	'upload_files' => [
		'upload_files_article_cover_image' => 'uploads/article/images/', 
		'upload_files_slider_image' => 'uploads/slider/images/'
	]
];