<?php
class Mail {

	public static function send($from, $to, $subject, $template, $data){
		require_once( pathToLib . 'plugins/swift/lib/swift_required.php');
		switch(strtoupper(SMTPEncryption)):
			case "SSL":
			case "TLS":
				$serverConn = Swift_SmtpTransport::newInstance(SMTPHost, SMTPPort, strtolower(SMTPEncryption))->setUsername(SMTPUser)->setPassword(SMTPPass);
				break;
			default:
				$serverConn = Swift_SmtpTransport::newInstance(SMTPHost, SMTPPort)->setUsername(SMTPUser)->setPassword(SMTPPass);
		endswitch;

		$Mail		= Swift_Mailer::newInstance($serverConn);
		$Message 	= Swift_Message::newInstance();
		$Headers 	= $Message->getHeaders();
		$Headers->addPathHeader('Return-Path', STMPRPath);

		$Message->setFrom($from);
		$Message->setSubject($subject);
		$Message->setTo($to);

		$Message->setBody(self::loadTemplate( pathToTemplate . $template, $data), "text/html");
		$Mail->batchSend($Message);
	}

   public static function loadTemplate($template, $options) {
        $tmpl = new Template( $template );
        $tmpl->replace($options);
        return $tmpl->render();
    }

}
?>
