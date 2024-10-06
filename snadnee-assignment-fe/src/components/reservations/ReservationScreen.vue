<template>
  <div class="mt-4">
    <AlertPopup ref="alert"></AlertPopup>
    <div class="d-flex justify-content-between">
      <h1>Reservations</h1>
      <div class="d-flex">
        <div class="my-auto mx-4">{{ loggedUserName }}</div>
        <button class="btn btn-secondary my-3" @click="handleLogout">Logout</button>
      </div>
    </div>
    <TableSelectionGrid v-on:reservationMade="handleReservationMade"
                        ref="tableSelectionGrid"></TableSelectionGrid>
    <UserReservationGrid v-on:reservationCancelled="handleReservationCanceled"
                         ref="userReservationGrid"></UserReservationGrid>
  </div>
</template>

<script>

import TableSelectionGrid from "@/components/reservations/TableSelectionGrid.vue";
import UserReservationGrid from "@/components/reservations/UserReservationGrid.vue";
import AlertPopup from "@/components/common/AlertPopup.vue";

export default {
  name: 'ReservationScreen',
  components: {AlertPopup, UserReservationGrid, TableSelectionGrid},
  computed: {
    loggedUserName () {
      return this.$store.getters.account.firstName + ' ' + this.$store.getters.account.lastName;
    }
  },
  methods: {
    handleReservationMade() {
      this.$refs.alert.success('Reservation created!', 3000);
      this.$refs.userReservationGrid.loadFromServer();
    },
    handleReservationCanceled() {
      this.$refs.alert.success('Reservation canceled successfully!', 3000);
      this.$refs.tableSelectionGrid.search();
    },
    handleLogout() {
      this.$store.dispatch('logout');
    }
  },
}
</script>

<style>

</style>