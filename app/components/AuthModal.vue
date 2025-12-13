<script setup>
// import { useAuth } from '~/composables/useAuth.js';

const useLocalAuth = () => {
    const isLoggedIn = useState('isLoggedIn', () => false)
    const showLoginModal = useState('showLoginModal', () => false)

    const login = () => {
        isLoggedIn.value = true
        closeLoginModal()
    }

    const logout = () => {
        isLoggedIn.value = false
    }

    const openLoginModal = () => {
        showLoginModal.value = true
    }

    const closeLoginModal = () => {
        showLoginModal.value = false
    }

    return {
        isLoggedIn,
        showLoginModal,
        login,
        logout,
        openLoginModal,
        closeLoginModal
    }
}
const { showLoginModal, closeLoginModal, login } = useLocalAuth()

const handleLogin = () => {
  // Simulate login
  login()
}

const handleGoogleLogin = () => {
  // Simulate Google login
  console.log('Logging in with Google...');
  login();
}
</script>

<template>
  <Transition name="fade">
    <div v-if="showLoginModal" class="auth-overlay" @click.self="closeLoginModal">
      <div class="auth-box">
        <button class="close-btn" @click="closeLoginModal">&times;</button>
        
        <div class="modal-logo">
           <img src="/logo.jpg" alt="Waterland Resort" />
        </div>

        <h2>Welcome Back</h2>
        <p class="subtitle">Please log in to continue</p>

        <form @submit.prevent="handleLogin">
          <div class="input-group">
            <input type="email" placeholder="Email Address" required />
          </div>
          <div class="input-group">
            <input type="password" placeholder="Password" required />
          </div>

          <button type="submit" class="auth-submit-btn">Log In</button>
        </form>

        <div class="divider">
          <span>OR</span>
        </div>

        <button type="button" class="google-btn" @click="handleGoogleLogin">
          <svg class="google-icon" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
            <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
              <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z" />
              <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z" />
              <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.734 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z" />
              <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z" />
            </g>
          </svg>
          Continue with Google
        </button>

        <div class="auth-footer">
          Don't have an account? <a href="#">Sign Up</a>
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
}
.auth-box {
  background: white; width: 100%; max-width: 400px; padding: 2.5rem;
  border-radius: 12px; box-shadow: 0 15px 40px rgba(0,0,0,0.3);
  position: relative; text-align: center;
}
.close-btn {
  position: absolute; top: 10px; right: 15px; background: none; border: none;
  font-size: 2rem; color: #888; cursor: pointer;
}
.modal-logo img { height: 60px; margin-bottom: 1rem; }
.auth-box h2 { color: #2c3e50; margin-bottom: 0.5rem; font-size: 1.8rem; }
.subtitle { color: #666; font-size: 0.9rem; margin-bottom: 2rem; }
.input-group { margin-bottom: 1rem; }
.input-group input {
  width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; transition: border 0.3s;
}
.input-group input:focus { outline: none; border-color: #1d3557; }
.auth-submit-btn {
  width: 100%; padding: 12px; background-color: #1d3557; color: white;
  border: none; border-radius: 6px; font-size: 1.1rem; font-weight: bold;
  cursor: pointer; margin-top: 1rem; transition: background 0.3s;
}
.auth-submit-btn:hover { background-color: #D59F4A; }

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
.google-btn:hover { background-color: #f8f9fa; }
.google-icon { width: 20px; height: 20px; }
.auth-footer { margin-top: 1.5rem; font-size: 0.9rem; color: #666; }
.auth-footer a { color: #1d3557; font-weight: bold; text-decoration: none; }
.auth-footer a:hover { text-decoration: underline; }

/* Vue Transition Animation */
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
