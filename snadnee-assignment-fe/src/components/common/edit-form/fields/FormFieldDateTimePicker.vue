<template>
  <div>
    <div>{{ title }}</div>
    <VueDatePicker v-model="v$.value.$model"
                   :model-type="'yyyy-MM-dd HH:mm'"
                   :enable-time-picker="true"
                   :time-picker-inline="true"
                   :minutes-increment="fieldOptions.minutesIncrement"
                   :min-date="fieldOptions.defaultDateTime()"
                   :min-time="{hours: fieldOptions.defaultDateTime().getHours(), minutes: fieldOptions.defaultDateTime().getMinutes()}"
                   :start-time="{hours: fieldOptions.defaultDateTime().getHours(), minutes: fieldOptions.defaultDateTime().getMinutes()}"
    ></VueDatePicker>
    <div v-if="v$.value.$error" class="text-danger">Field has an error.</div>
  </div>
</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import {useVuelidate} from "@vuelidate/core";

export default {
  name: "FormFieldDateTimePicker",
  props: {
    fieldOptions: Object,
  },
  components: {
    VueDatePicker
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