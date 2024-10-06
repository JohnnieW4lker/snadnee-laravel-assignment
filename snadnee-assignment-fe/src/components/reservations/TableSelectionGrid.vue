<template>
<div class="mb-4">
  <div class="d-flex my-3">
    <div>
      <label for="length" class="form-label">Date</label>
      <VueDatePicker v-model="v$.reservationDatetime.$model"
                     :model-type="'yyyy-MM-dd HH:mm'"
                     :enable-time-picker="true"
                     :time-picker-inline="true"
                     :minutes-increment="30"
                     :min-date="defaultDateTime()"
                     :min-time="{hours: defaultDateTime().getHours(), minutes: defaultDateTime().getMinutes()}"
                     :start-time="{hours: defaultDateTime().getHours(), minutes: defaultDateTime().getMinutes()}"
                     :auto-apply="true"></VueDatePicker>
    </div>
    <div class="mx-4">
      <label for="length" class="form-label">Length</label>
      <input name="length" type="number" class="form-control" step="30" min="30" max="120" required v-model.trim="v$.reservationLength.$model" v-on:keyup.enter="search">
    </div>
    <div class="form-group col-1">
      <label class="form-label">&nbsp;</label>
      <button @click="search" class="btn btn-custom-primary form-control">Find table</button>
    </div>
  </div>

  <div class="tables-wrapper p-4 w-100 rounded">
    <div class="row row-cols-4 gy-5" v-if="!loading">
      <div class="col d-flex justify-content-center" v-for="(item, key) in tables" :key="key">
        <TableIcon :table-configuration="item" v-on:tableSelected="handleTableSelected"></TableIcon>
      </div>
    </div>
    <div class="d-flex justify-content-center h-100" v-if="loading">
      <img class="m-auto" src="@/assets/loading.gif" style="width: 80px; height: 80px;" alt="loading"/>
    </div>
  </div>
  <ModalDialog title="New reservation" :show="showMakeReservationForm" v-on:closeRequest="showMakeReservationForm = false">
    <UniversalForm :field-configuration="formFieldConfiguration" :ref="'form'"></UniversalForm>
    <div class="loadingOverlay" v-if="submitWaiting">
      <div class="d-flex w-100 justify-content-center">
        <img src="@/assets/loading-button.gif" alt="loading">
      </div>
    </div>
  </ModalDialog>
</div>
</template>

<script>
import TableIcon from "@/components/reservations/components/TableIcon.vue";
import {useVuelidate} from "@vuelidate/core";
import {maxValue, minLength, minValue, required} from "@vuelidate/validators";
import ModalDialog from "@/components/reservations/components/ModalDialog.vue";
import UniversalForm from "@/components/common/edit-form/UniversalForm.vue";
import FormFieldString from "@/components/common/edit-form/fields/FormFieldString.vue";
import FormFieldNumber from "@/components/common/edit-form/fields/FormFieldNumber.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import '@vuepic/vue-datepicker/dist/main.css';

export default {
  name: 'TableSelectionGrid',
  components: {VueDatePicker, UniversalForm, ModalDialog, TableIcon},
  data(){
    return {
      tables: [],
      reservationDatetime: this.formatDateForDefaultPicker(this.defaultDateTime()),
      reservationLength: 30,
      loading: false,
      showMakeReservationForm: false,
      submitWaiting: false,
      formFieldConfiguration: [
        {
          guestFirstName: {
            key: 'guestFirstName',
            fieldOptions: {
              title: 'First Name',
              validations: {
                required,
                minLength: minLength(1)
              },
            },
            component: FormFieldString
          }
        },
        {
          guestLastName: {
            key: 'guestLastName',
            fieldOptions: {
              title: 'Last Name',
              validations: {
                required,
                minLength: minLength(1)
              },
            },
            component: FormFieldString
          }
        },
        {
          reservationDateTime: {
            key: 'reservationDateTime',
            fieldOptions: {
              title: 'Time',
              readonly: true,
              defaultDateTime: (new Date(this.reservationDatetime)).toLocaleString('cs-CZ'),
              validations: {
                required
              },
            },
            component: FormFieldString
          },
          reservationLengthInMinutes: {
            key: 'reservationLengthInMinutes',
            fieldOptions: {
              title: 'Length',
              readonly: true,
              validations: {
                required,
                minValue: minValue(30),
                maxValue: maxValue(120),
              },
            },
            component: FormFieldString
          }
        },
        {
          peopleCount: {
            key: 'peopleCount',
            fieldOptions: {
              title: 'People',
              max: 1,
              min: 1,
              validations: {
                required,
              },
            },
            component: FormFieldNumber
          },
        }
      ],
    };
  },
  setup () {
    return {
      v$: useVuelidate(),
    }
  },
  validations() {
    return {
      reservationDatetime: {
        required,
      },
      reservationLength: {
        required,
        minValue: minValue(30),
        maxValue: minValue(120),
      },
    }
  },
  methods: {
    search() {
      if (this.loading === true) {
        return;
      }

      this.loading = true;
      let utcTime = new Date(this.reservationDatetime);
      utcTime.setHours(utcTime.getHours() + 2);
      this.$store.dispatch('getTableAvailability', {
        reservationDatetime: utcTime.toISOString(),
        reservationLength: this.reservationLength,
      })
          .then((result) => {
            this.tables = result;
            this.loading = false;
          });
    },
    createSubmitEditFormCallback(tableId){
      const showFormProxy = (value) => this.showMakeReservationForm = value;
      const searchProxy = this.search;
      const emitProxy = this.$emit;
      let thisProxy = this;

      return async function submitEditForm(values){
        values.tableId = tableId;
        thisProxy.submitWaiting = true;
        this.$store.dispatch('createReservation', {data: values})
            .then(() => {
              showFormProxy(false);
              searchProxy();
              emitProxy('reservationMade');
              thisProxy.submitWaiting = false;
            })
            .catch((reason) => {
              console.log(reason);
              thisProxy.submitWaiting = false;
            });
      }
    },
    handleTableSelected(tableConfiguration) {
      let formComponent = this.$refs['form'];
      let account = this.$store.state.account;
      this.formFieldConfiguration[3].peopleCount.fieldOptions.max = tableConfiguration.seats;

      formComponent.setSubmitCallback(this.createSubmitEditFormCallback(tableConfiguration.id));
      formComponent.prefill({
        guestFirstName: account.firstName,
        guestLastName: account.lastName,
        reservationDateTime: this.reservationDatetime,
        reservationLengthInMinutes: this.reservationLength,
        peopleCount: 1,
      });

      this.showMakeReservationForm = true;
    },
    defaultDateTime() {
      const now = new Date();
      return new Date(
          now.getFullYear(), now.getMonth(), now.getDate() + 1, 15, 0, 0, 0
      );
    },
    formatDateForDefaultPicker(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');

      return `${year}-${month}-${day} ${hours}:${minutes}`;
    }
  },
  mounted() {
    this.search()
  },
  expose: ['search'],
}
</script>

<style scoped>
.tables-wrapper {
  min-height: 30vh;
  box-shadow: 0 0.5rem 4rem 0px rgba(0, 0, 0, 0.15) !important;
  background-color: #f4f4f4;
}
.loadingOverlay {
  position: absolute;
  width: 100%;
  height: 100%;
  background-color: rgba(85, 85, 85, 0.52);
  top: 0;
  left: 0;
  z-index: 1;
}
</style>