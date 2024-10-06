<template>
  <div class="form-group">
    <div>{{ title }}</div>
    <select class="form-select" v-model="value" @change="onChange">
      <option selected value="__placeholder">Select...</option>
      <option v-for="(item, index) in selectOptions" :key="index" :value="index">{{ item }}</option>
    </select>
    <div v-if="v$.value.$error" class="text-danger">Name field has an error.</div>
  </div>
</template>

<script>
import {useVuelidate} from "@vuelidate/core";

export default {
  name: "FormFieldSelect",
  props: {
    fieldOptions: Object,
  },
  setup() {
    return {v$: useVuelidate()}
  },
  data() {
    return {
      value: '__placeholder',
      selectOptions: {}
    }
  },
  mounted() {
    if (this.fieldOptions.selectOptions.constructor.name === 'Promise') {
      let thisReference = this;
      this.fieldOptions.selectOptions.then((result) => {
        thisReference.selectOptions = result;
      });
    } else {
      this.selectOptions = this.fieldOptions.selectOptions;
    }
  },
  computed: {
    title() {
      if (this.fieldOptions.title !== null) {
        return this.fieldOptions.title;
      }
      return '---';
    },
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
      this.v$.$reset();
      if (value === null) {
        this.value = '__placeholder';
        return;
      }
      this.value = value;
    },
    async validate() {
      let validationResult = await this.v$.$validate();
      if (validationResult) {
        this.v$.$reset();
      }

      return validationResult;
    },
    onChange() {
      if (this.value !== '__placeholder') {
        this.$emit('valueSelected', this)
      }
    }
  },
  expose: ['getValue', 'setValue', 'validate'],
}
</script>

<style scoped>

</style>