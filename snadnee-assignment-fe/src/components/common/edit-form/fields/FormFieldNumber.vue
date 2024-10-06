<template>
  <div class="form-group">
    <div>{{ title }}</div>
    <input
        type="number"
        :min="fieldOptions.min"
        :max="fieldOptions.max"
        step="1"
        v-model.trim="v$.value.$model"
        class="form-control"
        v-on:keyup.enter="$emit('valueSelected', this)"
    >
    <div v-if="v$.value.$error" class="text-danger">People not in range</div>
  </div>
</template>

<script>
import {useVuelidate} from '@vuelidate/core'

export default {
  name: "FormFieldNumber",
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