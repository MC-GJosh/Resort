<script setup>
import { ref } from 'vue';

const username = ref('');
const password = ref('');
const error = ref('');
const showSuccess = ref(false);
const router = useRouter();

const handleLogin = async () => {
  try {
    const { data, error: apiError } = await useFetch('/api/auth/login', {
      method: 'POST',
      body: { username: username.value, password: password.value }
    });

    if (apiError.value) {
      error.value = apiError.value.statusMessage || 'Login failed';
    } else {
      // Store user info (simplified for this demo)
      const userCookie = useCookie('user');
      userCookie.value = data.value.user;
      showSuccess.value = true;
      setTimeout(() => {
        router.push('/');
      }, 1500);
    }
  } catch (e) {
    error.value = 'An unexpected error occurred';
  }
};
</script>

<template>
  <div class="login-page">
    <div class="login-card">
      <h1>Login</h1>
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label>Username</label>
          <input v-model="username" type="text" required />
        </div>
        <div class="form-group">
          <label>Password</label>
          <input v-model="password" type="password" required />
        </div>
        <div v-if="error" class="error">{{ error }}</div>
        <button type="submit">Login</button>
      </form>
    </div>

    
    <div v-if="showSuccess" class="success-popup">
      <div class="success-content">
        <div class="checkmark">✓</div>
        <h2>Login Successful!</h2>
        <p>Redirecting you so fast...</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: #f0f2f5;
}

.login-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 400px;
}

h1 {
  text-align: center;
  margin-bottom: 1.5rem;
  color: #333;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  color: #666;
}

input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

button {
  width: 100%;
  padding: 0.75rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

button:hover {
  background-color: #0056b3;
}

.error {
  color: red;
  margin-bottom: 1rem;
  text-align: center;
}

.success-popup {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  animation: fadeIn 0.3s ease;
}

.success-content {
  background: white;
  padding: 2.5rem;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  transform: scale(0.9);
  animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

.checkmark {
  width: 60px;
  height: 60px;
  background-color: #28a745;
  color: white;
  border-radius: 50%;
  font-size: 35px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
}

.success-content h2 {
  color: #333;
  margin-bottom: 0.5rem;
}

.success-content p {
  color: #666;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes popIn {
  to { transform: scale(1); }
}
</style>
