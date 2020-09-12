<template>
    <div id="manualBook">
        <div class="row">
            <div class="col-6">
                <router-link to="/manualBook/TABook">
                   <h3>คู่มือสำหรับอาจารย์</h3>
                </router-link>
            </div>
            <div class="col-6 lineLeft">
                <router-link to="/manualBook/STUBook">
                    <h3>คู่มือสำหรับนักศึกษา</h3>
                </router-link>
            </div>
        </div>
        <div class="content">
            <router-view></router-view>
        </div>
        <router-link to="/" style="margin-top: 40px;">
            <span class="material-icons">keyboard_backspace</span>
            <h4>กลับสู่หน้าหลัก</h4>
        </router-link>
    </div>
</template>

<script>
export default {
    data(){
        return{
            baseUrl: this.$store.getters.baseUrl,
            windowWidth: window.innerWidth
        }
    },
    mounted() {
        window.addEventListener("resize", () => {
        this.windowWidth = window.innerWidth;
        });
    }
}
</script>