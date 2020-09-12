<template>
  <div id="editProfile-first">
    <div class="card-edit">
      <div class="header">
        <h2>ยืนยันตัวตน</h2>
      </div>
      <div class="body">
        <div class="name">
          <div class="row">
            <div class="col-md-3 right">
              <h3>ชื่อ-นามสกุล :</h3>
            </div>
            <div class="col-md-9 content">
              <h3>{{this.meuser.name}}</h3>
            </div>
          </div>
        </div>
        <div class="email">
          <div class="row">
            <div class="col-md-3 right">
              <h3>E-mail :</h3>
            </div>
            <div class="col-md-9 content">
              <h3>{{this.meuser.email}}</h3>
            </div>
          </div>
        </div>
        <div class="status">
          <div class="row">
            <div class="col-md-3 right">
              <h3>สถานะ :</h3>
            </div>
            <div class="col-md-9">
              <div class="input-status">
                <div class="style-type-radio left">
                  <label>
                    <h3>Teacher</h3>
                    <input type="radio" name="radio" v-model="status" value="3" />
                    <span class="radio"></span>
                  </label>
                </div>
                <div class="style-type-radio right">
                  <label>
                    <h3>Student</h3>
                    <input type="radio" name="radio" v-model="status" value="2" />
                    <span class="radio"></span>
                  </label>
                </div>
              </div>
              <h4>*กรุณาระบุสถานะของท่าน</h4>
            </div>
          </div>
        </div>
        <div class="form-student" v-if="status == '2'">
          <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-9">
              <div class="row">
                <div class="col-md-3 right">
                  <h3>รหัสนักศึกษา :</h3>
                </div>
                <div class="col-md-9">
                  <input
                    type="text"
                    name="stu_code"
                    placeholder="รหัสนักศึกษา..."
                    :maxlength="Codemax"
                    class="style-input"
                    v-model="studentid"
                  />
                </div>
              </div>
              <div class="year">
                <div class="row">
                  <div class="col-md-3 right">
                    <h3 class="head">ชั้นปี :</h3>
                  </div>
                  <div class="col-md-9">
                    <div class="select">
                      <v-select :options="options" v-model="grade" placeholder="เลือก..."></v-select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="footer center">
        <button @click="updatestatus()" class="btn btn-default submit">
          <h3>บันทึก</h3>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from "vue-select";

export default {
  data() {
    return {
      Codemax: 11,
      Telmax: 10,
      status: "",
      grade: "",
      studentid: "",
      options: ["4", "3", "2", "1"],
      meuser: []
    };
  },
  components: {
    "v-select": vSelect
  },
  mounted() {
    var token = this.$auth.getToken();
    this.$store.dispatch("getPersonal");
    axios
      .get("api/meandi", {
        headers: {
          Authorization: "Bearer " + token
        }
      })
      .then(response => {
        this.meuser = response.data;
      });
  },
  methods: {
    updatestatus() {
      var data = {
        role_id: this.status,
        grade_id: this.grade,
        student_id: this.studentid
      };
      axios
        .patch("api/mestatus", data, {
          headers: {
            Authorization: "Bearer " + this.$auth.getToken()
          }
        })
        .then(response => {
          this.$store.dispatch("getPersonal");
          console.log("change profile successful");
        })
        .catch(error => {
          this.profile_errors = error.response.data.errors;
        });
    }
  }
};
</script>