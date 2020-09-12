<template>
  <div id="progress-card">
    <div class="data-meet">
      <div class="pg-card" v-if="this.windowWidth > 767">
        <div class="row">
          <div class="col-md-3 space">
            <h4>{{parseTime(post.assignment.due_time)}}</h4>
          </div>
          <div class="col-md-4 space" style="padding-left: 0;">
            <h4>{{post.assignment.title}}</h4>
          </div>
          <div class="col-md-2 space" style="padding-left: 0;">
            <h4 v-if="isStudent">Group_{{index+1}}</h4>
            <a
              :href="'#group' + post.assignment.id"
              data-toggle="collapse"
              role="button"
              aria-expanded="false"
              aria-controls="collapseExample"
              v-if="!isStudent"
            >
              <h4 class="group" style="color:#419fff">Group_{{index+1}}</h4>
            </a>
          </div>
          <div class="col-md-3 space" style="padding-left: 0;">
            <div v-if="post.assignment.assignment_check[0]">
              <div v-if="post.assignment.assignment_check[0].returned == 0">
                <img class="img-status" src="../../../../../public/images/Icons/statusNoneDone.png" />
                <h4 class="text-status">ยังไม่ตรวจสอบ</h4>
              </div>
              <div v-if="post.assignment.assignment_check[0].returned == 1">
                <img class="img-status" src="../../../../../public/images/Icons/statusDone.png" />
                <h4 class="text-status">ตรวจสอบแล้ว</h4>
              </div>
            </div>
            <div v-else>
              <img class="img-status" src="../../../../../public/images/Icons/statusWait.png" />
              <h4 class="text-status">รอดำเนินการ</h4>
            </div>
            <!-- ******************* -->
            <div class="btn-group dropright">
              <button
                class="btn btn-default dropdown-toggle more-menu"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
              >
                <i class="material-icons">more_vert</i>
              </button>
              <div class="black" v-if="this.windowWidth <= 767"></div>
              <div class="dropdown-menu drop-list">
                <a
                  v-if="post.role_id == 1"
                  class="dropdown-item"
                  :href="'#comment' + post.id"
                  data-toggle="collapse"
                  role="button"
                  aria-expanded="false"
                  aria-controls="collapseExample"
                >
                  <img :src="`${baseUrl}/images/Icons/ProgressCalendar.png`" alt />
                  <h4>การนัดหมาย</h4>
                </a>
                <router-link :to="`checkProgress/`+post.assignment.id" v-if="!isStudent">
                  <a class="dropdown-item" href="#">
                    <img :src="`${baseUrl}/images/Icons/ProgressCheck.png`" alt />
                    <h4>ตรวจสอบความก้าวหน้า</h4>
                  </a>
                </router-link>
                <router-link :to="`replyProgress/`+post.assignment.id" v-if="isStudent">
                  <a class="dropdown-item" href="#">
                    <img :src="`${baseUrl}/images/Icons/ProgressCheck.png`" alt />
                    <h4>บันทึกความก้าวหน้า</h4>
                  </a>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="pg-card" v-if="this.windowWidth <= 767">
        <div class="btn-group dropright">
          <button
            class="btn btn-default dropdown-toggle more-menu"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i class="material-icons">more_vert</i>
          </button>
          <div class="black" v-if="this.windowWidth <= 767"></div>
          <div class="dropdown-menu drop-list">
            <a
              v-if="post.role_id == 1"
              class="dropdown-item"
              :href="'#comment' + post.id"
              data-toggle="collapse"
              role="button"
              aria-expanded="false"
              aria-controls="collapseExample"
            >
              <img :src="`${baseUrl}/images/Icons/ProgressCalendar.png`" alt />
              <h4>การนัดหมาย</h4>
            </a>
            <router-link :to="`checkProgress/`+post.assignment.id" v-if="!isStudent">
              <a class="dropdown-item" href="#">
                <img :src="`${baseUrl}/images/Icons/ProgressCheck.png`" alt />
                <h4>ตรวจสอบความก้าวหน้า</h4>
              </a>
            </router-link>
            <router-link :to="`replyProgress/`+post.assignment.id" v-if="isStudent">
              <a class="dropdown-item" href="#">
                <img :src="`${baseUrl}/images/Icons/ProgressCheck.png`" alt />
                <h4>บันทึกความก้าวหน้า</h4>
              </a>
            </router-link>
          </div>
        </div>
        <div class="status">
          <div v-if="post.assignment.assignment_check[0]">
            <div v-if="post.assignment.assignment_check[0].returned == 0">
              <img class="img-status" src="../../../../../public/images/Icons/statusNoneDone.png" />
            </div>
            <div v-if="post.assignment.assignment_check[0].returned == 1">
              <img class="img-status" src="../../../../../public/images/Icons/statusDone.png" />
            </div>
          </div>
          <div v-else>
            <img class="img-status" src="../../../../../public/images/Icons/statusWait.png" />
          </div>
        </div>
        <div class="title">
          <h4>{{post.assignment.title}}</h4>
        </div>
        <div class="datetime">
          <h4>วันที่ : {{parseTime(post.assignment.due_time)}}</h4>
        </div>
        <div class="group">
          <h4 v-if="!isStudent">
            กลุ่ม :
            <a
              :href="'#group' + post.assignment.id"
              data-toggle="collapse"
              role="button"
              aria-expanded="false"
              aria-controls="collapseExample"
            >
              <h4 class="group" style="color:#419fff">Group_{{index+1}}</h4>
            </a>
          </h4>
          <h4 v-if="isStudent">กลุ่ม : Group_{{index+1}}</h4>
        </div>
      </div>
    </div>

    <!-- ****group**** -->
    <div class="collapse" :id="'group' + post.assignment.id">
      <div class="card card-body">
        <div class="head-resp">
          <h2>ข้อมูลกลุ่ม</h2>
          <a
            :href="'#group' + post.assignment.id"
            data-toggle="collapse"
            role="button"
            aria-expanded="false"
            aria-controls="collapseExample"
          >
            <h2>✕</h2>
          </a>
        </div>
        <div class="row group showgroup">
          <div class="col-md-3">
            <h4 class="title">ชื่อกลุ่ม</h4>
          </div>
          <div class="col-md-9" style="padding-left:0">
            <h4>{{groupsname}}</h4>
          </div>
        </div>
        <div class="row showgroup">
          <div class="col-md-3">
            <h4 class="title">สมาชิกในกลุ่ม</h4>
          </div>
          <div class="col-md-9 nameList" style="padding-left:0">
            <div v-for="group in groups" :key="group.id">
              <h4 v-if="group.role.name=='Student'">{{group.student_id + " " + group.name}}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- end group -->
  </div>
</template>

<script>
import moment from "moment";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      baseUrl: this.$store.getters.baseUrl,
      comments: [],
      returned: "",
      comment_errors: [],
      focus_id: "",
      returned: "",
      windowWidth: window.innerWidth
    };
  },
  computed: {
    ...mapGetters(["isStudent"])
  },
  props: ["post", "classroomId", "showOption", "groups", "groupsname" , "index"],
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
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
    comment(post_id) {
      var data = {
        post_id: post_id,
        comment: this.comments[post_id]
      };

      axios
        .post("api/comments", data, {
          headers: {
            Authorization: "Bearer " + this.token
          }
        })
        .then(response => {
          this.comments[post_id] = "";
          delete this.comments[post_id];

          var index = this.posts.findIndex(x => x.id == post_id);
          this.posts[index].comments.push(response.data);
        })
        .catch(error => {
          this.comment_errors = error.response.data.errors;
          this.focus_id = post_id;
        });
    },
    postTimeFromNow(dateTime) {
      var displayTime = moment(dateTime, "YYYY-MM-DD").fromNow();
      return `Post on ${displayTime}`;
    },
    parseTimeFromNow(dateTime) {
      var displayTime = moment(dateTime, "YYYY-MM-DD").fromNow(true);
      return displayTime;
    },
    parseTime(dateTime) {
      moment.locale("th");
      var displayTime = moment(dateTime, "YYYY-MM-DD HH-mm")
        .add(543, "Y")
        .format("ll");
      return displayTime;
    },
    parseShortTime(dateTime) {
      var displayTime = moment(dateTime, "YYYY-MM-DD HH-mm-ss").format("llll");
      return displayTime;
    },
    removePost(postId) {
      this.$emit("removePost", postId);
    },
    timeCheck(due_time) {
      return moment().isSameOrAfter(due_time, "YYYY-MM-DD");
    }
  }
};
</script>