<template>
  <footer class="page-footer">
    <div class="container">
      <p>&copy; 2025 Waterland Resort. All rights reserved.</p>
    </div>
  </footer>
</template>

<script setup>
// No script needed
</script>

<style scoped>
.page-footer {
  width: 100%;
  background-color: rgba(0, 0, 0, 1); /* solid black */
  color: white; 
  padding: 0.8rem 0;
}

.page-footer .container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: center; /* center horizontally */
  align-items: center;     /* center vertically if needed */
  text-align: center;      /* fallback for text */
  padding: 0 1rem;
}

.page-footer p {
  margin: 0;
  font-size: 0.9rem;
}
</style>
