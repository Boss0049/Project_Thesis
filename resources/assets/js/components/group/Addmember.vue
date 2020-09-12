<!--<template>
  <div id="create-member">
    <div id="main-page">
      <div class="navtab">
        <ul>
          <li>
            <router-link to="../post" class="linkto">
              <h2>ข่าวสาร</h2>
            </router-link>
          </li>
          <li>
            <router-link to="../document" class="linkto">
              <h2>เอกสาร</h2>
            </router-link>
          </li>
          <li>
            <router-link to="../group" class="linkto">
              <h2 style="color: #262626; border-bottom: 2px solid #006ad8; font-weight: 500;">กลุ่ม</h2>
            </router-link>
          </li>
          <li>
            <router-link to="../progress" class="linkto">
              <h2>ความก้าวหน้า</h2>
            </router-link>
          </li>
        </ul>
      </div>
    </div>
    <div class="head-resp" v-if="this.windowWidth <= 767">
      <h2>สร้างกลุ่ม</h2>
      <router-link to="../group">
        <h2>✕</h2>
      </router-link>
    </div>
    <div class="form-group">
      <button
        v-if="this.windowWidth > 767"
        class="btn btn-defualt addGroup"
        v-on:click="addmember(member),addteacher(memteacher)"
      >
        <h3>สร้างกลุ่ม</h3>
      </button>
      <div class="member">
        <div class="row">
          <div class="col-lg-2" style="padding-right: 0;">
            <h3>สมาชิกในกลุ่ม :</h3>
            <span class="warning">*ไม่ต้องเพิ่มชื่อตนเอง</span>
          </div>
          <div class="col-lg-10 list">
            <div class="input-member" v-for="list in listsMem" v-bind:key="list.id">
              <h3>{{list.count}}.</h3>
              <div class="select">
                <v-select
                  :options="Student"
                  v-model="member[list.count-1]"
                  placeholder="พิมพ์ชื่อ หรือ รหัสนักศึกษา..."
                ></v-select>
              </div>
            </div>
            <div class="add-member" v-on:click="addMem()">
              <h4>+ เพิ่มสมาชิกในกลุ่ม</h4>
            </div>
            <span
              class="text-danger"
              v-if="group_errors.message"
            >{{"มีสมาชิกในกลุ่มซ้ำกรุณาเลือกใหม่"}}</span>
          </div>
        </div>
      </div>
      <div class="member">
        <div class="row">
          <div class="col-lg-2" style="padding-right: 0;">
            <h3>อาจารย์ในกลุ่ม :</h3>
          </div>
          <div class="col-lg-10">
            <div class="input-member" v-for="list in lists" v-bind:key="list.id">
              <h3>{{list.count}}.</h3>
              <div class="select">
                <v-select
                  :options="Teacher"
                  v-model="memteacher[list.count-1]"
                  placeholder="พิมพ์ชื่ออาจารย์..."
                ></v-select>
              </div>
            </div>
            <div class="add-member" v-on:click="add()">
              <h4>+ เพิ่มอาจารย์ที่ปรึกษา</h4>
            </div>
            <span
              class="text-danger"
              v-if="group_errors.message"
            >{{"มีสมาชิกในกลุ่มซ้ำกรุณาเลือกใหม่"}}</span>
          </div>
        </div>
      </div>
      <button
        v-if="this.windowWidth <= 767"
        class="btn btn-defualt addGroup"
        v-on:click="addmember(member),addteacher(memteacher)"
      >
        <h3>สร้างกลุ่ม</h3>
      </button>
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
      member: [],
      memteacher: [],
      added_users: [],
      newitem: { name: "", type: [] },
      Student: [],
      Teacher: [],
      listsMem: [{ count: "1" }, { count: "2" }, { count: "3" }],
      lists: [{ count: "1" }],
      group_errors: [],
      classroom_id: this.$route.params.id,
      group_id: this.$route.params.group_id,
      token: this.$auth.getToken(),
      users: [],
      classroom: "",
      added_users: [],
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
      .get("api/members/" + this.classroom_id, {
        headers: {
          Authorization: "Bearer " + this.token
        }
      })
      .then(response => {
        for (let i = 0; i < response.data.length; i++) {
          if (response.data[i].role.name == "Student")
            this.Student.push({
              label: response.data[i].name,
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
              label: response.data[i].name,
              value: response.data[i].id
            });
        }
        this.users = response.data;
      });
  },

  methods: {
    addmember(user_id) {
      var data = {
        group_id: this.group_id,
        user_id: user_id.map(e => e.value)
      };
      axios
        .post("api/members/addmember", data, {
          headers: {
            Authorization: "Bearer " + this.token
          }
        })
        .then(response => {
          window.location.href = "../group";
        })
        .catch(error => {
          this.group_errors = error.response.data;
          console.log(this.group_errors);
        });
    },
    addteacher(user_id) {
      var data = {
        group_id: this.group_id,
        user_id: user_id.map(e => e.value)
      };
      axios
        .post("api/members/addmember", data, {
          headers: {
            Authorization: "Bearer " + this.token
          }
        })
        .catch(error => {
          this.group_errors = message;
          console.log(this.group_errors);
        });
    },
    add() {
      this.lists.push({
        count: this.lists.length + 1,
        name: ""
      });
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