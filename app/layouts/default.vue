<template>
  <div>
    <AuthModal />
    <Header />
    <slot />
    <Footer />
  </div>
</template>

<script setup>
const route = useRoute();
const { openLoginModal, authSuccessMessage } = useAuth();

onMounted(() => {
  if (route.query.verified === 'true') {
    authSuccessMessage.value = 'Email verified successfully! You can now log in.';
    openLoginModal();
    // Clean URL
    const router = useRouter();
    router.replace({ query: null });
  } else if (route.query.verified === 'already') {
    authSuccessMessage.value = 'Email already verified. Please log in.';
    openLoginModal();
    const router = useRouter();
    router.replace({ query: null });
  }
});
</script>
