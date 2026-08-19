<?php
// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Set the recipient email address
    $to = "info@fridgerepairblacktown.com.au";
    
    // Set the email subject
    $subject = "New Quote Request from Website";

    // Grab and sanitize the form data
    $name = strip_tags(trim($_POST["name"]));
    $name = str_replace(array("\r","\n"),array(" "," "),$name); // Remove line breaks from name
    
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $country_code = trim($_POST["country_code"]);
    $phone = trim($_POST["phone"]);
    $message = trim($_POST["message"]);

    // Basic validation
    if ( empty($name) OR empty($phone) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Set a 400 (bad request) response code and exit
        http_response_code(400);
        echo "Please complete all fields correctly.";
        exit;
    }

    // Build the email content
    $email_content = "You have received a new quote request from the website.\n\n";
    $email_content .= "Name: $name\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Phone: $country_code $phone\n\n";
    $email_content .= "Message/Issue Description:\n$message\n";

    // Build the email headers
    $email_headers = "From: $name <$email>\r\n";
    $email_headers .= "Reply-To: $email\r\n";
    $email_headers .= "MIME-Version: 1.0\r\n";
    $email_headers .= "Content-Type: text/plain; charset=utf-8\r\n";

    // Send the email
    if (mail($to, $subject, $email_content, $email_headers)) {
        // Set a 200 (okay) response code
        http_response_code(200);
        echo "Success! Your message has been sent.";
    } else {
        // Set a 500 (internal server error) response code
        http_response_code(500);
        echo "Oops! Something went wrong and we couldn't send your message.";
    }

} else {
    // Not a POST request, set a 403 (forbidden) response code
    http_response_code(403);
    echo "There was a problem with your submission, please try again.";
}
?>