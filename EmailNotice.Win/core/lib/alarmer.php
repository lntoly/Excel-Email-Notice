<?php
/**
 *
 * @author ciogao@gmail.com
 * Date: 14-5-25
 */
namespace lib;

use constant\config as config;

class alarmer
{
    private static $self = NULL;

    public static function instanse()
    {
        if (self::$self == NULL) self::$self = new self;
        return self::$self;
    }

    private static $_config;

    public function __construct()
    {
        self::$_config = config::getNoticeEmail();
    }

    public function SendEmail($subject, $content, $to = array(), $cc = array(), $bcc = array(), $attachments = array())
    {
		$subject .= '您好，工资明细，请查收';
	
        $mail = new mailer\PHPMailer();
        $mail->IsSMTP();

        $mail->CharSet  = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->SMTPAuth = TRUE;
        $mail->Host     = self::$_config['smtp_host'];
        $mail->Port     = self::$_config['smtp_port'];
        $mail->Username = self::$_config['smtp_user'];
        $mail->Password = self::$_config['smtp_pwd'];
        $mail->SetFrom(self::$_config['mail_from'], "=?UTF-8?B?" . base64_encode(self::$_config['mail_from']) . "?=");
        $mail->Subject = "=?UTF-8?B?" . base64_encode(self::$_config['subject_pre'] . '-' . $subject) . "?=";
        $mail->MsgHTML($content);
        $mail->IsHTML(TRUE);

        if (count($attachments) > 0) {
            foreach ($attachments as $attachment) {
                $mail->AddAttachment($attachment);
            }
        }

        $to  = array($to);
        $cc  = array_merge(self::$_config['mail_cc'], $cc);
        $bcc = array_merge(self::$_config['mail_bcc'], $bcc);

        if (count($to) > 0) {
            foreach ($to as $address) {
                if (!empty($address)) $mail->AddAddress($address);
            }
        }

        if (count($cc) > 0) {
            foreach ($cc as $address) {
                if (!empty($address)) $mail->AddCC($address);
            }
        }

        if (count($bcc) > 0) {
            foreach ($bcc as $address) {
                if (!empty($address)) $mail->AddBCC($address);
            }
        }

        $sendResult = $mail->Send();

        if (!$sendResult) {

        }

        return $sendResult;
    }
}