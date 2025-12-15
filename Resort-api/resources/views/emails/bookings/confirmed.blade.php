<!DOCTYPE html>
<html>

<head>
    <title>Booking Confirmed</title>
</head>

<body>
    <h1>Great news! Your booking is confirmed.</h1>
    <p>Hi {{ $booking->customer_name }},</p>
    <p>We look forward to seeing you at Waterland Resort.</p>
    <p><strong>Details:</strong></p>
    <ul>
        <li>Date: {{ $booking->date }}</li>
        <li>Time: {{ $booking->time_slot }}</li>
        <li>Reference: {{ $booking->reference_number }}</li>
    </ul>
    <p>Thank you!</p>
</body>

</html>