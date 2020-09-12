<template>
  <div id="data-post">
    <div class="card-post">
      <div class="head-post">
        <div class="row">
          <div class="col-md-2 center col-resp-20 left-resp">
            <div
              class="img-profile"
              v-bind:style="{'background-image': `url(${post.user.avatar_url})`}"
            />
          </div>
          <div class="col-md-7 margin col-resp-60">
            <div class="name">
              <h3>{{post.user.name}}</h3>
              <h5
                v-bind:title="parseTime(post.created_at)"
                class="time"
              >{{ parseTimeFromNow(post.created_at) }}</h5>
            </div>
          </div>
          <div class="col-md-3 margin right col-resp-20">
            <div class="display">
              <span v-if="post.display == 0">
                <img :src="`${baseUrl}/images/Icons/IconFeedinProject.png`" />
                <h4 v-if="this.windowWidth > 1024">ทุกคน</h4>
              </span>
              <span v-else>
                <img :src="`${baseUrl}/images/Icons/IconFeedinGroup.png`" class="IconFeedinGroup" />
                <h4 v-if="this.windowWidth > 1024">เฉพาะในกลุ่ม</h4>
              </span>
            </div>
            <div class="btn-group dropright" v-if="isTeacher">
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
                  class="dropdown-item"
                  data-toggle="modal"
                  data-target="#delete"
                  href="javascript:void(0)"
                  @click="removePost(post.id)"
                >
                  <img :src="`${baseUrl}/images/Icons/ProjectDelete.png`" />
                  <h4>ลบ</h4>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="clearfix"></div>
      <div class="body-post">
        <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-10 body" style="padding-left: 0; margin: 5px 0;">
            <h4 v-html="renderHTML(post.detail)"></h4>
            <attachments :files="post.attachments"></attachments>
          </div>
        </div>
      </div>

      <div class="footer-post right">
        <h4 v-if="post.comments.length == 0">ยังไม่มีความคิดเห็น</h4>
        <h4 v-else>{{ post.comments.length }} ความคิดเห็น</h4>
        <div class="comment">
          <a
            :href="'#comment' + post.id"
            data-toggle="collapse"
            role="button"
            aria-expanded="false"
            aria-controls="collapseExample"
          >
            <img :src="`${baseUrl}/images/Icons/IconFeedCommenttalk.png`" />
            <h4>แสดงความคิดเห็น</h4>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import attachments from "../block/attachment.vue";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      baseUrl: this.$store.getters.baseUrl,
      windowWidth: window.innerWidth
    };
  },
  components: {
    attachments
  },
  computed: {
    ...mapGetters(["isTeacher"])
  },
  props: ["post", "classroomId", "showOption"],
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
    parseTimeFromNow(dateTime) {
      moment.locale("th");
      var displayTime = moment(dateTime, "YYYY-MM-DD HH-mm-ss").fromNow();
      return `ได้อัปโหลดไฟล์เมื่อ ${displayTime}`;
    },
    parseTime(dateTime) {
      moment.locale("th");
      var displayTime = moment(dateTime, "YYYY-MM-DD HH-mm-ss").format(
        "dddd, MMMM Do YYYY, h:mm:ss a"
      );
      return displayTime;
    },
    removePost(postId) {
      this.$emit("removePost", postId);
    }
  }
};
</script>
