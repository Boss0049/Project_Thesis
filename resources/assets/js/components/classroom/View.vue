<template>
  <div id="post">
    <!-- add-newpost -->
    <button
      type="button"
      class="btn btn-default add-post"
      data-toggle="modal"
      data-target="#addPost"
      v-if="isTeacher"
    >
      <img :src="`${baseUrl}/images/Icons/IconFeedPost.png`" />
      <h2 class="text-tooltip">โพสต์ข่าวสาร</h2>
    </button>
    <div
      class="modal fade"
      id="addPost"
      tabindex="-1"
      role="dialog"
      aria-labelledby="addPost"
      aria-hidden="true"
    >
      <postComponent></postComponent>
    </div>
    <!-- data-newpost -->
    <div class="classroom" v-if="classroom">
      <div class="data-post" v-if="!posts.length">
        <h1 class="no-post">ยังไม่มีข่าวสาร</h1>
      </div>
      <div class="data-post">
        <div v-for="(post,index) in posts" :key="post.id">
          <div v-if="post.type != 'post' && index == posts.length - 1">
            <h1 class="no-post" v-if="post.type != null">ยังไม่มีข่าวสาร</h1>
          </div>
          <div class="class-post-item" v-if="post.type === 'post'">
            <div v-if="post.display == 1">
              <div v-for="test in filteredPosts" :key="test.id">
                <div v-for="member in test.members" :key="member.id">
                  <post-card
                    :post="post"
                    :classroom-id="classroom.id"
                    :show-option="checkUserPost(post.user.id)"
                    v-on:removePost="removePost"
                    v-if="post.user_id == member.id"
                  ></post-card>
                </div>
              </div>
            </div>
            <div v-else>
              <post-card
                :post="post"
                :classroom-id="classroom.id"
                :show-option="checkUserPost(post.user.id)"
                v-on:removePost="removePost"
              ></post-card>
            </div>

            <div class="collapse" :id="'comment' + post.id">
              <div class="card card-body">
                <div
                  style="overflow-y: auto; overflow-x: hidden;  margin-bottom: 20px;"
                  v-if="post.comments != ''"
                >
                  <div class="row space" v-for="comment in post.comments" :key="comment.id">
                    <div class="col-md-2 center col-resp-20 svg">
                      <div
                        class="img-profile"
                        v-bind:style="{'background-image': `url(${comment.user.avatar_url})`}"
                      />
                    </div>
                    <div class="col-md-10 col-resp-80" style="padding-left: 0;">
                      <div class="name">
                        <h4>{{ comment.user.name }}</h4>
                        <h5
                          v-bind:title="parseTime(comment.created_at)"
                        >{{ parseTimeFromNow(comment.created_at) }}</h5>
                      </div>
                      <div class="body">
                        <h4>{{ comment.comment }}</h4>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row footer">
                  <div class="col-md-2 col-resp-20">
                    <div
                      class="img-profile"
                      v-bind:style="{'background-image': `url(${me.avatar_url})`}"
                    ></div>
                  </div>
                  <div class="col-md-10 col-resp-80" style="padding-left: 0;">
                    <div class="name">
                      <h4>{{me.name}}</h4>
                    </div>
                    <input
                      type="text"
                      class="input-comment"
                      id="comment"
                      placeholder="แสดงความคิดเห็น..."
                      v-model="comments[post.id]"
                      v-on:keyup.enter="comment(post.id)"
                    />
                    <div class="send" v-if="hello" v-on:click="comment(post.id)">
                      <img :src="`${baseUrl}/images/Icons/IconSendText.png`" />
                    </div>
                    <div class="send" v-if="!hello">
                      <img :src="`${baseUrl}/images/Icons/IconSendText.png`" />
                    </div>
                    <p class="text-danger" v-for="error in comment_errors.comment" :key="error.id">
                      <span v-if="comment_errors.comment && focus_id == post.id">{{ error }}</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
import PostCard from "../block/PostCard.vue";
import AssignmentCard from "../block/AssignmentCard.vue";
import postComponent from "../post/Post.vue";

export default {
  data() {
    return {
      baseUrl: this.$store.getters.baseUrl,
      count: "",
      classroom: "",
      posts: [],
      tests: [],
      me: "",
      hello: true,
      comments: {},
      token: "",
      comment_errors: [],
      focus_id: "",
      swal_config: {
        title: "ต้องการลบโพสต์ ?",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "ยืนยัน",
        cancelButtonText: "ยกเลิก",
        showCancelButton: true,
        reverseButtons: true
      },
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
  },
  components: {
    attachments,
    PostCard,
    AssignmentCard,
    postComponent,
    loading
  },
  computed: {
    ...mapGetters(["getUserId", "isTeacher"]),
    filteredPosts() {
      return _.uniqBy(this.tests, function(u) {
        return u.Unit_Type;
      });
    }
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
      this.hello = false;
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
          this.hello = true;
        })
        .catch(error => {
          this.comment_errors = error.response.data.errors;
          this.focus_id = post_id;
          this.hello = true;
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
      moment.locale("th");
      var displayTime = moment(dateTime, "YYYY-MM-DD HH-mm-ss").fromNow();
      return `${displayTime}`;
    },
    parseTime(dateTime) {
      moment.locale("th");
      var displayTime = moment(dateTime, "YYYY-MM-DD HH-mm-ss").format(
        "dddd, MMMM Do YYYY, h:mm:ss a"
      );
      return displayTime;
    }
  }
};
</script>
