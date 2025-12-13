<script setup>
</script>

<template>
  <div class="coming-soon-page">
    <div class="content">
      <h1>Hotel Rooms</h1>
      <h2>Coming Soon</h2>
      <p>Luxury accommodations are on their way. We can't wait to host you!</p>
      <NuxtLink to="/" class="back-btn">Back to Home</NuxtLink>
    </div>
  </div>
</template>

<style scoped>
.coming-soon-page {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('/room.jpg');
  background-size: cover;
  background-position: center;
  color: white;
  text-align: center;
}

.content {
  padding: 2rem;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 12px;
  backdrop-filter: blur(5px);
}

h1 {
  font-size: 3rem;
  margin-bottom: 0.5rem;
}

h2 {
  font-size: 2rem;
  color: #D59F4A;
  margin-bottom: 1.5rem;
  text-transform: uppercase;
  letter-spacing: 2px;
}

p {
  font-size: 1.2rem;
  margin-bottom: 2rem;
  max-width: 600px;
}

.back-btn {
  display: inline-block;
  padding: 12px 30px;
  background-color: #D59F4A;
  color: white;
  text-decoration: none;
  font-weight: bold;
  border-radius: 6px;
  transition: background 0.3s;
}

.back-btn:hover {
  background-color: #b58334;
}
</style>
