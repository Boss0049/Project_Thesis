import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

const routes = [
  {
    path: "/test",
    component: require("./components/wait/wait.vue"),
    meta: {
      forAuth: true,
    },
  },
  {
    path: "/",
    component: require("./components/classroom/List.vue"),
    meta: {
      forAuth: true,
    },
  },
  {
    path: "/manualBook",
    component: require("./components/manualBook/manualBook.vue"),
    children: [
      {
        path: "STUBook",
        name: "คู่มือนักศึกษา",
        component: require("./components/manualBook/STUBook.vue"),
      },
      {
        path: "TABook",
        name: "คู่มืออาจารย์",
        component: require("./components/manualBook/TABook.vue"),
      },
    ],
  },
  {
    path: "/:id/main",
    component: require("./components/main.vue"),
    meta: {
      forAuth: true,
      breadcrumb: [{ name: "หน้าหลัก", link: "/main/post" }],
    },
    children: [
      {
        path: "group",
        name: "กลุ่ม",
        component: require("./components/group/List.vue"),
        meta: {
          forAuth: true,
          navstyle: {
            transparent: true,
          },
          breadcrumb: [{ name: "กลุ่ม" }],
        },
      },
      {
        path: "createGroup",
        name: "สร้างกลุ่ม",
        component: require("./components/group/Create.vue"),
        meta: {
          forAuth: true,
          role: "is_student",
          breadcrumb: [
            { name: "กลุ่ม", link: "group" },
            { name: "สร้างกลุ่ม" },
          ],
        },
      },
      {
        path: "membergroup/:group_id",
        name: "สร้างกลุ่ม",
        component: require("./components/group/addmember.vue"),
        meta: {
          forAuth: true,
          role: "is_student",
          breadcrumb: [
            { name: "กลุ่ม", link: "../group" },
            { name: "สร้างกลุ่ม" },
          ],
        },
      },
      {
        path: "post",
        name: "ข่าวสาร",
        component: require("./components/classroom/View.vue"),
        meta: {
          forAuth: true,
          navstyle: {
            transparent: true,
          },
          breadcrumb: [{ name: "ข่าวสาร" }],
        },
      },
      {
        path: "document",
        name: "เอกสาร",
        component: require("./components/document/ViewDoc.vue"),
        forAuth: true,
        navstyle: {
          transparent: true,
        },
        meta: {
          breadcrumb: [{ name: "เอกสาร" }],
        },
      },
      {
        path: "progress",
        name: "ความก้าวหน้า",
        component: require("./components/assignment/progress.vue"),
        meta: {
          forAuth: true,
          navstyle: {
            transparent: true,
          },
          breadcrumb: [{ name: "ความก้าวหน้า" }],
        },
      },
      {
        path: "createMeet",
        name: "สร้างนัดหมาย",
        component: require("./components/assignment/Post.vue"),
        meta: {
          forAuth: true,
          role: "is_teacher",
          breadcrumb: [
            { name: "ความก้าวหน้า", link: "progress" },
            { name: "สร้างนัดหมาย" },
          ],
        },
      },
      {
        path: "createProgress",
        name: "บันทึกความก้าวหน้า",
        component: require("./components/assignment/Post.vue"),
        meta: {
          forAuth: true,
          role: "is_student",
          breadcrumb: [
            { name: "ความก้าวหน้า", link: "progress" },
            { name: "บันทึกความก้าวหน้า" },
          ],
        },
      },
      {
        path: "checkProgress/:assignment_id",
        name: "ตรวจสอบความก้าวหน้า",
        component: require("./components/assignment/View.vue"),
        meta: {
          forAuth: true,
          role: "is_teacher",
          breadcrumb: [
            { name: "ความก้าวหน้า", link: "../progress" },
            { name: "ตรวจสอบความก้าวหน้า" },
          ],
        },
      },
      {
        path: "replyProgress/:assignment_id",
        name: "บันทึกความก้าวหน้า",
        component: require("./components/assignment/View.vue"),
        meta: {
          forAuth: true,
          role: "is_student",
          breadcrumb: [
            { name: "ความก้าวหน้า", link: "../progress" },
            { name: "บันทึกความก้าวหน้า" },
          ],
        },
      },
    ],
  },
  {
    path: "/classroom/:id/assignment/:assignment_id",
    component: require("./components/assignment/View.vue"),
    meta: {
      forAuth: true,
    },
  },
  // {
  //   path: "/classroom/:group_id/members/add",
  //   component: require("./components/member/Add.vue"),
  //   meta: {
  //     forAuth: true,
  //     role: "is_student"
  //   }
  // },
  {
    path: "/login",
    component: require("./components/Login.vue"),
    meta: {
      forVisitors: true,
      navstyle: {
        hide: true,
      },
      footerstyle: {
        hide: true,
      },
    },
  },
  {
    path: "/admin",
    component: require("./components/admin.vue"),
    meta: {
      forVisitors: true,
      navstyle: {
        hide: true,
      },
      footerstyle: {
        hide: true,
      },
    },
  },
  {
    path: "/logout",
    component: require("./components/Logout.vue"),
    meta: {
      forAuth: true,
    },
  },
  {
    path: "/first",
    component: require("./components/wait/first.vue"),
    meta: {
      test: true,
      role: "is_confirm",
      navstyle: {
        hide: true,
      },
      footerstyle: {
        hide: true,
      },
    },
  },
  {
    path: "/classroom/create",
    component: require("./components/classroom/Create.vue"),
    meta: {
      forAuth: true,
      role: "is_teacher",
    },
  },
  // {
  //   path: "/classroom/:id",
  //   component: require("./components/classroom/View.vue"),
  //   meta: {
  //     forAuth: true,
  //     navstyle: {
  //       transparent: true
  //     }
  //   }
  // },
  {
    path: "/classroom/:id/edit",
    component: require("./components/classroom/Edit.vue"),
    meta: {
      forAuth: true,
      meta: {
        role: "is_teacher",
      },
    },
  },
  {
    path: "main/post/:id/post",
    component: require("./components/post/Post.vue"),
    meta: {
      forAuth: true,
    },
  },
  {
    path: "/classroom/:id/post/assignment",
    component: require("./components/assignment/Post.vue"),
    meta: {
      forAuth: true,
      role: "is_teacher",
    },
  },
  {
    path: "/classroom/:id/members/add",
    component: require("./components/member/Add.vue"),
    meta: {
      forAuth: true,
      role: "is_teacher",
    },
  },
  {
    path: "/classroom/:id/members",
    component: require("./components/member/View.vue"),
    meta: {
      forAuth: true,
    },
  },
  {
    path: "/classroom/:id/assignment/:assignment_id",
    component: require("./components/assignment/View.vue"),
    meta: {
      forAuth: true,
    },
  },
  {
    path: "/classroom/:id/assignment/:post_id/edit",
    component: require("./components/assignment/Edit.vue"),
    meta: {
      forAuth: true,
      role: "is_teacher",
    },
  },
  {
    path: "/classroom/:id/post/:post_id/edit",
    component: require("./components/post/Edit.vue"),
    meta: {
      forAuth: true,
    },
  },
  {
    path: "/profile",
    component: require("./components/user/profile.vue"),
    meta: {
      forAuth: true,
    },
  },
  {
    path: "/user/create",
    component: require("./components/user/create.vue"),
    meta: {
      forAuth: true,
      role: "is_teacher",
    },
  },
  {
    path: "/user/:id/edit",
    component: require("./components/user/edit.vue"),
    meta: {
      forAuth: true,
      role: "is_teacher",
    },
  },
  {
    path: "/user",
    component: require("./components/user/view.vue"),
    meta: {
      forAuth: true,
      role: "is_teacher",
    },
  },
  {
    path: "*",
    component: require("./components/error/404.vue"),
  },
];
const router = new VueRouter({
  mode: "history",
  base: __dirname,
  routes,
});

export default router;
