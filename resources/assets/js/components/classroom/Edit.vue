<template>
  <div id="edit-project" class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="newProject">แก้ไขโครงงาน / รายวิชา</h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">✕</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 name">
            <h3>ชื่อโครงงาน</h3>
            <input
              type="text"
              placeholder="ชื่อโครงงาน"
              v-model="classroom.name"
              :maxlength="max"
              focus
            />
            <p class="text-danger" v-for="error in classroom_errors.name" :key="error.id">
              <span v-if="classroom_errors.name">{{ error }}</span>
            </p>
            <h5>{{classroom.name.length}}/50</h5>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 year">
            <h3>ปีการศึกษา</h3>
            <div class="select">
              <v-select :options="years" placeholder="เลือก..." v-model="classroom.year"></v-select>
            </div>
            <p class="text-danger" v-for="error in classroom_errors.name" :key="error.id">
              <span v-if="classroom_errors.name">{{ error }}</span>
            </p>
          </div>
          <div class="col-md-6 semester">
            <h3>ภาคการศึกษา</h3>
            <div class="select">
              <v-select :options="semesters" placeholder="เลือก..." v-model="classroom.semester"></v-select>
            </div>
            <p class="text-danger" v-for="error in classroom_errors.name" :key="error.id">
              <span v-if="classroom_errors.name">{{ error }}</span>
            </p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-md-6 right">
            <button
              type="button"
              class="btn btn-default submit cancel"
              data-dismiss="modal"
              v-if="this.windowWidth > 767"
            >
              <h3>ยกเลิก</h3>
            </button>
          </div>
          <div class="col-md-6 left center-resp">
            <button
              type="button"
              class="btn btn-default submit"
              data-dismiss="modal"
              @click="update"
              :disabled="(classroom.name == '' || classroom.semester == '' || classroom.year == '') && hello == true"
            >
              <h3>ตกลง</h3>
            </button>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from "vue-select";
export default {
  components: {
    "v-select": vSelect
  },
  props: {
    idedit: ""
  },
  data() {
    return {
      editid: "",
      teacher: "",
      student: "",
      max: 50,
      hello: true,
      years: [new Date().getFullYear() + 542, new Date().getFullYear() + 543],
      semesters: ["-", "1", "2", "S"],
      classroom: {
        name: "",
        semester: "",
        year: ""
      },
      classroom_errors: [],
      windowWidth: window.innerWidth,
      token: this.$auth.getToken()
    };
  },
  watch: {
    idedit(oldVal) {
      this.editid = oldVal;
      axios
        .get("api/classroom/" + oldVal + "/get", {
          headers: {
            Authorization: "Bearer " + this.token
          }
        })
        .then(response => {
          this.classroom.name = response.data.name;
          this.classroom.semester = response.data.semester;
          this.classroom.year = response.data.year;
        });
    }
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  methods: {
    update() {
      this.hello = false;
      var data = {
        name: this.classroom.name,
        semester: this.classroom.semester,
        year: this.classroom.year
      };
      axios
        .patch("api/classroom/" + this.editid, data, {
          headers: {
            Authorization: "Bearer " + this.token
          }
        })
        .then(response => {
          this.hello = true;
          this.$router.go("/");
        })
        .catch(error => {
          this.hello = true;
        });
    }
  }
};
</script>