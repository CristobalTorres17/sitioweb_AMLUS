<?php


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


// Only process POST reqeusts.

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $csrfToken = $_POST["csrf_token"] ?? '';

    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        http_response_code(403);
        echo "Invalid submission token.";
        exit;
    }

    // Get the form fields and remove MORALspace.

    $name = strip_tags(trim($_POST["name"] ?? ''));

    $name = str_replace(array("\r", "\n"), array(" ", " "), $name);

    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);

    $number = trim($_POST["number"] ?? '');

    $company = strip_tags(trim($_POST["company"] ?? ''));

    $company = str_replace(array("\r", "\n"), array(" ", " "), $company);

    $message = trim($_POST["message"] ?? '');



    // Check that data was sent to the mailer.

    if (empty($name) or empty($number) or empty($company) or empty($message) or !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // Set a 400 (bad request) response code and exit.

        http_response_code(400);

        echo "Please complete the form and try again.";

        exit;
    }



    // Set the recipient email address.

    // FIXME: Update this to your desired email address.

    $recipient = "unicktheme@gmail.com";



    // Set the email subject.

    $sender = "New contact from $name";



    //Email Header

    $head = " /// THEMEGENIX \\\ ";



    // Build the email content.

    $email_content = "$head\n\n\n";

    $email_content .= "Name: $name\n";

    $email_content .= "Email: $email\n\n";

    $email_content .= "Number: $number\n\n";

    $email_content .= "Company: $company\n\n";

    $email_content .= "Message:\n$message\n";



    // Build the email headers.

    $email_headers = "From: $name <$email>\r\n";
    $email_headers .= "Reply-To: $name <$email>\r\n";
    $email_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";



    // Send the email.

    if (mail($recipient, $sender, $email_content, $email_headers)) {

        // Set a 200 (okay) response code.

        http_response_code(200);

        echo "Thank You! Your message has been sent.";

        unset($_SESSION['csrf_token']);
    } else {

        // Set a 500 (internal server error) response code.

        http_response_code(500);

        echo "Oops! Something went wrong and we couldn't send your message.";
    }
} else {

    // Not a POST request, set a 403 (forbidden) response code.

    http_response_code(403);

    echo "There was a problem with your submission, please try again.";
}
