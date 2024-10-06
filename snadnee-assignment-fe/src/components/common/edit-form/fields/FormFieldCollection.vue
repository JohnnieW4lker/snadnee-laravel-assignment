<template>
  <div>
    <div>{{ title }}</div>
    <div class="d-flex">
      <div class="flex-group w-100">
        <component
            :is="fieldOptions.component"
            :field-options="childFieldOptions"
            @value-selected="addTag"
            ref="inputComponent"
        ></component>
      </div>
      <span @click="fieldOptions.component.name !== 'FormFieldSelect' ? addTag($refs.inputComponent) : null" class="input-group-text pointer"><i class="bi bi-plus-lg"></i></span>
    </div>
    <div v-if="warningActive" class="text-danger">{{ warningText }}</div>
    <div class="row row-cols-3">
      <MultiValueTag
          v-for="(value, index) in values"
          v-bind:key="index"
          :ref="`tag_${index}`"
          :tag-value="value"
          :tag-index="index"
          @remove-request="removeTag"
          class="m-1"
      ></MultiValueTag>
    </div>
  </div>
</template>

<script>
import MultiValueTag from "@/components/admin/common/edit-form/fields/elements/MultiValueTag.vue";
import FormFieldSelect from "@/components/admin/common/edit-form/fields/FormFieldSelect.vue";
// TODO nejako oznacit ze sa jedna o kolekciu
// TODO UPGRADE odlisit field options a collection options
// TODO UPGRADE warning do bubliny
// TODO UPGRADE rozlysny value a label tagov, pre entity sa v tagoch nezobrazuju cisla ale nejaky iny field
export default {
  name: "FormFieldCollection",
  components: {MultiValueTag},
  props: {
    fieldOptions: Object
  },
  data() {
    return {
      values: [],
      warningActive: false,
      warningText: ''
    }
  },
  computed: {
    FormFieldSelect() {
      return FormFieldSelect
    },
    title() {
      if (this.fieldOptions.title !== null) {
        return this.fieldOptions.title;
      }
      return '---';
    },
    childFieldOptions() {
      return {
        title: '',
        validations: this.fieldOptions.validations,
        selectOptions: this.fieldOptions.selectOptions,
      };
    },
    allowRepeat() {
      if (typeof this.fieldOptions.allowRepeat !== "undefined") {
        return this.fieldOptions.allowRepeat;
      }
      return true;
    }
  },
  methods: {
    getValue() {
      return this.values;
    },
    // TODO UPGRADES all sets trough value formatter
    setValue(value) {
      if (value == null) {
        this.values = [];
        return;
      }
      this.values = value.map((x) => x);
    },
    removeTag(id) {
      this.warningActive = false;
      this.values.splice(id, 1);
    },
    addTag(input) {
      this.warningActive = false;
      if (this.allowRepeat === false && this.values.includes(input.getValue())) {
        this.warningText = 'form_field_collection.warning.repetition_not_allowed';
        this.warningActive = true;
        input.setValue(null);
        return;
      }

      let component = this;
      input.validate()
          .then((isValid) => {
            if (isValid) {
              component.values.push(input.getValue());
              input.setValue(null);
            }
          });
    },
    async validate() {
      this.warningActive = false;
      return true;
    }
  },
  expose: ['getValue', 'setValue', 'validate']
}
</script>

<style scoped>
.flex-group > input {
  width: 100%;
}
</style>