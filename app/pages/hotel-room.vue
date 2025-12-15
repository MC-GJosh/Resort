<script setup>
import { ref, onMounted } from 'vue';
import BookingConfirmationModal from '~/components/BookingConfirmationModal.vue';
import RoomReservationModal from '~/components/RoomReservationModal.vue';

const api = useApi();
const { isLoggedIn, openLoginModal } = useAuth();

// --- Room Data ---
const rooms = ref([]);
const loading = ref(true);
const showReservationModal = ref(false);
const showConfirmation = ref(false);
const selectedRoom = ref(null);

// --- Fetch rooms from API ---
const fetchRooms = async () => {
  loading.value = true;
  // Add artificial delay for "loading" feel (2.5 seconds)
  const delay = new Promise(resolve => setTimeout(resolve, 2500));
  
  const { data } = await api.get('/rooms');
  
  // Wait for both the API call and the delay
  await delay;
  
  if (data) {
    rooms.value = data;
  }
  loading.value = false;
};

onMounted(() => {
  fetchRooms();
});

const formatPrice = (price) => {
  return '₱' + parseFloat(price).toLocaleString();
};

const selectRoom = (room) => {
  if (!isLoggedIn.value) {
    openLoginModal();
    return;
  }
  selectedRoom.value = room;
  showReservationModal.value = true;
};

const getRoomImage = (room) => {
  const name = room.name.toLowerCase();
  
  if (name.includes('grand')) {
    return '/grandballroom.jpg';
  } else if (name.includes('pearl')) {
    return '/thepearlsuite.jpg';
  } else if (name.includes('suite')) {
    return '/hotelroom.jpg'; // Different from basic room
  } else {
    return '/room.jpg'; // Default fallback
  }
};

// ... existing code ...

const handleBookingSubmit = async (bookingData) => {
// ... existing handleBookingSubmit code ...
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
            >
              <div 
                class="room-img" 
                :style="{ backgroundImage: `url(${getRoomImage(room)})` }"
              >
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
                  Select Room
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </template>
  </div>

  <RoomReservationModal 
    :isVisible="showReservationModal"
    :rooms="rooms"
    :initialRoom="selectedRoom"
    @close="showReservationModal = false"
    @submit="handleBookingSubmit"
  />

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
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06);
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  display: flex;
  flex-direction: column;
  border: 1px solid rgba(0,0,0,0.03);
  position: relative;
}

.room-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.12);
}

.room-card:hover .room-img {
  transform: scale(1.02);
}

/* Image Placeholders */
.room-img {
  height: 250px;
  background-color: #ddd;
  position: relative;
  background-size: cover;
  background-position: center;
  transition: transform 0.6s ease;
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
</style>
