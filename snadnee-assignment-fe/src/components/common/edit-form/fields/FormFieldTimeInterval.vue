<template>
  <div class="form-group">
    <div>{{ title }}</div>
    <div class="d-flex">
      <div>
        <div>Years:</div>
        <input
            v-model="years"
            class="form-control"
            type="number"
            min="0"
        >
      </div>
      <div>
        <div>Months:</div>
        <input
            v-model="months"
            class="form-control"
            type="number"
            placeholder="Months"
            min="0"
        >
      </div>
      <div>
        <div>Days:</div>
        <input
            v-model.trim="days"
            class="form-control"
            type="number"
            placeholder="Days"
            min="0"
        >
      </div>
      <div>
        <div>Hours:</div>
        <input
            v-model.trim="hours"
            class="form-control"
            type="number"
            placeholder="Hours"
            min="0"
        >
      </div>
    </div>
    <div v-if="v$.value.$error" class="text-danger">Name field has an error.</div>
  </div>
</template>

<script>
import {useVuelidate} from "@vuelidate/core";

export default {
  name: "FormFieldTimeInterval",
  props: {
    fieldOptions: Object,
  },
  setup() {
    return {v$: useVuelidate()}
  },
  data() {
    return {
      value: '',
      years: 0,
      months: 0,
      days: 0,
      hours: 0
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
      this.v$.$reset();
      this.value = value;
      if (value === '' || value === null) {
        this.years = 0;
        this.months = 0;
        this.days = 0;
        this.hours = 0;
      } else {
        //P1Y0M0DT0H0M0S
        this.years = value.match(/(\d+)Y/)[1]
        this.months = value.match(/(\d+)M/)[1]
        this.days = value.match(/(\d+)DT/)[1]
        this.hours = value.match(/(\d+)H/)[1]
      }
    },
    async validate() {
      let validationResult = await this.v$.$validate();
      if (validationResult) {
        this.v$.$reset();
      }

      return validationResult;
    },
    getIntervalString() {
      return 'P' + this.years + 'Y' + this.months + 'M' + this.days + 'DT' + this.hours + 'H0M0S';
    }
  },
  watch: {
    years() {
      this.value = this.getIntervalString();
      this.v$.value.$touch();
    },
    months(){
      this.value = this.getIntervalString();
      this.v$.value.$touch();
    },
    days(){
      this.value = this.getIntervalString();
      this.v$.value.$touch();
    },
    hours(){
      this.value = this.getIntervalString();
      this.v$.value.$touch();
    }
  },
  expose: ['getValue', 'setValue', 'validate']
}
</script>

<style scoped>

</style>