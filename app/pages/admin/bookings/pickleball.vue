<script setup>
definePageMeta({
  layout: 'admin'
});

const api = useApi();
const { user, isLoggedIn } = useAuth();
const router = useRouter();

const loading = ref(true);
const bookings = ref([]);
const courts = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0 });

const filters = ref({
  status: '',
  court_id: '',
  date: ''
});

const fetchBookings = async () => {
  loading.value = true;
  
  const params = new URLSearchParams();
  if (filters.value.status) params.append('status', filters.value.status);
  if (filters.value.court_id) params.append('court_id', filters.value.court_id);
  if (filters.value.date) params.append('date', filters.value.date);
  
  const { data } = await api.get(`/admin/pickleball-bookings?${params.toString()}`);
  if (data) {
    bookings.value = data.data || [];
    pagination.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
      total: data.total || 0
    };
  }
  loading.value = false;
};

const fetchCourts = async () => {
  const { data } = await api.get('/courts');
  if (data) courts.value = data;
};

onMounted(() => {
  if (!isLoggedIn.value || user.value?.role !== 'admin') {
    router.push('/');
    return;
  }
  fetchCourts();
  fetchBookings();
});

const loadingId = ref(null);

const updateStatus = async (booking, newStatus) => {
  loadingId.value = booking.id;
  const { error } = await api.patch(`/admin/pickleball-bookings/${booking.id}/status`, {
    status: newStatus
  });
  
  // Simulate network delay for UX if too fast, but keep it snappy (1s)
  await new Promise(resolve => setTimeout(resolve, 1000));
  
  if (error) {
    alert(error.message || 'Failed to update status');
  } else {
    booking.status = newStatus;
  }
  loadingId.value = null;
};

// ... existing code ...


const applyFilters = () => {
  fetchBookings();
};

const clearFilters = () => {
  filters.value = { status: '', court_id: '', date: '' };
  fetchBookings();
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatPrice = (price) => '₱' + parseFloat(price || 0).toLocaleString();
</script>

<template>
  <div class="bookings-page">
    <div class="page-header">
      <div>
        <h1>🏸 Pickleball Bookings</h1>
        <p>Manage court reservations</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
      <select v-model="filters.status">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="confirmed">Confirmed</option>
        <option value="cancelled">Cancelled</option>
        <option value="completed">Completed</option>
      </select>
      <select v-model="filters.court_id">
        <option value="">All Courts</option>
        <option v-for="c in courts" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <input type="date" v-model="filters.date" />
      <button @click="applyFilters" class="filter-btn">Apply</button>
      <button @click="clearFilters" class="clear-btn">Clear</button>
    </div>

    <div v-if="loading" class="loading-container">
      <LoadingSpinner />
    </div>

    <div v-else class="table-wrapper">
      <div class="table-header">
        <span>Total: {{ pagination.total }} bookings</span>
      </div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Court</th>
            <th>Date</th>
            <th>Time</th>
            <th>Phone</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="bookings.length === 0">
            <td colspan="9" class="empty">No bookings found</td>
          </tr>
          <tr v-for="b in bookings" :key="b.id">
            <td>{{ b.id }}</td>
            <td><strong>{{ b.customer_name }}</strong></td>
            <td>{{ b.court?.name || '-' }}</td>
            <td>{{ formatDate(b.date) }}</td>
            <td>{{ b.time_slot }}</td>
            <td>{{ b.phone || '-' }}</td>
            <td>{{ b.payment_method }}<br><small>{{ b.reference_number || '-' }}</small></td>
            <td><span :class="['badge', b.status]">{{ b.status }}</span></td>
            <td class="actions">
              <div v-if="loadingId === b.id" class="updating-status">
                <span class="spinner"></span> Updating...
              </div>
              <select 
                v-else
                :value="b.status" 
                @change="updateStatus(b, $event.target.value)"
                class="status-select"
                :disabled="loadingId === b.id"
              >
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.bookings-page { max-width: 1400px; margin: 0 auto; }
.page-header { margin-bottom: 1.5rem; }
.page-header h1 { font-size: 1.8rem; color: #1d3557; margin: 0 0 0.25rem 0; }
.page-header p { color: #666; margin: 0; }

.filters-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.filters-bar select, .filters-bar input {
  padding: 0.6rem 1rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 0.95rem;
  background: white;
}

.filter-btn {
  background: #1d3557;
  color: white;
  border: none;
  padding: 0.6rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
}

.clear-btn {
  background: #f0f0f0;
  color: #333;
  border: none;
  padding: 0.6rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
}

.loading-container { 
  display: flex; 
  justify-content: center; 
  padding: 4rem; 
}

.table-wrapper { 
  background: white; 
  border-radius: 12px; 
  box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
  overflow: hidden; 
}

.table-header {
  padding: 1rem 1.5rem;
  background: #f8f9fa;
  border-bottom: 1px solid #eee;
  color: #666;
  font-size: 0.9rem;
}

table { width: 100%; border-collapse: collapse; }
th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
th { background: #f8f9fa; font-weight: 600; color: #1d3557; font-size: 0.8rem; text-transform: uppercase; }
td { font-size: 0.9rem; }
td small { color: #888; font-size: 0.8rem; }
.empty { text-align: center; color: #999; padding: 2rem; }

.badge {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}
.badge.pending { background: #fff3cd; color: #856404; }
.badge.confirmed { background: #d4edda; color: #155724; }
.badge.cancelled { background: #f8d7da; color: #721c24; }
.badge.completed { background: #cce5ff; color: #004085; }

.actions { min-width: 120px; }
.status-select {
  padding: 0.4rem 0.6rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.85rem;
  width: 100%;
}

.updating-status {
  font-size: 0.85rem;
  color: #666;
  display: flex;
  align-items: center;
  gap: 6px;
}

.spinner {
  width: 12px;
  height: 12px;
  border: 2px solid #ddd;
  border-top-color: #1d3557;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  display: inline-block;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
