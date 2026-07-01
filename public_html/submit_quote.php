<?php
// submit_quote.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form fields
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone = strip_tags(trim($_POST["phone"]));
    $course = strip_tags(trim($_POST["course"]));
    $message = trim($_POST["message"]);

    // Set the recipient email address.
    $recipient = "supernovaonline@outlook.com"; 

    // Build the email content.
    $subject = "New Course Inquiry from: $name";
    $email_content = "Name: $name\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Phone/WhatsApp: $phone\n";
    $email_content .= "Interested Course: $course\n\n";
    $email_content .= "Message:\n$message\n";

    // Build the email headers.
    $email_headers = "From: $name <$email>";

    // Send the email.
    if (mail($recipient, $subject, $email_content, $email_headers)) {
        http_response_code(200);
        echo "Success";
    } else {
        http_response_code(500);
        echo "Oops! Something went wrong and we couldn't send your message.";
    }
} else {
    http_response_code(403);
    echo "There was a problem with your submission, please try again.";
}
?>