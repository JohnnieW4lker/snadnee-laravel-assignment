<template>
  <div class="form-check my-auto px-5">
    <input
        v-model.trim="v$.value.$model"
        class="form-check-input"
        type="checkbox"
    >
    <label class="form-check-label">{{ title }}</label>
    <div v-if="v$.value.$error" class="text-danger">Name field has an error.</div>
  </div>
</template>

<script>
import {useVuelidate} from "@vuelidate/core";

export default {
  name: "FormFieldCheckbox",
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
      return !(this.value === null || this.value === false);

    },
    setValue(value) {
      this.v$.$reset();
      if (value === null) {
        this.value = false;
      }
      this.value = value;
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