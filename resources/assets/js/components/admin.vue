<template>
  <div id="admin-login">
    <div class="card-login">
      <div class="row">
        <div class="col-md-6 center style">
          <img src="../../../../public/images/logo/ProjectLogoColor.png" class="logo" />
          <h4 class="heading">Progress Tracking System</h4>
          <img
            src="../../../../public/images/logo/iconLogin.png"
            class="logo"
            v-if="this.windowWidth > 767"
          />
        </div>
        <div class="col-md-6 space center">
          <form v-on:submit.prevent="login">
            <div class="form-group">
              <input
                type="text"
                class="form-control"
                id="username"
                required
                v-model="form.username"
                placeholder="ชื่อผู้ใช้"
              />
            </div>
            <div class="form-group">
              <input
                type="password"
                class="form-control"
                id="password"
                required
                v-model="form.password"
                placeholder="รหัสผ่าน"
              />
              <h5 class="login-error" v-if="login_error">
                <span class="glyphicon glyphicon-alert"></span>ชื่อผู้ใช้งาน หรือรหัสผ่านไม่ถูกต้อง
              </h5>
            </div>
            <button type="submit" class="btn btn-default btn-login">
              <h4>เข้าสู่ระบบ</h4>
            </button>
            <div class="loading" v-if="loading">
              <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
              <h5>กำลังเข้าสู่ระบบ...</h5>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import loading from "./elements/loading";
import config from "../config.js";

export default {
  data() {
    return {
      form: {
        username: "",
        password: ""
      },
      login_error: false,
      loading: false,
      baseUrl: this.$store.getters.baseUrl,
      windowWidth: window.innerWidth
    };
  },
  components: { loading },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  methods: {
    login() {
      this.login_error = false;
      this.loading = true;
      var data = {
        grant_type: config.grant_type,
        client_id: config.client_id,
        client_secret: config.client_secret,
        username: this.form.username,
        password: this.form.password
      };
      axios
        .post("oauth/token", data)
        .then(response => {
          this.$auth.setToken(
            response.data.access_token,
            response.data.expires_in + Date.now()
          );
          this.setData();
        })
        .catch(response => {
          this.loading = false;
          this.login_error = true;
        });
    },
    setData() {
      axios
        .get("api/me", {
          headers: {
            Authorization: "Bearer " + this.$auth.getToken()
          }
        })
        .then(response => {
          this.$store.commit("storeUser", response.data);
          this.$router.push("/");
        });
    }
  }
};
</script>
