

export default defineEventHandler(async (event) => {
    const body = await readBody(event);
    const db = readDb();

    // Basic validation
    if (!body.selectedCourtId || !body.date || !body.selectedTimeSlots || body.selectedTimeSlots.length === 0 || !body.playerName) {
        throw createError({
            statusCode: 400,
            statusMessage: 'Missing required fields'
        });
    }

    // Check availability for ALL slots
    const unavailableSlots = body.selectedTimeSlots.filter((slot: string) =>
        db.bookings.some((b: any) =>
            b.courtId === body.selectedCourtId &&
            b.date === body.date &&
            b.time === slot
        )
    );

    if (unavailableSlots.length > 0) {
        throw createError({
            statusCode: 409,
            statusMessage: `Some slots are already booked: ${unavailableSlots.join(', ')}`
        });
    }

    const newBookings = body.selectedTimeSlots.map((slot: string) => ({
        id: Date.now().toString() + Math.random().toString(36).substr(2, 9),
        courtId: body.selectedCourtId,
        date: body.date,
        time: slot,
        customerName: body.playerName,
        phoneNumber: body.phone,
        paymentMethod: body.paymentMethod,
        referenceNumber: body.referenceNumber,
        status: 'Confirmed',
        createdAt: new Date().toISOString()
    }));

    db.bookings.push(...newBookings);
    writeDb(db);

    return { success: true, bookings: newBookings };
});
