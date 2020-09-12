<template>
  <div id="project" v-if="classrooms && getUser">
    <div v-if="isConfirm">
      <first-page></first-page>
    </div>
    <div v-if="isWait">
      <wait-page></wait-page>
    </div>
    <div v-else>
      <span v-if="isTeacher">
        <span v-for="user in users" v-bind:key="user.id">
          <span v-if="user.role.name == 'Wait'">
            <accept-page :user="user"></accept-page>
          </span>
        </span>
      </span>
      <div class="title">
        <h1>โครงงาน / รายวิชาทั้งหมด</h1>
        <hr />
      </div>
      <data-project></data-project>
      <button
        type="button"
        class="btn btn-default add-project"
        data-toggle="modal"
        data-target="#addProject"
        v-if="isTeacher"
      >
        <img :src="`${baseUrl}/images/Icons/add.png`" />
        <h2 class="text-tooltip">สร้างโครงงาน</h2>
      </button>

      <button
        type="button"
        class="btn btn-default btn-joinClass"
        @click="showJoin"
        v-if="isStudent"
      >
        <img :src="`${baseUrl}/images/Icons/IconPassword.png`" />
        <h2 class="text-tooltip">เข้าร่วมโครงงาน / รายวิชา</h2>
      </button>
      <button
        type="button"
        class="btn btn-default btn-joinClass teacher"
        @click="showJoin"
        v-if="isTeacher"
      >
        <img :src="`${baseUrl}/images/Icons/IconPassword.png`" />
        <h2 class="text-tooltip">เข้าร่วมโครงงาน / รายวิชา</h2>
      </button>

      <div
        class="modal fade"
        id="addProject"
        tabindex="-1"
        role="dialog"
        aria-labelledby="newProject"
        aria-hidden="true"
      >
        <model-crate></model-crate>
      </div>
    </div>
  </div>
  <loading v-else></loading>
</template> 

<script>
import loading from "../elements/loading";
import { mapGetters } from "vuex";
import model_crate from "../classroom/Create.vue";
import data_project from "../classroom/Data.vue";
import wait_page from "../wait/wait.vue";
import first_page from "../wait/first.vue";
import accept_page from "../wait/accept.vue";
import swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

export default {
  data() {
    return {
      classrooms: "",
      user: this.getUser,
      users: "",
      baseUrl: this.$store.getters.baseUrl,
      token: this.$auth.getToken(),
      show_join: false
    };
  },
  computed: mapGetters([
    "isTeacher",
    "isStudent",
    "getUser",
    "isWait",
    "isConfirm"
  ]),
  components: {
    "model-crate": model_crate,
    "data-project": data_project,
    "wait-page": wait_page,
    "first-page": first_page,
    "accept-page": accept_page,
    loading
  },
  created() {
    axios
      .get("api/members", {
        headers: {
          Authorization: "Bearer " + this.token
        }
      })
      .then(response => {
        this.users = response.data;
      });
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
  },
  methods: {
    showJoin() {
      var self = this;
      swal({
        title: "กรุณาระบุรหัสเพื่อเข้าร่วมโครงงาน / รายวิชา",
        input: "text",
        inputPlaceholder: "ระบุรหัสโครงงาน / รายวิชา",
        customClass: "test",
        showCancelButton: true,
        reverseButtons: true,
        inputValidator: function(value) {
          return new Promise(function(resolve, reject) {
            if (value) {
              resolve();
            } else {
              reject("กรุณาระบุรหัสเพื่อเข้าร่วมโครงงาน / รายวิชา");
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
          this.$router.go("/");
        });
    }
  }
};
</script>
