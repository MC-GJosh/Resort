<script setup>
import { ref, computed } from 'vue';
import BookingConfirmationModal from '~/components/BookingConfirmationModal.vue';
import PaymentMethod from '~/components/PaymentMethod.vue';

const usePageAuth = () => {
    const isLoggedIn = useState('isLoggedIn', () => false)
    const showLoginModal = useState('showLoginModal', () => false)

    const login = () => {
        isLoggedIn.value = true
        closeLoginModal()
    }

    const logout = () => {
        isLoggedIn.value = false
    }

    const openLoginModal = () => {
        showLoginModal.value = true
    }

    const closeLoginModal = () => {
        showLoginModal.value = false
    }

    return {
        isLoggedIn,
        showLoginModal,
        login,
        logout,
        openLoginModal,
        closeLoginModal
    }
}

// --- 1. Room Database (Simulated) ---
const rooms = [
  {
    id: 'standard',
    name: 'Standard Twin',
    price: 3500,
    capacity: 2,
    size: '25 sqm',
    description: 'A cozy retreat featuring twin beds, perfect for friends or solo travelers.',
    amenities: ['Free Wi-Fi', 'Hot Shower', 'Cable TV', 'Coffee Maker'],
    imageClass: 'img-standard'
  },
  {
    id: 'deluxe',
    name: 'Deluxe King',
    price: 5500,
    capacity: 2,
    size: '35 sqm',
    description: 'Spacious room with a king-sized bed and a private balcony with garden views.',
    amenities: ['King Bed', 'Balcony', 'Work Desk', 'Mini Bar', 'Rain Shower'],
    imageClass: 'img-deluxe'
  },
  {
    id: 'suite',
    name: 'Executive Family Suite',
    price: 8500,
    capacity: 4,
    size: '60 sqm',
    description: 'Our premium suite with a separate living area, kitchenette, and bathtub.',
    amenities: ['Living Room', 'Kitchenette', 'Bathtub', '2 Queen Beds', 'VIP Wi-Fi'],
    imageClass: 'img-suite'
  }
];

// --- 2. Booking State ---
const booking = ref({
  checkIn: '',
  checkOut: '',
  guests: 2,
  selectedRoom: rooms[0], // Default to first room
  guestName: '',
  email: '',
  phone: '',
  paymentMethod: 'GCash',
  referenceNumber: '',
  specialRequests: ''
});

const showConfirmation = ref(false);

// --- 3. "Deep" Logic: Computed Properties ---

// Calculate number of nights
const nightCount = computed(() => {
  if (!booking.value.checkIn || !booking.value.checkOut) return 0;
  const start = new Date(booking.value.checkIn);
  const end = new Date(booking.value.checkOut);
  
  // Calculate difference in time
  const timeDiff = end.getTime() - start.getTime();
  // Calculate difference in days (Time / (1000 * 3600 * 24))
  const days = Math.ceil(timeDiff / (1000 * 3600 * 24));
  
  return days > 0 ? days : 0; // Prevent negative dates
});

// Calculate Total Price
const totalPrice = computed(() => {
  return booking.value.selectedRoom.price * (nightCount.value === 0 ? 1 : nightCount.value);
});

const formatPrice = (price) => {
  return '₱' + price.toLocaleString();
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

const { isLoggedIn, openLoginModal } = usePageAuth();

const submitBooking = () => {
  if (!isLoggedIn.value) {
    openLoginModal();
    return;
  }
  showConfirmation.value = true;
};
</script>

<template>
  <div class="hotel-page">
    
     <!-- HERO SECTION -->
    <section class="hotel-hero">
      <div class="hero-content">
        <h1>Events at Waterland</h1>
        <p>From intimate gatherings to grand celebrations, we have the perfect space for you.</p>
      </div>
    </section>

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
            :class="{ active: booking.selectedRoom.id === room.id }"
          >
            <div class="room-img" :class="room.imageClass">
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
                {{ booking.selectedRoom.id === room.id ? 'Selected' : 'Select Room' }}
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
                <input type="date" v-model="booking.checkIn" required>
              </div>
              <div class="form-group">
                <label>Check-out Date</label>
                <input type="date" v-model="booking.checkOut" required>
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
                <option v-for="n in booking.selectedRoom.capacity" :key="n" :value="n">{{ n }} Guest(s)</option>
              </select>
            </div>

            <PaymentMethod 
              v-model="booking.paymentMethod" 
              v-model:referenceNumber="booking.referenceNumber" 
            />

            <button type="submit" class="confirm-btn">Confirm Booking</button>
          </form>
        </div>

        <div class="booking-summary">
          <div class="summary-card">
            <h3>Booking Summary</h3>
            <div class="summary-row room-highlight">
              <span>Room Type</span>
              <strong>{{ booking.selectedRoom.name }}</strong>
            </div>
            
            <div class="summary-divider"></div>

            <div class="summary-row">
              <span>Price per night</span>
              <span>{{ formatPrice(booking.selectedRoom.price) }}</span>
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
  /* Use your actual image here */
  background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('/landingpage.jpg'); 
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
  gap: 2rem;
  margin-bottom: 4rem;
}

.room-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.05);
  transition: transform 0.3s;
  border: 2px solid transparent;
  display: flex;
  flex-direction: column;
}

.room-card.active {
  border-color: #D59F4A;
  transform: scale(1.02);
  box-shadow: 0 10px 25px rgba(213, 159, 74, 0.2);
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
  padding: 5px 12px;
  border-radius: 20px;
  font-weight: bold;
  color: #2c3e50;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.room-details { padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column; }
.room-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem; }
.room-header h3 { font-size: 1.4rem; color: #2c3e50; }
.meta { font-size: 0.85rem; color: #888; }
.desc { font-size: 0.95rem; color: #555; margin-bottom: 1rem; line-height: 1.5; }

.amenities { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.5rem; }
.amenity-badge { background: #f0f0f0; font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; color: #555; }

.select-btn {
  margin-top: auto;
  width: 100%;
  padding: 0.8rem;
  background: #2c3e50;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s;
}
.select-btn:hover { background: #D59F4A; }

/* --- Booking Engine (Split Layout) --- */
.booking-section {
  background: white;
  border-top: 1px solid #eee;
  padding: 4rem 0;
}

.booking-container {
  display: grid;
  grid-template-columns: 2fr 1fr; /* Form takes 2/3, Summary takes 1/3 */
  gap: 3rem;
}

/* Form Styles */
.booking-form-wrapper h2 { margin-bottom: 2rem; color: #2c3e50; }

.form-group-row { display: flex; gap: 1.5rem; }
.form-group { display: flex; flex-direction: column; margin-bottom: 1.2rem; flex: 1; }
label { font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem; }
input, select, textarea {
  padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;
}
input:focus, select:focus, textarea:focus { outline: none; border-color: #D59F4A; }

.confirm-btn {
  background: #D59F4A; color: white; width: 100%; padding: 1rem;
  border: none; border-radius: 6px; font-size: 1.1rem; font-weight: bold;
  cursor: pointer; margin-top: 1rem;
}
.confirm-btn:hover { background: #b58334; }

.payment-info { margin-top: 10px; padding: 12px; background: #f1f8e9; border: 1px solid #c5e1a5; border-radius: 6px; color: #33691e; }

/* Summary Card Styles (Sticky) */
.summary-card {
  background: #fdfdfd;
  padding: 2rem;
  border-radius: 12px;
  border: 1px solid #eee;
  box-shadow: 0 5px 20px rgba(0,0,0,0.05);
  position: sticky;
  top: 140px; /* Sticks below the header */
}

.summary-card h3 { margin-bottom: 1.5rem; border-bottom: 2px solid #D59F4A; display: inline-block; padding-bottom: 5px; }
.summary-row { display: flex; justify-content: space-between; margin-bottom: 0.8rem; font-size: 0.95rem; }
.summary-divider { height: 1px; background: #eee; margin: 1rem 0; }
.summary-total { display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold; color: #2c3e50; }
.total-price { color: #D59F4A; }
.taxes { font-size: 0.8rem; color: #999; text-align: right; margin-top: 0.5rem; }

/* Mobile */
@media (max-width: 768px) {
  .booking-container { grid-template-columns: 1fr; } /* Stack on mobile */
  .hotel-page { padding-top: 100px; }
}
</style>