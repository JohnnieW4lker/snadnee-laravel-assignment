<template>
  <div>
    <div class="d-flex justify-content-evenly">
      <div class="seat" v-for="i in Math.floor(tableConfiguration.seats / 2)" :key="i"></div>
    </div>
    <div class="p-5 rounded" :class="backgroundClass" @click="handleClick">
      <b>{{ tableConfiguration.number }}</b>
    </div>
    <div class="d-flex justify-content-evenly">
      <div class="seat" v-for="i in Math.ceil(tableConfiguration.seats / 2)" :key="i"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "TableIcon",
  props: {
    tableConfiguration: Object,
  },
  data() {
    return {};
  },
  computed: {
    backgroundClass() {
      return this.tableConfiguration.availability === 'available' ? 'available-seat' : 'unavailable-seat';
    }
  },
  methods: {
    handleClick() {
      if (this.tableConfiguration.availability === 'available'){
        this.$emit('tableSelected', this.tableConfiguration);
      }
    }
  }
}
</script>

<style scoped>
.seat {
  width: 25px;
  height: 25px;
  border-radius: 10px;
  background-color: darkcyan;
  margin: 5px;
}
.available-seat {
  background-color: gray;
  cursor: pointer;
}

.available-seat:hover {
  background-color: #406e40;
  cursor: pointer;
}

.unavailable-seat {
  background-color: darkred;
}
</style>