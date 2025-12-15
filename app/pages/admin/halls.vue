<script setup>
definePageMeta({
  layout: 'admin'
});

const api = useApi();
const { user, isLoggedIn } = useAuth();
const router = useRouter();

const loading = ref(true);
const halls = ref([]);
const showForm = ref(false);
const editingHall = ref(null);
const submitting = ref(false);

const form = ref({
  name: '',
  slug: '',
  price_per_4hrs: '',
  min_capacity: 30,
  max_capacity: 100,
  size: '',
  description: '',
  amenities: [],
  image_class: 'small-hall-img',
  badge: '',
  is_premium: false,
  is_active: true
});

const amenityInput = ref('');

const fetchHalls = async () => {
  loading.value = true;
  const { data } = await api.get('/function-halls');
  if (data) halls.value = data;
  loading.value = false;
};

onMounted(() => {
  if (!isLoggedIn.value || user.value?.role !== 'admin') {
    router.push('/');
    return;
  }
  fetchHalls();
});

const openCreateForm = () => {
  editingHall.value = null;
  form.value = { name: '', slug: '', price_per_4hrs: '', min_capacity: 30, max_capacity: 100, size: '', description: '', amenities: [], image_class: 'small-hall-img', badge: '', is_premium: false, is_active: true };
  showForm.value = true;
};

const openEditForm = (hall) => {
  editingHall.value = hall;
  form.value = { 
    name: hall.name, slug: hall.slug || '', price_per_4hrs: hall.price_per_4hrs, 
    min_capacity: hall.min_capacity, max_capacity: hall.max_capacity, 
    size: hall.size || '', description: hall.description || '', 
    amenities: hall.amenities || [], image_class: hall.image_class || 'small-hall-img',
    badge: hall.badge || '', is_premium: hall.is_premium, is_active: hall.is_active 
  };
  showForm.value = true;
};

const closeForm = () => { showForm.value = false; editingHall.value = null; };

const addAmenity = () => { if (amenityInput.value.trim()) { form.value.amenities.push(amenityInput.value.trim()); amenityInput.value = ''; } };
const removeAmenity = (index) => { form.value.amenities.splice(index, 1); };

const submitForm = async () => {
  submitting.value = true;
  const payload = { ...form.value, price_per_4hrs: parseFloat(form.value.price_per_4hrs) };
  
  let result;
  if (editingHall.value) result = await api.put(`/admin/function-halls/${editingHall.value.id}`, payload);
  else result = await api.post('/admin/function-halls', payload);
  
  submitting.value = false;
  if (result.error) alert(result.error.message || 'Failed to save hall');
  else { closeForm(); fetchHalls(); }
};

const deleteHall = async (hall) => {
  if (!confirm(`Delete "${hall.name}"?`)) return;
  const { error } = await api.del(`/admin/function-halls/${hall.id}`);
  if (error) alert(error.message || 'Failed to delete hall');
  else fetchHalls();
};

const formatPrice = (price) => '₱' + parseFloat(price).toLocaleString();
</script>

<template>
  <div class="halls-page">
    <div class="page-header">
      <div>
        <h1>Function Halls Management</h1>
        <p>Manage event venues</p>
      </div>
      <button @click="openCreateForm" class="add-btn">+ Add Hall</button>
    </div>

    <div v-if="loading" class="loading">Loading halls...</div>

    <div v-else class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price/4hrs</th>
            <th>Capacity</th>
            <th>Size</th>
            <th>Premium</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="hall in halls" :key="hall.id">
            <td>{{ hall.id }}</td>
            <td><strong>{{ hall.name }}</strong><br><small>{{ hall.badge }}</small></td>
            <td>{{ formatPrice(hall.price_per_4hrs) }}</td>
            <td>{{ hall.min_capacity }} - {{ hall.max_capacity }}</td>
            <td>{{ hall.size || '-' }}</td>
            <td><span :class="['badge', hall.is_premium ? 'premium' : 'normal']">{{ hall.is_premium ? 'Premium' : 'Standard' }}</span></td>
            <td><span :class="['badge', hall.is_active ? 'active' : 'inactive']">{{ hall.is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="actions">
              <button @click="openEditForm(hall)" class="edit-btn">Edit</button>
              <button @click="deleteHall(hall)" class="delete-btn">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Form Modal -->
    <div v-if="showForm" class="modal-overlay" @click.self="closeForm">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ editingHall ? 'Edit Hall' : 'Add New Hall' }}</h2>
          <button @click="closeForm" class="close-btn">&times;</button>
        </div>
        
        <form @submit.prevent="submitForm" class="form">
          <div class="form-row">
            <div class="form-group"><label>Hall Name *</label><input v-model="form.name" type="text" required /></div>
            <div class="form-group"><label>Slug</label><input v-model="form.slug" type="text" placeholder="auto-generated" /></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>Price per 4 Hours *</label><input v-model="form.price_per_4hrs" type="number" step="0.01" required /></div>
            <div class="form-group"><label>Badge</label><input v-model="form.badge" type="text" placeholder="e.g. Grand Choice" /></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>Min Capacity *</label><input v-model="form.min_capacity" type="number" min="1" required /></div>
            <div class="form-group"><label>Max Capacity *</label><input v-model="form.max_capacity" type="number" min="1" required /></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>Size</label><input v-model="form.size" type="text" placeholder="e.g. 500 sq. meters" /></div>
            <div class="form-group"><label>Image Class</label><input v-model="form.image_class" type="text" /></div>
          </div>
          <div class="form-group"><label>Description</label><textarea v-model="form.description" rows="3"></textarea></div>
          <div class="form-group">
            <label>Amenities</label>
            <div class="features-input">
              <input v-model="amenityInput" type="text" placeholder="Add amenity" @keyup.enter.prevent="addAmenity" />
              <button type="button" @click="addAmenity" class="add-feature-btn">Add</button>
            </div>
            <div class="features-list">
              <span v-for="(a, i) in form.amenities" :key="i" class="feature-tag">{{ a }}<button type="button" @click="removeAmenity(i)">&times;</button></span>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group checkbox"><label><input type="checkbox" v-model="form.is_premium" /> Premium Hall</label></div>
            <div class="form-group checkbox"><label><input type="checkbox" v-model="form.is_active" /> Active</label></div>
          </div>
          <div class="form-actions">
            <button type="button" @click="closeForm" class="cancel-btn">Cancel</button>
            <button type="submit" class="submit-btn" :disabled="submitting">{{ submitting ? 'Saving...' : (editingHall ? 'Update' : 'Create') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.halls-page { max-width: 1200px; margin: 0 auto; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.page-header h1 { font-size: 1.8rem; color: #1d3557; margin: 0 0 0.25rem 0; }
.page-header p { color: #666; margin: 0; }
.add-btn { background: #D59F4A; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 600; }
.add-btn:hover { background: #b58334; }
.loading { text-align: center; padding: 3rem; color: #666; }
.table-wrapper { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
th { background: #f8f9fa; font-weight: 600; color: #1d3557; font-size: 0.85rem; text-transform: uppercase; }
small { color: #888; }
.badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.badge.active { background: #d4edda; color: #155724; }
.badge.inactive { background: #f8d7da; color: #721c24; }
.badge.premium { background: #fff3e0; color: #e65100; }
.badge.normal { background: #e3f2fd; color: #1976d2; }
.actions { display: flex; gap: 0.5rem; }
.edit-btn, .delete-btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; }
.edit-btn { background: #e3f2fd; color: #1976d2; }
.delete-btn { background: #ffebee; color: #c62828; }
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; padding: 1rem; }
.modal-content { background: white; border-radius: 12px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid #eee; }
.modal-header h2 { margin: 0; color: #1d3557; font-size: 1.3rem; }
.close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #999; }
.form { padding: 1.5rem; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #333; font-size: 0.9rem; }
.form-group input, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; box-sizing: border-box; }
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: #D59F4A; }
.form-group.checkbox label { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
.form-group.checkbox input { width: auto; }
.features-input { display: flex; gap: 0.5rem; }
.add-feature-btn { background: #f0f0f0; border: none; padding: 0.75rem 1rem; border-radius: 6px; cursor: pointer; }
.features-list { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem; }
.feature-tag { background: #e3f2fd; color: #1976d2; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; }
.feature-tag button { background: none; border: none; color: #1976d2; cursor: pointer; }
.form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #eee; }
.cancel-btn { background: #f0f0f0; color: #333; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; cursor: pointer; }
.submit-btn { background: #D59F4A; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; cursor: pointer; font-weight: 600; }
.submit-btn:disabled { background: #ccc; cursor: not-allowed; }
</style>
