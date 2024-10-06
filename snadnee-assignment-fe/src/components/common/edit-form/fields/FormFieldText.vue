<template>
  <div>
    <div>{{ title }}</div>
    <textarea
        class="form-control"
        style="resize: none"
        v-model="v$.value.$model"
        v-on:keyup.enter="$emit('valueSelected', this)"
    />
    <div v-if="v$.value.$error" class="text-danger">Name field has an error.</div>
  </div>
</template>

<script>
import {useVuelidate} from "@vuelidate/core";

export default {
  name: "FormFieldText",
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
  validations() {
    if (typeof this.fieldOptions.validations !== "undefined") {
      return {value: this.fieldOptions.validations};
    }
    return {value: {}};
  },
  computed: {
    title() {
      if (this.fieldOptions.title !== null) {
        return this.fieldOptions.title;
      }
      return '---';
    }
  },
  methods: {
    getValue(){
      return this.value;
    },
    setValue(value){
      this.value = value;
      this.v$.$reset();
    },
    async validate() {
      return await this.v$.$validate();
    }
  },
  expose: ['getValue', 'setValue', 'validate']
}
</script>

<style scoped>

</style>