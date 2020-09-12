<!-- <template>
  <div id="createGroup">
    <div class="head-resp" v-if="this.windowWidth <= 767">
      <h2>สร้างกลุ่ม</h2>
      <router-link to="./group">
        <h2>✕</h2>
      </router-link>
    </div>
    <div class="form-group">
      <div class="addGroup">
        <button v-if="this.windowWidth > 1300" class="btn btn-defualt btn-addGroup">
          <h3>สร้างกลุ่ม</h3>
        </button>
      </div>
      <div class="name">
        <h3>ชื่อโครงงาน / ชื่อกลุ่ม</h3>
        <input type="text" placeholder="ระบุชื่อโครงงาน / ชื่อกลุ่ม" />
        <!-- <p class="text-danger" v-for="error in group_errors.name" :key="error.id">
          <span v-if="group_errors.name">{{error}}</span>
        </p>
      </div>
      <!-- <div class="member">
        <div class="row">
            <div class="col-lg-3" style="padding-right:0;">
                <h3>สมาชิกในกลุ่ม :</h3>
            </div>
            <div class="col-lg-9">
                <div class="input-member" v-for="list in listsMem" v-bind:key="list.id">
                    <h3>{{list.count}}.</h3>
                    <div class="select">
                        <v-select
                        :options="Student"
                        placeholder="พิมพ์ชื่อ หรือ รหัสนักศึกษา..."
                        ></v-select>
                    </div>
                </div>
                <div class="add-member" v-on:click="addMem()">
                <h4>+ เพิ่มสมาชิกในกลุ่ม</h4>
      </div>-->
      <!-- <span
                    class="text-danger"
                    v-if="group_errors.message"
      >{{"มีสมาชิกในกลุ่มซ้ำกรุณาเลือกใหม่"}}</span>-->
      <!-- </div>
        </div>
      </div>
      <div class="member">
        <div class="row">
          <div class="col-lg-3" style="padding-right: 0;">
            <h3>อาจารย์ที่ปรึกษา :</h3>
          </div>
          <div class="col-lg-9">
            <div class="input-member" v-for="list in lists" v-bind:key="list.id">
              <h3>{{list.count}}.</h3>
              <div class="select">
                <v-select
                  :options="Teacher"
                  placeholder="พิมพ์ชื่ออาจารย์..."
                ></v-select>
              </div>
            </div>
            <div class="add-member" v-on:click="add()">
              <h4>+ เพิ่มอาจารย์ที่ปรึกษา</h4>
      </div>-->
      <!-- <span
              class="text-danger"
              v-if="group_errors.message"
      >{{"มีสมาชิกในกลุ่มซ้ำกรุณาเลือกใหม่"}}</span>-->
      <!-- </div>
        </div>
      </div>
      <div class="type">
        <h3>ประเภทโครงงาน</h3>
        <h5>(เลือกได้มากกว่า 1 ตัวเลือก)</h5>
        <div class="input-type">
          <div class="row">
            <div class="col-md-4">
              <label>
                Art
                <input type="checkbox" value="Art" />
                <span class="checkbox"></span>
              </label>
            </div>
            <div class="col-md-4 center">
              <label>
                Software
                <input type="checkbox" value="Software" />
                <span class="checkbox"></span>
              </label>
            </div>
            <div class="col-md-4 right">
              <label>
                Hardware
                <input type="checkbox" value="Hardware" />
                <span class="checkbox"></span>
              </label>
            </div>
          </div>
          <label>
            ไม่ระบุประเภท
            <h5>(กรณีไม่ใช่โครงงานนักศึกษาชั้นปีที่ 4)</h5>
            <input type="checkbox" value="null" />
            <span class="checkbox"></span>
          </label>
        </div>
        <!-- <p class="text-danger" v-for="error in group_errors.type" :key="error.id">
          <span v-if="group_errors.type">{{error}}</span>
        </p>
      </div>
      <button v-if="this.windowWidth <= 1300" class="btn btn-defualt btn-addGroup">
        <h3>สร้างกลุ่ม</h3>
      </button>
      <!-- <loading v-else></loading> 
    </div>
  </div>
</template> -->

<!-- <script>
// import loading from "../elements/loading";
// import vSelect from "vue-select";

// export default {
//   data() {
//     return {
//       windowWidth: window.innerWidth,
//       classroom_id: this.$route.params.id,
//       token: this.$auth.getToken(),
//       listsMem: [{ count: "1" }, { count: "2" }, { count: "3" }],
//       lists: [{ count: "1" }],
//       newitem: { name: "", type: [] },
//       Student: [],
//       Teacher: []
//     };
//   },
//   components: {
//     "v-select": vSelect,
//     loading
//   },
//   mounted() {
//     window.addEventListener("resize", () => {
//       this.windowWidth = window.innerWidth;
//     });
//   },
//   created() {
//     axios
//       .get("api/members/" + this.classroom_id, {
//         headers: {
//           Authorization: "Bearer " + this.token
//         }
//       })
//       .then(response => {
//         for (let i = 0; i < response.data.length; i++) {
//           if (response.data[i].role.name == "Student")
//             this.Student.push({
//               label: response.data[i].name,
//               value: response.data[i].id
//             });
//         }
//         this.users = response.data;
//       });

//     axios
//       .get("api/members/" + this.classroom_id, {
//         headers: {
//           Authorization: "Bearer " + this.token
//         }
//       })
//       .then(response => {
//         for (let i = 0; i < response.data.length; i++) {
//           if (response.data[i].role.name == "Teacher")
//             this.Teacher.push({
//               label: response.data[i].name,
//               value: response.data[i].id
//             });
//         }
//         this.users = response.data;
//       });
//   },
//   methods: {
//     create() {
//       var token = this.$auth.getToken();
//       this.loading = true;
//       var data = {
//         classroom_id: this.classroom_id,
//         name: this.group.name,
//         type: this.newitem.type.map(e => e).join(",")
//       };

      // axios
      //     .post("api/group", data, {
      //     headers: {
      //         Authorization: "Bearer " + token
      //     }
      //     })
      //     .then(response => {
      //     var group_id = response.data.id;
      //     this.$router.push(`membergroup/` + response.data.id);
      //     })
      //     .catch(error => {
      //     this.group_errors = error.response.data.errors;
      //     this.loading = false;
      //     });
    },
//     add() {
//       this.lists.push({
//         count: this.lists.length + 1,
//         name: ""
//       });
//     },
//     addMem() {
//       this.listsMem.push({
//         count: this.listsMem.length + 1,
//         name: ""
//       });
//     },
//     back() {
//       this.$router.go(-1);
//     }
//   }
// };
// </script>