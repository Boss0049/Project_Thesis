<template>
  <div v-if="classrooms">
    <div id="check">
      <div v-if="getRole != 'is_student' ">
        <h2 class="noProgress" v-if="all_done.length == 0">นักศึกษายังไม่ได้บันทึกความก้าวหน้า</h2>
        <div class="content" v-for="done in all_done" :key="done.id">
          <h2 class="text-status" v-if="done.returned == 0">
            <img :src="`${baseUrl}/images/Icons/statusNoneDone.png`" class="img-status" />
            ความก้าวหน้าที่ยังไม่ตรวจสอบ
          </h2>
          <h2 class="text-status" v-if="done.returned == 1">
            <img :src="`${baseUrl}/images/Icons/statusDone.png`" class="img-status" />
            ความก้าวหน้าที่ตรวจสอบแล้ว
          </h2>
          <div class="data">
            <div class="header">
              <div class="row">
                <div class="col-md-10 col-60-resp" style="padding-right:0;">
                  <h3 v-if="role.role_id == 1">อาจารย์นัดหมาย :</h3>
                  <h3 v-if="role.role_id == 2">นักศึกษาเข้าพบเอง :</h3>
                  <a
                    href="#group"
                    data-toggle="collapse"
                    role="button"
                    aria-expanded="false"
                    aria-controls="collapseExample"
                  >
                    <h4 class="group">Group_{{group.id}}</h4>
                  </a>
                  <span class="span">|</span>
                  <br class="show-br" />
                  <h4>{{parseTime(time)}}</h4>
                  <span>|</span>
                  <h4>{{title}}</h4>
                </div>
                <div class="col-md-2 space right col-40-resp">
                  <button
                    class="btn btn-default check-status"
                    @click="returnWork(done.id)"
                    v-if="!done.returned"
                  >
                    <h4>ยืนยัน</h4>
                  </button>
                </div>
              </div>
            </div>
            <div class="body">
              <h3 class="text-progress">รายงานความก้าวหน้า</h3>
              <h4>{{replies}}</h4>
            </div>
            <div class="footer">
              <div class="row">
                <div class="col-md-9">
                  <h3>ไฟล์ที่แนบมา</h3>
                  <h4 class="file">
                    <div
                      class="assignment-done-item"
                      v-for="file in done.files"
                      :key="file.id"
                      @click="download(file)"
                    >
                      <h4>{{ file.name }}</h4>
                    </div>
                  </h4>
                </div>
                <div class="col-md-3 right btn-comment-resp bottom">
                  <div class="add-comment">
                    <a
                      :href="'#comment' + done.id"
                      data-toggle="collapse"
                      role="button"
                      aria-expanded="false"
                      aria-controls="collapseExample"
                    >
                      <div class="add-comment">
                        <img src="../../../../../public/images/Icons/IconFeedCommenttalk.png" />
                        <h4>แสดงความคิดเห็น</h4>
                      </div>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- ****group**** -->
          <div class="collapse" id="group">
            <div class="card card-body">
              <div class="row view-group" style="margin-bottom:10px">
                <div class="col-md-3">
                  <h4 class="title">ชื่อกลุ่ม</h4>
                </div>
                <div class="col-md-9 nameList">
                  <h4>{{group.name}}</h4>
                </div>
              </div>
              <div class="row view-group">
                <div class="col-md-3">
                  <h4 class="title">สมาชิกในกลุ่ม</h4>
                </div>
                <div class="col-md-9 nameList">
                  <div v-for="(groupmem,index) in group.members" :key="index">
                    <h4 v-if="groupmem.role_id != 1">{{groupmem.student_id + " " + groupmem.name}}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- end group -->
          <!-- ****comment**** -->
          <div class="collapse" :id="'comment' + done.id">
            <div class="card card-body">
              <textarea
                class="form-control"
                rows="2"
                v-model="done.comment"
                placeholder="แสดงความคิดเห็น"
                v-if="done.returned == 0"
              ></textarea>
              <h4 v-if="done.returned == 0">*กดที่ปุ่มยืนยันเมื่อพิมพ์ข้อความเสร็จสิ้น</h4>
              <h4 class="view-comment" v-if="done.returned == 1">{{done.comment}}</h4>
            </div>
          </div>
          <!-- end comment -->
        </div>
      </div>
    </div>
  </div>
  <loading v-else></loading>
</template>

<script>
import moment from "moment";
import { mapGetters } from "vuex";
import loading from "../elements/loading";

export default {
  props: ["classroomId", "assignmentId", "title", "time", "group", "role"],
  data() {
    return {
      baseUrl: this.$store.getters.baseUrl,
      replies_props: "",
      count: "0",
      active_id: "",
      all_done: [],
      all_role: [],
      classroom_id: this.classroomId,
      assignment_id: this.assignmentId,
      token: this.$auth.getToken(),
      // group: "Group_001",
      classrooms: "",
      windowWidth: window.innerWidth,
      replies: ""
    };
  },
  components: { loading },
  computed: mapGetters(["getRole"]),
  created() {
    axios
      .get(
        `api/classroom/${this.classroom_id}/assignment/${this.assignment_id}`,
        {
          headers: {
            Authorization: "Bearer " + this.token
          }
        }
      )
      .then(response => {
        this.replies = response.data.replies[0].detail;
      });

    axios
      .get(
        `api/classroom/${this.classroom_id}/assignment/${this.assignment_id}/done`,
        {
          headers: {
            Authorization: "Bearer " + this.token
          }
        }
      )
      .then(response => {
        this.all_done = response.data;
      });
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });

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
    activeId(id) {
      if (this.active_id == id) {
        this.active_id = 0;
      } else {
        this.active_id = id;
      }
    },
    returnWork(id) {
      var index = this.all_done.findIndex(x => x.id == id);
      var data = {
        comment: this.all_done[index].comment,
        user_id: this.all_done[index].user_id
      };
      axios
        .put(
          `api/classroom/${this.classroom_id}/assignment/${this.assignment_id}`,
          data,
          {
            headers: {
              Authorization: "Bearer " + this.token
            }
          }
        )
        .then(response => {
          this.all_done[index].returned = 1;
          this.active_id = 0;
        });
      location.reload();
    },
    parseTime(dateTime) {
      moment.locale("th");
      var displayTime = moment(dateTime, "YYYY-MM-DD HH-mm")
        .add(543, "Y")
        .format("ll");
      return displayTime;
    },
    download(file) {
      axios
        .get(`api/assignment/download/${file.id}`, {
          headers: {
            Authorization: "Bearer " + this.token
          }
        })
        .then(response => {
          window.open(response.data.download_url, "_self");
        });
    }
  }
};
</script>