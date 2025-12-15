<script setup>
defineProps({
  text: {
    type: String,
    default: 'Loading Experience...'
  }
});
</script>

<template>
  <div class="loading-state">
    <div class="premium-spinner"></div>
    <p>{{ text }}</p>
  </div>
</template>

<style scoped>
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  color: #D59F4A;
}

.premium-spinner {
  width: 50px;
  height: 50px;
  border: 3px solid rgba(213, 159, 74, 0.3);
  border-radius: 50%;
  border-top-color: #D59F4A;
  animation: spin 1s ease-in-out infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
