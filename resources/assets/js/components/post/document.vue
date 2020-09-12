<template>
  <div class="document">
    <div class="title">
      <div class="row">
        <div class="col-lg-4">
          <h3 style="padding-left: 65px;">ชื่อเอกสาร</h3>
        </div>
        <div class="col-lg-1">
          <h3>ประเภท</h3>
        </div>
        <div class="col-lg-3">
          <h3>วันที่-เวลา</h3>
        </div>
        <div class="col-lg-4">
          <h3>ผู้สร้างเอกสาร</h3>
        </div>
      </div>
    </div>

    <!-- ******section teacher****** -->
    <div v-for="document in documents" :key="document.id"> 
      <div v-if="files && files.length">
        <div class="pg-card" v-for="file in files" :key="file.id">
          <div class="row">
            <div class="col-lg-4">
              <div
                v-if="image_extension.includes(file.type)"
                class="img-png"
                @click="download(file)"
              ></div>
              <div @click="download(file)" :class="`img-${file.type}`" class="img-eiei" v-else></div>
              <div class="name-file">
                <h4 @click="download(file)">{{ file.name }}</h4>
              </div>

              <h4 style="vertical-align: middle;">{{data.name}}</h4>
            </div>
            <div class="col-lg-1">
              <h4>{{data.type}}</h4>
            </div>
            <div class="col-lg-3">
              <h4>{{data.date}}</h4>
            </div>
            <div class="col-lg-4">
              <h4>{{data.user_create}}</h4>
              <!-- ****dropdown**** -->
              <div class="btn-group dropright">
                <button
                  class="dropdown-toggle more-menu"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                >
                  <i class="material-icons">more_vert</i>
                </button>
                <div class="dropdown-menu drop-list">
                  <a
                    :href="`${baseUrl}/thumbnail/file/${file.filepath}`"
                    download
                    class="dropdown-item"
                  >
                    <img :src="`${baseUrl}/thumbnail/file/${formDownload.png}`" />
                    <h4>ดาวน์โหลด</h4>
                  </a>
                  <a class="dropdown-item" href="#" v-on:click="deletefile()">
                    <img :src="`${baseUrl}/thumbnail/file/${fromDelete.png}`" />
                    <h4>ลบ</h4>
                  </a>
                </div>
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
  props: ["files"],
  data() {
    return {
      documents: [],
      count: "0",
      temp_image:
        "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDI0MiAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjwhLS0KU291cmNlIFVSTDogaG9sZGVyLmpzLzEwMCV4MjAwCkNyZWF0ZWQgd2l0aCBIb2xkZXIuanMgMi42LjAuCkxlYXJuIG1vcmUgYXQgaHR0cDovL2hvbGRlcmpzLmNvbQooYykgMjAxMi0yMDE1IEl2YW4gTWFsb3BpbnNreSAtIGh0dHA6Ly9pbXNreS5jbwotLT48ZGVmcz48c3R5bGUgdHlwZT0idGV4dC9jc3MiPjwhW0NEQVRBWyNob2xkZXJfMTVmYzhjOGY3ZjggdGV4dCB7IGZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToxMnB0IH0gXV0+PC9zdHlsZT48L2RlZnM+PGcgaWQ9ImhvbGRlcl8xNWZjOGM4ZjdmOCI+PHJlY3Qgd2lkdGg9IjI0MiIgaGVpZ2h0PSIyMDAiIGZpbGw9IiNFRUVFRUUiLz48Zz48dGV4dCB4PSI4OS44NTkzNzUiIHk9IjEwNS4yNzgxMjUiPjI0MngyMDA8L3RleHQ+PC9nPjwvZz48L3N2Zz4=",
      image_extension: ["jpeg", "jpg", "png", "svg", "gif"],
      baseUrl: this.$store.getters.baseUrl,
      token: this.$auth.getToken()
    };
  },
  methods: {
    download(file) {
      axios
        .get(`api/attachment/download/${file.filepath}`, {
          headers: {
            Authorization: "Bearer " + this.token
          }
        })
        .then(response => {
          window.open(response.data.download_url, "_blank");
        });
    }
  }
};
</script>