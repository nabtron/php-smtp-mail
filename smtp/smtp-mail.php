<?php
//
// This function was originally a part of phpBB2 (http://www.phpbb.com).
//
function smtp_mail($to, $subject, $message, $from = '')
{
    $headers = "" .
               //"Date: Fri, 3 Jan 2020 11:46:43 +0000" . "\r\n" .
               "From:" . $from . "\r\n" .
               "Reply-To:" . $from . "\r\n" .
               "Return-Path: <noreply@doctornabeel.com>\r\n" .
               "X-Mailer: PHPMailer 5.2.27 (https://github.com/PHPMailer/PHPMailer)" . "\r\n" .
               "Date: ".date("r")."\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
    
	$recipients = explode(',', $to);
	$user = 'khan@doctornabeel.com';
	$pass = '67%Secure!';
	// The server details that worked for you in the above step
	$smtp_host = 'mail.doctornabeel.com';
	//The port that worked for you in the above step
	$smtp_port = 26;
	if (!($socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 15))) {
		echo "Error connecting to '$smtp_host' ($errno) ($errstr)";
	}
	server_parse($socket, '220');
	fwrite($socket, 'EHLO ' . $smtp_host . "\r\n");
	server_parse($socket, '250');
	fwrite($socket, 'AUTH LOGIN' . "\r\n");
	server_parse($socket, '334');
	fwrite($socket, base64_encode($user) . "\r\n");
	server_parse($socket, '334');
	fwrite($socket, base64_encode($pass) . "\r\n");
	server_parse($socket, '235');
	fwrite($socket, 'MAIL FROM: <' . $user . '>' . "\r\n");
	server_parse($socket, '250');
	foreach ($recipients as $email) {
		fwrite($socket, 'RCPT TO: <' . $email . '>' . "\r\n");
		server_parse($socket, '250');
	}
	fwrite($socket, 'DATA' . "\r\n");
	server_parse($socket, '354');
	fwrite($socket, 'Subject: '
		. $subject . "\r\n" . 'To: <' . implode('>, <', $recipients) . '>'
		. "\r\n" . $headers . "\r\n\r\n" . $message . "\r\n");
	fwrite($socket, '.' . "\r\n");
	server_parse($socket, '250');
	fwrite($socket, 'QUIT' . "\r\n");
	fclose($socket);
	return true;
}
//Functin to Processes Server Response Codes
function server_parse($socket, $expected_response)
{
	$server_response = '';
	while (substr($server_response, 3, 1) != ' ') {
		if (!($server_response = fgets($socket, 256))) {
			echo 'Error while fetching server response codes.', __FILE__, __LINE__;
		}
	}
	if (!(substr($server_response, 0, 3) == $expected_response)) {
		echo 'Unable to send e-mail."' . $server_response . '"', __FILE__, __LINE__;
	}
}
