<template>
  <div>
    <div v-if="!hasValidToken" style="height: 100vh" class="d-grid">
      <LoginScreen></LoginScreen>
    </div>
    <div class="container" v-if="hasValidToken">
      <div class="row">
        <div id="main">
          <router-view></router-view>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import LoginScreen from "@/components/login/LoginScreen.vue";

export default {
  name: 'App',
  components: {LoginScreen},
  computed: {
    hasValidToken() {
      return this.$store.getters.hasValidToken;
    }
  },
}
</script>

<style>

</style>
