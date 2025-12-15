<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue';
import PaymentMethod from './PaymentMethod.vue';

const props = defineProps({
  isVisible: Boolean,
  rooms: {
    type: Array,
    required: true
  },
  initialRoom: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close', 'submit']);

const booking = ref({
  checkIn: '',
  checkOut: '',
  guests: 2,
  selectedRoom: null,
  guestName: '',
  email: '',
  phone: '',
  paymentMethod: 'GCash',
  referenceNumber: '',
  specialRequests: ''
});

// Watch for initialRoom changes
watch(() => props.initialRoom, (newVal) => {
  if (newVal) {
    booking.value.selectedRoom = newVal;
  }
}, { immediate: true });

// Computed Properties for Pricing
const nightCount = computed(() => {
  if (!booking.value.checkIn || !booking.value.checkOut) return 0;
  const start = new Date(booking.value.checkIn);
  const end = new Date(booking.value.checkOut);
  const timeDiff = end.getTime() - start.getTime();
  const days = Math.ceil(timeDiff / (1000 * 3600 * 24));
  return days > 0 ? days : 0;
});

const totalPrice = computed(() => {
  if (!booking.value.selectedRoom) return 0;
  const price = parseFloat(booking.value.selectedRoom.price);
  return price * (nightCount.value === 0 ? 1 : nightCount.value);
});

const formatPrice = (price) => {
  return '₱' + parseFloat(price).toLocaleString();
};

const validatePhone = (event) => {
  const input = event.target;
  input.value = input.value.replace(/\D/g, '').slice(0, 11);
  booking.value.phone = input.value;
};

const step = ref(1);

const goToPayment = () => {
  if (!booking.value.checkIn || !booking.value.checkOut || !booking.value.guestName || !booking.value.phone) {
    alert('Please fill in all required fields (Dates, Name, Phone) before proceeding.');
    return;
  }
  step.value = 2;
};

const closeModal = () => {
  step.value = 1; // Reset
  emit('close');
};

const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.isVisible) closeModal();
};

onMounted(() => window.addEventListener('keydown', handleKeydown));
onUnmounted(() => window.removeEventListener('keydown', handleKeydown));


const submitBooking = () => {
  emit('submit', booking.value);
};
</script>

<template>
  <div v-if="isVisible" class="modal-overlay">
    <div class="modal-content">
      <button class="close-btn" @click="closeModal">&times;</button>
      <h2>Reserve Room</h2>
      
      <form @submit.prevent="submitBooking" class="modal-grid">
        
        <!-- LEFT COLUMN: Room & Stay Details -->
        <div class="left-column">
          <h3>Stay Details</h3>
          
          <div class="form-group">
            <label>Select Room</label>
            <select v-model="booking.selectedRoom">
              <option v-for="room in rooms" :key="room.id" :value="room">
                {{ room.name }} ({{ formatPrice(room.price) }}/night)
              </option>
            </select>
          </div>

          <div class="form-group-row">
            <div class="form-group">
              <label>Check-in</label>
              <input type="date" v-model="booking.checkIn" :min="new Date().toISOString().split('T')[0]" required>
            </div>
            <div class="form-group">
              <label>Check-out</label>
              <input type="date" v-model="booking.checkOut" :min="booking.checkIn || new Date().toISOString().split('T')[0]" required>
            </div>
          </div>

          <div class="form-group">
            <label>Number of Guests</label>
            <select v-model="booking.guests">
              <option v-for="n in (booking.selectedRoom?.capacity || 4)" :key="n" :value="n">{{ n }} Guest(s)</option>
            </select>
          </div>

          <h3>Guest Information</h3>

          <div class="form-group">
            <label>Full Name</label>
            <input type="text" v-model="booking.guestName" placeholder="Juan Dela Cruz" required>
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" v-model="booking.email" placeholder="email@example.com" required>
          </div>

          <div class="form-group">
            <label>Phone</label>
            <input 
              type="tel" 
              v-model="booking.phone" 
              placeholder="0917-XXX-XXXX" 
              maxlength="11"
              @input="validatePhone"
              required
            >
          </div>
          
          <div class="form-group">
            <label>Special Requests</label>
            <textarea v-model="booking.specialRequests" rows="2" placeholder="Any special requests..."></textarea>
          </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="right-column">
          <div class="summary-section">
            <h4>Booking Summary</h4>
            <div class="summary-row">
              <span>Room:</span>
              <span>{{ booking.selectedRoom?.name }}</span>
            </div>
            <div class="summary-row">
               <span>Duration:</span>
               <span>{{ nightCount }} Night(s)</span>
            </div>
             <div class="summary-row" v-if="booking.checkIn">
                <span>Check-in:</span>
                <span>{{ booking.checkIn }}</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-total">
               <span>Total Price:</span>
               <span class="total-price">{{ formatPrice(totalPrice) }}</span>
            </div>
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
              v-model="booking.paymentMethod" 
              v-model:referenceNumber="booking.referenceNumber"
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
/* Common Modal Styles */
/* ... (Keep existing styles) ... */
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
  width: 95%; /* Wider */
  max-width: 1200px; /* Wider content area */
  height: auto;
  max-height: 95vh;
  overflow-y: auto;
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

.form-group-row { display: flex; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.5rem; width: 100%; }
label { font-weight: 600; font-size: 0.9rem; margin-bottom: 2px; }
input, select, textarea {
  padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; width: 100%;
}
input:focus, select:focus, textarea:focus { outline: none; border-color: #D59F4A; }

.summary-section { margin-top: 1rem; background: #fff; padding: 1rem; border-radius: 6px; border: 1px solid #eee; }
.summary-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; }
.summary-divider { height: 1px; background: #eee; margin: 0.5rem 0; }
.summary-total { display: flex; justify-content: space-between; font-weight: bold; font-size: 1.1rem; color: #2c3e50; }
.total-price { color: #D59F4A; }

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

.payment-section { margin-top: 1rem; }

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
