<script setup>
definePageMeta({
  layout: 'admin'
});

const api = useApi();
const { user, isLoggedIn } = useAuth();
const router = useRouter();

const loading = ref(true);
const bookings = ref([]);
const halls = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0 });

const filters = ref({
  status: '',
  function_hall_id: ''
});

const fetchBookings = async () => {
  loading.value = true;
  
  const params = new URLSearchParams();
  if (filters.value.status) params.append('status', filters.value.status);
  if (filters.value.function_hall_id) params.append('function_hall_id', filters.value.function_hall_id);
  
  const { data } = await api.get(`/admin/hall-bookings?${params.toString()}`);
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

const fetchHalls = async () => {
  const { data } = await api.get('/function-halls');
  if (data) halls.value = data;
};

onMounted(() => {
  if (!isLoggedIn.value || user.value?.role !== 'admin') {
    router.push('/');
    return;
  }
  fetchHalls();
  fetchBookings();
});

const updateStatus = async (booking, newStatus) => {
  const { error } = await api.patch(`/admin/hall-bookings/${booking.id}/status`, {
    status: newStatus
  });
  
  if (error) {
    alert(error.message || 'Failed to update status');
  } else {
    booking.status = newStatus;
  }
};

const applyFilters = () => fetchBookings();
const clearFilters = () => { filters.value = { status: '', function_hall_id: '' }; fetchBookings(); };

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
        <h1>🎪 Hall Bookings</h1>
        <p>Manage function hall reservations</p>
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
      <select v-model="filters.function_hall_id">
        <option value="">All Halls</option>
        <option v-for="h in halls" :key="h.id" :value="h.id">{{ h.name }}</option>
      </select>
      <button @click="applyFilters" class="filter-btn">Apply</button>
      <button @click="clearFilters" class="clear-btn">Clear</button>
    </div>

    <div v-if="loading" class="loading-container">
      <LoadingSpinner />
    </div>

    <div v-else class="table-wrapper">
      <div class="table-header"><span>Total: {{ pagination.total }} bookings</span></div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Booker</th>
            <th>Hall</th>
            <th>Event Date</th>
            <th>Guests</th>
            <th>Catering</th>
            <th>Total</th>
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
            <td><strong>{{ b.full_name }}</strong><br><small>{{ b.phone }}</small></td>
            <td>{{ b.function_hall?.name || '-' }}</td>
            <td>{{ formatDate(b.event_date) }}</td>
            <td>{{ b.guest_count }}</td>
            <td>
              <span v-if="b.catering_package">{{ b.catering_package }}</span>
              <span v-else class="no-catering">None</span>
            </td>
            <td>{{ formatPrice(b.total_price) }}</td>
            <td><span :class="['badge', b.status]">{{ b.status }}</span></td>
            <td class="actions">
              <select :value="b.status" @change="updateStatus(b, $event.target.value)" class="status-select">
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
.filters-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.filters-bar select, .filters-bar input { padding: 0.6rem 1rem; border: 1px solid #ddd; border-radius: 6px; font-size: 0.95rem; background: white; }
.filter-btn { background: #1d3557; color: white; border: none; padding: 0.6rem 1.5rem; border-radius: 6px; cursor: pointer; }
.clear-btn { background: #f0f0f0; color: #333; border: none; padding: 0.6rem 1.5rem; border-radius: 6px; cursor: pointer; }
.loading-container { 
  display: flex; 
  justify-content: center; 
  padding: 4rem; 
}
.table-wrapper { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; }
.table-header { padding: 1rem 1.5rem; background: #f8f9fa; border-bottom: 1px solid #eee; color: #666; font-size: 0.9rem; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
th { background: #f8f9fa; font-weight: 600; color: #1d3557; font-size: 0.8rem; text-transform: uppercase; }
td { font-size: 0.9rem; }
td small { color: #888; font-size: 0.8rem; }
.empty { text-align: center; color: #999; padding: 2rem; }
.no-catering { color: #999; font-style: italic; }
.badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
.badge.pending { background: #fff3cd; color: #856404; }
.badge.confirmed { background: #d4edda; color: #155724; }
.badge.cancelled { background: #f8d7da; color: #721c24; }
.badge.completed { background: #cce5ff; color: #004085; }
.actions { min-width: 120px; }
.status-select { padding: 0.4rem 0.6rem; border: 1px solid #ddd; border-radius: 4px; font-size: 0.85rem; width: 100%; }
</style>
