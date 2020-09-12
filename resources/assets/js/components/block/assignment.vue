<template>
  <div id="create-meet">
    <div id="main-page">
      <div class="navtab">
        <ul>
          <li>
            <router-link to="./post" class="linkto">
              <h2>ข่าวสาร</h2>
            </router-link>
          </li>
          <li>
            <router-link to="./document" class="linkto">
              <h2>เอกสาร</h2>
            </router-link>
          </li>
          <li>
            <router-link to="./group" class="linkto">
              <h2>กลุ่ม</h2>
            </router-link>
          </li>
          <li>
            <router-link to="./progress" class="linkto">
              <h2
                style="color: #262626; border-bottom: 2px solid #006ad8; font-weight: 500;"
              >ความก้าวหน้า</h2>
            </router-link>
          </li>
        </ul>
      </div>
    </div>
    <div class="head-resp">
      <h2 v-if="getRole != 'is_student'">สร้างนัดหมาย</h2>
      <h2 v-if="getRole == 'is_student'">การเข้าพบ</h2>
      <router-link to="./progress" v-if="this.windowWidth <= 767">
        <h2>✕</h2>
      </router-link>
    </div>
    <div class="space-input-resp">
      <h2 class="symbol">*</h2>
      <input
        v-if="getRole != 'is_student'"
        type="text"
        placeholder="ระบุเรื่องที่นัดหมาย"
        class="title"
        :maxlength="max"
        v-model="form.title"
      />
      <input
        v-if="getRole == 'is_student'"
        type="text"
        placeholder="ระบุเรื่องที่เข้าพบ"
        class="title"
        :maxlength="max"
        v-model="form.title"
      />
      <h5>{{form.title.length}}/50</h5>
      <p class="text-danger" v-for="error in errors.title" :key="error.id">
        <span v-if="errors.title">{{ error }}</span>
      </p>
    </div>
    <!-- ********************TA******************** -->
    <div v-if="getRole != 'is_student'">
      <div class="row">
        <div class="col-md-6">
          <h3 v-if="this.windowWidth > 1300">วันที่นัดหมาย</h3>
          <h3 v-if="this.windowWidth <= 1300">วันที่</h3>
          <div class="datePic">
            <datetime format="DD-MM-YYYY" v-model="form.duedate"></datetime>
            <p class="text-danger" v-for="error in errors.due_time" :key="error.id">
              <span v-if="errors.due_time">{{ error }}</span>
            </p>
          </div>
        </div>
        <div class="col-md-6 right">
          <h3>กลุ่ม</h3>
          <div class="select">
            <v-select multiple :options="options" v-model="group"></v-select>
          </div>
          <p class="text-danger" v-for="error in errors.group_id" :key="error.id">
            <span v-if="errors.group_id">{{ error }}</span>
          </p>
        </div>
      </div>
      <div class="detail">
        <img src="../../../../../public/images/Icons/IconProgressPin.png" />
        <h3>รายละเอียดการนัดหมาย</h3>
        <textarea name="body" v-model="form.detail" class="text-detail" />
      </div>
      <button
        type="submit"
        class="btn btn-default submit"
        @click="submit(group)"
        :disabled="form.detail=='' || group =='' || form.duedate=='' || form.title ==''"
      >
        <h3>สร้างนัดหมาย</h3>
      </button>
    </div>
    <!-- ********************stu******************** -->
    <div v-if="getRole == 'is_student'">
      <div class="row set-margin-row">
        <div class="col-md-6">
          <h3 v-if="this.windowWidth > 1300">วันที่เข้าพบ</h3>
          <h3 v-if="this.windowWidth <= 1300">วันที่</h3>
          <div class="datePic">
            <datetime format="DD-MM-YYYY" v-model="form.duedate"></datetime>
          </div>
          <p class="text-danger" v-for="error in errors.due_time" :key="error.id">
            <span v-if="errors.due_time">{{ error }}</span>
          </p>
        </div>
        <div class="col-md-6 right">
          <h3>กลุ่ม</h3>
          <div class="select">
            <v-select :options="options" v-model="group"></v-select>
          </div>
          <p class="text-danger" v-for="error in errors.group_id" :key="error.id">
            <span v-if="errors.group">{{ error }}</span>
          </p>
        </div>

        <button
          type="submit"
          class="btn btn-default btn-next"
          @click="STUsubmit(group)"
          :disabled="group =='' || form.duedate=='' || form.title ==''"
        >
          <h3>ถัดไป</h3>
        </button>
      </div>
    </div>

    <div class="clearfix"></div>
  </div>
</template>

<script>
import datetime from "vuejs-datetimepicker";
import vSelect from "vue-select";
import moment from "moment";
import { mapGetters } from "vuex";
import swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

export default {
  props: ["detail", "errors"],
  data() {
    return {
      max: 50,
      form: this.detail,
      group: [],
      windowWidth: window.innerWidth,
      classroom_id: this.$route.params.id,
      options: [],
      swal_config: {
        title: "ต้องการสร้างการนัดหมายหรือไม่ ?",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "ยกเลิก",
        confirmButtonText: "ยืนยัน",
        showCancelButton: true,
        reverseButtons: true
      },
      swal_config2: {
        title: "ต้องการสร้างการเข้าพบหรือไม่ ?",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "ยกเลิก",
        confirmButtonText: "ยืนยัน",
        showCancelButton: true,
        reverseButtons: true
      }
    };
  },
  computed: mapGetters(["getRole"]),
  created() {
    var token = this.$auth.getToken();
    var classroom_id = this.$route.params.id;
    this.$store.dispatch("getPersonal");

    axios
      .get("api/group/" + classroom_id, {
        headers: {
          Authorization: "Bearer " + token
        }
      })
      .then(response => {
        for (let i = 0; i < response.data.length; i++) {
          this.options.push({
            label: response.data[i].name,
            value: response.data[i].id
          });
        }
        this.test = response.data;
      });
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  methods: {
    submit(group_id) {
      swal(this.swal_config)
        .then(() => {
          this.$emit("submit", this.form, group_id);
        })
        .catch(swal.noop);
    },
    STUsubmit(group_id) {
      swal(this.swal_config2)
        .then(() => {
          this.$emit("submit", this.form, group_id);
        })
        .catch(swal.noop);
    }
  },
  components: {
    datetime,
    "v-select": vSelect
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  }
};
</script>
