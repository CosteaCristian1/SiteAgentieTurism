<?php
session_start();
include 'db_config.php';

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';
require 'dompdf/autoload.inc.php';  

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $offer_id = $_POST['offer_id'];
    $user_id = $_SESSION['id'];

    $sql = "SELECT * FROM offers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $offer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $offer = $result->fetch_assoc();
    $stmt->close();

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    $dompdf = new Dompdf();
    $html = "
    <h1>Travel Ticket</h1>
    <p><strong>Location:</strong> {$offer['location']}</p>
    <p><strong>Description:</strong> {$offer['description']}</p>
    <p><strong>Price:</strong> {$offer['price']}</p>
    <p><strong>Date From:</strong> {$offer['available_from']}</p>
    <p><strong>Date To:</strong> {$offer['available_to']}</p>
    <p><strong>Country:</strong> {$offer['country']}</p>
    <p><strong>Customer Name:</strong> {$user['name']}</p>
    <p><strong>Email:</strong> {$user['email']}</p>
    ";
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $pdf_output = $dompdf->output();
    $pdf_file = 'ticket_' . time() . '.pdf';
    file_put_contents($pdf_file, $pdf_output);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();                         
        $mail->Host = 'smtp.gmail.com';         
        $mail->SMTPAuth = true;                   
        $mail->Username = 'cristiancostea1@gmail.com'; 
        $mail->Password = 'aguiugbyodskoyau '; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;                       

        $mail->setFrom('cristiancostea1@gmail.com', 'Travel Agency');
        $mail->addAddress($user['email'], $user['name']);
        $mail->addAttachment($pdf_file);

        $mail->isHTML(true);
        $mail->Subject = 'Travel Offer Confirmation';
        $mail->Body    = 'Thank you for your purchase. Please find your ticket attached.';

        $mail->send();
        echo "<script>alert('Offer purchased successfully. Please check your email for the ticket.'); window.location.href = 'index.php';</script>";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    unlink($pdf_file);
}
?>
