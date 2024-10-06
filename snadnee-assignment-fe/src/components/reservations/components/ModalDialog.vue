<template>
  <div class="modal fade" :id="title + '-modal'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">{{ title }}</h5>
          <button type="button" @click="$emit('closeRequest')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <slot></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from "bootstrap";

export default {
  name: "ModalDialog",
  props: {
    show: {
      type: Boolean,
      required: true
    },
    title: {
      type: String,
      required: false
    }
  },
  data() {
    return {
      modal: null
    }
  },
  watch: {
    show(newValue){
      if (newValue === true) {
        this.modal = new Modal(document.getElementById(this.title + '-modal'),{backdrop: 'static', keyboard: false});
        this.modal.show();
      } else {
        this.modal.hide();
        this.modal = null;
      }
    }
  }
};
</script>

<style scoped>

</style>