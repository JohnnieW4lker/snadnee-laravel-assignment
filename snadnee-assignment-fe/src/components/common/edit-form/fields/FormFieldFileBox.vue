<template>
  <div class="form-group">
    <div>{{ title }}</div>
    <!--    TODO UPGRADES scroolbar-->
    <!--    TODO UPGRADES file size-->
    <div class="overflow-y-scroll" style="max-height: 150px">
      <UploadedFileListItem
          v-for="file in uploadedFiles" :key="file.id"
          :uploaded-file-id="file.id"
          :uploaded-file-name="file.originalName"
          :uploaded-file-type="file.fileMimeType"
          @file-delete-request="deleteFile"
          @file-download-request="downloadFile"
          class="mb-3"
      ></UploadedFileListItem>
      <UploadedFileListItem
          v-for="(file, key) in uploadQueue" :key="key"
          :uploaded-file-id="key"
          :uploaded-file-name="file.name"
          :uploaded-file-type="file.type"
          @file-delete-request="deleteFileFromQueue"
          class="mb-3"
      ></UploadedFileListItem>
    </div>
    <div class="mb-3">
      <input class="form-control" type="file" multiple ref="fileField" @change="updateInputFiles">
    </div>
    <div>
      <button type="button" class="btn btn-custom-accent" @click="sendToUploadQueue" style="width: 6.5rem; height: 2.5rem">
        <span v-if="!isUploading">{{ $t('form.field.file_box.upload') }}</span>
        <img class="h-100" src="@/assets/loading.gif" v-if="isUploading" alt="uploading">
      </button>
    </div>
  </div>
</template>

<script>

import UploadedFileListItem from "@/components/admin/common/edit-form/fields/elements/UploadedFileListItem.vue";

export default {
  name: "FormFieldFileBox",
  components: {UploadedFileListItem},
  props: {
    fieldOptions: Object,
  },
  data() {
    return {
      uploadedFiles: [],
      uploadQueue: [],
      inputFiles: [],
      isUploading: false
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
  methods: {
    updateInputFiles(event) {
      this.inputFiles = event.target.files;
    },
    getValue() {
      return this.uploadedFiles;
    },
    setValue(value) {
      if (value === null) {
        this.uploadedFiles = [];
      }
      this.uploadedFiles = value;
    },
    async upload() {
      if (this.isUploading || this.uploadQueue.length === 0) {
        return;
      }

      this.isUploading = true;
      let selfReference = this;
      const formData = new FormData();
      this.uploadQueue.forEach((file) => {
        formData.append("files[]", file);
      })

      await this.$store.dispatch('uploadFiles', formData)
          .then((result) => {
            selfReference.isUploading = false;
            selfReference.inputFiles = [];
            selfReference.uploadQueue = [];
            result.data.forEach((resultFile) => {
              selfReference.uploadedFiles.push(resultFile)
            })
          });
    },
    deleteFile(uploadedFile) {
      let targetKey;
      this.uploadedFiles.forEach((file, key) => {
        if (uploadedFile.getFileId() === file.id) {
          targetKey = key;
        }
      })
      this.uploadedFiles.splice(targetKey, 1);
    },
    downloadFile(uploadedFile) {
      this.$store.dispatch('downloadFile', {
        fileId: uploadedFile.getFileId(),
        fileType: uploadedFile.getFileType(),
        fileName: uploadedFile.getFileName()
      })
    },
    deleteFileFromQueue(uploadedFile) {
      this.uploadQueue.splice(uploadedFile.getFileId(), 1);
    },
    sendToUploadQueue() {
      if (this.inputFiles.length === 0) {
        return;
      }
      Array.from(this.inputFiles).forEach((file) => {
        this.uploadQueue.push(file)
      })
      this.inputFiles = [];
      this.$refs.fileField.value = null;
    },
    async validate() {
      return true;
    }
  },
  expose: ['getValue', 'setValue', 'validate', 'upload']
}
</script>

<style scoped>

</style>