# Railway Deployment Guide

This guide walks you through deploying **both** your Nuxt Frontend and Laravel Backend to Railway.

## Prerequisites
- A [Railway](https://railway.app/) account.
- Your project code pushed to GitHub.

## Part 1: Deploy the Backend (Laravel)

1. **New Project**: In Railway, click "New Project" > "Deploy from GitHub repo" > Select your repository.
2. **Service Configuration**:
   - **Crucial Step**: Since your backend is in the `Resort-api` folder, you must tell Railway where to look.
   - Click on your new **Service Card** to open it.
   - Click on the **Settings** tab at the top.
   - Scroll down to the **Build** section.
   - Find the field labeled **Root Directory** (sometimes called Source Directory).
   - Change it to `/Resort-api`.
   - Railway will detect the `Dockerfile` inside `Resort-api`.
3. **Environment Variables**:
   - Go to the **Variables** tab.
   - Add the following keys (copy from your local `.env` but update for production):
     - `APP_NAME`: Resort
     - `APP_ENV`: production
     - `APP_KEY`: (Generate one with `php artisan key:generate` locally and copy it here)
     - `APP_DEBUG`: false
     - `APP_URL`: (This will be the Railway URL, e.g., `https://backend-production.up.railway.app`)
4. **Create a Database** (You don't need one beforehand!):
   - In your Railway project dashboard, click the **+ New** button > **Database** > **MySQL**.
   - Railway will spin up a fresh database for you.
   - Once it's running, click on the new MySQL service card.
   - Go to the **Variables** tab of the *MySQL service*. You will see variables like `MYSQL_URL`, `MYSQLPASSWORD`, etc.
   - Now, go back to your **Laravel Backend Service** > **Variables**.
   - Add the variable `DATABASE_URL` and set the value to `${{ MySQL.MYSQL_URL }}` (Railway allows you to reference other services like this).
   - *Alternatively*, if `DATABASE_URL` doesn't work for your Laravel setup, manually add these variables to your Backend Service:
     - `DB_CONNECTION`: `mysql`
     - `DB_HOST`: `${{ MySQL.MYSQLHOST }}`
     - `DB_PORT`: `${{ MySQL.MYSQLPORT }}`
     - `DB_DATABASE`: `${{ MySQL.MYSQLDATABASE }}`
     - `DB_USERNAME`: `${{ MySQL.MYSQLUSER }}`
     - `DB_PASSWORD`: `${{ MySQL.MYSQLPASSWORD }}`
5. **Deploy**:
   - Railway should automatically deploy. Watch the build logs.
   - Once active, note the **Public Networking URL** by going to Settings > Networking > Generate Domain (if none exists). It will look like `https://resort-api-production.up.railway.app`.

## Part 2: Deploy the Frontend (Nuxt)

1. **Add Service**: In the **SAME** Railway project, click "New" > "GitHub Repo" > Select the SAME repository again.
2. **Service Configuration**:
   - Go to Service Settings > **Root Directory**.
   - Ensure it is `/` (root), or just leave it blank (Default).
   - Railway should detect the `Dockerfile` in your root folder.
3. **Environment Variables**:
   - Go to **Variables**.
   - `NUXT_PUBLIC_API_BASE`: `https://resort-api-production.up.railway.app/api` (Use the **Backend URL** from Part 1).
   - **Note**: Ensure your backend URL includes `/api` if that's how your Nuxt app expects it (check `nuxt.config.ts`).
4. **Deploy**:
   - Wait for the build to finish.
   - Note the **Public Networking URL** (e.g., `https://resort-frontend.up.railway.app`).

## Part 3: Connect and Verify

1. **Update Backend CORS**:
   - Go back to your **Backend Service** > **Variables**.
   - Add/Update `CORS_ALLOWED_ORIGINS` (or `FRONTEND_URL`): 
     - Value: `https://resort-frontend.up.railway.app` (The Frontend URL from Part 2).
   - **Redeploy** the Backend service.

2. **Verification**:
   - Open your Frontend URL.
   - Try to register or login.
   - Check the Network tab in your browser (F12) to ensure requests are hitting your Railway Backend URL.

