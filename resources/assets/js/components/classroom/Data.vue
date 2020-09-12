<template>
  <div id="data-project" v-if="classrooms && getUser">
    <button
      class="btn btn-default search"
      type="button"
      id="activeSearch"
      data-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false"
      v-if="this.windowWidth > 767"
    >
      <i class="material-icons">search</i>
    </button>
    <input
      type="text"
      v-model="search"
      class="filter-search dropdown-menu dropdown-menu-right"
      aria-labelledby="activeSearch"
      placeholder="ค้นหาชื่อโครงงาน / รายวิชา"
    />

    <input
      type="text"
      v-model="search"
      class="filter-search-resp"
      placeholder="ค้นหาชื่อโครงงาน / รายวิชา"
      v-if="this.windowWidth <= 767"
    />

    <div class="row">
      <div
        class="col-md-4 space-jy col-resp-50"
        v-for="(classroom,index) in filterSearch"
        :key="index"
      >
        <div class="card-project">
          <div class="btn-group dropright" v-if="isTeacher">
            <button
              class="btn btn-default dropdown-toggle more-menu"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <i class="material-icons">more_vert</i>
            </button>
            <div class="black"></div>
            <div class="dropdown-menu drop-list">
              <div class="test">
                <a
                  class="dropdown-item"
                  data-toggle="modal"
                  data-target="#editProject"
                  v-on:click="editdata(classroom.id)"
                >
                  <img :src="`${baseUrl}/images/Icons/ProjectEdit.png`" />
                  <h4>แก้ไข</h4>
                </a>
                <a
                  class="dropdown-item"
                  href="#"
                  data-toggle="modal"
                  v-on:click="deleteClass(classroom.id,index)"
                >
                  <img :src="`${baseUrl}/images/Icons/ProjectDelete.png`" />
                  <h4>ลบ</h4>
                </a>
              </div>
            </div>
          </div>

          <!-- Modal -->
          <div
            class="modal fade"
            id="editProject"
            tabindex="-1"
            role="dialog"
            aria-labelledby="editProject"
            aria-hidden="true"
          >
            <model-edit :idedit="idedit"></model-edit>
          </div>
          <div class="body-project">
            <router-link :to="classroom.id + '/main/post'">
              <h3 class="name">{{ classroom.name }}</h3>
            </router-link>
          </div>
          <div class="row" style="margin:0;">
            <div class="col-6">
              <h4 class="codeJoin">รหัส {{classroom.join_code}}</h4>
            </div>
            <div class="col-6 right">
              <h4 class="codeJoin" style>ชั้นปีที่ {{ classroom.grade }}</h4>
            </div>
          </div>
          <div class="detail">
            <h4>ภาคเรียนที่ {{classroom.semester}} / {{classroom.year}}</h4>
            <h4 class="group">{{classroom.group.length}} กลุ่ม</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import model_edit from "../classroom/Edit";
import swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      idedit: "",
      search: "",
      baseUrl: this.$store.getters.baseUrl,
      classrooms: [],
      status: false,
      show_join: false,
      user: this.getUser,
      windowWidth: window.innerWidth,
      swal_config: {
        title: "ต้องการลบ โครงงาน/รายวิชา หรือไม่ ?",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "ยกเลิก",
        confirmButtonText: "ยืนยัน",
        showCancelButton: true,
        reverseButtons: true
      }
    };
  },
  components: {
    "model-edit": model_edit
  },
  computed: {
    ...mapGetters(["isTeacher", "getUser"]),
    filterSearch() {
      return this.classrooms.filter(classroom => {
        return classroom.name.toLowerCase().match(this.search.toLowerCase());
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
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  methods: {
    deleteClass(id, index) {
      var token = this.$auth.getToken();
      var self = this;
      swal(this.swal_config)
        .then(() => {
          axios
            .delete("api/classroom/" + id, {
              headers: {
                Authorization: "Bearer " + token
              }
            })
            .then(response => {
              this.classrooms.splice(index, 1);
            });
        })
        .catch(swal.noop);
    },
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
    },
    editdata(id) {
      this.idedit = id;
      this.$emit("editdata");
    }
  }
};
</script>
