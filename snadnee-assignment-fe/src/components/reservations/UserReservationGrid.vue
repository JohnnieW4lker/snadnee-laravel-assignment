<template>
  <div>
    <h2>My reservations</h2>
    <EasyDataTable
        theme-color="var(--accent)"
        :loading="loading"
        :headers="headers"
        :items="items"
    >
      <template #loading>
        <img src="@/assets/loading.gif" style="width: 80px; height: 80px;" alt="loading"/>
      </template>
      <template #item-operations="items">
        <div class="d-flex justify-content-evenly">
          <a v-for="(key) in operations" v-bind:key="key" @click="handleOperation(key, items)">
            <img :src="require('@/assets/action-icons/' + key + '.png')" :alt="key" width="15" class="pointer">
          </a>
        </div>
      </template>
      <template #item-guestName="items">
        {{ items.guestLastName }} {{ items.guestFirstName }}
      </template>
      <template #item-reservationDateTime="items">
        {{ formatTimezone(items.reservationDateTime) }}
      </template>
    </EasyDataTable>
    <ModalDialog title="Cancel reservation" :show="showCancelReservationModal"
                 v-on:closeRequest="showCancelReservationModal = false; this.reservationToCancel = null">
      <p>Are you sure you want to cancel this reservation? This action cannot be undone.</p>
      <div class="d-flex">
        <button @click="cancelReservation()" class="btn btn-danger">Yes</button>
        <button @click="showCancelReservationModal = false" class="btn btn-secondary mx-4">No</button>
      </div>
    </ModalDialog>
  </div>
</template>

<script>
import ModalDialog from "@/components/reservations/components/ModalDialog.vue";

export default {
  name: 'UserReservationGrid',
  components: {
    ModalDialog,
    EasyDataTable: window['EasyDataTable'],
  },
  data() {
    return {
      headers: [
        {text: 'Date', value: "reservationDateTime", sortable: true},
        {text: 'People', value: "peopleCount", sortable: true},
        {text: 'Guest', value: "guestName", sortable: true},
        {text: '', value: "operations"},
      ],
      items: [],
      loading: false,
      operations: ['remove'],
      showCancelReservationModal: false,
      reservationToCancel: null,
    }
  },
  methods: {
    async loadFromServer() {
      this.loading = true;

      this.$store.dispatch('getReservations')
          .then((result) => {
            this.items = result;
            this.loading = false;
          });
    },
    handleOperation(operationCode, entry) {
      switch (operationCode) {
        case 'remove':
          this.reservationToCancel = entry;
          this.showCancelReservationModal = true;
          break;
      }
    },
    cancelReservation() {
      this.loading = true;
      this.showCancelReservationModal = false;
      this.$store.dispatch('cancelReservation', this.reservationToCancel)
          .then(() => {
            this.loadFromServer();
            this.$emit('reservationCancelled')
          })
          .catch(() => {
          });
      this.reservationToCancel = null;
    },
    formatTimezone(reservationDateTime) {
      let date = new Date(reservationDateTime)
      date.setHours(date.getHours() - 2);

      return date.toLocaleString('cs-CZ')
    }
  },
  mounted() {
    this.loadFromServer()
  },
  expose: ['loadFromServer'],
}
</script>

<style scoped>

</style>