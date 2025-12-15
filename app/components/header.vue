<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const { user, isLoggedIn, openLoginModal, logout } = useAuth();

// Reactive state to track scroll position
const isScrolled = ref(false);
const mobileMenuOpen = ref(false);

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

const showLogoutModal = ref(false);

const handleLogout = async () => {
  await logout();
  showLogoutModal.value = true;
  await new Promise(resolve => setTimeout(resolve, 1500));
  showLogoutModal.value = false;
  closeMobileMenu();
  window.location.href = '/';
};

const dropdownOpen = ref(false);

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};

// Close dropdown when clicking outside
const closeDropdown = (e) => {
  if (!e.target.closest('.user-menu')) {
    dropdownOpen.value = false;
  }
};

// Set up listeners
onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  window.addEventListener('click', closeDropdown);
});

// Clean up listeners
onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
  window.removeEventListener('click', closeDropdown);
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
                    <li><NuxtLink to="/pool">Pool</NuxtLink></li>
                    <li><NuxtLink to="/pickleball">Pickleball</NuxtLink></li>
                    <li><NuxtLink to="/function-hall">Function Hall</NuxtLink></li>
                    <li><NuxtLink to="/hotel-room">Room</NuxtLink></li>
                    <li><NuxtLink to="/aboutus">About Us</NuxtLink></li>
                    
                    <!-- User Dropdown (Replaces old user info) -->
                    <li v-if="isLoggedIn" class="user-menu" @click.stop="toggleDropdown">
                      <div class="user-name-btn">
                        <span>{{ user?.name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                          <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                        </svg>
                      </div>
                      
                      <div class="dropdown-menu" :class="{ 'show': dropdownOpen }">
                        <NuxtLink v-if="user?.role === 'admin'" to="/admin" class="dropdown-item admin" @click="dropdownOpen = false">
                          Admin Dashboard
                        </NuxtLink>
                        <!-- <a href="#" class="dropdown-item">My Bookings</a> -->
                        <a href="#" @click.prevent="handleLogout" class="dropdown-item logout">Log Out</a>
                      </div>
                    </li>
                    <li v-else><a href="#" @click.prevent="openLoginModal">Log In</a></li>
                </ul>
            </nav>
        </div>
        
        <!-- Mobile Overlay -->
        <div class="mobile-overlay" :class="{ 'open': mobileMenuOpen }" @click="closeMobileMenu"></div>
        
        <!-- Mobile Navigation Sidebar -->
        <div class="mobile-nav" :class="{ 'open': mobileMenuOpen }">
            <ul>
                <li><NuxtLink to="/" @click="closeMobileMenu">Home</NuxtLink></li>
                <li><NuxtLink to="/pool" @click="closeMobileMenu">Pool</NuxtLink></li>
                <li><NuxtLink to="/pickleball" @click="closeMobileMenu">Pickleball</NuxtLink></li>
                <li><NuxtLink to="/function-hall" @click="closeMobileMenu">Function Hall</NuxtLink></li>
                <li><NuxtLink to="/hotel-room" @click="closeMobileMenu">Room</NuxtLink></li>
                <li><NuxtLink to="/aboutus" @click="closeMobileMenu">About Us</NuxtLink></li>
                
                <!-- Admin Link -->
                <li v-if="user?.role === 'admin'"><NuxtLink to="/admin" @click="closeMobileMenu" class="mobile-admin-link">Dashboard</NuxtLink></li>

                <!-- Show user info when logged in -->
                <template v-if="isLoggedIn">
                  <li class="user-info-mobile">
                    <span>Welcome, {{ user?.name }}</span>
                  </li>
                  <li class="logout-item">
                    <a href="#" @click.prevent="handleLogout">Log Out</a>
                  </li>
                </template>
                <li v-else class="login-item">
                  <a href="#" @click.prevent="openLoginModal(); closeMobileMenu()">Log In</a>
                </li>
            </ul>
        </div>
    </header>

    <!-- Logout Success Modal -->
    <div v-if="showLogoutModal" class="modal-overlay">
      <div class="modal-content">
        <div class="spinner-container">
          <div class="spinner-check"></div>
        </div>
        <h3>Log Out Complete</h3>
        <p>You have been successfully logged out.</p>
      </div>
    </div>
</template>

<style scoped>
.floating-header {
  position: fixed; top: 0; left: 0; width: 100%;
  background-color: transparent; color: white; z-index: 1000;
  padding: 2rem 0; transition: padding 0.3s, background-color 0.3s;
}
.floating-header.scrolled { 
  padding: 1rem 0; 
  background-color: rgba(29, 53, 87, 0.95); 
  backdrop-filter: blur(10px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
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
.desktop-nav ul { list-style: none; display: flex; gap: 2rem; margin: 0; padding: 0; align-items: center; }
.desktop-nav a { color: white; text-decoration: none; font-weight: bold; font-size: 1.05rem; transition: all 0.3s ease; position: relative; }
.desktop-nav a:hover { color: #D59F4A; transform: translateY(-1px); }

/* User Dropdown */
.user-menu {
  position: relative;
  cursor: pointer;
}

.user-name-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: white;
  font-weight: 600;
  transition: color 0.3s;
}

.user-name-btn:hover {
  color: #D59F4A;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border-radius: 8px;
  min-width: 200px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  padding: 0.5rem 0;
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.dropdown-menu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0.8rem 1.2rem;
  color: #333 !important;
  font-size: 0.95rem !important;
  font-weight: 500 !important;
  transition: background 0.2s;
}

.dropdown-item:hover {
  background: #f8f9fa;
  color: #D59F4A !important;
  transform: translateX(5px);
}

.dropdown-item.admin {
  color: #D59F4A !important;
  font-weight: bold !important;
  border-bottom: 1px solid #f1f1f1;
}

.dropdown-item.logout {
  color: #ff6b6b !important;
  border-top: 1px solid #f1f1f1;
  margin-top: 0.5rem;
}

.dropdown-item.logout:hover {
  background: #fff5f5;
  color: #ff4757 !important;
}

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

/* Mobile user info */
.user-info-mobile {
  padding: 1rem 1.25rem;
}
.user-info-mobile span {
  color: #D59F4A;
  font-weight: 600;
}
.logout-item a {
  background-color: #ff6b6b !important;
  margin: 1rem;
  border-radius: 8px;
  text-align: center;
  color: white !important;
  font-weight: bold;
}
.logout-item a:hover {
  background-color: #ff4757 !important;
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

.admin-link {
  color: #D59F4A !important;
  border: 1px solid #D59F4A;
  padding: 0.5rem 1rem;
  border-radius: 4px;
}

.admin-link:hover {
  background: #D59F4A;
  color: white !important;
}

.mobile-admin-link {
  color: #D59F4A !important;
}

/* Logout Modal Styles */
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex; justify-content: center; align-items: center;
  z-index: 2000; backdrop-filter: blur(5px);
}

.modal-content {
  background: white; padding: 2.5rem; border-radius: 15px; text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  width: 90%; max-width: 400px;
  animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes popIn {
  from { transform: scale(0.8); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.spinner-container { margin-bottom: 1.5rem; display: flex; justify-content: center; }

.spinner-check {
  width: 60px; height: 60px;
  border: 5px solid #D59F4A; border-radius: 50%; position: relative;
}

.spinner-check::after {
  content: ''; position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%) rotate(45deg);
  width: 15px; height: 25px;
  border-bottom: 4px solid #D59F4A; border-right: 4px solid #D59F4A;
}

.modal-content h3 { color: #2c3e50; margin-bottom: 0.5rem; font-size: 1.5rem; }
.modal-content p { color: #666; margin-bottom: 0; }
</style>
