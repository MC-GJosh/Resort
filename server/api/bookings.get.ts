

export default defineEventHandler((event) => {
    const db = readDb();
    return db.bookings;
});
