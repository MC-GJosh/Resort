<script setup>
definePageMeta({
  layout: 'admin'
});

const api = useApi();
const { user, isLoggedIn } = useAuth();
const router = useRouter();

const loading = ref(true);
const dashboardData = ref(null);

const fetchDashboard = async () => {
  loading.value = true;
  const { data, error } = await api.get('/admin/dashboard');
  if (error) {
    console.error('Dashboard error:', error);
    if (error.message?.includes('Admin access required')) {
      router.push('/');
    }
  } else {
    dashboardData.value = data;
  }
  loading.value = false;
};

onMounted(() => {
  if (!isLoggedIn.value || user.value?.role !== 'admin') {
    router.push('/');
    return;
  }
  fetchDashboard();
});

const formatPrice = (price) => {
  return '₱' + parseFloat(price || 0).toLocaleString();
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString('en-PH', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};
</script>

<template>
  <div class="dashboard-page">
    <div class="page-header">
      <h1>Dashboard</h1>
      <p>Welcome back! Here's an overview of your resort.</p>
    </div>

    <div v-if="loading" class="loading">
      <p>Loading dashboard...</p>
    </div>

    <template v-else-if="dashboardData">
      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon users">👥</div>
          <div class="stat-info">
            <span class="stat-value">{{ dashboardData.stats.users }}</span>
            <span class="stat-label">Total Users</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon courts">🏸</div>
          <div class="stat-info">
            <span class="stat-value">{{ dashboardData.stats.courts }}</span>
            <span class="stat-label">Courts</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon rooms">🛏️</div>
          <div class="stat-info">
            <span class="stat-value">{{ dashboardData.stats.rooms }}</span>
            <span class="stat-label">Rooms</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon halls">🎪</div>
          <div class="stat-info">
            <span class="stat-value">{{ dashboardData.stats.function_halls }}</span>
            <span class="stat-label">Function Halls</span>
          </div>
        </div>
      </div>

      <!-- Revenue Cards -->
      <div class="revenue-section">
        <h2>Revenue Overview</h2>
        <div class="revenue-grid">
          <div class="revenue-card total">
            <span class="revenue-label">Total Revenue</span>
            <span class="revenue-value">{{ formatPrice(dashboardData.revenue.total) }}</span>
          </div>
          <div class="revenue-card">
            <span class="revenue-label">Pickleball</span>
            <span class="revenue-value">{{ formatPrice(dashboardData.revenue.pickleball) }}</span>
          </div>
          <div class="revenue-card">
            <span class="revenue-label">Rooms</span>
            <span class="revenue-value">{{ formatPrice(dashboardData.revenue.rooms) }}</span>
          </div>
          <div class="revenue-card">
            <span class="revenue-label">Halls</span>
            <span class="revenue-value">{{ formatPrice(dashboardData.revenue.halls) }}</span>
          </div>
        </div>
      </div>

      <!-- Today's Activity -->
      <div class="today-section">
        <h2>Today's Activity</h2>
        <div class="today-grid">
          <div class="today-card">
            <span class="today-value">{{ dashboardData.today.pickleball }}</span>
            <span class="today-label">Court Bookings</span>
          </div>
          <div class="today-card">
            <span class="today-value">{{ dashboardData.today.room_checkins }}</span>
            <span class="today-label">Room Check-ins</span>
          </div>
          <div class="today-card">
            <span class="today-value">{{ dashboardData.today.hall_events }}</span>
            <span class="today-label">Hall Events</span>
          </div>
        </div>
      </div>

      <!-- Recent Bookings -->
      <div class="recent-section">
        <h2>Recent Bookings</h2>
        
        <div class="bookings-grid">
          <!-- Pickleball -->
          <div class="booking-panel">
            <h3>🏸 Pickleball</h3>
            <div v-if="dashboardData.recent.pickleball.length === 0" class="empty">
              No recent bookings
            </div>
            <div v-else class="booking-list">
              <div v-for="b in dashboardData.recent.pickleball" :key="b.id" class="booking-item">
                <div class="booking-info">
                  <strong>{{ b.customer_name }}</strong>
                  <span>{{ b.court?.name }} • {{ b.time_slot }}</span>
                </div>
                <div class="booking-meta">
                  <span class="date">{{ formatDate(b.date) }}</span>
                  <span :class="['status', b.status]">{{ b.status }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Rooms -->
          <div class="booking-panel">
            <h3>🛏️ Rooms</h3>
            <div v-if="dashboardData.recent.rooms.length === 0" class="empty">
              No recent bookings
            </div>
            <div v-else class="booking-list">
              <div v-for="b in dashboardData.recent.rooms" :key="b.id" class="booking-item">
                <div class="booking-info">
                  <strong>{{ b.guest_name }}</strong>
                  <span>{{ b.room?.name }}</span>
                </div>
                <div class="booking-meta">
                  <span class="date">{{ formatDate(b.check_in) }}</span>
                  <span :class="['status', b.status]">{{ b.status }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Halls -->
          <div class="booking-panel">
            <h3>🎪 Halls</h3>
            <div v-if="dashboardData.recent.halls.length === 0" class="empty">
              No recent bookings
            </div>
            <div v-else class="booking-list">
              <div v-for="b in dashboardData.recent.halls" :key="b.id" class="booking-item">
                <div class="booking-info">
                  <strong>{{ b.full_name }}</strong>
                  <span>{{ b.function_hall?.name }}</span>
                </div>
                <div class="booking-meta">
                  <span class="date">{{ formatDate(b.event_date) }}</span>
                  <span :class="['status', b.status]">{{ b.status }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<style scoped>
.dashboard-page {
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2rem;
  color: #1d3557;
  margin: 0 0 0.5rem 0;
}

.page-header p {
  color: #666;
  margin: 0;
}

.loading {
  text-align: center;
  padding: 3rem;
  color: #666;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.stat-icon.users { background: #e3f2fd; }
.stat-icon.courts { background: #fff3e0; }
.stat-icon.rooms { background: #f3e5f5; }
.stat-icon.halls { background: #e8f5e9; }

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1d3557;
}

.stat-label {
  font-size: 0.9rem;
  color: #666;
}

/* Revenue Section */
.revenue-section {
  margin-bottom: 2rem;
}

.revenue-section h2 {
  font-size: 1.2rem;
  color: #1d3557;
  margin-bottom: 1rem;
}

.revenue-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.revenue-card {
  background: white;
  padding: 1.25rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
}

.revenue-card.total {
  background: linear-gradient(135deg, #1d3557 0%, #457b9d 100%);
  color: white;
}

.revenue-card.total .revenue-label {
  color: rgba(255,255,255,0.8);
}

.revenue-card.total .revenue-value {
  color: white;
}

.revenue-label {
  font-size: 0.85rem;
  color: #666;
  margin-bottom: 0.5rem;
}

.revenue-value {
  font-size: 1.4rem;
  font-weight: 700;
  color: #D59F4A;
}

/* Today Section */
.today-section {
  margin-bottom: 2rem;
}

.today-section h2 {
  font-size: 1.2rem;
  color: #1d3557;
  margin-bottom: 1rem;
}

.today-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.today-card {
  background: white;
  padding: 1.5rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  text-align: center;
}

.today-value {
  display: block;
  font-size: 2.5rem;
  font-weight: 700;
  color: #1d3557;
}

.today-label {
  font-size: 0.9rem;
  color: #666;
}

/* Recent Section */
.recent-section h2 {
  font-size: 1.2rem;
  color: #1d3557;
  margin-bottom: 1rem;
}

.bookings-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.booking-panel {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  overflow: hidden;
}

.booking-panel h3 {
  padding: 1rem 1.25rem;
  margin: 0;
  background: #f8f9fa;
  font-size: 1rem;
  color: #1d3557;
  border-bottom: 1px solid #eee;
}

.empty {
  padding: 2rem;
  text-align: center;
  color: #999;
}

.booking-list {
  max-height: 300px;
  overflow-y: auto;
}

.booking-item {
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #f0f0f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.booking-item:last-child {
  border-bottom: none;
}

.booking-info {
  display: flex;
  flex-direction: column;
}

.booking-info strong {
  color: #1d3557;
  font-size: 0.95rem;
}

.booking-info span {
  color: #888;
  font-size: 0.8rem;
}

.booking-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 0.25rem;
}

.date {
  font-size: 0.8rem;
  color: #666;
}

.status {
  font-size: 0.7rem;
  padding: 2px 8px;
  border-radius: 10px;
  text-transform: uppercase;
  font-weight: 600;
}

.status.pending { background: #fff3cd; color: #856404; }
.status.confirmed { background: #d4edda; color: #155724; }
.status.cancelled { background: #f8d7da; color: #721c24; }
.status.completed { background: #cce5ff; color: #004085; }
.status.checked_in { background: #d4edda; color: #155724; }
.status.checked_out { background: #e2e3e5; color: #383d41; }

/* Responsive */
@media (max-width: 1200px) {
  .stats-grid, .revenue-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .bookings-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .stats-grid, .revenue-grid, .today-grid {
    grid-template-columns: 1fr;
  }
}
</style>
