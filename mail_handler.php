<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

header('Content-Type: application/json');

// Gmail credentials
$gmail_user = "shonetamale55@gmail.com";
$gmail_app_password = "YOUR_APP_PASSWORD_HERE"; // ⚠️ replace with your Gmail App Password

function sendEmail($subject, $body) {
    global $gmail_user, $gmail_app_password;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $gmail_user;
        $mail->Password = $gmail_app_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($gmail_user, 'East African Safari Website');
        $mail->addAddress($gmail_user);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = nl2br($body);

        $mail->send();
    } catch (Exception $e) {
        echo json_encode(["message" => "Email error: {$mail->ErrorInfo}"]);
        exit;
    }
}

// Determine form type
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["form_type"]) && $_POST["form_type"] === "contact") {
        $name = htmlspecialchars($_POST["name"]);
        $email = htmlspecialchars($_POST["email"]);
        $phone = htmlspecialchars($_POST["phone"]);
        $message = htmlspecialchars($_POST["message"]);

        $body = "
            <h2>New Contact Message - East African Safari</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Message:</strong><br>$message</p>
        ";

        sendEmail("New Contact Message from $name", $body);
        echo json_encode(["message" => "Message sent successfully!"]);
        exit;
    }

    if (isset($_POST["form_type"]) && $_POST["form_type"] === "newsletter") {
        $email = htmlspecialchars($_POST["email"]);
        $body = "<h2>New Newsletter Subscription</h2><p><strong>Email:</strong> $email</p>";
        sendEmail("New Newsletter Subscription", $body);
        echo json_encode(["message" => "Thank you for subscribing!"]);
        exit;
    }

    // Booking form
    if (!isset($_POST["form_type"])) {
        $body = "<h2>New Safari Booking Request</h2>";
        foreach ($_POST as $key => $value) {
            $body .= "<p><strong>" . ucfirst($key) . ":</strong> " . htmlspecialchars($value) . "</p>";
        }
        sendEmail("New Safari Booking Request", $body);
        echo json_encode(["message" => "Booking submitted successfully!"]);
        exit;
    }
}
?>