<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: 'GCash'
  },
  referenceNumber: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue', 'update:referenceNumber']);

const selectedMethod = ref(props.modelValue);
const refNum = ref(props.referenceNumber);

watch(selectedMethod, (val) => {
  emit('update:modelValue', val);
});

watch(refNum, (val) => {
  emit('update:referenceNumber', val);
});
</script>

<template>
  <div class="payment-method-component">
    <div class="form-group">
      <label>Payment Method</label>
      <select v-model="selectedMethod">
        <option value="GCash">GCash</option>
        <option value="Bank Transfer">Bank Transfer</option>
        <option value="Pay on Site">Pay on Site</option>
      </select>
    </div>

    <div v-if="selectedMethod === 'GCash'" class="payment-info">
      <p>Please send your payment to GCash Number: <strong>0917-123-4567</strong> (Waterland Resort).</p>
      <p><small>Reference number will be required upon confirmation.</small></p>
      <div class="form-group" style="margin-top: 10px;">
        <label>Reference Number</label>
        <input type="text" v-model="refNum" placeholder="e.g. 123456789" />
      </div>
    </div>

    <div v-if="selectedMethod === 'Bank Transfer'" class="payment-info">
      <p><strong>Bank Transfer Details:</strong></p>
      <ul style="list-style: none; padding: 0; margin: 0.5rem 0;">
        <li>Bank Name: <strong>BDO</strong></li>
        <li>Account Name: <strong>Waterland Resort</strong></li>
        <li>Account Number: <strong>1234-5678-9012</strong></li>
      </ul>
      <p><small>Please enter the reference number below and upload your proof of payment to the link sent to your email or message us on our Facebook page to confirm your booking.</small></p>
      <div class="form-group" style="margin-top: 10px;">
        <label>Reference Number</label>
        <input type="text" v-model="refNum" placeholder="e.g. 123456789" />
      </div>
    </div>

    <div v-if="selectedMethod === 'Pay on Site'" class="payment-info">
      <p><strong>Pay on Site:</strong></p>
      <p>Please proceed to the front desk to settle your payment before your scheduled time.</p>
    </div>
  </div>
</template>

<style scoped>
.form-group { display: flex; flex-direction: column; margin-bottom: 1.2rem; }
label { font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem; }
input, select {
  padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; width: 100%;
}
input:focus, select:focus { outline: none; border-color: #D59F4A; }

.payment-info { margin-top: 10px; padding: 12px; background: #f1f8e9; border: 1px solid #c5e1a5; border-radius: 6px; color: #33691e; }
</style>
