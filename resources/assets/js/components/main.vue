<template>
  <div id="main-page">
    <div class="navtab">
      <ul>
        <li>
          <router-link to="post">
            <h2 v-if="this.windowWidth > 767">ข่าวสาร</h2>
            <img
              v-bind:src="`${baseUrl}/images/Icons/Mobile/Mobile-1Feed.png`"
              class="img-menu-1"
              v-if="this.windowWidth <= 767"
            />
          </router-link>
        </li>
        <li>
          <router-link to="document">
            <h2 v-if="this.windowWidth > 767">เอกสาร</h2>
            <img
              src="../../../../public/images/Icons/Mobile/Mobile-3Doc.png"
              class="img-menu-2"
              v-if="this.windowWidth <= 767"
            />
          </router-link>
        </li>
        <li>
          <router-link to="group">
            <h2 v-if="this.windowWidth > 767">กลุ่ม</h2>
            <img
              src="../../../../public/images/Icons/Mobile/Mobile-5Group.png"
              class="img-menu-3"
              v-if="this.windowWidth <= 767"
            />
          </router-link>
        </li>
        <li>
          <router-link to="progress">
            <h2 v-if="this.windowWidth > 767">ความก้าวหน้า</h2>
            <img
              src="../../../../public/images/Icons/Mobile/Mobile-7Progress.png"
              class="img-menu-4"
              v-if="this.windowWidth <= 767"
            />
          </router-link>
        </li>
      </ul>
    </div>
    <div class="body-main">
      <div class="head-newpost" v-if="this.windowWidth > 767">
        <div class="style-breadcrumb">
          <ul>
            <router-link to="/">
              <h2>โครงงาน / รายวิชาทั้งหมด 〉</h2>
            </router-link>
            <!-- <router-link to="post">
              <h2>Project_{{$route.params.id}} 〉</h2>
            </router-link>-->
            <li
              v-for="(breadcrumb, idx) in breadcrumbList"
              :key="idx"
              @click="routeTo(idx)"
              :class="{'linked': !!breadcrumb.link}"
            >
              <h2>{{ breadcrumb.name }}</h2>
            </li>
          </ul>
        </div>
      </div>
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      breadcrumbList: [],
      baseUrl: this.$store.getters.baseUrl,
      windowWidth: window.innerWidth
    };
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
  },
  created() {
    this.$store.dispatch("getPersonal");
    // this.$watch("$route", this.setStyle);
  },
  mounted() {
    this.updateList();
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  watch: {
    $route() {
      this.updateList();
    }
  },
  methods: {
    routeTo(pRouteTo) {
      if (this.breadcrumbList[pRouteTo].link)
        this.$router.push(this.breadcrumbList[pRouteTo].link);
    },
    updateList() {
      this.breadcrumbList = this.$route.meta.breadcrumb;
    }
  }
};
</script>