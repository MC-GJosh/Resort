<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
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

// Reactive state to track scroll position
const isScrolled = ref(false);
const mobileMenuOpen = ref(false);
const { openLoginModal } = useLocalAuth();

// Function to check scroll position
const handleScroll = () => {
  isScrolled.value = window.scrollY > 50;
};

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value;
};

const closeMobileMenu = () => {
  mobileMenuOpen.value = false;
};

// Set up the scroll listener
onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

// Clean up the listener
onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <header class="floating-header" :class="{ 'scrolled': isScrolled }">
        <div class="container">
            <NuxtLink to="/" class="logo-container">
                <img src="/logo.jpg" alt="Waterland Resort Logo" class="header-logo" />
            </NuxtLink>
            
            <!-- Mobile Menu Button -->
            <button class="mobile-menu-btn" @click="toggleMobileMenu" aria-label="Toggle menu">
                <div class="hamburger" :class="{ 'open': mobileMenuOpen }">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
            
            <!-- Desktop Navigation -->
            <nav class="desktop-nav">
                <ul>
                    <li><NuxtLink to="/">Home</NuxtLink></li>
                    <li><NuxtLink to="/pb">Pickleball</NuxtLink></li>
                    <li><NuxtLink to="/function-hall">Function Hall</NuxtLink></li>
                    <li><NuxtLink to="/hotel-room">Room</NuxtLink></li>
                    <li><NuxtLink to="/aboutus">About Us</NuxtLink></li>
                    <li><a href="#" @click.prevent="openLoginModal">Log In</a></li>
                </ul>
            </nav>
        </div>
        
        <!-- Mobile Overlay -->
        <div class="mobile-overlay" :class="{ 'open': mobileMenuOpen }" @click="closeMobileMenu"></div>
        
        <!-- Mobile Navigation Sidebar -->
        <div class="mobile-nav" :class="{ 'open': mobileMenuOpen }">
            <ul>
                <li><NuxtLink to="/" @click="closeMobileMenu">Home</NuxtLink></li>
                <li><NuxtLink to="/pb" @click="closeMobileMenu">Pickleball</NuxtLink></li>
                <li><NuxtLink to="/function-hall" @click="closeMobileMenu">Function Hall</NuxtLink></li>
                <li><NuxtLink to="/hotel-room" @click="closeMobileMenu">Room</NuxtLink></li>
                <li><NuxtLink to="/aboutus" @click="closeMobileMenu">About Us</NuxtLink></li>
                <li class="login-item"><a href="#" @click.prevent="openLoginModal(); closeMobileMenu()">Log In</a></li>
            </ul>
        </div>
    </header>
</template>

<style scoped>
.floating-header {
  position: fixed; top: 0; left: 0; width: 100%;
  background-color: transparent; color: white; z-index: 1000;
  padding: 2rem 0; transition: padding 0.3s, background-color 0.3s;
}
.floating-header.scrolled { padding: 1rem 0; background-color: rgba(29, 53, 87, 0.95); }
.container {
  max-width: 1200px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem;
  position: relative;
}
@media (max-width: 768px) {
  .container {
    flex-direction: row-reverse;
  }
}
.logo-container { height: 60px; }
.header-logo { height: 100%; width: auto; }

/* Desktop Navigation */
.desktop-nav ul { list-style: none; display: flex; gap: 2rem; margin: 0; padding: 0; }
.desktop-nav a { color: white; text-decoration: none; font-weight: bold; font-size: 1.1rem; transition: color 0.3s; }
.desktop-nav a:hover { color: #D59F4A; }

/* Mobile Menu Button - Clean & Simple */
.mobile-menu-btn {
  display: none;
  background: transparent;
  border: 2px solid white;
  border-radius: 10px;
  cursor: pointer;
  padding: 12px;
  z-index: 1002;
  transition: all 0.3s ease;
}
.mobile-menu-btn:hover {
  border-color: #D59F4A;
}
.hamburger {
  width: 32px;
  height: 24px;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.hamburger span {
  display: block;
  height: 3px;
  width: 100%;
  background-color: white;
  border-radius: 2px;
  transition: all 0.3s ease;
}
.hamburger.open span:nth-child(1) {
  transform: rotate(45deg) translate(7px, 7px);
  background-color: #D59F4A;
}
.hamburger.open span:nth-child(2) {
  opacity: 0;
}
.hamburger.open span:nth-child(3) {
  transform: rotate(-45deg) translate(7px, -7px);
  background-color: #D59F4A;
}

/* Mobile Navigation Sidebar */
.mobile-nav {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 25vw;
  min-width: 280px;
  height: 100vh;
  background-color: #1d3557;
  box-shadow: 4px 0 30px rgba(0,0,0,0.4);
  transform: translateX(-100%);
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  overflow-y: auto;
  z-index: 1001;
}
.mobile-nav.open {
  transform: translateX(0);
}
.mobile-nav ul {
  list-style: none;
  padding: 6rem 0 2rem 0;
  margin: 0;
}
.mobile-nav li {
  border-bottom: 1px solid rgba(255,255,255,0.08);
}
.mobile-nav li:last-child {
  border-bottom: none;
}
.mobile-nav a {
  color: white;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.95rem;
  display: block;
  padding: 1rem 1.25rem;
  transition: all 0.2s ease;
  letter-spacing: 0.3px;
}
.mobile-nav a:hover {
  background-color: #0077b6;
  color: white;
  padding-left: 2rem;
}
.mobile-nav .login-item a {
  background-color: #D59F4A;
  margin: 1rem;
  border-radius: 8px;
  text-align: center;
  color: #1d3557;
  font-weight: bold;
}
.mobile-nav .login-item a:hover {
  background-color: #c48a3d;
  padding-left: 1.5rem;
}

/* Mobile Overlay */
.mobile-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background: rgba(0,0,0,0.5);
  z-index: 1000;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}
.mobile-overlay.open {
  opacity: 1;
  visibility: visible;
}

/* Responsive */
@media (max-width: 768px) {
  .desktop-nav {
    display: none;
  }
  .mobile-menu-btn {
    display: block;
  }
  .mobile-nav {
    display: block;
  }
  .mobile-overlay {
    display: block;
  }
  .logo-container {
    height: 45px;
  }
  .floating-header {
    padding: 1rem 0;
    background-color: rgba(29, 53, 87, 0.95);
  }
}
</style>


