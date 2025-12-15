# Why Vercel Can't See Your Backend

**The Problem:**
You deployed your Frontend (Nuxt) to Vercel, which puts it on the public internet. However, your Backend (Laravel) is still running on your computer at `localhost:8000`.

- **Localhost** means "this computer".
- When you visit your Vercel site, `localhost` refers to the *visitor's* computer, not yours. Since visitors don't have your Laravel API running on their device, the site fails to connect.

## Solution 1: For Testing/Demos (Easiest)
Use a tool like **Ngrok** to create a public tunnel to your local backend.

1.  Download and install [Ngrok](https://ngrok.com/).
2.  Start your Laravel backend: `php artisan serve`
3.  In a new terminal, run: `ngrok http 8000`
4.  Ngrok will give you a URL like `https://1234-56-78.ngrok-free.app`.
5.  Go to your Vercel Project Settings > **Environment Variables**.
6.  Add a new variable:
    - **Key**: `NUXT_PUBLIC_API_BASE`
    - **Value**: `https://1234-56-78.ngrok-free.app/api` (The Ngrok URL + /api)
7.  Redeploy your Vercel project.

*Note: If you restart Ngrok, the URL changes, and you'll have to update Vercel again.*

## Solution 2: For Production (Permanent)
You must deploy your Laravel backend to a public hosting provider so it runs online 24/7.

**Recommended Hosts for Laravel:**
- **Railway.app** (Paid/Trial)
- **Heroku** (Paid)
- **DigitalOcean** (VPS)
- **Render**

**Steps:**
1.  Deploy your `Resort-api` folder to one of these hosts.
2.  Get the public URL (e.g., `https://my-resort-api.railway.app`).
3.  Update your Vercel Environment Variables:
    - **Key**: `NUXT_PUBLIC_API_BASE`
    - **Value**: `https://my-resort-api.railway.app/api`
