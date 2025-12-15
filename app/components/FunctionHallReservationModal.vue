<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import PaymentMethod from './PaymentMethod.vue';

const props = defineProps({
  isVisible: Boolean,
  halls: {
    type: Array,
    required: true
  },
  initialHall: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close', 'submit']);

const bookingForm = ref({
  fullName: '',
  phoneNumber: '',
  address: '',
  email: '',
  eventDate: '',
  guestCount: 50,
  selectedHall: null,
  paymentMethod: 'GCash',
  referenceNumber: '',
  cateringPackage: '',
  selectedMainDish: '',
  selectedAppetizer: '',
  selectedDrink: '',
  availMiniBar: false,
  notes: ''
});

// Watch for initialHall changes
watch(() => props.initialHall, (newVal) => {
  if (newVal) {
    bookingForm.value.selectedHall = newVal;
  }
}, { immediate: true });

// Menu Options (Preserved from original)
const menuOptions = {
  mainDishes: ['Fried Chicken', 'Pork BBQ Skewers', 'Beef Caldereta', 'Breaded Fish Fillet', 'Roast Beef'],
  appetizers: ['Lumpiang Shanghai', 'Spaghetti Bolognese', 'Creamy Carbonara', 'Buttered Vegetables', 'Calamares'],
  drinks: ['Iced Tea', 'Blue Lemonade', 'Soda / Soft Drinks', 'Fruit Juice']
};

const validatePhone = (event) => {
  const input = event.target;
  input.value = input.value.replace(/\D/g, '').slice(0, 11);
  bookingForm.value.phoneNumber = input.value;
};

const formatPrice = (price) => {
  if (!price) return '₱0';
  return '₱' + parseFloat(price).toLocaleString();
};

const step = ref(1);

const goToPayment = () => {
  // Simple validation check before proceeding
  if (!bookingForm.value.eventDate || !bookingForm.value.fullName || !bookingForm.value.phoneNumber) {
    alert('Please fill in all required fields (Date, Name, Phone, Address) before proceeding.');
    return;
  }
  step.value = 2;
};

const closeModal = () => {
  step.value = 1; // Reset step on close
  emit('close');
};

const submitBooking = () => {
  emit('submit', bookingForm.value);
};

const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.isVisible) closeModal();
};

onMounted(() => window.addEventListener('keydown', handleKeydown));
onUnmounted(() => window.removeEventListener('keydown', handleKeydown));
</script>

<template>
  <div v-if="isVisible" class="modal-overlay">
    <div class="modal-content">
      <button class="close-btn" @click="closeModal">&times;</button>
      <h2>Reserve Function Hall</h2>
      
      <form @submit.prevent="submitBooking" class="modal-grid">
        
        <!-- LEFT COLUMN: Personal & Event Details -->
        <div class="left-column">
          <h3>Event Details</h3>
          
          <div class="form-group">
            <label>Select Hall</label>
            <select v-model="bookingForm.selectedHall">
              <option v-for="hall in halls" :key="hall.id" :value="hall">
                {{ hall.name }} ({{ formatPrice(hall.price_per_4hrs) }})
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Event Date</label>
            <input type="date" v-model="bookingForm.eventDate" :min="new Date().toISOString().split('T')[0]" required />
          </div>

          <div class="form-group">
             <label>Expected Guests</label>
             <input 
               type="number" 
               v-model="bookingForm.guestCount" 
               :min="bookingForm.selectedHall?.min_capacity || 30" 
               :max="bookingForm.selectedHall?.max_capacity || 400"
               required 
             />
          </div>

          <h3>Contact Information</h3>
          
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" v-model="bookingForm.fullName" placeholder="e.g. Juan Dela Cruz" required />
          </div>

          <div class="form-group">
            <label>Contact Number</label>
            <input 
              type="tel" 
              v-model="bookingForm.phoneNumber" 
              placeholder="e.g. 09171234567" 
              maxlength="11"
              @input="validatePhone"
              required 
            />
          </div>

          <div class="form-group">
            <label>Email Address</label>
            <input type="email" v-model="bookingForm.email" placeholder="e.g. juan@email.com" />
          </div>

          <div class="form-group">
            <label>Complete Address</label>
            <input type="text" v-model="bookingForm.address" placeholder="House No., Street, City, Province" required />
          </div>
        </div>

        <!-- RIGHT COLUMN: Catering & Payment -->
        <div class="right-column">
          <h3>Catering & Extras</h3>
          
          <div class="form-group">
            <label>Catering Package</label>
            <select v-model="bookingForm.cateringPackage">
              <option value="" disabled>Select a package</option>
              <option value="15-20pax">15-20 Pax Package</option>
              <option value="20-25pax">20-25 Pax Package</option>
              <option value="25-30pax">25-30 Pax Package</option>
            </select>
          </div>

          <div v-if="bookingForm.cateringPackage" class="menu-selection">
            <p><strong>Customize Menu:</strong></p>
            
            <div class="form-group">
              <select v-model="bookingForm.selectedMainDish">
                <option value="" disabled>Select Main Dish</option>
                <option v-for="dish in menuOptions.mainDishes" :key="dish" :value="dish">{{ dish }}</option>
              </select>
            </div>

            <div class="form-group">
              <select v-model="bookingForm.selectedAppetizer">
                <option value="" disabled>Select Appetizer</option>
                <option v-for="app in menuOptions.appetizers" :key="app" :value="app">{{ app }}</option>
              </select>
            </div>

            <div class="form-group">
              <select v-model="bookingForm.selectedDrink">
                <option value="" disabled>Select Drink</option>
                <option v-for="drink in menuOptions.drinks" :key="drink" :value="drink">{{ drink }}</option>
              </select>
            </div>

            <div class="form-group checkbox-group">
              <input type="checkbox" id="miniBar" v-model="bookingForm.availMiniBar" />
              <label for="miniBar">Avail Mini Bar (+ Extra Charge)</label>
            </div>
          </div>

          <div class="form-group">
            <label>Notes / Special Requests</label>
            <textarea v-model="bookingForm.notes" rows="2"></textarea>
          </div>

          <!-- Step 1 Trigger -->
          <button 
            v-if="step === 1" 
            type="button" 
            class="action-btn continue-btn" 
            @click="goToPayment"
          >
            Continue to Payment
          </button>

          <!-- Step 2: Payment -->
          <div v-if="step === 2" class="payment-section fade-in">
            <h3>Payment Details</h3>
            <PaymentMethod 
              v-model="bookingForm.paymentMethod" 
              v-model:referenceNumber="bookingForm.referenceNumber"
              :allowPayOnSite="false"
            />
            
            <div class="step-actions">
              <button type="button" class="action-btn back-btn" @click="step = 1">Back</button>
              <button type="submit" class="action-btn confirm-btn">Confirm Booking</button>
            </div>
          </div>
        </div>

      </form>
    </div>
  </div>
</template>

<style scoped>
/* Copied & Adapted from PickleballReservationModal */
.modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex; justify-content: center; align-items: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  width: 95%; /* Wider width */
  max-width: 1400px; /* Maximize width */
  height: auto; /* Fit content */
  max-height: 95vh; /* Prevent overflow of screen */
  overflow-y: auto; /* Allow scrolling only if absolutely necessary */
  position: relative;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.close-btn {
  position: absolute; top: 15px; right: 20px;
  background: none; border: none; font-size: 2rem; cursor: pointer; color: #666; z-index: 10;
}

h2 { margin-bottom: 1.5rem; color: #2c3e50; text-align: center; }
h3 { margin-bottom: 1rem; color: #2c3e50; font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }

.modal-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.left-column, .right-column { display: flex; flex-direction: column; gap: 1rem; }

.right-column {
  background: #fdfdfd;
  padding: 1.5rem;
  border-radius: 8px;
  border: 1px solid #eee;
}

.form-group { display: flex; flex-direction: column; gap: 0.5rem; }
label { font-weight: 600; font-size: 0.9rem; margin-bottom: 2px; }
input, select, textarea {
  padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; width: 100%;
}
input:focus, select:focus, textarea:focus { outline: none; border-color: #D59F4A; }

.menu-selection {
  padding: 15px;
  background-color: #fff3e0;
  border: 1px solid #ffe0b2;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.checkbox-group { flex-direction: row; align-items: center; gap: 0.5rem; }
.checkbox-group input { width: auto; }

/* Buttons */
.action-btn {
  width: 100%; padding: 1rem;
  border: none; border-radius: 6px; font-size: 1.1rem; font-weight: bold;
  cursor: pointer; margin-top: 1rem;
  transition: all 0.2s;
}

.continue-btn { background: #2c3e50; color: white; }
.continue-btn:hover { background: #34495e; }

.confirm-btn { background: #D59F4A; color: white; }
.confirm-btn:hover { background: #b58334; }

.back-btn { background: #e0e0e0; color: #333; margin-top: 0; }
.back-btn:hover { background: #d0d0d0; }

.step-actions {
  display: flex; gap: 1rem; margin-top: 1.5rem;
}

.step-actions button {
  flex: 1;
}

.fade-in {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
  .modal-grid { grid-template-columns: 1fr; }
}
</style>
