<script setup>
import { onMounted, onUnmounted } from 'vue';
const props = defineProps({
  isVisible: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    default: 'Request Submitted'
  },
  message: {
    type: String,
    default: 'Wait for email confirmation'
  }
});



const emit = defineEmits(['close']);

const close = () => {
  emit('close');
};

const handleKeydown = (e) => {
  if (e.key === 'Escape' && props.isVisible) close();
};

onMounted(() => window.addEventListener('keydown', handleKeydown));
onUnmounted(() => window.removeEventListener('keydown', handleKeydown));
</script>

<template>
  <div v-if="isVisible" class="modal-overlay">
    <div class="modal-content">
      <div class="spinner-container">
        <div class="spinner-check"></div>
      </div>
      <h3>{{ title }}</h3>
      <p>{{ message }}</p>
      <button @click="close" class="close-btn">Close</button>
    </div>
  </div>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
  backdrop-filter: blur(5px);
}

.modal-content {
  background: white;
  padding: 2.5rem;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  max-width: 400px;
  width: 90%;
  animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes popIn {
  from { transform: scale(0.8); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.spinner-container {
  margin-bottom: 1.5rem;
  display: flex;
  justify-content: center;
}

.spinner-check {
  width: 60px;
  height: 60px;
  border: 5px solid #D59F4A;
  border-radius: 50%;
  position: relative;
}

.spinner-check::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) rotate(45deg);
  width: 15px;
  height: 25px;
  border-bottom: 4px solid #D59F4A;
  border-right: 4px solid #D59F4A;
}

h3 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
  font-size: 1.5rem;
}

p {
  color: #666;
  margin-bottom: 2rem;
}

.close-btn {
  background-color: #D59F4A;
  color: white;
  border: none;
  padding: 0.8rem 2rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  transition: background 0.3s;
}

.close-btn:hover {
  background-color: #b58334;
}
</style>
