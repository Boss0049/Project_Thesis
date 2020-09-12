<template>
  <div id="app">
    <navbar></navbar>
    <transition name="fade">
      <router-view />
    </transition>
  </div>
</template>

<script>
import navbar from "./components/NavbarComponent.vue";
import mainComponent from "./components/main.vue";

export default {
  data() {
    return {
      transparent: false
    };
  },
  components: {
    navbar: navbar,
    mainComponent
  }
};
</script>
