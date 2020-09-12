<template>
  <div id="create-post">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title" id="newProject">โพสต์ข่าวสาร</h2>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" data-dismiss="modal">✕</span>
          </button>
        </div>
        <div class="modal-body">
          <upload
            :callback="'/api/attachment/upload'"
            v-on:complete="uploadedFile"
            v-on:remove="removeFile"
          ></upload>
          <post :detail="form" :errors="post_errors" v-on:post="post"></post>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import upload from "../block/upload.vue";
import post from "../block/post.vue";

export default {
  data() {
    return {
      form: {
        detail: "",
        display: "",
        hello: true
      },
      uploaded_files: [],
      post_errors: []
    };
  },
  components: {
    upload,
    post
  },
  methods: {
    store(token, classroom_id, form) {
      var data = {
        post: form.detail,
        display: form.display.value,
        classroom_id: classroom_id,
        files: this.uploaded_files
      };

      axios
        .post("api/post", data, {
          headers: {
            Authorization: "Bearer " + token
          }
        })
        .then(response => {
          // console.log(form.display.value);
          this.$router.go("post");
        })
        .catch(error => {
          this.post_errors = error.response.data.errors;
        });
    },
    post(form) {
      var classroom_id = this.$route.params.id;
      var token = this.$auth.getToken();
      form.hello = false;

      axios
        .get("api/classroom/" + classroom_id, {
          headers: {
            Authorization: "Bearer " + token
          }
        })
        .then(response => {
          this.store(token, classroom_id, form);
          form.hello = true;
        })
        .catch(error => {
          form.hello = true;
        });
    },
    uploadedFile(file) {
      this.uploaded_files = file;
    },
    removeFile(file) {
      this.uploaded_files = file;
    }
  }
};
</script>