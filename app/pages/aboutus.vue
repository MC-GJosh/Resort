<script setup>
// Uses default layout automatically
</script>

<template>
  <div class="about-page">
    
    <section class="about-hero">
      <div class="hero-content">
        <h1>About Us</h1>
        <h3>Enjoy Your Time with Us at Waterland</h3>
      </div>
    </section>

    <div class="content-container">
      <div class="welcome-section">
        <p class="intro-text">
          Welcome to <strong>Waterland Resort</strong>, where relaxation and recreation meet.
          We offer swimming pools, professional pickleball courts, comfortable hotel rooms,
          and function halls for events and celebrations.
        </p>
      </div>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">🏊</div>
          <h3>Swimming Programs</h3>
          <p>
            Dive into fun and fitness! Our swimming programs welcome all levels—from beginners to competitive swimmers.
            Kids and adults can learn, train, and improve in a safe, enjoyable environment. Enrollment is always open.
          </p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">🎉</div>
          <h3>Celebrations & Events</h3>
          <p>
            Make your special moments unforgettable in our elegant function halls. Perfect for weddings, birthdays,
            corporate events, and family gatherings. Spacious venues with modern amenities to bring your celebration to life.
          </p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">🎯</div>
          <h3>Pickleball Fun</h3>
          <p>
            Enjoy America's fastest-growing sport on our professional pickleball courts! Whether you're a beginner or seasoned player,
            our courts offer the perfect setting for friendly matches, tournaments, and staying active with friends.
          </p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">🏨</div>
          <h3>Comfortable Stays</h3>
          <p>
            Relax and recharge in our well-appointed hotel rooms. Designed for comfort with modern amenities,
            our rooms provide the perfect retreat after a day of activities or the ideal base for your weekend getaway.
          </p>
        </div>
      </div>

      <div class="highlight-banner">
        <p>Experience the perfect blend of relaxation, recreation, and celebration at Waterland Resort</p>
      </div>
    </div>
  </div>
</template>


<style scoped>

/* GENERAL PAGE */
.about-page {
  min-height: 100vh; 
  background-color: #f4f4f4;
  font-family: 'Segoe UI', sans-serif;
  color: #333;
  padding-bottom: 2rem; 
}

/* HERO SECTION - Matching other pages */
.about-hero { 
  background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('/landingpage.jpg'); 
  background-size: cover; 
  background-position: center; 
  height: 40vh; 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  text-align: center; 
  color: white; 
  margin: 0;
  padding: 0;
}
.hero-content h1 { 
  font-size: 3rem; 
  margin-bottom: 0.75rem; 
  letter-spacing: 2px; 
  color: white; 
  text-shadow: 2px 2px 6px rgba(0,0,0,0.6); 
  font-weight: 600;
}
.hero-content h3 {
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.9);
  text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
  letter-spacing: 3px;
  font-weight: 400;
}

.content-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 5rem 2.5rem 3rem 2.5rem;
}

.welcome-section {
  text-align: center;
  margin-bottom: 4rem;
}

.intro-text {
  color: #444; 
  font-size: 1.3rem;
  line-height: 2;
  margin-bottom: 0;
  max-width: 800px;
  margin: 0 auto;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2.5rem;
  margin-bottom: 4rem;
}

.feature-card {
  background: white;
  padding: 3rem 2.5rem;
  border-radius: 20px;
  box-shadow: 
    0 15px 35px rgba(0,0,0,0.1),
    0 5px 15px rgba(0,0,0,0.05);
  border: 1px solid rgba(255,255,255,0.6);
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  text-align: center;
  position: relative;
}

.feature-card:hover {
  transform: translateY(-8px);
  box-shadow: 
    0 25px 50px rgba(0,0,0,0.15),
    0 10px 20px rgba(0,0,0,0.1);
  border-color: rgba(213, 159, 74, 0.2);
}

.feature-icon {
  font-size: 3.5rem;
  margin-bottom: 1.5rem;
  filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
  transition: transform 0.3s ease;
}

.feature-card:hover .feature-icon {
  transform: scale(1.1);
}

.feature-card h3 {
  color: #1d3557;
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  font-weight: 600;
}

.feature-card p {
  color: #555;
  font-size: 1.1rem;
  line-height: 1.9;
  margin-bottom: 0;
}

.highlight-banner {
  background: linear-gradient(135deg, #1d3557 0%, #2c4a6a 100%);
  padding: 3rem 3rem;
  border-radius: 20px;
  text-align: center;
  box-shadow: 
    0 15px 40px rgba(29, 53, 87, 0.25),
    0 5px 15px rgba(29, 53, 87, 0.15);
  border: 1px solid rgba(255,255,255,0.1);
  position: relative;
  overflow: hidden;
}

.highlight-banner::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(rgba(255,255,255,0.05), transparent);
  pointer-events: none;
}

.highlight-banner p {
  color: white;
  font-size: 1.4rem;
  font-weight: 500;
  margin-bottom: 0;
  letter-spacing: 0.5px;
  line-height: 1.8;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
  .features-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .feature-card {
    padding: 2rem 1.5rem;
  }
  
  .highlight-banner {
    padding: 2rem 1.5rem;
  }
  
  .highlight-banner p {
    font-size: 1.2rem;
  }
}
</style>