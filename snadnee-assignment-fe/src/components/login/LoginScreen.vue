<template>
  <div class="d-flex justify-content-center my-auto">
    <div class="login-box px-3 py-5">
      <div v-if="!registering">
        <div class="mb-3">
          <LoginForm></LoginForm>
        </div>
        <a class="text-decoration-underline" @click="registering = true">New user</a>
      </div>
      <div v-if="registering">
        <div class="mb-3">
          <RegistrationForm></RegistrationForm>
        </div>
        <a class="text-decoration-underline" @click="registering = false">Already have an account?</a>
      </div>
    </div>
  </div>
</template>

<script>
import LoginForm from "@/components/login/LoginForm.vue";
import RegistrationForm from "@/components/login/RegistrationForm.vue";

export default {
  name: 'LoginScreen',
  components: {RegistrationForm, LoginForm},
  data() {
    return {
      registering: false,
    };
  },
}
</script>

<style scoped>
.login-box{
  background-color: #FFFFFF;
  box-shadow: 0 0 45px 0 rgba(0,0,0,0.2);
  border-radius: 1rem;
}
</style>