import fs from 'node:fs';
import path from 'node:path';

const dbPath = path.resolve(process.cwd(), 'server/data/db.json');

export const readDb = () => {
    if (!fs.existsSync(dbPath)) {
        return { users: [], courts: [], bookings: [] };
    }
    const data = fs.readFileSync(dbPath, 'utf-8');
    return JSON.parse(data);
};

export const writeDb = (data) => {
    fs.writeFileSync(dbPath, JSON.stringify(data, null, 2), 'utf-8');
};
