<template>
  <div id="create-project" class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="newProject">สร้างโครงงาน / รายวิชาใหม่</h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">✕</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-8 name">
            <h3>ชื่อโครงงาน / รายวิชา</h3>
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
          <div class="col-md-4 study-year">
            <h3>นักศึกษา</h3>
            <div class="select">
              <v-select multiple :options="students" placeholder="เลือก..." v-model="student"></v-select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 teacher">
            <h3>อาจารย์ที่ปรึกษา</h3>
            <div class="select">
              <v-select multiple :options="Teacher" placeholder="เลือก..." v-model="memteacher"></v-select>
            </div>
          </div>
          <div class="col-md-3 year">
            <h3>ปีการศึกษา</h3>
            <div class="select">
              <v-select :options="year" placeholder="เลือก..." v-model="classroom.year"></v-select>
            </div>
            <p class="text-danger" v-for="error in classroom_errors.name" :key="error.id">
              <span v-if="classroom_errors.name">{{ error }}</span>
            </p>
          </div>
          <div class="col-md-3 semester">
            <h3>ภาคการศึกษา</h3>
            <div class="select">
              <v-select :options="semester" placeholder="เลือก..." v-model="classroom.semester"></v-select>
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
              :disabled="(classroom.year === '' || classroom.name === '' || classroom.semester === '' || memteacher.length == 0 || student.length == 0) && hello == true"
              @click="create(memteacher)"
            >
              <h3>สร้าง</h3>
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
  data() {
    return {
      teacher: "",
      student: [],
      max: 50,
      memteacher: [],
      hello: true,
      students: [
        { value: 4, label: "ชั้นปีที่4" },
        { value: 3, label: "ชั้นปีที่3" },
        { value: 2, label: "ชั้นปีที่2" },
        { value: 1, label: "ชั้นปีที่1" }
      ],
      Teacher: [],
      year: [
        {
          value: new Date().getFullYear() + 542,
          label: new Date().getFullYear() + 542
        },
        {
          value: new Date().getFullYear() + 543,
          label: new Date().getFullYear() + 543
        }
      ],
      semester: [
        { value: "-", label: "-" },
        { value: 1, label: "1" },
        { value: 2, label: "2" },
        { value: "S", label: "S" }
      ],
      token: this.$auth.getToken(),
      classroom: {
        name: "",
        semester: "",
        year: ""
      },
      classroom_errors: [],
      windowWidth: window.innerWidth
    };
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  created() {
    axios
      .get("api/user2", {
        headers: {
          Authorization: "Bearer " + this.token
        }
      })
      .then(response => {
        for (let i = 0; i < response.data.length; i++) {
          this.Teacher.push({
            label: response.data[i].name.toLocaleUpperCase(),
            value: response.data[i].id
          });
        }
        this.users = response.data;
      })
      .catch(error => {
        this.classroom_errors = error.response.data.errors;
      });
  },
  methods: {
    create(user_id) {
      var token = this.$auth.getToken();
      this.hello = false;
      var data = {
        name: this.classroom.name,
        semester: this.classroom.semester.value,
        year: this.classroom.year.value,
        graeddb: this.student
          .map(e => e.value)
          .sort()
          .join(","),
        tu_id: user_id.map(e => e.value),
        graed: this.student.map(e => e.value)
      };

      axios
        .post("api/classroom", data, {
          headers: {
            Authorization: "Bearer " + token
          }
        })
        .then(response => {
          var classroom_id = response.data.id;
          this.hello = true;
          this.$router.go("/");
        })
        .catch(error => {
          this.hello = true;
          this.classroom_errors = error.response.data.errors;
        });
    },
    back() {
      this.$router.go(-1);
    }
  }
};
</script>

