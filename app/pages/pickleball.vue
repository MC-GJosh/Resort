<script setup>
import { ref, onMounted } from 'vue';
import BookingConfirmationModal from '~/components/BookingConfirmationModal.vue';
import PickleballReservationModal from '~/components/PickleballReservationModal.vue';

const api = useApi();
const { isLoggedIn, openLoginModal } = useAuth();

const courts = ref([]);
const bookings = ref([]);
const showReservationModal = ref(false);
const showConfirmation = ref(false);
const initialCourtId = ref('');
const loading = ref(true);

const formatPrice = (price) => {
  return '₱' + parseFloat(price).toLocaleString();
};

const fetchData = async () => {
  loading.value = true;
  
  // Fetch courts from Laravel API
  const { data: courtsData } = await api.get('/courts');
  if (courtsData) {
    courts.value = courtsData;
  }

  // Fetch all bookings for availability display
  const { data: bookingsData } = await api.get('/pickleball-bookings/all');
  if (bookingsData) {
    bookings.value = bookingsData;
  }
  
  loading.value = false;
};

onMounted(() => {
  fetchData();
});

const selectCourt = (court) => {
  if (!isLoggedIn.value) {
    openLoginModal();
    return;
  }
  initialCourtId.value = court.id.toString();
  showReservationModal.value = true;
};

const handleBookingSubmit = async (bookingData) => {
  const { data, error } = await api.post('/pickleball-bookings', {
    court_id: parseInt(bookingData.selectedCourtId),
    date: bookingData.date,
    time_slots: bookingData.selectedTimeSlots,
    customer_name: bookingData.playerName,
    phone: bookingData.phone,
    payment_method: bookingData.paymentMethod,
    reference_number: bookingData.referenceNumber
  });

  if (error) {
    alert(error.message || 'Booking failed');
  } else {
    console.log('Booking Submitted:', data);
    showReservationModal.value = false;
    showConfirmation.value = true;
    // Refresh bookings to show the new one as taken
    fetchData();
  }
};
</script>

<template>
  <div class="pickleball-page">
    
     <!-- HERO SECTION -->
    <section class="pb-hero">
      <div class="hero-content">
        <h1>Pickleball Courts</h1>
        <p>Book your court and enjoy the game!</p>
      </div>
    </section>

    <section class="court-list">
      <div class="container">
        <div class="section-title">
          <h2>Available Courts</h2>
          <p>Choose your preferred court for an amazing game.</p>
        </div>

        <LoadingSpinner v-if="loading" />

        <div v-else class="courts-grid">
          <div 
            v-for="court in courts" 
            :key="court.id" 
            class="court-card" 
          >
            <div class="court-img" :style="{ backgroundImage: `url(${court.image})` }">
              <span class="price-tag">{{ formatPrice(court.rate) }} <small>/ hour</small></span>
            </div>
            
            <div class="court-details">
              <div class="court-header">
                <h3>{{ court.name }}</h3>
                <span class="meta">{{ court.location }} • {{ court.surface }}</span>
              </div>
              <p class="desc">{{ court.description }}</p>
              
              <div class="features">
                <span v-for="(item, index) in court.features" :key="index" class="feature-badge">✓ {{ item }}</span>
              </div>

              <button @click="selectCourt(court)" class="select-btn">
                Select Court
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>

  <PickleballReservationModal 
    :isVisible="showReservationModal" 
    :courts="courts"
    :bookings="bookings"
    :initialCourtId="initialCourtId"
    @close="showReservationModal = false"
    @submit="handleBookingSubmit"
  />

  <BookingConfirmationModal 
    :isVisible="showConfirmation" 
    title="Booking Confirmed" 
    message="Your court is reserved. Wait for email confirmation." 
    @close="showConfirmation = false" 
  />
</template>

<style scoped>
/* --- Hero --- */
.pb-hero {
  background-color: #2c3e50;
  background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('/pickballcourt.jpg'); 
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

/* --- Court List --- */
.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.section-title { text-align: center; margin-bottom: 3rem; }



.courts-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  margin-bottom: 5rem;
}

.court-card {
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
  position: relative;
}

.court-card:hover {
  transform: translateY(-8px);
  box-shadow: 
    0 20px 40px rgba(0,0,0,0.12),
    0 8px 16px rgba(0,0,0,0.08);
  border-color: rgba(213, 159, 74, 0.2);
}

/* Image Placeholders */
.court-img {
  height: 200px;
  background-color: #ddd;
  position: relative;
  background-size: cover;
  background-position: center;
}

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

.court-details { padding: 2rem; flex-grow: 1; display: flex; flex-direction: column; }
.court-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem; }
.court-header h3 { font-size: 1.3rem; color: #2c3e50; font-weight: 600; }
.meta { font-size: 0.8rem; color: #888; }
.desc { font-size: 0.95rem; color: #555; margin-bottom: 1.25rem; line-height: 1.6; }

.features { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.5rem; }
.feature-badge { background: #f5f5f5; font-size: 0.75rem; padding: 5px 10px; border-radius: 6px; color: #555; transition: background 0.2s; }
.feature-badge:hover { background: #e8e8e8; }

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

/* Mobile */
@media (max-width: 1024px) {
  .courts-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .courts-grid { grid-template-columns: 1fr; }
  .pickleball-page { padding-top: 100px; }
}
</style>
