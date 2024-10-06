<template>
  <div class="form-group">
    <div>{{ title }}</div>
    <input
        v-model.trim="v$.value.$model"
        class="form-control"
        v-on:keyup.enter="$emit('valueSelected', this)"
        type="password"
    >
    <password-meter :password="value" />
    <div v-if="v$.value.$error" class="text-danger">Name field has an error.</div>
  </div>
</template>

<script>
import {useVuelidate} from "@vuelidate/core";
import PasswordMeter from 'vue-simple-password-meter';

export default {
  name: "FormFieldPassword",
  components: {
    PasswordMeter,
  },
  props: {
    fieldOptions: Object,
  },
  setup () {
    return { v$: useVuelidate() }
  },
  data() {
    return {
      value: ''
    }
  },
  computed: {
    title() {
      if (this.fieldOptions.title !== null) {
        return this.fieldOptions.title;
      }
      return '---';
    }
  },
  validations() {
    if (typeof this.fieldOptions.validations !== "undefined") {
      return {value: this.fieldOptions.validations};
    }
    return {value: {}};
  },
  methods: {
    getValue() {
      return this.value;
    },
    setValue(value) {
      this.value = value;
      this.v$.$reset();
    },
    async validate() {
      let validationResult = await this.v$.$validate();
      if (validationResult) {
        this.v$.$reset();
      }

      return validationResult;
    }
  },
  expose: ['getValue', 'setValue', 'validate']
}
</script>

<style scoped>

</style>