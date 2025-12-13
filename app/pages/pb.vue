<script setup>
import { ref, onMounted } from 'vue';
import BookingConfirmationModal from '~/components/BookingConfirmationModal.vue';
import PickleballReservationModal from '~/components/PickleballReservationModal.vue';

const courts = ref([]);
const bookings = ref([]);
const showReservationModal = ref(false);
const showConfirmation = ref(false);
const initialCourtId = ref('');

const formatPrice = (price) => {
  return '₱' + price.toLocaleString();
};

const fetchData = async () => {
  const { data: courtsData } = await useFetch('/api/courts');
  if (courtsData.value) {
    courts.value = courtsData.value;
  }

  const { data: bookingsData } = await useFetch('/api/bookings');
  if (bookingsData.value) {
    bookings.value = bookingsData.value;
  }
};

onMounted(() => {
  fetchData();
});

const selectCourt = (court) => {
  initialCourtId.value = court.id;
  showReservationModal.value = true;
};

const handleBookingSubmit = async (bookingData) => {
  try {
    const { data, error } = await useFetch('/api/bookings', {
      method: 'POST',
      body: bookingData
    });

    if (error.value) {
      alert(error.value.statusMessage || 'Booking failed');
    } else {
      console.log('Booking Submitted:', data.value);
      showReservationModal.value = false;
      showConfirmation.value = true;
      // Refresh bookings to show the new one as taken
      fetchData();
    }
  } catch (e) {
    alert('An unexpected error occurred');
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

        <div class="courts-grid">
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
  max-width: 1400px; /* Increased max-width to fit 4 cards better */
  margin: 0 auto;
  padding: 0 2rem;
}

.section-title { text-align: center; margin-bottom: 3rem; }

.courts-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* Force 4 columns */
  gap: 1.5rem;
  margin-bottom: 4rem;
}

.court-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.05);
  transition: transform 0.3s;
  border: 2px solid transparent;
  display: flex;
  flex-direction: column;
}

.court-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

/* Image Placeholders */
.court-img {
  height: 200px; /* Slightly reduced height */
  background-color: #ddd;
  position: relative;
  background-size: cover;
  background-position: center;
}
.img-court1 { background-color: #a8dadc; }
.img-court2 { background-color: #457b9d; }
.img-court3 { background-color: #1d3557; }
.img-court4 { background-color: #e63946; }

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

.court-details { padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column; }
.court-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.5rem; }
.court-header h3 { font-size: 1.2rem; color: #2c3e50; }
.meta { font-size: 0.8rem; color: #888; }
.desc { font-size: 0.9rem; color: #555; margin-bottom: 1rem; line-height: 1.5; }

.features { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.5rem; }
.feature-badge { background: #f0f0f0; font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; color: #555; }

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

/* Mobile */
@media (max-width: 1024px) {
  .courts-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .courts-grid { grid-template-columns: 1fr; }
  .pickleball-page { padding-top: 100px; }
}
</style>
