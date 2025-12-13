

export default defineEventHandler(async (event) => {
    const body = await readBody(event);
    const { id } = body;

    if (!id) {
        throw createError({
            statusCode: 400,
            statusMessage: 'Booking ID is required'
        });
    }

    const db = readDb();
    const initialLength = db.bookings.length;
    db.bookings = db.bookings.filter(b => b.id !== id);

    if (db.bookings.length === initialLength) {
        throw createError({
            statusCode: 404,
            statusMessage: 'Booking not found'
        });
    }

    writeDb(db);

    return { success: true };
});
