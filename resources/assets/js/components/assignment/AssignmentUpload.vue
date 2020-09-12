<template>
  <div v-if="classrooms">
    <div id="assignment" v-if="loaded">
      <div id="check" v-if="status">
        <div class="head-resp">
          <h2>ตรวจสอบความก้าวหน้า</h2>
          <router-link to="../progress" v-if="this.windowWidth <= 767">
            <h2>✕</h2>
          </router-link>
        </div>
        <h2 v-if="status.returned" class="text-status">
          <img :src="`${baseUrl}/images/Icons/statusDone.png`" class="img-status" />
          ความก้าวหน้าที่ตรวจสอบแล้ว
        </h2>
        <h2 v-else class="text-status">
          <img :src="`${baseUrl}/images/Icons/statusNoneDone.png`" class="img-status" />
          ความก้าวหน้าที่ยังไม่ตรวจสอบ
        </h2>
        <div class="content">
          <div class="data">
            <div class="header">
              <h3 v-if="this.assignment_post.role_id == 1">อาจารย์นัดหมาย :</h3>
              <h3 v-if="this.assignment_post.role_id == 2">นักศึกษาเข้าพบเอง :</h3>
              <h4>Group_{{this.assignment_post.group.id}}</h4>
              <br v-if="this.windowWidth <= 767" />
              <span v-if="this.windowWidth > 767">|</span>
              <h4>{{parseTime(this.assignment_post.due_time)}}</h4>
              <span>|</span>
              <h4>{{this.assignment_post.title}}</h4>
            </div>
            <div>
              <div class="body">
                <h3 class="text-progress">รายงานความก้าวหน้า</h3>
                <h4 v-if="this.replies[0].detail != null">{{this.replies[0].detail}}</h4>
                <h4 v-else>{{this.assignment_post.detail}}</h4>
              </div>
              <div class="footer">
                <div class="row">
                  <div class="col-md-9">
                    <h3>ไฟล์ที่แนบมา</h3>
                    <h4 class="file" v-for="file in this.uploaded_files" :key="file.id">
                      <div class="assignment-done-item" @click="download(file)">
                        <div>{{ file.name }}</div>
                      </div>
                    </h4>
                  </div>
                  <div class="col-md-3 right btn-comment-resp bottom">
                    <div class="add-comment">
                      <a
                        href="#comment"
                        data-toggle="collapse"
                        role="button"
                        aria-expanded="false"
                        aria-controls="collapseExample"
                      >
                        <div class="add-comment">
                          <img src="../../../../../public/images/Icons/IconFeedCommenttalk.png" />
                          <h4>ดูความคิดเห็น</h4>
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- ****comment**** -->
          <div class="collapse" id="comment">
            <div class="card card-body">
              <h4 class="view-comment">{{ status.comment }}</h4>
            </div>
          </div>
          <!-- end comment -->
        </div>
      </div>

      <div id="reply-progress" v-if="!is_done">
        <!-- <router-link to="../progress" class="close-page" v-if="this.windowWidth > 767">✕</router-link> -->
        <!-- <h3 style="margin-bottom: 20px;">อาจารย์ที่ปรึกษานัดหมาย :</h3> -->
        <div class="head-resp">
          <h2>บันทึกความก้าวหน้า</h2>
          <router-link to="../progress" v-if="this.windowWidth <= 767">
            <h2>✕</h2>
          </router-link>
        </div>
        <div class="header">
          <h3>รายงานความก้าวหน้า</h3>
          <br v-if="this.windowWidth <= 767" />
          <div class="data">
            <h4>{{parseTime(this.assignment_post.due_time)}}</h4>
            <span>|</span>
            <h4>{{this.assignment_post.title}}</h4>
          </div>
        </div>
        <div class="body">
          <upload
            :callback="`/api/classroom/${classroomId}/assignment/${assignmentId}/upload`"
            v-on:complete="uploadedFile"
            v-on:remove="removeFile"
          ></upload>
          <!-- v-if="this.assignment_post.role_id != 2" -->
          <textarea name="content" v-model="detail" class="text-detail" />
          <p class="text-danger" v-for="error in classroom_errors.detail" :key="error.id">
            <span v-if="classroom_errors.detail">{{ error }}</span>
          </p>
        </div>
        <button
          class="btn btn-default submit"
          @click="confirm(),add()"
          v-if="!is_done"
          :disabled="(detail === '' ) && hello == true"
        >
          <h3>บันทึก</h3>
        </button>
      </div>
    </div>
  </div>
  <loading v-else></loading>
</template>

<script>
import upload from "../block/upload.vue";
import moment from "moment";
import loading from "../elements/loading.vue";

export default {
  props: ["classroomId", "assignmentId"],
  data() {
    return {
      baseUrl: this.$store.getters.baseUrl,
      detail: "",
      group: [],
      uploaded_files: [],
      loaded: false,
      is_done: "",
      hello: true,
      status: {},
      classroom_id: this.classroomId,
      assignment_id: this.assignmentId,
      windowWidth: window.innerWidth,
      token: this.$auth.getToken(),
      classroom_errors: [],
      options: {
        url: `/api/classroom/${this.$route.params.id}/assignment/${this.$route.params.assignment_id}/upload`,
        paramName: "file",
        headers: {
          Authorization: "Bearer " + this.$auth.getToken()
        }
      },
      classrooms: ""
    };
  },
  components: {
    upload,
    loading
  },
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
        this.assignment_post = response.data.assignment;
        this.uploaded_files = response.data.uploaded_files;
        this.replies = response.data.replies;
        this.is_done = response.data.is_done;
        this.status = response.data.status;
        this.loaded = true;
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
    uploadedFile(file) {
      this.uploaded_files = file;
    },
    removeFile(file) {
      this.uploaded_files = file;
    },
    parseTime(dateTime) {
      moment.locale("th");
      var displayTime = moment(dateTime, "YYYY-MM-DD HH-mm")
        .add(543, "Y")
        .format("ll");
      return displayTime;
    },
    confirm() {
      axios.get(
        `api/classroom/${this.classroom_id}/assignment/${this.assignment_id}/confirm`,
        {
          headers: {
            Authorization: "Bearer " + this.token
          }
        }
      );
      // .then(response => {
      //   this.$router.push(`../progress`);
      // });
    },
    add() {
      var token = this.$auth.getToken();
      axios
        .post(
          `api/classroom/${this.classroom_id}/assignment/${this.assignment_id}/storereply`,
          {
            detail: this.detail
          },
          {
            headers: {
              Authorization: "Bearer " + this.token
            }
          }
        )
        .then(response => {
          this.hello = false;
          this.$router.push(`../progress`);
          this.group.push(this.assignment_post.group.id);
          axios
            .post(
              "api/send-mail",
              {
                group_id: this.group
              },
              {
                headers: {
                  Authorization: "Bearer " + this.token
                }
              }
            )
            .catch(error => {
              this.post_errors = error.response.data.errors;
            });
          this.hello = true;
        })
        .catch(error => {
          this.classroom_errors = error.response.data.errors;
          this.hello = true;
        });
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

