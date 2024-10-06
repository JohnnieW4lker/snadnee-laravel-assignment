<template>
  <div v-if="active" class="position-absolute top-0 mt-3 d-flex justify-content-center" style="width: 100%">
    <div class="alert align-content-center pointer" :class="alertClass" role="alert" @click="restart">
      {{ message }}
    </div>
  </div>
</template>

<script>

export default {
  name: "AlertPopup",
  data() {
    return {
      message: '',
      alertClass: 'alert-primary',
      active: false,
      timeout: null,
    }
  },
  methods: {
    warn(message, time) {
      this.createPopup(message, 'alert-warning', time)
    },
    error(message, time) {
      this.createPopup(message, 'alert-danger', time)
    },
    success(message, time) {
      this.createPopup(message, 'alert-success', time)
    },
    restart() {
      if (this.timeout !== null) {
        clearTimeout(this.timeout);
        this.timeout = null;
      }
      this.active = false;
    },
    createPopup(message, alertClass, time) {
      this.restart();
      this.message = message;
      this.alertClass = alertClass;
      this.active = true;
      setTimeout(() => this.active = false, time);
    }
  },
  expose: ['warn', 'error', 'success'],
}
</script>

<style scoped>

</style>