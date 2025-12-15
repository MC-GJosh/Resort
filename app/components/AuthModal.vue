<script setup>
import { ref } from 'vue';

const { 
  showLoginModal, 
  showRegisterModal,
  closeLoginModal, 
  closeRegisterModal,
  login, 
  register,
  switchToRegister,
  switchToLogin,
  authLoading,
  authError,
  authSuccessMessage
} = useAuth();

// Login form
const loginEmail = ref('');
const loginPassword = ref('');

// Register form
const registerName = ref('');
const registerEmail = ref('');
const registerPhone = ref('');
const registerPassword = ref('');
const registerPasswordConfirm = ref('');

const handleLogin = async () => {
  const success = await login(loginEmail.value, loginPassword.value);
  if (success) {
    // Clear form
    loginEmail.value = '';
    loginPassword.value = '';
  }
};

const handleRegister = async () => {
  const success = await register(
    registerName.value,
    registerEmail.value,
    registerPhone.value,
    registerPassword.value,
    registerPasswordConfirm.value
  );
  if (success) {
    // Clear form
    registerName.value = '';
    registerEmail.value = '';
    registerPhone.value = '';
    registerPassword.value = '';
    registerPasswordConfirm.value = '';
  }
};

const validatePhone = (event) => {
  const input = event.target;
  input.value = input.value.replace(/\D/g, '').slice(0, 11);
  registerPhone.value = input.value;
};
</script>

<template>
  <!-- LOGIN MODAL -->
  <Transition name="fade">
    <div v-if="showLoginModal" class="auth-overlay" @click.self="closeLoginModal">
      <div class="auth-box">
        <button class="close-btn" @click="closeLoginModal">&times;</button>
        
        <div class="modal-logo">
           <img src="/logo.jpg" alt="Waterland Resort" />
        </div>

        <h2>Welcome Back</h2>
        <p class="subtitle">Please log in to continue</p>

        <div v-if="authError" class="error-message">
          {{ authError }}
        </div>
        <div v-if="authSuccessMessage" class="success-message">
          {{ authSuccessMessage }}
        </div>

        <form @submit.prevent="handleLogin">
          <div class="input-group">
            <input 
              type="email" 
              v-model="loginEmail"
              placeholder="Email Address" 
              required 
              :disabled="authLoading"
            />
          </div>
          <div class="input-group">
            <input 
              type="password" 
              v-model="loginPassword"
              placeholder="Password" 
              required 
              :disabled="authLoading"
            />
          </div>

        <button type="submit" class="auth-submit-btn" :disabled="authLoading">
            {{ authLoading ? 'Logging in...' : 'Log In' }}
          </button>
        </form>

        <div class="auth-footer">
          Don't have an account? <a href="#" @click.prevent="switchToRegister">Sign Up</a>
        </div>
      </div>
    </div>
  </Transition>

  <!-- REGISTER MODAL -->
  <Transition name="fade">
    <div v-if="showRegisterModal" class="auth-overlay" @click.self="closeRegisterModal">
      <div class="auth-box">
        <button class="close-btn" @click="closeRegisterModal">&times;</button>
        
        <div class="modal-logo">
           <img src="/logo.jpg" alt="Waterland Resort" />
        </div>

        <h2>Create Account</h2>
        <p class="subtitle">Join Waterland Resort today</p>

        <div v-if="authError" class="error-message">
          {{ authError }}
        </div>

        <div v-if="showSuccess" class="success-view">
          <div class="success-icon">✉️</div>
          <h3>Check Your Email</h3>
          <p>{{ successMessage }}</p>
          <button @click="closeRegisterModal" class="auth-submit-btn">Got it</button>
        </div>

        <form v-else @submit.prevent="handleRegister">
          <div class="input-group">
            <input 
              type="text" 
              v-model="registerName"
              placeholder="Full Name" 
              required 
              :disabled="authLoading"
            />
          </div>
          <div class="input-group">
            <input 
              type="email" 
              v-model="registerEmail"
              placeholder="Email Address" 
              required 
              :disabled="authLoading"
            />
          </div>
          <div class="input-group">
            <input 
              type="tel" 
              v-model="registerPhone"
              placeholder="Phone Number (e.g., 09171234567)" 
              maxlength="11"
              @input="validatePhone"
              :disabled="authLoading"
            />
          </div>
          <div class="input-group">
            <input 
              type="password" 
              v-model="registerPassword"
              placeholder="Password (min 8 characters)" 
              required 
              minlength="8"
              :disabled="authLoading"
            />
          </div>
          <div class="input-group">
            <input 
              type="password" 
              v-model="registerPasswordConfirm"
              placeholder="Confirm Password" 
              required 
              :disabled="authLoading"
            />
          </div>

          <button type="submit" class="auth-submit-btn" :disabled="authLoading">
            {{ authLoading ? 'Creating Account...' : 'Sign Up' }}
          </button>
        </form>

        <div class="auth-footer">
          Already have an account? <a href="#" @click.prevent="switchToLogin">Log In</a>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.auth-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px);
  z-index: 9999; display: flex; justify-content: center; align-items: center;
  padding: 1rem;
}
.auth-box {
  background: white; width: 100%; max-width: 400px; padding: 2.5rem;
  border-radius: 12px; box-shadow: 0 15px 40px rgba(0,0,0,0.3);
  position: relative; text-align: center;
  max-height: 90vh;
  overflow-y: auto;
}
.close-btn {
  position: absolute; top: 10px; right: 15px; background: none; border: none;
  font-size: 2rem; color: #888; cursor: pointer;
}
.modal-logo img { height: 60px; margin-bottom: 1rem; }
.auth-box h2 { color: #2c3e50; margin-bottom: 0.5rem; font-size: 1.8rem; }
.subtitle { color: #666; font-size: 0.9rem; margin-bottom: 1.5rem; }

.error-message {
  background: #fee; 
  color: #c00; 
  padding: 0.75rem; 
  border-radius: 6px; 
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.success-message {
  background: #d4edda; 
  color: #155724; 
  padding: 0.75rem; 
  border-radius: 6px; 
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.input-group { margin-bottom: 1rem; }
.input-group input {
  width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; transition: border 0.3s;
  box-sizing: border-box;
}
.input-group input:focus { outline: none; border-color: #1d3557; }
.input-group input:disabled { background: #f5f5f5; cursor: not-allowed; }

.auth-submit-btn {
  width: 100%; padding: 12px; background-color: #1d3557; color: white;
  border: none; border-radius: 6px; font-size: 1.1rem; font-weight: bold;
  cursor: pointer; margin-top: 1rem; transition: background 0.3s;
}
.auth-submit-btn:hover:not(:disabled) { background-color: #D59F4A; }
.auth-submit-btn:disabled { background-color: #ccc; cursor: not-allowed; }

.divider {
  display: flex; align-items: center; margin: 1.5rem 0; color: #888; font-size: 0.8rem;
}
.divider::before, .divider::after {
  content: ""; flex: 1; height: 1px; background: #eee;
}
.divider span { padding: 0 10px; }

.google-btn {
  width: 100%; padding: 10px; background: white; border: 1px solid #ddd;
  border-radius: 6px; display: flex; align-items: center; justify-content: center;
  gap: 10px; cursor: pointer; font-weight: 500; color: #444; transition: background 0.2s;
}
.google-btn:hover:not(:disabled) { background-color: #f8f9fa; }
.google-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.google-icon { width: 20px; height: 20px; }

.auth-footer { margin-top: 1.5rem; font-size: 0.9rem; color: #666; }
.auth-footer a { color: #1d3557; font-weight: bold; text-decoration: none; }
.auth-footer a:hover { text-decoration: underline; }

/* Vue Transition Animation */
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.success-view {
  text-align: center;
  padding: 2rem 0;
}
.success-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}
</style>
