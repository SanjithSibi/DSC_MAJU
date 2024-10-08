<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_name = $_POST['name'];
    $email = $_POST['email'];
    $num = $_POST['phone'];
    $subject = $_POST['subject'];
    $msg = $_POST['message'];

    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){

   $secretKey = '6LeBEioqAAAAAPxZ7CZr_ebSf-gDkKNeOS-D209Z';
   $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret'.$secretKey.'&response'.$_POST['g-recaptcha-response']);
    $response=json_decode($verifyResponse);

    if($response->success){

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'mail.dcsmaju.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@dcsmaju.com';
        $mail->Password = 'Info@dcsmaju82'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('info@dcsmaju.com', 'Cleaning Enquiry');
        $mail->addAddress('info@dcsmaju.com', 'DCSMaju');

        $mail->isHTML(true);
        $mail->Subject = 'Hello, I would like to make an appointment';
        $mail->Body = "
            <b>Name:</b> $user_name<br>
            <b>Email:</b> $email<br>
            <b>Mobile Number:</b> $num<br>
            <b>Subject:</b> $subject<br>
            <b>Message:</b> $msg<br>
        ";

        $mail->send();
        $response['status'] = 'success';
        $response['message'] = 'Message has been sent';
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }


    $mails = new PHPMailer(true);
    
    try {
        $mails->isSMTP();
        $mails->Host = 'mail.dcsmaju.com';
        $mails->SMTPAuth = true;
        $mails->Username = 'info@dcsmaju.com';
        $mails->Password = 'Info@dcsmaju82'; 
        $mails->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mails->Port = 465;

        $mails->setFrom('info@dcsmaju.com', 'Cleaning Enquiry');
        $mails->addAddress($email, 'Franchise');

        $mails->isHTML(true);
        $mails->Subject = 'Thank you for Dropping An Enquiry!';
        $mails->Body = "
           Hi There!<br><br>
           <b>Name:</b> $user_name<br>
            <b>Email:</b> $email<br>
            <b>Mobile Number:</b> $num<br>
            <b>Subject:</b> $subject<br>
            <b>Message:</b> $msg<br><br>

Thank you for dropping your inquiry at DCS MAJU! We are in the process of assisting
your needs to bring you a cleaning expertise perfectly sorted for a welcoming
experience.<br>

Please note that we are unavailable during public holiday and we shall get back to you within 1 -
2 working days. Sharing this email with anyone other than yourself is not permitted.<br><br>

Best Regards,<br>

DCS MAJU Admin Department.
        ";

        $mails->send();
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = "Message could not be sent. Mailer Error: {$mails->ErrorInfo}";
    }
}
else{
    $_SESSION['status'] = 'recaptcha verification api: Something went wrong';
    header("Location:{$_SERVER["HTTP_REFERER"]}");
    exit(0);

}
}

else{
    
}

}

header('Content-Type: application/json');
echo json_encode($response);
?>
