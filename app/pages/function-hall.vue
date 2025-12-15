<script setup>
import { ref, onMounted } from 'vue';
import BookingConfirmationModal from '~/components/BookingConfirmationModal.vue';
import FunctionHallReservationModal from '~/components/FunctionHallReservationModal.vue';

const api = useApi();
const { isLoggedIn, openLoginModal } = useAuth();

// --- Hall Data ---
const halls = ref([]);
const loading = ref(true);
const showReservationModal = ref(false);
const showConfirmation = ref(false);
const selectedHall = ref(null);

// --- Fetch halls from API ---
const fetchHalls = async () => {
  loading.value = true;
  
  // Add artificial delay for "loading" feel (2.5 seconds)
  const delay = new Promise(resolve => setTimeout(resolve, 2500));
  
  const { data } = await api.get('/function-halls');
  
  // Wait for delay
  await delay;
  
  if (data) {
    halls.value = data;
  }
  loading.value = false;
};

onMounted(() => {
  fetchHalls();
});

// --- Hall Selection Logic ---
const selectHall = (hall) => {
  if (!isLoggedIn.value) { 
    openLoginModal(); 
    return; 
  }
  selectedHall.value = hall;
  showReservationModal.value = true;
};

const handleBookingSubmit = async (bookingData) => {
  
  const { data, error } = await api.post('/hall-bookings', {
    function_hall_id: bookingData.selectedHall.id,
    full_name: bookingData.fullName,
    phone: bookingData.phoneNumber,
    email: bookingData.email,
    address: bookingData.address,
    event_date: bookingData.eventDate,
    guest_count: bookingData.guestCount,
    catering_package: bookingData.cateringPackage,
    main_dish: bookingData.selectedMainDish,
    appetizer: bookingData.selectedAppetizer,
    drink: bookingData.selectedDrink,
    avail_mini_bar: bookingData.availMiniBar,
    payment_method: bookingData.paymentMethod,
    reference_number: bookingData.referenceNumber,
    notes: bookingData.notes
  });
  
  if (error) {
    alert(error.message || 'Booking failed');
  } else {
    showReservationModal.value = false;
    showConfirmation.value = true;
  }
};

const formatPrice = (price) => {
  return '₱' + parseFloat(price).toLocaleString();
};
const getHallImage = (hall) => {
  const name = hall.name.toLowerCase();
  if (name.includes('grand')) {
    return '/grandballroom.jpg';
  } else {
    return '/functionhall.jpg';
  }
};
</script>

<template>
  <div class="function-hall-page">

    <!-- HERO SECTION -->
    <section class="hall-hero">
      <div class="hero-content">
        <h1>Events at Waterland</h1>
        <p>From intimate gatherings to grand celebrations, we have the perfect space for you.</p>
      </div>
    </section>

    <LoadingSpinner v-if="loading" text="Loading function halls..." />

    <template v-else>
      <!-- HALL CHOICES -->
      <section class="hall-choices">
        <div class="container">

          <div 
            v-for="hall in halls" 
            :key="hall.id"
            class="hall-card" 
          >
            <div 
              class="card-image" 
              :style="{ backgroundImage: `url(${getHallImage(hall)})` }"
            >
              <span class="badge" :class="{ premium: hall.is_premium }">{{ hall.badge }}</span>
            </div>
            <div class="card-details">
              <h2>{{ hall.name }}</h2>
              <p class="desc">{{ hall.description }}</p>
              <ul class="features">
                <li><strong>Capacity:</strong> {{ hall.min_capacity }} - {{ hall.max_capacity }} Guests</li>
                <li><strong>Size:</strong> {{ hall.size }}</li>
                <li><strong>Amenities:</strong> {{ hall.amenities?.join(', ') }}</li>
                <li><strong>Price:</strong> {{ formatPrice(hall.price_per_4hrs) }} / 4 Hours</li>
              </ul>
              <button @click="selectHall(hall)" class="select-btn">
                Book {{ hall.name }}
              </button>
            </div>
          </div>

        </div>
      </section>
    </template>

  </div>

  <FunctionHallReservationModal 
    :isVisible="showReservationModal"
    :halls="halls"
    :initialHall="selectedHall"
    @close="showReservationModal = false"
    @submit="handleBookingSubmit"
  />

  <BookingConfirmationModal :isVisible="showConfirmation" @close="showConfirmation = false" />
</template>

<style scoped>
/* GENERAL PAGE */
.function-hall-page { font-family: 'Segoe UI', sans-serif; color: #333; background-color: #f4f4f4; padding-bottom: 2rem; }

/* HERO */
.hall-hero { background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('/functionhall.jpg'); background-size: cover; background-position: center; height: 40vh; display: flex; align-items: center; justify-content: center; text-align: center; color: white; }
.hero-content h1 { font-size: 2.5rem; margin-bottom: 0.5rem; letter-spacing: 2px; }
.hero-content p { font-size: 1.1rem; }

/* HALL CHOICES */
.hall-choices { padding: 4rem 2rem; }
.container { max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; }
.hall-card { 
  background: white; 
  border-radius: 20px; 
  overflow: hidden; 
  box-shadow: 
    0 10px 30px rgba(0,0,0,0.08),
    0 4px 10px rgba(0,0,0,0.04);
  width: 100%; 
  max-width: 500px; 
  display: flex; 
  flex-direction: column; 
  border: 1px solid rgba(255,255,255,0.6); 
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); 
}

.hall-card:hover { 
  transform: translateY(-8px); 
  box-shadow: 
    0 20px 40px rgba(0,0,0,0.12),
    0 8px 16px rgba(0,0,0,0.08); 
}

.card-image { height: 250px; background-size: cover; background-position: center; position: relative; }
.small-hall-img { background-color: #8da399; }
.big-hall-img { background-color: #4a5c66; }
.badge { position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.75); color: white; padding: 6px 16px; border-radius: 24px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 600; }
.badge.premium { background: #D59F4A; color: #1d3557; font-weight: bold; box-shadow: 0 3px 8px rgba(213, 159, 74, 0.3); }

.card-details { padding: 2rem; flex-grow: 1; display: flex; flex-direction: column; }
.card-details h2 { color: #2c3e50; margin-bottom: 0.75rem; font-size: 1.5rem; font-weight: 600; }
.desc { font-size: 0.95rem; color: #666; margin-bottom: 1.75rem; line-height: 1.7; }
.features { list-style: none; padding: 0; margin-bottom: 2rem; flex-grow: 1; }
.features li { margin-bottom: 0.9rem; border-bottom: 1px dashed #e0e0e0; padding-bottom: 0.6rem; color: #444; font-size: 0.95rem; }
.select-btn { 
  width: 100%; 
  padding: 1.1rem; 
  background-color: #2c3e50; 
  color: white; 
  border: none; 
  border-radius: 10px; 
  cursor: pointer; 
  transition: all 0.2s ease; 
  font-weight: 600; 
  box-shadow: 0 4px 6px rgba(44, 62, 80, 0.2); 
  border-bottom: 3px solid rgba(0,0,0,0.2); 
}
.select-btn:hover { 
  background-color: #D59F4A; 
  transform: translateY(-2px); 
  box-shadow: 0 8px 15px rgba(213, 159, 74, 0.25); 
  border-bottom: 3px solid rgba(0,0,0,0.2); 
}
.select-btn:active {
  transform: translateY(1px);
  box-shadow: 0 2px 4px rgba(213, 159, 74, 0.2);
  border-bottom: 1px solid rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
  .hero-content h1 { font-size: 2rem; }
}
</style>
