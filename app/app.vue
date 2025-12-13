<template>
  <div>
    <AuthModal />
    <Header />
    <NuxtPage />    <!-- This renders your page content -->
    <Footer></Footer>  <!-- Your basic footer component -->
  </div>
</template>

<script setup>

</script>

<style>
/* Global styles */
* {
  box-sizing: border-box;
}

html, body {
  margin: 0;
  padding: 0;
  overflow-x: hidden; /* prevent horizontal scroll */
  font-family: sans-serif;
}
</style>
