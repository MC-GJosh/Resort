<script setup>
import { watchEffect } from 'vue';
const { user, isLoggedIn, logout } = useAuth();
const router = useRouter();

// Watch for auth state changes
watchEffect(() => {
  if (process.client) {
    if (!isLoggedIn.value || user.value?.role !== 'admin') {
      router.push('/');
    }
  }
});

const handleLogout = async () => {
  await logout();
  router.push('/');
};

const menuItems = [
  { name: 'Dashboard', icon: '📊', path: '/admin' },
  { name: 'Courts', icon: '🏸', path: '/admin/courts' },
  { name: 'Rooms', icon: '🛏️', path: '/admin/rooms' },
  { name: 'Function Halls', icon: '🎪', path: '/admin/halls' },
  { name: 'Pickleball Bookings', icon: '📅', path: '/admin/bookings/pickleball' },
  { name: 'Room Bookings', icon: '📋', path: '/admin/bookings/rooms' },
  { name: 'Hall Bookings', icon: '📝', path: '/admin/bookings/halls' },
];

const mounted = ref(false);
onMounted(() => {
  mounted.value = true;
});
</script>

<template>
  <div v-if="mounted && isLoggedIn && user?.role === 'admin'" class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-header">
        <img src="/logo.jpg" alt="Waterland" class="sidebar-logo" />
        <h2>Admin Panel</h2>
      </div>
      
      <nav class="sidebar-nav">
        <NuxtLink 
          v-for="item in menuItems" 
          :key="item.path" 
          :to="item.path"
          class="nav-item"
        >
          <span class="nav-icon">{{ item.icon }}</span>
          <span class="nav-text">{{ item.name }}</span>
        </NuxtLink>
        <div class="nav-divider"></div>

        <NuxtLink to="/" class="nav-item back-home-link">
          <span class="nav-icon">🏠</span>
          <span class="nav-text">Back to Website</span>
        </NuxtLink>
      </nav>

      <div class="sidebar-footer">
        <div class="user-info">
          <span class="user-name">{{ user?.name }}</span>
          <span class="user-role">Administrator</span>
        </div>
        <button @click="handleLogout" class="logout-btn">Logout</button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
      <header class="admin-header">
        <div class="header-left">
          <NuxtLink to="/" class="back-link">← Back to Website</NuxtLink>
        </div>
        <div class="header-right">
          <span class="welcome">Welcome, {{ user?.name }}!</span>
        </div>
      </header>
      
      <div class="admin-content">
        <slot />
      </div>
    </main>
  </div>
</template>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background: #f0f2f5;
}

/* Sidebar */
.admin-sidebar {
  width: 260px;
  background: linear-gradient(180deg, #1d3557 0%, #0d1b2a 100%);
  color: white;
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  left: 0;
  top: 0;
  z-index: 100;
}

.sidebar-header {
  padding: 1.5rem;
  text-align: center;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

.sidebar-logo {
  height: 50px;
  margin-bottom: 0.5rem;
}

.sidebar-header h2 {
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0;
  color: #D59F4A;
}

.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
  overflow-y: auto;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 0.9rem 1.5rem;
  color: rgba(255,255,255,0.8);
  text-decoration: none;
  transition: all 0.2s;
  border-left: 3px solid transparent;
}

.nav-item:hover {
  background: rgba(255,255,255,0.05);
  color: white;
}

.nav-item.router-link-active,
.nav-item.router-link-exact-active {
  background: rgba(213, 159, 74, 0.15);
  color: #D59F4A;
  border-left-color: #D59F4A;
}

.nav-icon {
  font-size: 1.2rem;
  margin-right: 0.75rem;
  width: 24px;
  text-align: center;
}

.nav-text {
  font-size: 0.95rem;
}

.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid rgba(255,255,255,0.1);
}

.nav-divider {
  height: 1px;
  background: rgba(255,255,255,0.1);
  margin: 0.5rem 1.5rem;
}

.back-home-link {
  margin-top: 0.5rem;
}

.user-info {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}

.user-name {
  font-weight: 600;
  color: white;
}

.user-role {
  font-size: 0.8rem;
  color: #D59F4A;
}

.logout-btn {
  width: 100%;
  padding: 0.75rem;
  background: rgba(255,255,255,0.1);
  color: white;
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.logout-btn:hover {
  background: #ff6b6b;
  border-color: #ff6b6b;
}

/* Main Content */
.admin-main {
  flex: 1;
  margin-left: 260px;
  display: flex;
  flex-direction: column;
}

.admin-header {
  background: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  position: sticky;
  top: 0;
  z-index: 50;
}

.back-link {
  color: #1d3557;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.back-link:hover {
  color: #D59F4A;
}

.welcome {
  color: #666;
}

.admin-content {
  padding: 2rem;
  flex: 1;
}

/* Responsive */
@media (max-width: 768px) {
  .admin-sidebar {
    width: 70px;
  }
  
  .sidebar-header h2,
  .nav-text,
  .user-info {
    display: none;
  }
  
  .admin-main {
    margin-left: 70px;
  }
  
  .nav-item {
    justify-content: center;
    padding: 1rem;
  }
  
  .nav-icon {
    margin: 0;
  }
}
</style>
