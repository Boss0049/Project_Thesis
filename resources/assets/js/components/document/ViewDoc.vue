<template>
  <div id="document">
    <h1 v-if="this.windowWidth <= 767">เอกสาร</h1>
    <div class="title" v-if="this.windowWidth > 767">
      <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-3">
          <h3 class>ชื่อเอกสาร</h3>
        </div>
        <div class="col-md-2">
          <h3 class>ประเภท</h3>
        </div>
        <div class="col-md-3">
          <h3 class>วันที่-เวลา</h3>
        </div>
        <div class="col-md-3">
          <h3 class>ผู้สร้างเอกสาร</h3>
        </div>
      </div>
    </div>

    <div v-if="classroom">
      <div v-for="(post,index) in posts" :key="index">
        <!-- <div v-if="post.type != 'post'">
          <h1 class="no-post" v-if="index == posts.length - 1">ยังไม่มีข่าวสาร</h1>
        </div>-->
        <div v-if="post.display == 1">
          <div v-for="test in tests" :key="test.id">
            <div v-for="member in test.members" :key="member.id">
              <document
                :files="post.attachments"
                :users="post.user.name"
                :post="post"
                :classroom-id="classroom.id"
                :show-option="checkUserPost(post.user.id)"
                v-on:removePost="removePost"
                v-if="post.user_id == member.id"
              ></document>
            </div>
          </div>
        </div>
        <div v-else>
          <document
            :files="post.attachments"
            :users="post.user.name"
            :post="post"
            :classroom-id="classroom.id"
            :show-option="checkUserPost(post.user.id)"
            v-on:removePost="removePost"
          ></document>
        </div>
      </div>
    </div>
    <loading v-else></loading>
  </div>
</template>

<script>
import loading from "../elements/loading";
import moment from "moment";
import swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { mapGetters } from "vuex";
import attachments from "../block/attachment.vue";
import Document from "./Document.vue";
import AssignmentCard from "../block/AssignmentCard.vue";
import postComponent from "../post/Post.vue";

export default {
  data() {
    return {
      baseUrl: this.$store.getters.baseUrl,
      classroom: "",
      posts: [],
      tests: [],
      comments: {},
      token: "",
      comment_errors: [],
      focus_id: "",
      windowWidth: window.innerWidth
    };
  },
  created() {
    var classroom_id = this.$route.params.id;
    this.token = this.$auth.getToken();

    axios
      .get("api/classroom/" + classroom_id, {
        headers: {
          Authorization: "Bearer " + this.token
        }
      })
      .then(response => {
        this.classroom = response.data.classroom;
        this.posts = response.data.posts;
      })
      .catch(error => {
        this.$router.push("/");
      });

    axios
      .get("api/group/" + classroom_id, {
        headers: {
          Authorization: "Bearer " + this.token
        }
      })
      .then(response => {
        this.tests = response.data;
      })
      .catch(error => {
        this.$router.push("/");
      });
  },
  components: {
    attachments,
    Document,
    AssignmentCard,
    postComponent,
    loading
  },
  computed: {
    ...mapGetters(["getUserId", "isTeacher"])
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  methods: {
    teachers() {
      var teachers = this.classroom.members.filter(user => {
        return user.role.actions === "is_teacher";
      });
      return teachers;
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
    checkUserPost(user_id) {
      return this.getUserId == user_id;
    },
    removeAssignment(post_id) {
      var self = this;
      swal(this.swal_config)
        .then(() => {
          axios
            .delete(
              `api/classroom/${self.classroom.id}/assignment/${post_id}`,
              {
                headers: {
                  Authorization: "Bearer " + self.token
                }
              }
            )
            .then(response => {
              var index = self.posts.findIndex(
                x => x.assignment.id == post_id && x.type == "assignment"
              );
              self.posts.splice(index, 1);

              console.log("remove success");
            });
        })
        .catch(swal.noop);
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
    },
    deleteClass() {
      var self = this;
      swal(this.swal_config)
        .then(() => {
          axios
            .delete(`api/classroom/${self.classroom.id}`, {
              headers: {
                Authorization: "Bearer " + self.token
              }
            })
            .then(response => {
              console.log("remove class success");
              self.$router.push("/");
            });
        })
        .catch(swal.noop);
    },
    parseTimeFromNow(dateTime) {
      var displayTime = moment(dateTime, "YYYY-MM-DD HH-mm-ss").fromNow();
      return `ส่งเมื่อ ${displayTime}`;
    },
    parseTime(dateTime) {
      var displayTime = moment(dateTime, "YYYY-MM-DD HH-mm-ss").format(
        "dddd, MMMM Do YYYY, h:mm:ss a"
      );
      return displayTime;
    }
  }
};
</script>