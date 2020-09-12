<template>
  <div id="close">
    <router-link to="./progress" class="close-page" v-if="this.windowWidth > 767">✕</router-link>
    <assignment-post
      v-if="getRole != 'is_student'"
      :detail="form"
      v-on:submit="add"
      :errors="post_errors"
    ></assignment-post>
    <assignment-post v-else :detail="form" v-on:submit="addstu" :errors="post_errors"></assignment-post>
    <div class="control-area"></div>
  </div>
</template>

<script>
import upload from "../block/upload.vue";
import assignmentPost from "../block/assignment.vue";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      form: {
        title: "",
        detail: "",
        duedate: ""
      },
      classroom_id: this.$route.params.id,
      token: this.$auth.getToken(),
      uploaded_files: [],
      post_errors: [],
      windowWidth: window.innerWidth
    };
  },
  computed: mapGetters(["getRole"]),
  methods: {
    add(form, group_id) {
      var data = {
        title: form.title,
        detail: form.detail,
        duetime: form.duedate,
        classroom_id: this.classroom_id,
        files: this.uploaded_files,
        group_id: group_id.map(e => e.value)
      };

      axios
        .post(`api/classroom/${this.classroom_id}/assignment`, data, {
          headers: {
            Authorization: "Bearer " + this.token
          }
        })
        .then(response => {
          this.$router.push(`progress`);

          axios
            .post("api/send-mail", data, {
              headers: {
                Authorization: "Bearer " + this.token
              }
            })
            .catch(error => {
              this.post_errors = error.response.data.errors;
            });
        })
        .catch(error => {
          this.post_errors = error.response.data.errors;
        });
    },
    addstu(form, group_id) {
      var data = {
        title: form.title,
        detail: form.detail,
        duetime: form.duedate,
        classroom_id: this.classroom_id,
        files: this.uploaded_files,
        group_id: [group_id.value]
      };

      axios
        .post(`api/classroom/${this.classroom_id}/assignment`, data, {
          headers: {
            Authorization: "Bearer " + this.token
          }
        })
        .then(response => {
          this.$router.push(`replyProgress/` + response.data.id);
          // this.$router.go(-1);
        })
        .catch(error => {
          this.post_errors = error.response.data.errors;
        });
    },
    uploadedFile(file) {
      this.uploaded_files = file;
    },
    removeFile(file) {
      this.uploaded_files = file;
    },
    back() {
      this.$router.go(-1);
    }
  },
  components: {
    upload,
    assignmentPost: assignmentPost
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  }
};
</script>

