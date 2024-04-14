<?php
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

// $mail = new PHPMailer(true);

// try {
//     // Server settings
//     $mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
//     $mail->isSMTP();
//     $mail->Host = 'smtp.gmail.com';
//     $mail->SMTPAuth = true;
//     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//     $mail->Port = 587;

//     $mail->Username = 'vishalbaste726@gmail.com'; // YOUR gmail email
//     $mail->Password = '@149GTVNBskn'; // YOUR gmail password

//     // Sender and recipient settings
//     $mail->setFrom('vishalbaste726@gmail.com', 'Vishal Baste');
//     $mail->addAddress('vishalbaste020@example.com', 'Vishal');
//     $mail->addReplyTo('vishalbaste726@gmail.com', 'Vishal Baste'); // to set the reply to

//     // Setting the email content
//     $mail->IsHTML(true);
//     $mail->Subject = "Send email using Gmail SMTP and PHPMailer";
//     $mail->Body = 'HTML message body. <b>Gmail</b> SMTP email body.';
//     $mail->AltBody = 'Plain text message body for non-HTML email client. Gmail SMTP email body.';

//     $mail->send();
//     echo "Email message sent.";
// } catch (Exception $e) {
//     echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";
// }
?>