<script setup>
const scrollToSection = (id) => {
  const element = document.getElementById(id);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
};

const facilities = [
  {
    id: 'fh',
    title: 'Functional Hall',
    description: 'Experience the perfect venue for your special occasions. Our Functional Hall is designed to host weddings, corporate events, and grand parties with elegance and style. Equipped with modern amenities and customizable layouts, we ensure your event is memorable.',
    image: '/functionhall.jpg',
    link: '/fh',
    buttonText: 'Available Soon!',
    disabled: true
  },
  {
    id: 'hr',
    title: 'Hotel Room',
    description: 'Relax and recharge in our luxurious hotel rooms. Designed for comfort and tranquility, each room features premium bedding, modern en-suite bathrooms, and stunning views. Whether you are here for a weekend getaway or a long vacation, our rooms provide the perfect sanctuary.',
    image: '/room.jpg',
    link: '/hr',
    buttonText: 'Available Soon!',
    disabled: true
  },
  {
    id: 'pb',
    title: 'Pickleball Court',
    description: 'Get active and enjoy a game on our professional-grade Pickleball courts. Perfect for both beginners and seasoned players, our courts offer a high-quality surface and excellent lighting for day or night play. Join the fun and stay fit at Waterland Resort.',
    image: '/pickballcourt.jpg',
    link: '/pb',
    buttonText: 'Book a Court',
    disabled: false
  }
];
</script>

<template>
  <div class="landing-page">
    <!-- HERO SECTION -->
    <section class="hero-section">
      <div class="overlay">
        <div class="hero-content">
          <h1>WATERLAND RESORT</h1>
          <h2>Relax, Recharge, and Enjoy</h2>
          <div class="hero-buttons">
            <button @click="scrollToSection('facilities-section')" class="cta-btn primary">Explore Facilities</button>
          </div>
        </div>
      </div>
    </section>

    <!-- FACILITIES SECTION -->
    <section id="facilities-section" class="facilities-section">
      <div class="container">
        
        <div 
          v-for="(facility, index) in facilities" 
          :key="facility.id" 
          class="facility-row" 
          :class="{ 'reverse': index % 2 !== 0 }"
        >
          <div class="facility-image">
            <img :src="facility.image" :alt="facility.title" />
          </div>
          
          <div class="facility-content">
            <h3>{{ facility.title }}</h3>
            <p>{{ facility.description }}</p>
            
            <div v-if="facility.disabled" class="facility-btn disabled">
              {{ facility.buttonText }}
            </div>
            <NuxtLink v-else :to="facility.link" class="facility-btn">
              {{ facility.buttonText }}
            </NuxtLink>
          </div>
        </div>

      </div>
    </section>

  </div>
</template>

<style scoped>
/* Prevent horizontal scroll & Enable Smooth Scroll */
html, body {
  scroll-behavior: smooth;
  overflow-x: hidden;
}

/* ------------------ Hero Section ------------------ */
.hero-section {
  position: relative; width: 100%; height: 100vh;
  background-image: url('/landingpage.jpg'); background-size: cover; background-position: center;
  display: flex; align-items: center; justify-content: center;
}
.overlay {
  position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: rgba(0,0,0,0.4);
}
.hero-content {
  text-align: center; color: white; z-index: 1;
}
.hero-content h1 {
  font-size: 4rem; font-weight: bold; text-shadow: 2px 2px 10px rgba(0,0,0,0.7); margin-bottom: 1rem;
}
.hero-content h2 {
  font-size: 2rem; margin-bottom: 2rem; text-shadow: 1px 1px 5px rgba(0,0,0,0.5); font-weight: 300;
}
.cta-btn {
  padding: 12px 30px; background-color: #1d3557; color: white; border: none; border-radius: 6px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: background 0.3s;
}
.cta-btn:hover { background-color: #D59F4A; }

/* ------------------ Facilities Section ------------------ */
.facilities-section {
  padding: 6rem 2rem;
  background-color: #fdfdfd;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.facility-row {
  display: flex;
  align-items: center;
  gap: 4rem;
  margin-bottom: 6rem;
}

.facility-row:last-child {
  margin-bottom: 0;
}

/* Alternating Layout */
.facility-row.reverse {
  flex-direction: row-reverse;
}

.facility-image {
  flex: 1;
  height: 300px;
  overflow: hidden;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.facility-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.facility-image:hover img {
  transform: scale(1.05);
}

.facility-content {
  flex: 1;
  padding: 1rem;
}

.facility-content h3 {
  font-size: 2.5rem;
  color: #1d3557;
  margin-bottom: 1.5rem;
  font-family: 'Georgia', serif; /* Or any serif font to match the reference image style */
}

.facility-content p {
  font-size: 1.1rem;
  color: #555;
  line-height: 1.8;
  margin-bottom: 2rem;
}

.facility-btn {
  display: inline-block;
  padding: 12px 28px;
  background-color: #0077b6; /* Matches the blue in the reference image */
  color: white;
  text-decoration: none;
  font-weight: bold;
  border-radius: 4px;
  text-transform: uppercase;
  font-size: 0.9rem;
  letter-spacing: 1px;
  transition: background-color 0.3s;
}

.facility-btn:hover {
  background-color: #023e8a;
}

.facility-btn.disabled {
  background-color: #ccc;
  cursor: not-allowed;
  pointer-events: none;
}

/* Mobile Responsiveness */
@media (max-width: 968px) {
  .facility-row {
    flex-direction: column;
    gap: 2rem;
    margin-bottom: 4rem;
  }
  
  .facility-row.reverse {
    flex-direction: column;
  }

  .facility-image {
    width: 100%;
    height: 300px;
  }

  .facility-content {
    text-align: center;
  }

  .hero-content h1 { font-size: 2.5rem; }
  .hero-content h2 { font-size: 1.5rem; }
}
</style>