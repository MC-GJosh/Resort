<!DOCTYPE html>
<html>

<head>
    <title>Booking Cancelled</title>
</head>

<body>
    <h1>Booking Cancelled</h1>
    <p>Hi {{ $booking->customer_name }},</p>
    <p>Your booking with reference <strong>{{ $booking->reference_number }}</strong> has been cancelled.</p>
    <p>If you did not request this, please contact us immediately.</p>
    <p>Thank you,<br>Waterland Resort</p>
</body>

</html>