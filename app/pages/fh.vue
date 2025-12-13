<script setup>
import { ref } from 'vue';
import BookingConfirmationModal from '~/components/BookingConfirmationModal.vue';
import PaymentMethod from '~/components/PaymentMethod.vue';
// import { useAuth } from '~/composables/useAuth.js';

const useLocalAuth = () => {
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

// --- Form State ---
const bookingForm = ref({
  fullName: '',
  phoneNumber: '',
  address: '',
  email: '',
  eventDate: '',
  guestCount: 0,
  selectedHall: 'small', // Default selection
  paymentMethod: 'GCash',
  referenceNumber: '',
  cateringPackage: '', // New field for catering
  selectedMainDish: '',
  selectedAppetizer: '',
  selectedDrink: '',
  availMiniBar: false,
  notes: ''
});

// --- Menu Options ---
const menuOptions = {
  mainDishes: ['Fried Chicken', 'Pork BBQ Skewers', 'Beef Caldereta', 'Breaded Fish Fillet', 'Roast Beef'],
  appetizers: ['Lumpiang Shanghai', 'Spaghetti Bolognese', 'Creamy Carbonara', 'Buttered Vegetables', 'Calamares'],
  drinks: ['Iced Tea', 'Blue Lemonade', 'Soda / Soft Drinks', 'Fruit Juice']
};


const showConfirmation = ref(false);

// --- Hall Selection Logic ---
const selectHall = (hallType) => {
  bookingForm.value.selectedHall = hallType;

  const formElement = document.getElementById('booking-section');
  if (formElement) {
    formElement.scrollIntoView({ behavior: 'smooth' });
  }
};

const { isLoggedIn, openLoginModal } = useLocalAuth();

const submitBooking = () => {
  if (!isLoggedIn.value) { openLoginModal(); return; }
  // In a real app, you would send data to backend here
  showConfirmation.value = true;
};

const validatePhone = (event) => {
  const input = event.target;
  input.value = input.value.replace(/\D/g, '').slice(0, 11);
  bookingForm.value.phoneNumber = input.value;
};
</script>

<template>
  <div class="function-hall-page">

    <!-- HEADER (Small by default) -->


    <!-- HERO SECTION -->
    <section class="hall-hero">
      <div class="hero-content">
        <h1>Events at Waterland</h1>
        <p>From intimate gatherings to grand celebrations, we have the perfect space for you.</p>
      </div>
    </section>

    <!-- HALL CHOICES -->
    <section class="hall-choices">
      <div class="container">

        <div class="hall-card" :class="{ active: bookingForm.selectedHall === 'small' }">
          <div class="card-image small-hall-img">
            <span class="badge">Intimate Choice</span>
          </div>
          <div class="card-details">
            <h2>The Pearl Suite</h2>
            <p class="desc">A cozy, elegant atmosphere designed for close-knit gatherings, meetings, and private parties.</p>
            <ul class="features">
              <li><strong>Capacity:</strong> 30 - 70 Guests</li>
              <li><strong>Size:</strong> 120 sq. meters</li>
              <li><strong>Amenities:</strong> Basic Sound System, 1 Projector, Private Restroom, AC</li>
              <li><strong>Price:</strong> ₱15,000 / 4 Hours</li>
            </ul>
            <button @click="selectHall('small')" class="select-btn">Book Pearl Suite</button>
          </div>
        </div>

        <div class="hall-card" :class="{ active: bookingForm.selectedHall === 'big' }">
          <div class="card-image big-hall-img">
            <span class="badge premium">Grand Choice</span>
          </div>
          <div class="card-details">
            <h2>The Grand Ballroom</h2>
            <p class="desc">Our flagship venue featuring high ceilings and luxurious decor for weddings and corporate galas.</p>
            <ul class="features">
              <li><strong>Capacity:</strong> 150 - 400 Guests</li>
              <li><strong>Size:</strong> 500 sq. meters</li>
              <li><strong>Amenities:</strong> Full Stage, Professional Lighting & Audio, Dressing Room, VIP Lounge</li>
              <li><strong>Price:</strong> ₱45,000 / 4 Hours</li>
            </ul>
            <button @click="selectHall('big')" class="select-btn">Book Ballroom</button>
          </div>
        </div>

      </div>
    </section>

    <!-- BOOKING FORM -->
    <section id="booking-section" class="booking-wrapper">
      <div class="form-container">
        <div class="form-header">
          <h2>Secure Your Date</h2>
          <p>You are booking: 
            <span class="highlight">
              {{ bookingForm.selectedHall === 'small' ? 'The Pearl Suite (Small)' : 'The Grand Ballroom (Big)' }}
            </span>
          </p>
        </div>

        <form @submit.prevent="submitBooking" class="actual-form">
          <div class="form-group">
            <label>Full Name of Booker</label>
            <input type="text" v-model="bookingForm.fullName" placeholder="e.g. Juan Dela Cruz" required />
          </div>

          <div class="form-row">
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
          </div>

          <div class="form-group">
            <label>Complete Address</label>
            <input type="text" v-model="bookingForm.address" placeholder="House No., Street, City, Province" required />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Event Date</label>
              <input type="date" v-model="bookingForm.eventDate" required />
            </div>
            <div class="form-group">
              <label>Expected Guests</label>
              <input type="number" v-model="bookingForm.guestCount" min="1" required />
            </div>
          </div>

          <div class="form-group">
            <label>Select Hall</label>
            <select v-model="bookingForm.selectedHall">
              <option value="small">The Pearl Suite (Small)</option>
              <option value="big">The Grand Ballroom (Big)</option>
            </select>
          </div>

          <div class="form-group">
            <label>Catering Options</label>
            <select v-model="bookingForm.cateringPackage">
              <option value="" disabled>Select a package</option>
              <option value="15-20pax">15-20 Pax Package</option>
              <option value="20-25pax">20-25 Pax Package</option>
              <option value="25-30pax">25-30 Pax Package</option>
            </select>
          </div>

          <div v-if="bookingForm.cateringPackage" class="menu-selection">
            <p><strong>Customize Your Menu:</strong></p>
            
            <div class="form-group">
              <label>Choose Main Dish</label>
              <select v-model="bookingForm.selectedMainDish">
                <option value="" disabled>Select Main Dish</option>
                <option v-for="dish in menuOptions.mainDishes" :key="dish" :value="dish">{{ dish }}</option>
              </select>
            </div>

            <div class="form-group">
              <label>Choose Appetizer / Pasta / Veg</label>
              <select v-model="bookingForm.selectedAppetizer">
                <option value="" disabled>Select Appetizer</option>
                <option v-for="app in menuOptions.appetizers" :key="app" :value="app">{{ app }}</option>
              </select>
            </div>

            <div class="form-group">
              <label>Choose Drink</label>
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

          <PaymentMethod 
            v-model="bookingForm.paymentMethod" 
            v-model:referenceNumber="bookingForm.referenceNumber" 
          />



          <button type="submit" class="submit-btn">Submit Booking Request</button>
        </form>
      </div>
    </section>

  </div>
  <BookingConfirmationModal :isVisible="showConfirmation" @close="showConfirmation = false" />
</template>

<style scoped>
/* GENERAL PAGE */
.function-hall-page { font-family: 'Segoe UI', sans-serif; color: #333; background-color: #f4f4f4; }



/* HERO */
.hall-hero { background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('/landingpage.jpg'); background-size: cover; background-position: center; height: 40vh; display: flex; align-items: center; justify-content: center; text-align: center; color: white; }
.hero-content h1 { font-size: 2.5rem; margin-bottom: 0.5rem; letter-spacing: 2px; }
.hero-content p { font-size: 1.1rem; }

/* HALL CHOICES */
.hall-choices { padding: 4rem 2rem; }
.container { max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; }
.hall-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.05); width: 100%; max-width: 500px; display: flex; flex-direction: column; border: 2px solid transparent; transition: transform 0.3s, box-shadow 0.3s; }
.hall-card.active { border-color: #D59F4A; transform: translateY(-5px); }
.hall-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }

.card-image { height: 250px; background-size: cover; background-position: center; position: relative; }
.small-hall-img { background-color: #8da399; }
.big-hall-img { background-color: #4a5c66; }
.badge { position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.7); color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
.badge.premium { background: #D59F4A; color: black; font-weight: bold; }

.card-details { padding: 2rem; flex-grow: 1; display: flex; flex-direction: column; }
.card-details h2 { color: #2c3e50; margin-bottom: 0.5rem; }
.desc { font-size: 0.95rem; color: #666; margin-bottom: 1.5rem; line-height: 1.5; }
.features { list-style: none; padding: 0; margin-bottom: 2rem; flex-grow: 1; }
.features li { margin-bottom: 0.8rem; border-bottom: 1px dashed #eee; padding-bottom: 0.5rem; color: #444; }
.select-btn { width: 100%; padding: 1rem; background-color: #2c3e50; color: white; border: none; border-radius: 8px; cursor: pointer; transition: background 0.3s; }
.select-btn:hover { background-color: #D59F4A; }

/* BOOKING FORM */
.booking-wrapper { background-color: white; padding: 4rem 2rem; border-top: 1px solid #eee; }
.form-container { max-width: 800px; margin: 0 auto; background: #fdfdfd; padding: 3rem; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #eee; }
.form-header { text-align: center; margin-bottom: 2rem; }
.highlight { color: #D59F4A; font-weight: bold; }
.actual-form { display: flex; flex-direction: column; gap: 1.5rem; }
.form-row { display: flex; gap: 1.5rem; }
.form-group { display: flex; flex-direction: column; gap: 0.5rem; flex: 1; }
label { font-weight: 600; font-size: 0.9rem; color: #444; }
input, select, textarea { padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; background: #fff; }
input:focus, select:focus, textarea:focus { outline: none; border-color: #D59F4A; box-shadow: 0 0 0 3px rgba(213, 159, 74, 0.1); }
.submit-btn { background-color: #D59F4A; color: white; font-weight: bold; padding: 1.2rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1.1rem; margin-top: 1rem; transition: background 0.3s; }
.submit-btn:hover { background-color: #b58334; }

.payment-info { margin-top: 10px; padding: 12px; background: #f1f8e9; border: 1px solid #c5e1a5; border-radius: 6px; color: #33691e; }

.menu-selection {
  margin-top: 10px;
  padding: 15px;
  background-color: #fff3e0;
  border: 1px solid #ffe0b2;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.checkbox-group {
  flex-direction: row;
  align-items: center;
  gap: 0.5rem;
}
.checkbox-group input {
  width: auto;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .hero-content h1 { font-size: 2rem; }
  .form-row { flex-direction: column; gap: 1.5rem; }
  .form-container { padding: 1.5rem; }
}
</style>
