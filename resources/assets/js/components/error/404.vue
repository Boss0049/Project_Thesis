<template>
    <div id="page404" v-if="classrooms">
    <div class="obj">
        <img v-bind:src="`${baseUrl}/images/logo/404.png`" class="img-404" />
        <h1>ERROR 404 Page not found</h1>
        <h3>The requested URL was not found on this server.</h3>
        <router-link to="/">
        <button class="btn btn-default back">
            <h3> Back </h3>
        </button>
        </router-link>
    </div>
    </div>
    <loading v-else></loading> 
</template>

<script>
import loading from '../elements/loading'
    export default {
        data(){
            return{
                baseUrl: this.$store.getters.baseUrl,
                classrooms: ""
            }
        },
        components:{loading},
        mounted(){
            var token = this.$auth.getToken();
            this.$store.dispatch("getPersonal");
            axios
            .get("api/classroom", {
                headers: {
                Authorization: "Bearer " + token
                }
            })
            .then(response => {
                this.classrooms = response.data;
            });
        }
    }
</script>