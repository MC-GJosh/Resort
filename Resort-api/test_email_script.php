<?php
try {
    Mail::raw('This is a test email checking your Brevo connection.', function ($msg) {
        $msg->to('joshcastillo0231@gmail.com')
            ->subject('Brevo Connection Test');
    });
    echo "SUCCESS: Email sent successfully!";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
