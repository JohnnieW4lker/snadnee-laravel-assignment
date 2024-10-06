<template>
  <form class="h-100">
    <div v-for="(line, index) in fieldConfiguration" :key="index"
         :class="'row' + ' row-cols-' + Object.keys(line).length"
         class="mb-4"
    >
      <component v-for="(field) in line"
                 :is="field.component"
                 :key="field.key"
                 :field-options="field.fieldOptions"
                 :ref="`field_${field.key}`"
      ></component>
    </div>
    <button type="button" class="btn btn-custom-primary mb-3" @click="submit" style="height: 2.3rem">
      <span v-if="!beingSubmitted">Submit</span>
      <img class="h-100" src="@/assets/loading.gif" v-if="beingSubmitted" alt="uploading">
    </button>
  </form>
</template>

<script>

export default {
  name: "UniversalForm",
  props: {
    fieldConfiguration: Object,
  },
  data() {
    return {
      submitCallback: null,
      beingSubmitted: false
    }
  },
  methods: {
    async submit() {
      if (this.beingSubmitted) {
        return;
      }
      this.beingSubmitted = true;
      let keyValues = {};
      let validationPromises = [];
      this.fieldConfiguration.forEach((row) => {
        for (const key in row) {
          let field = this.$refs[`field_${row[key].key}`][0];
          validationPromises.push(field.validate());
          keyValues[row[key].key] = field.getValue();
        }
      })
      let values = await Promise.all(validationPromises);

      if (values.every(v => v === false)) {
        this.beingSubmitted = false;
        return;
      }

      this.submitCallback(keyValues)
          .finally(() => {
            this.beingSubmitted = false
          });
    },
    setSubmitCallback(submitCallback) {
      this.submitCallback = submitCallback;
    },
    prefill(values) {
      this.fieldConfiguration.forEach((row) => {
        for (const key in row) {
          let resolvedValues = this.resolve(key, values);
          if (typeof row[key].valueFormatter === 'function') {
            this.$refs[`field_${row[key].key}`][0].setValue(row[key].valueFormatter(resolvedValues));
          } else {
            this.$refs[`field_${row[key].key}`][0].setValue(resolvedValues);
          }
        }
      })
    },
    empty() {
      this.fieldConfiguration.forEach((row) => {
        for (const key in row) {
          this.$refs[`field_${row[key].key}`][0].setValue(null);
        }
      })
    },
    resolve(path, obj, separator = '.') {
      if (path.split(separator).length === 1) {
        if (Array.isArray(obj)) {
          return obj.map((value) => value[path])
        } else {
          return obj[path];
        }
      }
      let firstAccessors = path.split(separator).slice(0, -1).join(separator);
      if (!Array.isArray(firstAccessors)) {
        firstAccessors = [firstAccessors];
      }
      let lastAccessor = path.split(separator).slice(-1)
      let reducedProperties = firstAccessors.reduce((prev, curr) => prev?.[curr], obj)
      if (Array.isArray(reducedProperties)) {
        return reducedProperties.map((value) => value[lastAccessor])
      }
      return reducedProperties[lastAccessor];
    },
  },
  expose: [
    'setSubmitCallback',
    'prefill',
    'empty'
  ],
}
</script>

<style scoped>

</style>