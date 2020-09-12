<template>
  <div id="data-group" v-if="groups && getUser">
    <h1 v-if="this.windowWidth <= 767">กลุ่ม</h1>
    <h2 class="noGroup" v-if="!groups.length">นักศึกษายังไม่ได้สร้างกลุ่ม</h2>
    <div class="row">
      <div class="col-md-4 col-50-resp" v-for="(group,index) in groups" :key="index">
        <div class="card-group">
          <div class="type art" v-if="group.type.split(`,`)[0] == 'Art'"></div>
          <div class="type software" v-if="group.type.split(`,`)[0] == 'Software'"></div>
          <div class="type hardware" v-if="group.type.split(`,`)[0] == 'Hardware'"></div>
          <div class="btn-group dropright">
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
                  href="#"
                  data-toggle="modal"
                  v-on:click="deleteClass(group.id,index)"
                >
                  <img :src="`${baseUrl}/images/Icons/ProjectDelete.png`" />
                  <h4>ลบ</h4>
                </a>
              </div>
            </div>
          </div>

          <h4 class="idGroup">Group_{{index+1}}</h4>
          <h4 class="name-group" style="width: 100%;">
            {{group.name.slice(0, 40)}}
            <span v-if="group.name.length > 30">...</span>
            <span
              class="readMore"
              data-toggle="modal"
              :data-target="'#exampleModal'+ group.id"
            >เพิ่มเติม</span>
          </h4>
          <div class="detail">
            <a v-bind:href="group.path" target="_blank">
              <img src="../../../../../public/images/Icons/IconGroupFolder.png" />
              <h4>โฟลเดอร์ทั้งหมด</h4>
            </a>
            <div class="num-group">
              <img src="../../../../../public/images/Icons/IconGroupMember.png" />
              <span v-for="(namestu,index) in group.members" :key="index">
                <span v-if="namestu.role.name=='Student'">
                  <h4 v-if="index + 1 == group.members.length-1">{{ index+1 }}</h4>
                </span>
              </span>
            </div>
          </div>
        </div>
        <!-- Modal -->
        <div
          class="modal fade"
          :id="'exampleModal' + group.id"
          tabindex="-1"
          role="dialog"
          aria-labelledby="detail"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h2 class="modal-title" id="detail">ข้อมูล Group_{{index+1}}</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true" data-dismiss="modal">✕</span>
                </button>
              </div>
              <div class="modal-body">
                <!-- {{group.name}}
                {{group.user_id}}
                {{group.type}}-->
                <div class="row">
                  <div class="col-md-4 right">
                    <h3>ชื่อโครงงาน / ชื่อกลุ่ม :</h3>
                  </div>
                  <div class="col-md-8">
                    <h4>{{group.name}}</h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4 right">
                    <h3>ประเภทโครงงาน :</h3>
                  </div>
                  <div class="col-md-8">
                    <div v-for="type in group.type.split(`,`)" :key="type.id" class="typeText">
                      <h4 class="typeArt" v-if="type == 'Art'">{{type}}</h4>
                      <h4 class="typeSoftware" v-if="type == 'Software'">{{type}}</h4>
                      <h4 class="typeHardware" v-if="type == 'Hardware'">{{type}}</h4>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4 right">
                    <h3 style="padding-top: 7px;">สมาชิกในกลุ่ม :</h3>
                  </div>
                  <div class="col-md-8">
                    <div :id="'accordionExample'+group.id">
                      <div class="card" style="border:none">
                        <div v-for="(namestu,index) in group.members" :key="index">
                          <div v-if="namestu.role.name=='Student'">
                            <div
                              style="margin-bottom: 5px; cursor:pointer;"
                              data-toggle="collapse"
                              :data-target="'#collapse' + namestu.id + group.id"
                              aria-expanded="true"
                              aria-controls="collapseOne"
                            >
                              <div
                                class="img-profile"
                                v-bind:style="{'background-image': `url(${namestu.avatar_url})`}"
                              ></div>
                              <h4 class="memberName">{{namestu.student_id + " " + namestu.name}}</h4>
                            </div>

                            <div
                              :id="'collapse' + namestu.id + group.id"
                              class="collapse"
                              :data-parent="'#accordionExample'+group.id"
                            >
                              <div class="card-body">
                                <div>{{"Email:"+ " " +namestu.email}}</div>
                                <div>{{"Facebook:"+ " " +namestu.facebook}}</div>
                                <div>{{"Phone:" + " " +namestu.phone}}</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4 right">
                    <h3 style="padding-top: 7px;">อาจารย์ที่ปรึกษา :</h3>
                  </div>
                  <div class="col-md-8">
                    <div id="accordionExample">
                      <div class="card" style="border:none">
                        <div v-for="(nametea,index) in group.members" :key="index">
                          <div v-if="nametea.role.name=='Teacher'">
                            <div
                              style="margin-bottom: 5px; cursor:pointer;"
                              data-toggle="collapse"
                              :data-target="'#collapse' + nametea.id + group.id"
                              aria-expanded="true"
                              aria-controls="collapseOne"
                            >
                              <div
                                class="img-profile"
                                v-bind:style="{'background-image': `url(${nametea.avatar_url})`}"
                              ></div>
                              <h4 class="memberName">{{nametea.name}}</h4>
                            </div>

                            <div
                              :id="'collapse' + nametea.id + group.id"
                              class="collapse"
                              data-parent="#accordionExample"
                            >
                              <div class="card-body">
                                <div>{{"Email:"+ " " +nametea.email}}</div>
                                <div>{{"Facebook:"+ " " +nametea.facebook}}</div>
                                <div>{{"Phone:" + " " +nametea.phone}}</div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      count: 0,
      type: "",
      search: "",
      baseUrl: this.$store.getters.baseUrl,
      groups: [],
      groupmem: [],
      status: false,
      show_join: false,
      user: this.getUser,
      st: "",
      tu: "",
      windowWidth: window.innerWidth,
      swal_config: {
        title: "ต้องการลบ กลุ่ม หรือไม่ ?",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "ยกเลิก",
        confirmButtonText: "ยืนยัน",
        showCancelButton: true,
        reverseButtons: true
      }
    };
  },
  computed: {
    ...mapGetters(["isStudent", "isTeacher", "getUser"])
  },
  methods: {
    deleteClass(id, index) {
      var token = this.$auth.getToken();
      var self = this;
      swal(this.swal_config)
        .then(() => {
          axios
            .delete("api/group/" + id, {
              headers: {
                Authorization: "Bearer " + token
              }
            })
            .then(response => {
              this.groups.splice(index, 1);
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
    }
  },
  mounted() {
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
        this.groups = response.data;
      });

    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  }
};
</script>
