<template>
  <div id="accept">
    <div class="card-accept">
      <div class="header">
        <h2>การยืนยันสถานะอาจารย์</h2>
      </div>
      <div class="body">
        <div class="row">
          <div class="col-7 space">
            <h3>{{user.name}}</h3>
          </div>
          <div class="col-5">
            <div class="row">
              <div class="col-6">
                <button class="btn btn-default cancel submit" @click="cancel(user.id)">
                  <h4>ยกเลิก</h4>
                </button>
              </div>
              <div class="col-6 right">
                <button class="btn btn-default submit" @click="updatestatus(user.id)">
                  <h4>อนุมัติ</h4>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      windowWidth: window.innerWidth,
      token: this.$auth.getToken(),
      accept_errors: ""
    };
  },
  props: ["user"],
  created() {
    axios
      .get("api/members", {
        headers: {
          Authorization: "Bearer " + this.token
        }
      })
      .then(response => {
        this.users = response.data;
      });
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  methods: {
    updatestatus(id) {
      var data = {
        user_id: id,
        role_id: 1
      };
      axios
        .patch("api/accept", data, {
          headers: {
            Authorization: "Bearer " + this.$auth.getToken()
          }
        })
        .then(response => {
          this.$store.dispatch("getPersonal");
          this.$router.go("/");
          // window.location.href = "/";
        })
        .catch(error => {
          this.accept_errors = error.response;
        });
    },
    cancel(id) {
      var data = {
        user_id: id,
        role_id: 5
      };
      axios
        .patch("api/accept", data, {
          headers: {
            Authorization: "Bearer " + this.$auth.getToken()
          }
        })
        .then(response => {
          this.$store.dispatch("getPersonal");
          this.$router.go("/");
          // window.location.href = "/";
        })
        .catch(error => {
          this.accept_errors = error.response;
        });
    }
  }
};
</script>