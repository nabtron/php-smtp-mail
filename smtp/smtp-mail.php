<?php

/** 
  * usage example:
  * smtp_mail('to@email.com','subject','message body','sender name <sender@email.com>');
  */
function smtp_mail($to, $subject, $message, $from = '')
{
	// configurations
	$user = 'user@domain.com';
	$pass = 'password';
	$smtp_host = 'mail.domain.com'; // for ssl use: ssl://mail.domain.com
	$smtp_port = 26;

	$headers = "" .
               "From:" . $from . "\r\n" .
               "Reply-To:" . $from . "\r\n" .
               "Return-Path:" . $from . "\r\n" .
               "X-Mailer: PHPMailer 5.2.27 (https://github.com/PHPMailer/PHPMailer)" . "\r\n" .
               "Date: ".date("r")."\r\n" .
               "MIME-Version: 1.0" . "\r\n";
               "Content-Type: text/plain; charset=UTF-8" . "\r\n";
    	$recipients = explode(',', $to);
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
