<template>
  <div class="row-addfile">
    <h3>รายละเอียด</h3>
    <vue-clip ref="vc" :options="options" :on-complete="uploadComplete">
      <template slot="clip-uploader-action">
        <div class="upload-drop">
          <div class="dz-message">
            <div class="addfile" style="display:inline-block; float:right">
              <img :src="`${baseUrl}/../images/Icons/AddFile.png`" />
              <h3 v-if="this.windowWidth > 767">แนบไฟล์</h3>
            </div>
          </div>
        </div>
      </template>

      <template slot="clip-uploader-body" slot-scope="props">
        <div v-for="file in props.files" :key="file.id">
          <div v-if="file.status != 'success'">
            <div @click="removeFile(file)">
              {{ file.name }} {{ file.status }}
              <div class="progress">
                <div
                  class="progress-bar"
                  role="progressbar"
                  :aria-valuenow="file.progress"
                  aria-valuemin="0"
                  aria-valuemax="100"
                  v-bind:style="{width: file.progress + '%'}"
                >
                  <span class="sr-only">{{ file.progress }} Complete</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </vue-clip>
    <div v-bind:class="{'is-active' : active}">
      <div class="upload-list" v-for="file in uploaded_files" :key="file.id">
        <h5>{{ file.name }}</h5>
        <button class="btn btn-default removeFile" @click="removeFile(file)">
          <h5>delete</h5>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["callback", "uploadFiles"],
  data() {
    return {
      active: false,
      baseUrl: this.$store.getters.baseUrl,
      uploaded_files: [],
      options: {
        url: this.callback,
        paramName: "file",
        headers: {
          Authorization: "Bearer " + this.$auth.getToken()
        }
      },
      windowWidth: window.innerWidth
    };
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  watch: {
    uploadFiles: function() {
      this.uploaded_files = this.uploadFiles;
    }
  },
  methods: {
    removeFile(file) {
      var index = this.uploaded_files.findIndex(
        x => x.filename == file.filename
      );
      this.uploaded_files.splice(index, 1);
      this.$emit("remove", this.uploaded_files);
    },
    uploadComplete(file, status, xhr) {
      this.uploaded_files.push(JSON.parse(xhr.response));
      this.$emit("complete", this.uploaded_files);
    }
  }
};
</script>