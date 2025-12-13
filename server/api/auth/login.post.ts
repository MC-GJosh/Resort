

export default defineEventHandler(async (event) => {
    const body = await readBody(event);
    const { username, password } = body;

    const db = readDb();
    const user = db.users.find(u => u.username === username && u.password === password);

    if (user) {
        // In a real app, use a proper session/token mechanism
        // For now, we'll return a simple mock token and user info
        return {
            token: 'mock-token-' + user.id,
            user: {
                id: user.id,
                username: user.username,
                role: user.role,
                name: user.name
            }
        };
    } else {
        throw createError({
            statusCode: 401,
            statusMessage: 'Invalid credentials'
        });
    }
});
