<script setup>
import { ref, computed, watch } from 'vue';
import PaymentMethod from './PaymentMethod.vue';

const props = defineProps({
  isVisible: Boolean,
  courts: {
    type: Array,
    required: true
  },
  bookings: {
    type: Array,
    default: () => []
  },
  initialCourtId: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['close', 'submit']);

const booking = ref({
  date: '',
  selectedTimeSlots: [], // Multi-select
  selectedCourtId: props.initialCourtId || (props.courts[0] ? props.courts[0].id : ''),
  playerName: '',
  phone: '',
  paymentMethod: 'GCash',
  referenceNumber: ''
});

// Watch for initialCourtId changes to update the local state when the modal opens
watch(() => props.initialCourtId, (newVal) => {
  if (newVal) {
    booking.value.selectedCourtId = newVal;
  }
});

// Reset time slots when date changes
watch(() => booking.value.date, () => {
  booking.value.selectedTimeSlots = [];
});

const timeSlots = [
  '6:00 AM - 7:00 AM', '7:00 AM - 8:00 AM', '8:00 AM - 9:00 AM',
  '9:00 AM - 10:00 AM', '10:00 AM - 11:00 AM', '11:00 AM - 12:00 PM',
  '12:00 PM - 1:00 PM', '1:00 PM - 2:00 PM', '2:00 PM - 3:00 PM',
  '3:00 PM - 4:00 PM', '4:00 PM - 5:00 PM', '5:00 PM - 6:00 PM'
];

const isSlotBooked = (slot) => {
  if (!booking.value.date || !booking.value.selectedCourtId) return false;
  const courtId = parseInt(booking.value.selectedCourtId);
  const selectedDate = booking.value.date;
  
  return props.bookings.some(b => {
    // Handle Laravel API format (court_id, time_slot, date as ISO string)
    const bCourtId = b.court_id || b.courtId;
    const bTimeSlot = b.time_slot || b.time;
    const bDate = b.date ? b.date.split('T')[0] : ''; // Handle ISO date format
    
    return bCourtId === courtId && 
           bDate === selectedDate && 
           bTimeSlot === slot;
  });
};

const selectedCourt = computed(() => {
  const courtId = parseInt(booking.value.selectedCourtId);
  return props.courts.find(c => c.id === courtId) || props.courts[0];
});

const totalCost = computed(() => {
  if (!selectedCourt.value) return 0;
  return parseFloat(selectedCourt.value.rate) * booking.value.selectedTimeSlots.length;
});

const formatPrice = (price) => {
  return '₱' + price.toLocaleString();
};

const validatePhone = (event) => {
  const input = event.target;
  input.value = input.value.replace(/\D/g, '').slice(0, 11);
  booking.value.phone = input.value;
};

const closeModal = () => {
  emit('close');
};

const submitBooking = () => {
  emit('submit', booking.value);
};
</script>

<template>
  <div v-if="isVisible" class="modal-overlay" @click.self="closeModal">
    <div class="modal-content">
      <button class="close-btn" @click="closeModal">&times;</button>
      <h2>Reserve Court</h2>
      
      <form @submit.prevent="submitBooking" class="modal-grid">
        
        <!-- LEFT COLUMN: Booking Details -->
        <div class="left-column">
          <div class="form-group">
            <label>Select Court</label>
            <select v-model="booking.selectedCourtId">
              <option v-for="court in courts" :key="court.id" :value="court.id">
                {{ court.name }} ({{ formatPrice(court.rate) }}/hr)
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Date</label>
            <input type="date" v-model="booking.date" :min="new Date().toISOString().split('T')[0]" required>
          </div>

          <div class="form-group">
            <label>Time Slots (Select Multiple)</label>
            <div class="time-slot-grid" :class="{ disabled: !booking.date }">
              <label 
                v-for="slot in timeSlots" 
                :key="slot" 
                class="time-slot-checkbox" 
                :class="{ 
                  active: booking.selectedTimeSlots.includes(slot),
                  booked: isSlotBooked(slot)
                }"
              >
                <input 
                  type="checkbox" 
                  :value="slot" 
                  v-model="booking.selectedTimeSlots" 
                  style="display: none;"
                  :disabled="!booking.date || isSlotBooked(slot)"
                >
                {{ slot }}
                <span v-if="isSlotBooked(slot)" class="booked-label">(Booked)</span>
              </label>
            </div>
            <p v-if="!booking.date" class="hint-text">Please select a date first.</p>
            <p v-else-if="booking.selectedTimeSlots.length === 0" class="error-text">Please select at least one time slot.</p>
          </div>

          <div class="form-group">
            <label>Player Name</label>
            <input type="text" v-model="booking.playerName" placeholder="Juan Dela Cruz" required>
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
        </div>

        <!-- RIGHT COLUMN: Payment & Summary -->
        <div class="right-column">
          <div class="payment-section">
            <h3>Payment Details</h3>
            <!-- Reusable Payment Method Component -->
            <PaymentMethod 
              v-model="booking.paymentMethod" 
              v-model:referenceNumber="booking.referenceNumber" 
            />
          </div>

          <div class="summary-section">
            <div class="summary-row">
              <span>Rate per hour:</span>
              <span>{{ formatPrice(selectedCourt.rate) }}</span>
            </div>
            <div class="summary-row">
              <span>Hours:</span>
              <span>{{ booking.selectedTimeSlots.length }}</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-row total">
              <span>Total Cost:</span>
              <span class="total-price">{{ formatPrice(totalCost) }}</span>
            </div>
          </div>

          <button type="submit" class="confirm-btn" :disabled="booking.selectedTimeSlots.length === 0">Confirm Booking</button>
        </div>

      </form>
    </div>
  </div>
</template>

<style scoped>
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
  width: 100%;
  max-width: 900px; /* Wider for 2 columns */
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.close-btn {
  position: absolute; top: 15px; right: 20px;
  background: none; border: none; font-size: 2rem; cursor: pointer; color: #666; z-index: 10;
}

h2 { margin-bottom: 1.5rem; color: #2c3e50; text-align: center; }

/* Grid Layout */
.modal-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}

.left-column, .right-column {
  display: flex;
  flex-direction: column;
}

.right-column {
  background: #f9f9f9;
  padding: 1.5rem;
  border-radius: 8px;
  border: 1px solid #eee;
}

.form-group { display: flex; flex-direction: column; margin-bottom: 1.2rem; }
label { font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem; }
input, select {
  padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;
}
input:focus, select:focus { outline: none; border-color: #D59F4A; }

/* Time Slots */
.time-slot-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 8px;
}
.time-slot-grid.disabled { opacity: 0.5; pointer-events: none; }

.time-slot-checkbox {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 6px;
  text-align: center;
  cursor: pointer;
  font-size: 0.8rem;
  transition: all 0.2s;
}

.time-slot-checkbox:hover { background: #f9f9f9; }
.time-slot-checkbox.active {
  background: #D59F4A;
  color: white;
  border-color: #D59F4A;
}

.hint-text { color: #888; font-style: italic; font-size: 0.85rem; margin-top: 5px; }
.error-text { color: red; font-size: 0.8rem; margin-top: 5px; }

/* Right Column Styles */
.payment-section h3 { margin-bottom: 1rem; color: #2c3e50; font-size: 1.1rem; }

.summary-section {
  margin-top: auto; /* Push to bottom of right column if space permits */
  padding-top: 1rem;
  border-top: 1px solid #ddd;
}

.summary-row {
  display: flex; justify-content: space-between; align-items: center;
  font-size: 1rem; margin-bottom: 0.5rem;
}
.summary-row.total { font-size: 1.3rem; font-weight: bold; color: #2c3e50; margin-top: 0.5rem; }
.summary-divider { height: 1px; background: #ddd; margin: 0.5rem 0; }

.total-price { color: #D59F4A; }

.confirm-btn {
  background: #D59F4A; color: white; width: 100%; padding: 1rem;
  border: none; border-radius: 6px; font-size: 1.1rem; font-weight: bold;
  cursor: pointer; margin-top: 1.5rem;
}
.confirm-btn:disabled { background: #ccc; cursor: not-allowed; }
.confirm-btn:hover:not(:disabled) { background: #b58334; }

.time-slot-checkbox.booked {
  background: #eee;
  color: #aaa;
  cursor: not-allowed;
  border-color: #eee;
}

.booked-label {
  display: block;
  font-size: 0.7rem;
  color: #e74c3c;
  margin-top: 2px;
}

@media (max-width: 768px) {
  .modal-grid { grid-template-columns: 1fr; }
  .right-column { margin-top: 1rem; }
}
</style>
