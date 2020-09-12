<template>
  <div id="navbar-page" v-if="!hide">
    <div class="nav-tab">
      <div class="row">
        <div class="col-md-4 space col-resp">
          <router-link to="/">
            <img
              v-bind:src="`${baseUrl}/images/logo/ProjectLogoCMM.png`"
              class="logo"
              v-if="this.windowWidth > 767"
            />
            <img
              v-bind:src="`${baseUrl}/images/logo/ProjectLogoColor.png`"
              class="logo"
              v-if="this.windowWidth <= 767"
            />
          </router-link>
        </div>
        <div class="col-md-8 right col-resp" v-if="profile.name != null">
          <div class="dropdown">
            <button
              class="btn btn-default name"
              type="button"
              id="dropdownMenuButton"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <div
                class="img-profile"
                v-bind:style="{'background-image': `url(${profile.avatar_url})`}"
              ></div>
              <h2>
                <span v-if="this.windowWidth > 767">{{profile.name}}</span>
                <i class="material-icons">arrow_drop_down</i>
              </h2>
            </button>
            <div class="black" v-if="this.windowWidth <= 767"></div>
            <div
              class="dropdown-menu dropdown-menu-right drop-list"
              aria-labelledby="dropdownMenuButton"
            >
              <a class="dropdown-item" data-toggle="modal" data-target="#editProfile">
                <img :src="`${baseUrl}/images/Icons/DropNameEditProfile.png`" />
                <h4>แก้ไขโปรไฟล์</h4>
              </a>
              <router-link to="/manualBook/TABook" class="dropdown-item">
                <img :src="`${baseUrl}/images/Icons/DropNameManual.png`" />
                <h4>คู่มือการใช้งาน</h4>
              </router-link>
              <router-link to="/logout" class="dropdown-item">
                <img :src="`${baseUrl}/images/Icons/DropNameLogout.png`" />
                <h4>ออกจากระบบ</h4>
              </router-link>
            </div>
          </div>
          <!-- ***Edit Profile*** -->
          <div
            class="modal fade bd-example-modal-lg"
            id="editProfile"
            tabindex="-1"
            role="dialog"
            aria-labelledby="profile"
            aria-hidden="true"
          >
            <profile></profile>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { mapState, mapGetters } from "vuex";
import profile from "../components/user/profile.vue";
import swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

export default {
  data() {
    return {
      transparent: false,
      hide: false,
      baseUrl: this.$store.getters.baseUrl,
      profile: {},
      windowWidth: window.innerWidth,
      show_join: false
    };
  },
  components: {
    profile
  },
  computed: {
    ...mapState(["isLoggedIn"]),
    ...mapGetters(["getName", "isTeacher", "getUser"])
  },
  mounted() {
    this.$store.watch(
      state => {
        return state.user;
      },
      user => {
        this.profile = Object.assign({}, this.getUser);
      }
    );
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  created() {
    this.$store.dispatch("getPersonal");
    this.setStyle(this.$route);
    this.$watch("$route", this.setStyle);
  },
  methods: {
    setStyle(route) {
      this.transparent = false;
      this.hide = false;
      if (route.meta.navstyle) {
        if (route.meta.navstyle.transparent) {
          this.transparent = route.meta.navstyle.transparent;
        }

        if (route.meta.navstyle.hide) {
          this.hide = route.meta.navstyle.hide;
        }
      }
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
      }).then(function(code) {
        self.requestJoin(code);
      });
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
          this.$router.push(response.data.classroom_id + "/main/post");
        });
    }
  }
};
</script>