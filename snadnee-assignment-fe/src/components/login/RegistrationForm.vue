<template>
  <div>
    <h2>New user</h2>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input name="email" type="email" class="form-control" required v-model.trim="v$.email.$model" v-on:keyup.enter="register">
      <div v-if="v$.email.$error" class="text-danger">Invalid email</div>
    </div>
    <div class="row row-cols-2">
      <div class="col mb-3">
        <label for="firstName" class="form-label">First name</label>
        <input name="firstName" class="form-control" required v-model.trim="v$.firstName.$model" v-on:keyup.enter="register">
        <div v-if="v$.firstName.$error" class="text-danger">Required</div>
      </div>
      <div class="col mb-3">
        <label for="lastName" class="form-label">Last name</label>
        <input name="lastName" class="form-control" required v-model.trim="v$.lastName.$model" v-on:keyup.enter="register">
        <div v-if="v$.lastName.$error" class="text-danger">Required</div>
      </div>
    </div>
    <div class="mb-3">
      <div class="form-label">Phone</div>
      <MazPhoneNumberInput
          v-model="v$.phone.$model"
          v-model:country-code="countryCode"
          show-code-on-list
          block
      />
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input name="password" type="password" class="form-control" required v-model.trim="v$.password.$model" v-on:keyup.enter="register">
      <div v-if="v$.password.$error" class="text-danger">Required</div>
    </div>
    <div class="my-3 text-danger" v-if="loginError !== null">{{ loginError }}</div>
    <button @click="register" class="btn btn-custom-primary" style="width: 11em">
      <span v-if="!loading">
        Register
      </span>
      <img src="@/assets/loading-button.gif" v-if="loading" alt="loading" style="height: 1em">
    </button>
  </div>
</template>

<script>
import {useVuelidate} from "@vuelidate/core";
import {required, email} from "@vuelidate/validators";
import MazPhoneNumberInput from 'maz-ui/components/MazPhoneNumberInput'

export default {
  name: "LoginForm",
  components: [
    MazPhoneNumberInput
  ],
  data() {
    return {
      email: '',
      password: '',
      firstName: '',
      lastName: '',
      phone: '',
      loginError: '',
      countryCode: 'CZ',
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
      },
      firstName: {
        required,
      },
      lastName: {
        required,
      },
      phone: {
        required,
      }
    }
  },
  methods: {
    async register(){
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

      this.$store.dispatch('register', {
        email: this.email,
        password: this.password,
        firstName: this.firstName,
        lastName: this.lastName,
        phone: this.phone,
      }).then(() => {
        thisProxy.v$.$reset();
        this.loading = false;
      }).catch((error) => {
        this.loading = false;
        thisProxy.v$.$reset();
        thisProxy.loginError = error.message
      });
    }
  }
}
</script>

<style scoped>

</style>