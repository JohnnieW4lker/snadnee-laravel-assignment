<template>
    <div class="d-flex justify-content-center">
      <img :src="require('@/assets/login-pic.webp')" alt="login" class="login-pic">
    </div>
    <div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input name="email" type="email" class="form-control" required v-model.trim="v$.email.$model" v-on:keyup.enter="login">
        <div v-if="v$.email.$error" class="text-danger">Invalid email</div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input name="password" type="password" class="form-control" required v-model.trim="v$.password.$model" v-on:keyup.enter="login">
        <div v-if="v$.password.$error" class="text-danger">Required</div>
      </div>
      <div class="my-3 text-danger" v-if="loginError !== null">{{ loginError }}</div>
      <button @click="login" class="btn btn-custom-primary" style="width: 8em">
        <span v-if="!loading">
          Login
        </span>
        <img src="@/assets/loading-button.gif" v-if="loading" alt="loading" style="height: 1em">
      </button>
    </div>
</template>

<script>
import {useVuelidate} from "@vuelidate/core";
import {required, email} from "@vuelidate/validators";

export default {
  name: "LoginForm",
  data() {
    return {
      email: '',
      password: '',
      loginError: '',
      loading: false,
    }
  },
  setup () {
    return { v$: useVuelidate() }
  },
  validations() {
    return {
      email: {
        required,
        email,
      },
      password: {
        required,
      }
    }
  },
  methods: {
    async login(){
      if (this.loading === true) {
        return;
      }

      let validationResult = await this.v$.$validate();
      if (!validationResult) {
        return;
      }

      this.loading = true;
      let thisProxy = this;
      thisProxy.loginError = null;

      this.$store.dispatch('login', {
        email: this.email,
        password: this.password
      }).then(() => {
        thisProxy.v$.$reset();
        this.loading = false;
      }).catch(() => {
        thisProxy.v$.$reset();
        thisProxy.loginError = 'Invalid credentials';
        this.loading = false;
      });
    }
  }
}
</script>

<style scoped>
  .login-pic {
    border-radius: 2rem;
    width: 20%;
  }
</style>