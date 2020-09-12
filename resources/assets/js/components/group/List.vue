<template>
  <div id="group-page" v-if="classrooms && getUser">
    <data-project></data-project>
    <router-link to="createGroup">
      <button type="button" class="btn btn-default add-group" v-if="isStudent">
        <img :src="`${baseUrl}/images/Icons/add.png`" />
        <h2 class="text-tooltip">สร้างกลุ่ม</h2>
      </button>
    </router-link>
  </div>
  <loading v-else></loading>
</template> 

<script>
import loading from "../elements/loading";
import swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { mapGetters } from "vuex";
import model_crate from "../group/Create.vue";
import data_project from "../group/Data.vue";

export default {
  data() {
    return {
      classrooms: "",
      status: false,
      show_join: false,
      user: this.getUser,
      groups: [],
      baseUrl: this.$store.getters.baseUrl
    };
  },
  computed: mapGetters(["isStudent", "getUser"]),
  components: {
    "model-crate": model_crate,
    "data-project": data_project,
    loading
  },
  methods: {
    showJoin() {
      var self = this;
      swal({
        title: "Enter code to join class",
        input: "text",
        inputPlaceholder: "Enter code",
        customClass: "test",
        showCancelButton: true,
        inputValidator: function(value) {
          return new Promise(function(resolve, reject) {
            if (value) {
              resolve();
            } else {
              reject("Please enter the code.");
            }
          });
        }
      })
        .then(function(code) {
          self.requestJoin(code);
        })
        .catch(swal.noop);
    },
    requestJoin(code) {
      var token = this.$auth.getToken();
      var data = {
        code: code
      };
      axios
        .post("api/classroom/join", data, {
          headers: {
            Authorization: "Bearer " + token
          }
        })
        .then(response => {
          this.$router.push("classroom/" + response.data.classroom_id);
        });
    }
  },
  mounted() {
    var token = this.$auth.getToken();
    this.$store.dispatch("getPersonal");

    axios
      .get("api/classroom", {
        headers: {
          Authorization: "Bearer " + token
        }
      })
      .then(response => {
        this.classrooms = response.data;
      });
  }
};
</script>