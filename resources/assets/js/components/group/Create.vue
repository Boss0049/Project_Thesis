<template>
  <div id="createGroup">
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
              <h2 style="color: #262626; border-bottom: 2px solid #006ad8; font-weight: 500;">กลุ่ม</h2>
            </router-link>
          </li>
          <li>
            <router-link to="./progress" class="linkto">
              <h2>ความก้าวหน้า</h2>
            </router-link>
          </li>
        </ul>
      </div>
    </div>
    <div class="head-resp" v-if="this.windowWidth <= 767">
      <h2>สร้างกลุ่ม</h2>
      <router-link to="./group">
        <h2>✕</h2>
      </router-link>
    </div>
    <div class="form-group">
      <div class="addGroup" v-if="this.windowWidth > 1300">
        <button
          v-if="!loading"
          class="btn btn-defualt btn-addGroup"
          v-on:click="create(member,memteacher)"
          :disabled="(newitem.name === '' || newitem.type.length == 0 || memteacher.length == 0) && hello == true"
        >
          <h3>สร้างกลุ่ม</h3>
        </button>
        <loading v-else></loading>
      </div>
      <div class="name">
        <h3>ชื่อโครงงาน / ชื่อกลุ่ม</h3>
        <input type="text" placeholder="ระบุชื่อโครงงาน / ชื่อกลุ่ม" v-model="newitem.name" />
        <p class="text-danger" v-for="error in group_errors.name" :key="error.id">
          <span v-if="group_errors.name">{{error}}</span>
        </p>
      </div>
      <div class="member">
        <div class="row">
          <div class="col-lg-3" style="padding-right:0;">
            <h3>สมาชิกในกลุ่ม :</h3>
          </div>
          <div class="col-lg-9">
            <h3 class="myName">
              1.
              <span>{{this.me.student_id + " " + this.me.name}}</span>
            </h3>
            <div class="input-member" v-for="(list,index) in listsMem" v-bind:key="index">
              <h3>{{index+2}}.</h3>
              <div class="select">
                <v-select
                  :options="Student"
                  v-model="member[index]"
                  placeholder="พิมพ์ชื่อ หรือ รหัสนักศึกษา..."
                ></v-select>
              </div>
              <span
                class="text-danger"
                v-if="group_errors.message"
              >{{"มีสมาชิกในกลุ่มซ้ำกรุณาเลือกใหม่"}}</span>
              <button class="btn btn-default deleteMember" v-on:click="deletelistMem(index)">✕</button>
            </div>
            <div class="add-member" v-on:click="addMem()">
              <h4>+ เพิ่มสมาชิกในกลุ่ม</h4>
            </div>
          </div>
        </div>
      </div>
      <div class="member">
        <div class="row">
          <div class="col-lg-3" style="padding-right: 0;">
            <h3>อาจารย์ที่ปรึกษา :</h3>
          </div>
          <div class="col-lg-9">
            <div class="input-member" v-for="(list,index) in lists" v-bind:key="index">
              <h3>{{index+1}}.</h3>
              <div class="select">
                <v-select
                  :options="Teacher"
                  v-model="memteacher[index]"
                  placeholder="พิมพ์ชื่ออาจารย์..."
                ></v-select>
              </div>
              <span
                class="text-danger"
                v-if="group_errors.message"
              >{{"มีสมาชิกในกลุ่มซ้ำกรุณาเลือกใหม่"}}</span>
              <button class="btn btn-default deleteMember" v-on:click="deletelist(index)">✕</button>
            </div>
            <div class="add-member" v-on:click="add()">
              <h4>+ เพิ่มอาจารย์ที่ปรึกษา</h4>
            </div>
          </div>
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
                <input
                  type="checkbox"
                  value="Art"
                  v-model="newitem.type"
                  :disabled="newitem.type == 'null'"
                />
                <span class="checkbox"></span>
              </label>
            </div>
            <div class="col-md-4 center">
              <label>
                Software
                <input
                  type="checkbox"
                  value="Software"
                  v-model="newitem.type"
                  :disabled="newitem.type == 'null'"
                />
                <span class="checkbox"></span>
              </label>
            </div>
            <div class="col-md-4 right">
              <label>
                Hardware
                <input
                  type="checkbox"
                  value="Hardware"
                  v-model="newitem.type"
                  :disabled="newitem.type == 'null'"
                />
                <span class="checkbox"></span>
              </label>
            </div>
          </div>
          <label>
            ไม่ระบุประเภท
            <h5>(กรณีไม่ใช่โครงงานนักศึกษาชั้นปีที่ 4)</h5>
            <input
              type="checkbox"
              value="null"
              v-model="newitem.type"
              :disabled="newitem.type.length >= 1 && newitem.type != 'null'"
            />
            <span class="checkbox"></span>
          </label>
        </div>
        <p class="text-danger" v-for="error in group_errors.type" :key="error.id">
          <span v-if="group_errors.type">{{error}}</span>
        </p>
      </div>
      <span v-if="this.windowWidth <= 1300">
        <button
          v-if="!loading"
          class="btn btn-defualt btn-addGroup"
          v-on:click="create(member,memteacher)"
          :disabled="(newitem.name === '' || newitem.type.length == 0 || memteacher.length == 0) && hello == true"
        >
          <h3>สร้างกลุ่ม</h3>
        </button>
        <loading v-else></loading>
      </span>
    </div>
  </div>
</template>

<script>
import loading from "../elements/loading";
import vSelect from "vue-select";

export default {
  data() {
    return {
      me: "",
      loading: false,
      windowWidth: window.innerWidth,
      classroom_id: this.$route.params.id,
      token: this.$auth.getToken(),
      listsMem: [{ count: "2" }, { count: "3" }, { count: "4" }],
      lists: [{ count: "1" }],
      newitem: { name: "", type: [] },
      Student: [],
      Teacher: [],
      member: [],
      memteacher: [],
      group_errors: [],
      hello: true
    };
  },
  components: {
    "v-select": vSelect,
    loading
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  created() {
    axios
      .get("api/meandi", {
        headers: {
          Authorization: "Bearer " + this.token
        }
      })
      .then(response => {
        this.me = response.data;
      })
      .catch(error => {});
    axios
      .get("api/members/" + this.classroom_id, {
        headers: {
          Authorization: "Bearer " + this.token
        }
      })
      .then(response => {
        for (let i = 0; i < response.data.length; i++) {
          if (response.data[i].role.name == "Student")
            this.Student.sort().push({
              label:
                response.data[i].student_id +
                " " +
                response.data[i].name.charAt(0).toUpperCase() +
                response.data[i].name.slice(1).toLowerCase(),
              value: response.data[i].id
            });
        }
        this.users = response.data;
      });

    axios
      .get("api/members/" + this.classroom_id, {
        headers: {
          Authorization: "Bearer " + this.token
        }
      })
      .then(response => {
        for (let i = 0; i < response.data.length; i++) {
          if (response.data[i].role.name == "Teacher")
            this.Teacher.push({
              label: response.data[i].name.toLocaleUpperCase(),
              value: response.data[i].id
            });
        }
        this.users = response.data;
      });
  },
  methods: {
    create(user_idstu, user_idta) {
      var token = this.$auth.getToken();
      this.loading = true;
      this.hello = false;
      var data = {
        classroom_id: this.classroom_id,
        name: this.newitem.name,
        type: this.newitem.type.map(e => e).join(","),
        user_idstu: user_idstu.map(e => e.value),
        user_idta: user_idta.map(e => e.value)
      };

      axios
        .post("api/group", data, {
          headers: {
            Authorization: "Bearer " + token
          }
        })
        .then(response => {
          console.log("Hello1");
          var group_id = response.data.id;
          console.log("Hello2");
          this.hello = true;
          console.log("Hello3");
          this.$router.push("group");
        })
        .catch(error => {
          console.log("error");
          this.loading = false;
          console.log("error1");
          this.hello = true;
          this.group_errors = error.response.data.errors;
        });
    },
    add() {
      this.lists.push({
        count: this.lists.length + 1,
        name: ""
      });
    },
    deletelist(index) {
      this.lists.splice(index, 1);
      this.memteacher.splice(index, 1);
    },
    deletelistMem(index) {
      this.listsMem.splice(index, 1);
      this.member.splice(index, 1);
    },
    addMem() {
      this.listsMem.push({
        count: this.listsMem.length + 1,
        name: ""
      });
    },
    back() {
      this.$router.go(-1);
    }
  }
};
</script>