<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/config.php';

use PHPMailer\PHPMailer;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$sitePath = __DIR__ . '/';

class commonActs {
    /**
     * Listener queue emails data.
     */
    static function receiveEmailDataFromQueue() {
        global $queue_name, $queue_host, $queue_port, $queue_user, $queue_pass;
        $queue_host = commonConfig::getenv('QUEUE_HOST');
        $queue_port = commonConfig::getenv('QUEUE_PORT');
        $queue_user = commonConfig::getenv('QUEUE_USER');
        $queue_pass = commonConfig::getenv('QUEUE_PASS');
        $queue_name = commonConfig::getenv('QUEUE_NAME');
        try {
            $queue_connect = new AMQPStreamConnection($queue_host, $queue_port, $queue_user, $queue_pass);
            $channel = $queue_connect->channel();
            $channel->queue_declare($queue_name, false, false, false, false);
            $callback = function ($msg) {
                commonActs::receiveEmailDataFromQueueCallback($msg);
            };
            $channel->basic_consume($queue_name, '', false, true, false, false, $callback);
            while ($channel->is_open()) {
                $channel->wait();
            }
            $channel->close();
            $queue_connect->close();
        }
        catch (Exception $exc) {
          error_log($exc->getMessage() . $exc->getTraceAsString());
          return FALSE;
        }
    }
    /**
     * Send email from queue data.
     */
    static function receiveEmailDataFromQueueCallback($msg) {
        global $xmailer, $from, $from_name, $sitePath, $mail;
        try {
            $xmailer = substr(strrchr(commonConfig::getenv('USER_FROM'), "@"), 1);
            $from = commonConfig::getenv('USER_FROM');
            $mail = new PHPMailer\PHPMailer();
            $mail->CharSet = 'UTF-8';
            $dataEmail = unserialize(base64_decode($msg->body));
            if (!self::validateEmailDataToQueue($dataEmail)) {
                return FALSE;
            }
            $mail->XMailer = $xmailer;
            $mail->setFrom($from, $from_name);
            $mail->addReplyTo($from, $from_name);
            $mail->addAddress($dataEmail['email'], $dataEmail['email_uname']);
            $mail->Subject = $dataEmail['email_subject'];
            $mail->DKIM_selector = 'mail';
            $mail->AddCustomHeader('List-Unsubscribe', '<' . $dataEmail['unsubsribeUrl'] . '>', '<' . $from . '>');
            $msgHtml = $dataEmail['email_body'];
            $mail->msgHTML($msgHtml);
            var_dump($xmailer, $from, $from_name, $sitePath, $mail);
            var_dump($dataEmail);
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return FALSE;
        }
        if (!$mail->send()) {
            commonActs::writesLog("mail_queue_email_send_error.log", $dataEmail['email']);
        }
        else {
            commonActs::writesLog("mail_queue_email_send.log", $dataEmail['email']);
        }
        return TRUE;
    }

    /**
     * Validate email data from queue.
     */
    static function validateEmailDataToQueue($emailData) {
        $tplData = [
            "unsubsribeUrl",
            "email",
            "email_subject",
            "email_body",
            "email_uname",
        ];
        foreach ($tplData as $key => $value) {
            if (empty($emailData[$value])) {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * Writes log.
     */
    static function writesLog(string $filename, string $msg) {
        global $sitePath;
        try {
            file_put_contents($sitePath . 'log/' . $filename, date("Y-m-d H:i:s") . "\t  " . $msg . "\n", FILE_APPEND);
        }
        catch (\Throwable $th) {
            error_log($th->getMessage() . $th->getTraceAsString());
        }
    }
}
