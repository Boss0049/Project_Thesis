<template>
  <div id="edit-profile" class="modeltest">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" v-if="profile.role">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title" id="profile">บันทึกข้อมูล</h2>
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
            @click="ToggleEnableclose()"
          >
            <span aria-hidden="true">✕</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4 center-resp">
              <div
                class="profile-picture profile-picture-large"
                v-bind:style="{'background-image': `url(${profile.avatar_url})`}"
              >
                <upload-avatar
                  v-if="showUpload"
                  v-on:uploaded="uploadComplete"
                  :callback="'/api/me/avatar/upload'"
                ></upload-avatar>
              </div>
            </div>
            <div class="col-md-8 left space center-resp">
              <div class="name">
                <h2>{{profile.name}}</h2>
                <h2 v-if="isStudent">รหัสศึกษา {{profile.student_id}}</h2>
                <h2 v-if="isStudent">ชั้นปี {{profile.grade_id}}</h2>
              </div>
            </div>
          </div>
          <div class="row margin">
            <div class="col-md-4 left-resp">
              <h2>
                E-mail
                <span v-if="this.windowWidth > 767">:</span>
              </h2>
            </div>
            <div class="col-md-8 left">
              <a :href="'mailto:'+ profile.email" target="_top">
                <h2>{{ profile.email }}</h2>
              </a>
            </div>
          </div>
          <div class="row margin">
            <div class="col-md-4 left-resp">
              <h2>
                เบอร์โทรศัพท์
                <span v-if="this.windowWidth > 767">:</span>
              </h2>
            </div>
            <div class="col-md-8 left">
              <div class="body-edit">
                <a :href="'tel:'+ profile.phone" target="_top" v-if="!editable">
                  <h2>{{ profile.phone }}</h2>
                </a>
                <div class="form-input" v-if="editable">
                  <input
                    type="text"
                    name="tel"
                    placeholder="00-000-00000"
                    :maxlength="max"
                    v-model="profile.phone"
                    class="editData"
                  />
                  <!-- <p class="text-danger" v-for="error in profile_errors.phone" :key="error.id">
                    <span v-if="profile_errors.phone">{{ error }}</span>
                  </p>-->
                </div>
              </div>
            </div>
          </div>
          <div class="row margin">
            <div class="col-md-4 left-resp">
              <h2>
                Facebook
                <span v-if="this.windowWidth > 767">:</span>
              </h2>
            </div>
            <div class="col-md-8 left">
              <div class="body-edit">
                <h2 v-if="!editable">{{ profile.facebook }}</h2>
                <div class="form-input" v-if="editable">
                  <input
                    type="text"
                    name="facebook"
                    placeholder="เฟซบุ๊ก"
                    v-model="profile.facebook"
                    class="editData"
                  />
                  <!-- <p class="text-danger" v-for="error in profile_errors.facebook" :key="error.id">
                    <span v-if="profile_errors.facebook">{{ error }}</span>
                  </p>-->
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-default submit"
            aria-label="Close"
            @click="ToggleEnableEdit()"
            v-if="!editable"
          >
            <h2>เเก้ไข</h2>
          </button>
          <button
            type="button"
            class="btn btn-default submit"
            aria-label="Close"
            v-if="editable"
            @click="changeProfile()"
          >
            <h2>บันทึก</h2>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import swal from "sweetalert2";
import VueLetterAvatar from "vue-letter-avatar";
import "sweetalert2/dist/sweetalert2.min.css";
import uploadAvatar from "../block/uploadAvatar.vue";
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      error_pro: [],
      max: 10,
      name: "",
      typemail: "testtest.test@mail.kmutt.ac.th",
      profile: {},
      security: {
        password: "",
        new_password: "",
        new_password_confirmation: ""
      },
      showUpload: false,
      profile_errors: {},
      editable: false,
      swal_config: {
        title: "Complete",
        text: "Update complete!",
        type: "success",
        confirmButtonColor: "#3085d6"
      },
      windowWidth: window.innerWidth,
      user: this.getUser
    };
  },
  components: {
    uploadAvatar
  },
  computed: {
    ...mapGetters(["getUser", "isStudent", "isTeacher"])
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
    this.profile = Object.assign({}, this.getUser);
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  methods: {
    uploadComplete(url) {
      this.profile.avatar_url = url;
      this.$store.dispatch("getPersonal");
    },
    ToggleEnableclose() {
      this.editable = false;
      this.showUpload = false;
    },
    ToggleEnableEdit() {
      this.editable = !this.editable;
      this.showUpload = !this.showUpload;
      this.profile = Object.assign({}, this.getUser);
    },
    changeProfile() {
      var data = {
        facebook: this.profile.facebook,
        phone: this.profile.phone
      };
      axios
        .patch("api/me", data, {
          headers: {
            Authorization: "Bearer " + this.$auth.getToken()
          }
        })
        .then(response => {
          this.$store.dispatch("getPersonal");
          this.editable = !this.editable;
          this.showUpload = !this.showUpload;
          console.log("change profile successful");
        })
        .catch(error => {
          this.profile_errors = error.response.data.errors;
        });
    }
  }
};
</script>
