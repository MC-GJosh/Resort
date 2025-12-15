<script setup>
// Uses default layout automatically
</script>

<template>
  <div class="pool-page">
    
    <!-- HERO SECTION -->
    <section class="pool-hero">
      <div class="hero-content">
        <h1>Swimming Pool</h1>
        <p>Dive into relaxation and fun</p>
      </div>
    </section>

    <section class="pool-content">
      <div class="container">
        
        <div class="pool-details-card">
          <div class="pool-img-container">
            <img src="/pool.jpg" alt="Swimming Pool" />
          </div>
          <div class="pool-specs">
            <h2>Our Swimming Pool</h2>
            <p class="pool-desc">
              Experience our state-of-the-art swimming facility designed for both recreation and competitive training.
            </p>
            <div class="specs-grid">
              <div class="spec-item">
                <span class="spec-label">Length</span>
                <span class="spec-value">25 Meters</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Lanes</span>
                <span class="spec-value">6 Lanes</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Depth</span>
                <span class="spec-value">6 Feet</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Water</span>
                <span class="spec-value">Temperature Controlled</span>
              </div>
            </div>
          </div>
        </div>

        <div class="coming-soon-card">
          <div class="icon-img-container">
            <img src="/swimming.jpg" alt="Swimming" />
          </div>
          <h2>Learn to Swim Program</h2>
          <p class="status-badge">Coming Soon</p>
          <p class="description">
            We're excited to announce our upcoming Learn to Swim Program! 
            Designed for all ages and skill levels, from beginners to advanced swimmers. 
            Professional instructors will guide you on your swimming journey in a safe, 
            supportive environment.
          </p>
          <p class="subtitle">Stay tuned for enrollment details!</p>
        </div>

        <div class="info-section">
          <h3>What to Expect</h3>
          <div class="benefits-grid">
            <div class="benefit-item">
              <span class="benefit-icon">✓</span>
              <p>Professional certified instructors</p>
            </div>
            <div class="benefit-item">
              <span class="benefit-icon">✓</span>
              <p>Age-appropriate classes</p>
            </div>
            <div class="benefit-item">
              <span class="benefit-icon">✓</span>
              <p>Safe learning environment</p>
            </div>
            <div class="benefit-item">
              <span class="benefit-icon">✓</span>
              <p>Flexible scheduling options</p>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
</template>

<style scoped>
.pool-page {
  font-family: 'Segoe UI', sans-serif;
  color: #333;
  background-color: #f4f4f4;
}

/* HERO */
.pool-hero {
  background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('/swimmingheader.jpg');
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

.hero-content h1 {
  font-size: 3.5rem;
  margin-bottom: 0.5rem;
  text-shadow: 2px 2px 6px rgba(0,0,0,0.6);
  font-weight: 600;
}

.hero-content p {
  font-size: 1.2rem;
  text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}

/* CONTENT */
.pool-content {
  padding: 2rem 2rem 5rem;
}

.container {
  max-width: 1000px;
  margin: 0 auto;
}

.pool-details-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 
    0 15px 35px rgba(0,0,0,0.1),
    0 5px 15px rgba(0,0,0,0.05);
  border: 1px solid rgba(255,255,255,0.5);
  margin-bottom: 4rem;
  display: flex;
  flex-direction: column;
  transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.pool-details-card:hover {
  transform: translateY(-5px);
  box-shadow: 
    0 25px 50px rgba(0,0,0,0.15),
    0 10px 20px rgba(0,0,0,0.1);
}

.pool-img-container {
  height: 300px;
  overflow: hidden;
  position: relative;
}

.pool-img-container::after {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  box-shadow: inset 0 0 20px rgba(0,0,0,0.2);
  pointer-events: none;
  z-index: 2;
}

.pool-img-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.pool-details-card:hover .pool-img-container img {
  transform: scale(1.05);
}

.pool-specs {
  padding: 3rem;
}

.pool-specs h2 {
  font-size: 2.2rem;
  color: #1d3557;
  margin-bottom: 1rem;
  font-weight: 600;
}

.pool-desc {
  font-size: 1.1rem;
  color: #555;
  margin-bottom: 2rem;
  line-height: 1.8;
}

.specs-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  background: #f9f9f9;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: inset 0 2px 6px rgba(0,0,0,0.05);
  border: 1px solid rgba(0,0,0,0.03);
}

.spec-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.spec-label {
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #888;
  margin-bottom: 0.5rem;
  font-weight: 600;
}

.spec-value {
  font-size: 1.2rem;
  color: #1d3557;
  font-weight: bold;
}

.coming-soon-card {
  background: white;
  padding: 4rem 3rem;
  border-radius: 20px;
  box-shadow: 
    0 15px 35px rgba(0,0,0,0.1),
    0 5px 15px rgba(0,0,0,0.05);
  border: 1px solid rgba(255,255,255,0.5);
  text-align: center;
  margin-bottom: 4rem;
  position: relative;
}

.icon-img-container {
  width: 120px;
  height: 120px;
  margin: 0 auto 1.5rem;
  border-radius: 50%;
  overflow: hidden;
  box-shadow: 
    0 10px 25px rgba(0,0,0,0.15),
    0 4px 10px rgba(0,0,0,0.1);
  border: 4px solid white;
  transform: translateZ(0); 
}

.icon-img-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.coming-soon-card h2 {
  font-size: 2.5rem;
  color: #1d3557;
  margin-bottom: 1rem;
  font-weight: 600;
}

.status-badge {
  display: inline-block;
  padding: 12px 28px;
  background-color: #D59F4A;
  color: white;
  text-decoration: none;
  font-weight: bold;
  border-radius: 6px;
  text-transform: uppercase;
  font-size: 0.9rem;
  letter-spacing: 1px;
  transition: all 0.2s ease;
  box-shadow: 0 4px 6px rgba(29, 53, 87, 0.2);
  border-bottom: 3px solid rgba(0,0,0,0.1);
  margin-bottom: 2rem;
}

.status-badge:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(213, 159, 74, 0.3);
  background-color: #D59F4A;
  border-bottom: 3px solid rgba(0,0,0,0.15);
}

.description {
  font-size: 1.2rem;
  line-height: 1.9;
  color: #555;
  margin-bottom: 1.5rem;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.subtitle {
  font-size: 1.1rem;
  color: #D59F4A;
  font-weight: 600;
  margin-top: 2rem;
}

.info-section {
  background: white;
  padding: 3rem;
  border-radius: 20px;
  box-shadow: 
    0 15px 35px rgba(0,0,0,0.1),
    0 5px 15px rgba(0,0,0,0.05);
  border: 1px solid rgba(255,255,255,0.5);
}

.info-section h3 {
  font-size: 2rem;
  color: #1d3557;
  margin-bottom: 2rem;
  text-align: center;
  font-weight: 600;
}

.benefits-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2rem;
}

.benefit-item {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.benefit-icon {
  background: #D59F4A;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  flex-shrink: 0;
}

.benefit-item p {
  font-size: 1.1rem;
  color: #555;
  margin: 0;
}

/* Mobile */
@media (max-width: 768px) {
  .specs-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }

  .benefits-grid {
    grid-template-columns: 1fr;
  }
  
  .coming-soon-card, .pool-specs {
    padding: 2rem;
  }
  
  .hero-content h1 {
    font-size: 2.5rem;
  }
}
</style>
