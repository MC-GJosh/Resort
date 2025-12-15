<script setup>
definePageMeta({
  layout: 'admin'
});

const api = useApi();
const { user, isLoggedIn } = useAuth();
const router = useRouter();

const loading = ref(true);
const courts = ref([]);
const showForm = ref(false);
const editingCourt = ref(null);
const submitting = ref(false);

const form = ref({
  name: '',
  rate: '',
  location: '',
  surface: '',
  description: '',
  image: '/pickballcourtsolo.jpg',
  features: [],
  is_active: true
});

const featureInput = ref('');

const fetchCourts = async () => {
  loading.value = true;
  const { data } = await api.get('/courts');
  if (data) {
    courts.value = data;
  }
  loading.value = false;
};

onMounted(() => {
  if (!isLoggedIn.value || user.value?.role !== 'admin') {
    router.push('/');
    return;
  }
  fetchCourts();
});

const openCreateForm = () => {
  editingCourt.value = null;
  form.value = {
    name: '',
    rate: '',
    location: '',
    surface: 'Standard Hard',
    description: '',
    image: '/pickballcourtsolo.jpg',
    features: [],
    is_active: true
  };
  showForm.value = true;
};

const openEditForm = (court) => {
  editingCourt.value = court;
  form.value = {
    name: court.name,
    rate: court.rate,
    location: court.location || '',
    surface: court.surface || '',
    description: court.description || '',
    image: court.image || '/pickballcourtsolo.jpg',
    features: court.features || [],
    is_active: court.is_active
  };
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  editingCourt.value = null;
};

const addFeature = () => {
  if (featureInput.value.trim()) {
    form.value.features.push(featureInput.value.trim());
    featureInput.value = '';
  }
};

const removeFeature = (index) => {
  form.value.features.splice(index, 1);
};

const submitForm = async () => {
  submitting.value = true;
  
  const payload = {
    ...form.value,
    rate: parseFloat(form.value.rate)
  };
  
  let result;
  if (editingCourt.value) {
    result = await api.put(`/admin/courts/${editingCourt.value.id}`, payload);
  } else {
    result = await api.post('/admin/courts', payload);
  }
  
  submitting.value = false;
  
  if (result.error) {
    alert(result.error.message || 'Failed to save court');
  } else {
    closeForm();
    fetchCourts();
  }
};

const deleteCourt = async (court) => {
  if (!confirm(`Delete "${court.name}"? This cannot be undone.`)) return;
  
  const { error } = await api.del(`/admin/courts/${court.id}`);
  if (error) {
    alert(error.message || 'Failed to delete court');
  } else {
    fetchCourts();
  }
};

const formatPrice = (price) => {
  return '₱' + parseFloat(price).toLocaleString();
};
</script>

<template>
  <div class="courts-page">
    <div class="page-header">
      <div>
        <h1>Courts Management</h1>
        <p>Manage pickleball courts</p>
      </div>
      <button @click="openCreateForm" class="add-btn">+ Add Court</button>
    </div>

    <div v-if="loading" class="loading">Loading courts...</div>

    <div v-else class="courts-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Rate</th>
            <th>Location</th>
            <th>Surface</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="court in courts" :key="court.id">
            <td>{{ court.id }}</td>
            <td><strong>{{ court.name }}</strong></td>
            <td>{{ formatPrice(court.rate) }}/hr</td>
            <td>{{ court.location || '-' }}</td>
            <td>{{ court.surface || '-' }}</td>
            <td>
              <span :class="['badge', court.is_active ? 'active' : 'inactive']">
                {{ court.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="actions">
              <button @click="openEditForm(court)" class="edit-btn">Edit</button>
              <button @click="deleteCourt(court)" class="delete-btn">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Form Modal -->
    <div v-if="showForm" class="modal-overlay" @click.self="closeForm">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ editingCourt ? 'Edit Court' : 'Add New Court' }}</h2>
          <button @click="closeForm" class="close-btn">&times;</button>
        </div>
        
        <form @submit.prevent="submitForm" class="form">
          <div class="form-row">
            <div class="form-group">
              <label>Court Name *</label>
              <input v-model="form.name" type="text" required />
            </div>
            <div class="form-group">
              <label>Rate per Hour *</label>
              <input v-model="form.rate" type="number" step="0.01" required />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Location</label>
              <input v-model="form.location" type="text" placeholder="e.g. Left Side" />
            </div>
            <div class="form-group">
              <label>Surface</label>
              <input v-model="form.surface" type="text" placeholder="e.g. Standard Hard" />
            </div>
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea v-model="form.description" rows="3"></textarea>
          </div>

          <div class="form-group">
            <label>Image URL</label>
            <input v-model="form.image" type="text" />
          </div>

          <div class="form-group">
            <label>Features</label>
            <div class="features-input">
              <input v-model="featureInput" type="text" placeholder="Add a feature" @keyup.enter.prevent="addFeature" />
              <button type="button" @click="addFeature" class="add-feature-btn">Add</button>
            </div>
            <div class="features-list">
              <span v-for="(f, i) in form.features" :key="i" class="feature-tag">
                {{ f }}
                <button type="button" @click="removeFeature(i)">&times;</button>
              </span>
            </div>
          </div>

          <div class="form-group checkbox">
            <label>
              <input type="checkbox" v-model="form.is_active" />
              Active
            </label>
          </div>

          <div class="form-actions">
            <button type="button" @click="closeForm" class="cancel-btn">Cancel</button>
            <button type="submit" class="submit-btn" :disabled="submitting">
              {{ submitting ? 'Saving...' : (editingCourt ? 'Update' : 'Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.courts-page {
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 1.8rem;
  color: #1d3557;
  margin: 0 0 0.25rem 0;
}

.page-header p {
  color: #666;
  margin: 0;
}

.add-btn {
  background: #D59F4A;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.add-btn:hover {
  background: #b58334;
}

.loading {
  text-align: center;
  padding: 3rem;
  color: #666;
}

/* Table */
.courts-table {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #eee;
}

th {
  background: #f8f9fa;
  font-weight: 600;
  color: #1d3557;
  font-size: 0.85rem;
  text-transform: uppercase;
}

td {
  color: #333;
}

.badge {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge.active {
  background: #d4edda;
  color: #155724;
}

.badge.inactive {
  background: #f8d7da;
  color: #721c24;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.edit-btn, .delete-btn {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.85rem;
  transition: background 0.2s;
}

.edit-btn {
  background: #e3f2fd;
  color: #1976d2;
}

.edit-btn:hover {
  background: #bbdefb;
}

.delete-btn {
  background: #ffebee;
  color: #c62828;
}

.delete-btn:hover {
  background: #ffcdd2;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
}

.modal-header h2 {
  margin: 0;
  color: #1d3557;
  font-size: 1.3rem;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #999;
}

.form {
  padding: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #333;
  font-size: 0.9rem;
}

.form-group input, .form-group textarea, .form-group select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  box-sizing: border-box;
}

.form-group input:focus, .form-group textarea:focus {
  outline: none;
  border-color: #D59F4A;
}

.form-group.checkbox label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.form-group.checkbox input {
  width: auto;
}

.features-input {
  display: flex;
  gap: 0.5rem;
}

.add-feature-btn {
  background: #f0f0f0;
  border: none;
  padding: 0.75rem 1rem;
  border-radius: 6px;
  cursor: pointer;
}

.features-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.feature-tag {
  background: #e3f2fd;
  color: #1976d2;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.feature-tag button {
  background: none;
  border: none;
  color: #1976d2;
  cursor: pointer;
  font-size: 1rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #eee;
}

.cancel-btn {
  background: #f0f0f0;
  color: #333;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
}

.submit-btn {
  background: #D59F4A;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.submit-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
