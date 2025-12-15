<script setup>
import { ref, computed, onMounted } from 'vue';
import BookingConfirmationModal from '~/components/BookingConfirmationModal.vue';
import PaymentMethod from '~/components/PaymentMethod.vue';

const api = useApi();
const { isLoggedIn, openLoginModal } = useAuth();

// --- Room Data ---
const rooms = ref([]);
const loading = ref(true);

// --- Booking State ---
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

const showConfirmation = ref(false);
const submitting = ref(false);

// --- Fetch rooms from API ---
const fetchRooms = async () => {
  loading.value = true;
  const { data } = await api.get('/rooms');
  if (data) {
    rooms.value = data;
    if (data.length > 0) {
      booking.value.selectedRoom = data[0];
    }
  }
  loading.value = false;
};

onMounted(() => {
  fetchRooms();
});

// --- Computed Properties ---
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

const selectRoom = (room) => {
  booking.value.selectedRoom = room;
  const el = document.getElementById('booking-engine');
  if (el) el.scrollIntoView({ behavior: 'smooth' });
};

const validatePhone = (event) => {
  const input = event.target;
  input.value = input.value.replace(/\D/g, '').slice(0, 11);
  booking.value.phone = input.value;
};

const submitBooking = async () => {
  if (!isLoggedIn.value) {
    openLoginModal();
    return;
  }
  
  submitting.value = true;
  
  const { data, error } = await api.post('/room-bookings', {
    room_id: booking.value.selectedRoom.id,
    check_in: booking.value.checkIn,
    check_out: booking.value.checkOut,
    guests: booking.value.guests,
    guest_name: booking.value.guestName,
    email: booking.value.email,
    phone: booking.value.phone,
    payment_method: booking.value.paymentMethod,
    reference_number: booking.value.referenceNumber,
    special_requests: booking.value.specialRequests
  });
  
  submitting.value = false;
  
  if (error) {
    alert(error.message || 'Booking failed');
  } else {
    showConfirmation.value = true;
    // Reset form
    booking.value.checkIn = '';
    booking.value.checkOut = '';
    booking.value.guestName = '';
    booking.value.email = '';
    booking.value.phone = '';
    booking.value.referenceNumber = '';
    booking.value.specialRequests = '';
  }
};
</script>

<template>
  <div class="hotel-page">
    
     <!-- HERO SECTION -->
    <section class="hotel-hero">
      <div class="hero-content">
        <h1>Hotel Accommodations</h1>
        <p>Experience comfort and luxury at Waterland Resort.</p>
      </div>
    </section>

    <LoadingSpinner v-if="loading" text="Loading rooms..." />

    <template v-else>
      <section class="room-list">
        <div class="container">
          <div class="section-title">
            <h2>Our Accommodations</h2>
            <p>Choose the perfect space for your relaxation.</p>
          </div>

          <div class="rooms-grid">
            <div 
              v-for="room in rooms" 
              :key="room.id" 
              class="room-card" 
              :class="{ active: booking.selectedRoom?.id === room.id }"
            >
              <div class="room-img" :class="room.image_class">
                <span class="price-tag">{{ formatPrice(room.price) }} <small>/ night</small></span>
              </div>
              
              <div class="room-details">
                <div class="room-header">
                  <h3>{{ room.name }}</h3>
                  <span class="meta">{{ room.size }} • Max {{ room.capacity }} Pax</span>
                </div>
                <p class="desc">{{ room.description }}</p>
                
                <div class="amenities">
                  <span v-for="(item, index) in room.amenities" :key="index" class="amenity-badge">✓ {{ item }}</span>
                </div>

                <button @click="selectRoom(room)" class="select-btn">
                  {{ booking.selectedRoom?.id === room.id ? 'Selected' : 'Select Room' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="booking-engine" class="booking-section">
        <div class="container booking-container">
          
          <div class="booking-form-wrapper">
            <h2>Complete Your Reservation</h2>
            <form @submit.prevent="submitBooking">
              
              <div class="form-group-row">
                <div class="form-group">
                  <label>Check-in Date</label>
                  <input type="date" v-model="booking.checkIn" :min="new Date().toISOString().split('T')[0]" required>
                </div>
                <div class="form-group">
                  <label>Check-out Date</label>
                  <input type="date" v-model="booking.checkOut" :min="booking.checkIn || new Date().toISOString().split('T')[0]" required>
                </div>
              </div>

              <div class="form-group">
                <label>Full Name</label>
                <input type="text" v-model="booking.guestName" placeholder="Juan Dela Cruz" required>
              </div>

              <div class="form-group-row">
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
              </div>

              <div class="form-group">
                <label>Number of Guests</label>
                <select v-model="booking.guests">
                  <option v-for="n in (booking.selectedRoom?.capacity || 4)" :key="n" :value="n">{{ n }} Guest(s)</option>
                </select>
              </div>

              <div class="form-group">
                <label>Special Requests (Optional)</label>
                <textarea v-model="booking.specialRequests" placeholder="Any special requests..."></textarea>
              </div>

              <PaymentMethod 
                v-model="booking.paymentMethod" 
                v-model:referenceNumber="booking.referenceNumber" 
              />

              <button type="submit" class="confirm-btn" :disabled="submitting">
                {{ submitting ? 'Processing...' : 'Confirm Booking' }}
              </button>
            </form>
          </div>

          <div class="booking-summary">
            <div class="summary-card">
              <h3>Booking Summary</h3>
              <div class="summary-row room-highlight">
                <span>Room Type</span>
                <strong>{{ booking.selectedRoom?.name || 'Select a room' }}</strong>
              </div>
              
              <div class="summary-divider"></div>

              <div class="summary-row">
                <span>Price per night</span>
                <span>{{ booking.selectedRoom ? formatPrice(booking.selectedRoom.price) : '₱0' }}</span>
              </div>
              <div class="summary-row">
                <span>Duration</span>
                <span>{{ nightCount === 0 ? '1' : nightCount }} Night(s)</span>
              </div>
              <div class="summary-row" v-if="booking.checkIn">
                <span>Check-in</span>
                <span>{{ booking.checkIn }}</span>
              </div>

              <div class="summary-divider"></div>

              <div class="summary-total">
                <span>Total Amount</span>
                <span class="total-price">{{ formatPrice(totalPrice) }}</span>
              </div>
              
              <p class="taxes">Includes taxes & fees</p>
            </div>
          </div>
        </div>
      </section>
    </template>
  </div>
  <BookingConfirmationModal 
    :isVisible="showConfirmation" 
    title="Booking Confirmed" 
    message="Your room is reserved. Wait for email confirmation." 
    @close="showConfirmation = false" 
  />
</template>

<style scoped>
/* --- Hero --- */
.hotel-hero {
  background-color: #2c3e50;
  background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('/room.jpg'); 
  background-size: cover;
  background-position: center;
  height: 40vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: white;
  margin-bottom: 3rem;
}

.hero-content h1 { font-size: 3.5rem; margin-bottom: 0.5rem; }



/* --- Room List --- */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.section-title { text-align: center; margin-bottom: 3rem; }

.rooms-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2.5rem;
  margin-bottom: 5rem;
}

.room-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 
    0 10px 25px rgba(0,0,0,0.08),
    0 4px 10px rgba(0,0,0,0.04);
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  border: 1px solid rgba(255,255,255,0.6);
  display: flex;
  flex-direction: column;
}

.room-card.active {
  border-color: #D59F4A;
  transform: translateY(-8px);
  box-shadow: 
    0 20px 40px rgba(213, 159, 74, 0.2),
    0 8px 16px rgba(213, 159, 74, 0.1);
}

/* Image Placeholders */
.room-img {
  height: 250px;
  background-color: #ddd;
  position: relative;
  background-size: cover;
  background-position: center;
}
.img-standard { background-color: #a8dadc; }
.img-deluxe { background-color: #457b9d; }
.img-suite { background-color: #1d3557; }

.price-tag {
  position: absolute;
  bottom: 15px;
  right: 15px;
  background: white;
  padding: 6px 14px;
  border-radius: 24px;
  font-weight: bold;
  color: #2c3e50;
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
  font-size: 0.95rem;
}

.room-details { padding: 2rem; flex-grow: 1; display: flex; flex-direction: column; }
.room-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem; }
.room-header h3 { font-size: 1.5rem; color: #2c3e50; font-weight: 600; }
.meta { font-size: 0.85rem; color: #888; }
.desc { font-size: 0.95rem; color: #555; margin-bottom: 1.25rem; line-height: 1.6; }

.amenities { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.5rem; }
.amenity-badge { background: #f5f5f5; font-size: 0.8rem; padding: 5px 10px; border-radius: 6px; color: #555; transition: background 0.2s; }
.amenity-badge:hover { background: #e8e8e8; }

.select-btn {
  margin-top: auto;
  width: 100%;
  padding: 0.9rem;
  background: #2c3e50;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-weight: 600;
  box-shadow: 0 4px 6px rgba(44, 62, 80, 0.2);
  border-bottom: 3px solid rgba(0,0,0,0.2);
}
.select-btn:hover { 
  background: #D59F4A; 
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(213, 159, 74, 0.3);
  border-bottom: 3px solid rgba(0,0,0,0.2);
}
.select-btn:active {
  transform: translateY(1px);
  box-shadow: 0 2px 4px rgba(213, 159, 74, 0.2);
  border-bottom: 1px solid rgba(0,0,0,0.1);
}

/* --- Booking Engine (Split Layout) --- */
.booking-section {
  background: white;
  border-top: 1px solid #eee;
  padding: 4rem 0;
}

.booking-container {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 3rem;
}

/* Form Styles */
.booking-form-wrapper h2 { margin-bottom: 2rem; color: #2c3e50; }

.form-group-row { display: flex; gap: 1.5rem; }
.form-group { display: flex; flex-direction: column; margin-bottom: 1.2rem; flex: 1; }
label { font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem; }
input, select, textarea {
  padding: 0.9rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);
}
input:focus, select:focus, textarea:focus { 
  outline: none; 
  border-color: #D59F4A; 
  box-shadow: 
    0 0 0 4px rgba(213, 159, 74, 0.15),
    inset 0 1px 2px rgba(0,0,0,0.03); 
  transform: translateY(-1px);
}

textarea {
  min-height: 80px;
  resize: vertical;
}

.confirm-btn {
  background: #D59F4A; color: white; width: 100%; padding: 1.1rem;
  border: none; border-radius: 10px; font-size: 1.1rem; font-weight: bold;
  cursor: pointer; margin-top: 1rem; transition: all 0.2s ease;
  box-shadow: 0 4px 10px rgba(213, 159, 74, 0.3);
  border-bottom: 3px solid rgba(0,0,0,0.15);
}
.confirm-btn:hover:not(:disabled) { 
  background: #c7923e; 
  transform: translateY(-2px); 
  box-shadow: 0 8px 16px rgba(213, 159, 74, 0.4);
  border-bottom: 3px solid rgba(0,0,0,0.15); 
}
.confirm-btn:active:not(:disabled) {
  transform: translateY(1px);
  box-shadow: 0 2px 4px rgba(213, 159, 74, 0.2);
  border-bottom: 1px solid rgba(0,0,0,0.1);
}
.confirm-btn:disabled { background: #ccc; cursor: not-allowed; border: none; }

/* Summary Card Styles (Sticky) */
.summary-card {
  background: #fdfdfd;
  padding: 2.5rem;
  border-radius: 20px;
  border: 1px solid rgba(255,255,255,0.8);
  box-shadow: 
    0 15px 30px rgba(0,0,0,0.08),
    0 5px 15px rgba(0,0,0,0.04);
  position: sticky;
  top: 140px;
}

.summary-card h3 { margin-bottom: 1.5rem; border-bottom: 2px solid #D59F4A; display: inline-block; padding-bottom: 5px; }
.summary-row { display: flex; justify-content: space-between; margin-bottom: 0.8rem; font-size: 0.95rem; }
.summary-divider { height: 1px; background: #eee; margin: 1rem 0; }
.summary-total { display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold; color: #2c3e50; }
.total-price { color: #D59F4A; }
.taxes { font-size: 0.8rem; color: #999; text-align: right; margin-top: 0.5rem; }

/* Mobile */
@media (max-width: 768px) {
  .booking-container { grid-template-columns: 1fr; }
  .hotel-page { padding-top: 100px; }
  .form-group-row { flex-direction: column; gap: 0; }
}
</style>