<template>
  <div id="progress-page" v-if="classroom">
    <div v-if="isTeacher">
      <h1 v-if="this.windowWidth <= 767">ความก้าวหน้า</h1>
      <router-link to="createMeet" v-if="this.windowWidth <= 767">
        <button class="btn btn-default addMeet">
          <h3>สร้างนัดหมาย</h3>
        </button>
      </router-link>
      <div v-if="this.windowWidth <= 767" class="meet">
        <h2>การเข้าพบ</h2>
        <div class="select">
          <v-select :options="options" v-model="search"></v-select>
        </div>
      </div>

      <div class="row group-meet" v-if="this.windowWidth > 767">
        <div class="col-md-8">
          <h2>ความก้าวหน้าในการเข้าพบ</h2>
          <div class="select">
            <v-select :options="options" v-model="search"></v-select>
          </div>
        </div>
        <div class="col-md-4 right">
          <router-link to="createMeet">
            <button class="btn btn-default addMeet">
              <h3>สร้างนัดหมาย</h3>
            </button>
          </router-link>
        </div>
      </div>
      <!-- ******************** -->
      <div class="showdata">
        <h3>อาจารย์ที่ปรึกษานัดหมาย :</h3>
        <div class="row" v-if="this.windowWidth > 767">
          <div class="col-md-3">
            <h4>วันที่นัดหมาย</h4>
          </div>
          <div class="col-md-4" style="padding-left: 0;">
            <h4>เรื่องที่นัดหมาย</h4>
          </div>
          <div class="col-md-2" style="padding-left: 0;">
            <h4>กลุ่ม</h4>
          </div>
          <div class="col-md-3" style="padding-left: 0;">
            <h4>สถานะ</h4>
          </div>
        </div>
      </div>
      <!-- TA -->
      <div class="classroom" v-if="classroom ">
        <div class="data-post" v-if="!posts.length">
          <h3 class="no-post">ยังไม่มีการบันทึกความก้าวหน้า</h3>
        </div>
        <div class="data-post" v-else>
          <div v-for="(post,index) in filterSearch" :key="index">
            <div v-if="post.role_id == 1">
              <div class="class-post-item" v-if="post.type == 'assignment' && post.assignment">
                <div v-for="(test,index) in tests" :key="index">
                  <assignment-card
                    :post="post"
                    :index="index"
                    :groups="test.members"
                    :groupsname="test.name"
                    :classroom-id="classroom.id"
                    :show-option="checkUserPost(post.user.id)"
                    v-on:removePost="removeAssignment"
                    v-if="post.assignment.group.id == test.id"
                  ></assignment-card>
                </div>
                <!-------------------------------------- comment ----------------------------------->
                <div class="sec-comment collapse" :id="'comment' + post.id">
                  <div class="card card-body">
                    <div class="head-resp">
                      <h2>การนัดหมาย</h2>
                      <a
                        :href="'#comment' + post.id"
                        data-toggle="collapse"
                        role="button"
                        aria-expanded="false"
                        aria-controls="collapseExample"
                      >
                        <h2>✕</h2>
                      </a>
                    </div>
                    <div class="title">
                      <img :src="`${baseUrl}/images/Icons/IconProgressPin.png`" class="pin" />
                      <h4>การนัดหมาย :</h4>
                      <div class="content">
                        <h4>{{post.assignment.detail}}</h4>
                      </div>
                    </div>
                    <div style="overflow-y: auto; overflow-x: hidden;">
                      <div class="row space" v-for="comment in post.comments" :key="comment.id">
                        <div class="col-md-2 center col-25-resp">
                          <div
                            class="img-profile"
                            v-bind:style="{'background-image': `url(${comment.user.avatar_url})`}"
                          />
                        </div>
                        <div class="col-md-10 data-comment col-75-resp" style="padding-left: 0;">
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
                    <div class="row footer space" v-on:submit.prevent="comment(post.id)">
                      <div class="col-md-2 col-25-resp">
                        <div
                          class="img-profile"
                          v-bind:style="{'background-image': `url(${me.avatar_url})`}"
                        ></div>
                      </div>
                      <div class="col-md-10 col-75-resp" style="padding-left: 0;">
                        <div class="name">
                          <h4>{{me.name}}</h4>
                        </div>
                        <form>
                          <div class="input">
                            <input
                              type="text"
                              class="input-comment"
                              id="comment"
                              placeholder="แสดงความคิดเห็น..."
                              v-model="comments[post.id]"
                            />
                            <div class="send" v-on:click="comment(post.id)">
                              <img
                                :src="`${baseUrl}/images/Icons/IconSendText.png`"
                                class="img-send"
                              />
                            </div>
                          </div>
                          <p
                            class="text-danger"
                            v-for="error in comment_errors.comment"
                            :key="error.id"
                          >
                            <span v-if="comment_errors.comment && focus_id == post.id">{{ error }}</span>
                          </p>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
                <!-------------------------------------- endcomment ----------------------------------->
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- ******************** -->
      <div class="showdata">
        <h3>นักศึกษาเข้าพบด้วยตนเอง :</h3>
        <div class="row" v-if="this.windowWidth > 767">
          <div class="col-md-3">
            <h4>วันที่นัดหมาย</h4>
          </div>
          <div class="col-md-4" style="padding-left: 0;">
            <h4>เรื่องที่เข้าพบ</h4>
          </div>
          <div class="col-md-2" style="padding-left: 0;">
            <h4>กลุ่ม</h4>
          </div>
          <div class="col-md-3" style="padding-left: 0;">
            <h4>สถานะ</h4>
          </div>
        </div>
      </div>
      <!-- STU   -->
      <div class="classroom" v-if="classroom ">
        <div class="data-post" v-if="!posts.length">
          <h3 class="no-post">ยังไม่มีการบันทึกความก้าวหน้า</h3>
        </div>
        <div class="data-post" v-else>
          <div v-for="(post,index) in filterSearch" :key="index">
            <div v-if="post.role_id == 2">
              <div class="class-post-item" v-if="post.type == 'assignment' && post.assignment">
                <div v-for="(test,index) in tests" :key="index">
                  <assignment-card
                    :post="post"
                    :index="index"
                    :groups="test.members"
                    :groupsname="test.name"
                    :classroom-id="classroom.id"
                    :show-option="checkUserPost(post.user.id)"
                    v-on:removePost="removeAssignment"
                    v-if="post.assignment.group.id == test.id"
                  ></assignment-card>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ******************************************* STU side ******************************************* -->
    <div v-if="isStudent">
      <h1 v-if="this.windowWidth <= 767">ความก้าวหน้า</h1>
      <router-link to="createProgress">
        <button type="button" class="btn btn-default btn-add-progress">
          <img :src="`${baseUrl}/images/Icons/IconProgressAdd.png`" />
          <h2 class="text-tooltip">นักศึกษาเข้าพบเอง</h2>
        </button>
      </router-link>

      <div class="showdata">
        <h3>อาจารย์ที่ปรึกษานัดหมาย :</h3>
        <div class="row" v-if="this.windowWidth > 767">
          <div class="col-md-3">
            <h4>วันที่นัดหมาย</h4>
          </div>
          <div class="col-md-4" style="padding-left: 0;">
            <h4>เรื่องที่นัดหมาย</h4>
          </div>
          <div class="col-md-2" style="padding-left: 0;">
            <h4>กลุ่ม</h4>
          </div>
          <div class="col-md-3" style="padding-left: 0;">
            <h4>สถานะ</h4>
          </div>
        </div>
      </div>
      <!-- TA -->
      <div class="classroom" v-if="classroom ">
        <div class="data-post" v-if="!posts.length">
          <h3 class="no-post">ยังไม่มีการบันทึกความก้าวหน้า</h3>
        </div>
        <div class="data-post" v-else>
          <div v-for="post in posts" :key="post.id">
            <div v-if="post.role_id == 1">
              <div class="class-post-item" v-if="post.type == 'assignment' && post.assignment">
                <div v-for="(test,index) in tests" :key="index">
                  <assignment-card
                    :post="post"
                    :index="index"
                    :groups="test.members"
                    :groupsname="test.name"
                    :classroom-id="classroom.id"
                    :show-option="checkUserPost(post.user.id)"
                    v-on:removePost="removeAssignment"
                    v-if="post.assignment.group.id == test.id"
                  ></assignment-card>
                </div>
                <!-------------------------------------- comment ----------------------------------->

                <div class="sec-comment collapse" :id="'comment' + post.id">
                  <div class="card card-body">
                    <div class="head-resp">
                      <h2>การนัดหมาย</h2>
                      <a
                        :href="'#comment' + post.id"
                        data-toggle="collapse"
                        role="button"
                        aria-expanded="false"
                        aria-controls="collapseExample"
                      >
                        <h2>✕</h2>
                      </a>
                    </div>
                    <div class="title">
                      <img :src="`${baseUrl}/images/Icons/IconProgressPin.png`" class="pin" />
                      <h4>การนัดหมาย :</h4>
                      <div class="content">
                        <h4>{{post.assignment.detail}}</h4>
                      </div>
                    </div>
                    <div style="overflow-y: auto; overflow-x: hidden;">
                      <div class="row space" v-for="comment in post.comments" :key="comment.id">
                        <div class="col-md-2 center col-25-resp">
                          <div
                            class="img-profile"
                            v-bind:style="{'background-image': `url(${comment.user.avatar_url})`}"
                          />
                        </div>
                        <div class="col-md-10 data-comment col-75-resp" style="padding-left: 0;">
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

                    <div class="row footer space" v-on:submit.prevent="comment(post.id)">
                      <div class="col-md-2 col-25-resp">
                        <div
                          class="img-profile"
                          v-bind:style="{'background-image': `url(${me.avatar_url})`}"
                        ></div>
                      </div>
                      <div class="col-md-10 col-75-resp" style="padding-left: 0;">
                        <div class="name">
                          <h4>{{me.name}}</h4>
                        </div>
                        <form>
                          <div class="input">
                            <input
                              type="text"
                              class="input-comment"
                              id="comment"
                              placeholder="แสดงความคิดเห็น..."
                              v-model="comments[post.id]"
                            />
                            <div class="send" v-on:click="comment(post.id)">
                              <img
                                :src="`${baseUrl}/images/Icons/IconSendText.png`"
                                class="img-send"
                              />
                            </div>
                          </div>
                          <p
                            class="text-danger"
                            v-for="error in comment_errors.comment"
                            :key="error.id"
                          >
                            <span v-if="comment_errors.comment && focus_id == post.id">{{ error }}</span>
                          </p>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
                <!-------------------------------------- endcomment ----------------------------------->
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- ******************** -->
      <div class="showdata">
        <h3>นักศึกษาเข้าพบด้วยตนเอง :</h3>
        <div class="row" v-if="this.windowWidth > 767">
          <div class="col-md-3">
            <h4>วันที่นัดหมาย</h4>
          </div>
          <div class="col-md-4" style="padding-left: 0;">
            <h4>เรื่องที่เข้าพบ</h4>
          </div>
          <div class="col-md-2" style="padding-left: 0;">
            <h4>กลุ่ม</h4>
          </div>
          <div class="col-md-3" style="padding-left: 0;">
            <h4>สถานะ</h4>
          </div>
        </div>
      </div>

      <!-- STU   -->
      <div class="classroom" v-if="classroom ">
        <div class="data-post" v-if="!posts.length">
          <h3 class="no-post">ยังไม่มีการบันทึกความก้าวหน้า</h3>
        </div>
        <div class="data-post" v-else>
          <div v-for="post in posts" :key="post.id">
            <div v-if="post.role_id == 2">
              <div class="class-post-item" v-if="post.type == 'assignment' && post.assignment">
                <div v-for="(test,index) in tests" :key="index">
                  <assignment-card
                    :post="post"
                    :index="index"
                    :groups="test.members"
                    :groupsname="test.name"
                    :classroom-id="classroom.id"
                    :show-option="checkUserPost(post.user.id)"
                    v-on:removePost="removeAssignment"
                    v-if="post.assignment.group.id == test.id"
                  ></assignment-card>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <loading v-else></loading>
</template>

<script>
import vSelect from "vue-select";
import moment from "moment";
import swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { mapGetters } from "vuex";
import attachments from "../block/attachment.vue";
import PostCard from "../block/PostCard.vue";
import AssignmentCard from "../block/AssignmentCard.vue";
import loading from "../elements/loading";

export default {
  data() {
    return {
      baseUrl: this.$store.getters.baseUrl,
      teacher: true,
      student: false,
      options: [{ label: "ทั้งหมด", value: "" }],
      viewGroup: "ทั้งหมด",
      classroom: "",
      search: "ทั้งหมด",
      posts: [],
      tests: [],
      comments: {},
      token: "",
      comment_errors: [],
      focus_id: "",
      swal_config: {
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      },
      windowWidth: window.innerWidth,
      me: ""
    };
  },
  updated() {
    this.viewGroup;
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
        for (let i = 0; i < response.data.length; i++) {
          this.options.push({
            label: response.data[i].name,
            value: response.data[i].id
          });
        }
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
    "v-select": vSelect,
    loading
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  computed: {
    ...mapGetters(["getUserId", "isTeacher", "isStudent"]),
    filterSearch() {
      return this.posts.filter(post => {
        return post.group_id.toString().match(this.search.value);
      });
    }
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
      return `ส่งเมื่อ ${displayTime}`;
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

