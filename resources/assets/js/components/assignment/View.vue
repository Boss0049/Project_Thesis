<template>
  <div id="check-progress" v-if="assignment_post">
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
              <h2>กลุ่ม</h2>
            </router-link>
          </li>
          <li>
            <router-link to="../progress" class="linkto">
              <h2
                style="color: #262626; border-bottom: 2px solid #006ad8; font-weight: 500;"
              >ความก้าวหน้า</h2>
            </router-link>
          </li>
        </ul>
      </div>
    </div>
    <router-link to="../progress" class="close-page" v-if="this.windowWidth > 767">✕</router-link>
    <div v-if="getRole != 'is_student'">
      <div class="head-resp">
        <h2>ตรวจสอบความก้าวหน้า</h2>
        <router-link to="../progress" v-if="this.windowWidth <= 767">
          <h2>✕</h2>
        </router-link>
      </div>
    </div>
    <assignment-upload
      v-bind:classroom-id="classroom_id"
      v-bind:assignment-id="assignment_id"
      v-if="getRole == 'is_student'"
    ></assignment-upload>

    <assignment-done
      v-bind:classroom-id="classroom_id"
      v-bind:assignment-id="assignment_id"
      v-bind:time="time"
      v-bind:title="title"
      v-bind:replies="replies"
      v-bind:group="group"
      v-bind:role="assignment_post"
      v-if="getRole == 'is_teacher'"
    ></assignment-done>
  </div>
</template>

<script>
import AssignmentUpload from "./AssignmentUpload.vue";
import AssignmentDone from "./AssignmentDone.vue";
import attachment from "../block/attachment.vue";
import moment from "moment";
import { mapGetters } from "vuex";
import vSelect from "vue-select";

export default {
  data() {
    return {
      title: "",
      time: "",
      group: "",
      replies: [],
      date: ["2 พฤศจิกายน 2562", "5 พฤศจิกายน 2562", "30 พฤศจิกายน 2562"],
      dataStatus: ["ทั้งหมด", "ยังไม่ตรวจสอบ", "ตรวจสอบแล้ว"],
      status: "ทั้งหมด",
      baseUrl: this.$store.getters.baseUrl,
      assignment_post: {
        user: {}
      },
      attachment: [],
      windowWidth: window.innerWidth,
      classroom_id: this.$route.params.id,
      assignment_id: this.$route.params.assignment_id,
      token: this.$auth.getToken()
    };
  },
  computed: mapGetters(["getRole"]),
  components: {
    assignmentUpload: AssignmentUpload,
    assignmentDone: AssignmentDone,
    attachment: attachment,
    "v-select": vSelect
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
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
        this.attachment = response.data.attachment;
        this.replies = response.data.replies;
        this.title = this.assignment_post.title;
        this.time = this.assignment_post.due_time;
        this.group = this.assignment_post.group;
      });
  },
  methods: {
    renderHTML(text) {
      if (text) {
        return text.replace(/(\r\n|\n)/g, "<br/>");
      } else {
        return text;
      }
    },
    timeCheck() {
      return moment().isSameOrAfter(
        this.assignment_post.due_time,
        "DD-MM-YYYY"
      );
    },
    back() {
      this.$router.go(-1);
    },
    postTimeFromNow(dateTime) {
      var displayTime = moment(dateTime, "DD-MM-YYYY").fromNow();
      return `Post on ${displayTime}`;
    },
    parseTimeFromNow(dateTime) {
      var displayTime = moment(dateTime, "DD-MM-YYYY").fromNow(true);
      return displayTime;
    },
    parseTime(dateTime) {
      var displayTime = moment(dateTime, "DD-MM-YYYY").format(
        "dddd, MMMM Do YYYY, h:mm:ss a"
      );
      return displayTime;
    },
    parseShortTime(dateTime) {
      var displayTime = moment(dateTime, "DD-MM-YYYY").format("llll");
      return displayTime;
    },
    removePost(post_id) {
      var self = this;
      swal(this.swal_config)
        .then(() => {
          axios
            .delete(`api/post/${post_id}`, {
              headers: {
                Authorization: "Bearer " + self.token
              }
            })
            .then(response => {
              var index = self.posts.findIndex(
                x => x.id == post_id && x.type == "post"
              );
              self.posts.splice(index, 1);

              console.log("remove success");
            });
        })
        .catch(swal.noop);
    }
  }
};
</script>