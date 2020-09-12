<template>
  <div id="wait">
    <div class="card-wait">
      <span class="material-icons">error_outline</span>
      <h1>ท่านสามารถเข้าใช้งานเว็บไซต์ได้</h1>
      <h1>หลังจากได้รับการยืนยันสถานะ</h1>
      <button class="btn btn-default submit" @click="logout()">
        <h3>ออกจากระบบ</h3>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    logout() {
      window.location.href = "/logout";
    }
  }
};
</script>