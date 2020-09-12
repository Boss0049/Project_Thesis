<template>
  <div id="login-page">
    <div class="position">
      <div class="card-login">
        <img src="../../../../public/images/logo/ProjectLogoCMM.png" class="img-logo" />
        <br />
        <img src="../../../../public/images/logo/iconLogin.png" class="img-login" />
        <h4>
          อาจารย์และนักศึกษาสามารถลงชื่อเข้าใช้ระบบ
          <br />ด้วย E-mail ของทางมหาวิทยาลัยได้ที่นี่
        </h4>
        <button @click="logingoogle()" class="btn btn-default btn-login">
          <h3>เข้าสู่ระบบ</h3>
        </button>
        <router-link to="/manualBook/TABook">
          <img :src="`${baseUrl}/images/Icons/IconManualHome.png`" class="img-manualBook" />
          <h4>คู่มือการใช้งาน</h4>
        </router-link>
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
      baseUrl: this.$store.getters.baseUrl
    };
  },
  computed: {
    // googleAuth: () => window.config.googleAuth,
    url: () => `/login/google`
  },
  components: { loading },
  mounted() {
    window.addEventListener("message", this.onMessage, false);
  },

  beforeDestroy() {
    window.removeEventListener("message", this.onMessage);
  },
  methods: {
    logingoogle() {
      const newWindow = openWindow("login/google", "message");
    },
    onMessage(e) {
      if (e.origin !== window.origin || !e.data.token) {
        return;
      }
      this.$auth.setToken(e.data.token, e.data.expires + Date.now());
      this.setData();
    },

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

/**
 * @param  {Object} options
 * @return {Window}
 */
function openWindow(url, title, options = {}) {
  if (typeof url === "object") {
    options = url;
    url = "";
  }

  options = { url, title, width: 600, height: 720, ...options };

  const dualScreenLeft =
    window.screenLeft !== undefined ? window.screenLeft : window.screen.left;
  const dualScreenTop =
    window.screenTop !== undefined ? window.screenTop : window.screen.top;
  const width =
    window.innerWidth ||
    document.documentElement.clientWidth ||
    window.screen.width;
  const height =
    window.innerHeight ||
    document.documentElement.clientHeight ||
    window.screen.height;

  options.left = width / 2 - options.width / 2 + dualScreenLeft;
  options.top = height / 2 - options.height / 2 + dualScreenTop;

  const optionsStr = Object.keys(options)
    .reduce((acc, key) => {
      acc.push(`${key}=${options[key]}`);
      return acc;
    }, [])
    .join(",");

  const newWindow = window.open(url, title, optionsStr);

  if (window.focus) {
    newWindow.focus();
  }

  return newWindow;
}
</script>
