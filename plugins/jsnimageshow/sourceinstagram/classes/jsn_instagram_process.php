<?php
function _is_curl_installed() {
    if  (in_array  ('curl', get_loaded_extensions())) {
        return true;
    }
    else {
        return false;
    }
}

// Ouput text to user based on test
if (!_is_curl_installed()) {
  echo "cURL is NOT <span style=\"color:red\">installed</span> on this server";
  return false;
}
if (isset($_GET['code'])) {
	$code = $_GET['code'];
	
	$cookie = isset($_COOKIE['jsn_instagram_cookie']) ? $_COOKIE['jsn_instagram_cookie'] : '';
	if (!empty($cookie)) {
		$content		= json_decode($cookie);
		$client_id 		= (isset($content->instagram_app_id)) ? $content->instagram_app_id : '';
		$client_secret 	= (isset($content->instagram_secret)) ? $content->instagram_secret : '';
		$redirect_uri 	= (isset($content->callback_url)) ? $content->callback_url : '';
		
		$apiData = array(
			'client_id'       => $client_id,
			'client_secret'   => $client_secret,
			'grant_type'      => 'authorization_code',
			'redirect_uri'    => $redirect_uri,
			'code'            => $code
		);
		$apiHost = 'https://api.instagram.com/oauth/access_token';
	
		try {
			$curl = curl_init($apiHost);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $apiData);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($curl);
			curl_close($curl);
			if ($result) {
				setcookie('jsn_instagram_access_token_cookie', $result, time() + (3* 60), '/');
				setcookie('jsn_instagram_cookie', '', 0, '/');
			}
		} catch(Exception $e) {
			var_dump($e);
		}
	}
}
?>
<script type="text/javascript">
	window.close();
</script>
		
		