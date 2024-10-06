<template>
  <div class="form-group">
    <div>{{ title }}</div>
    <div class="input-group">
      <input
          v-model="value"
          class="form-control"
          type="number"
          min="0"
      >
      <div class="input-group-append">
        <span class="input-group-text">{{ currencySymbol }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import {useVuelidate} from "@vuelidate/core";

export default {
  name: "FormFieldPrice",
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
    },
    currencySymbol(){
      if (typeof this.fieldOptions.currencySymbol !== "undefined") {
        return {value: this.fieldOptions.currencySymbol};
      }

      return '€';
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