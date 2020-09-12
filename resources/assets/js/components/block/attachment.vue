<template>
  <div id="attachment" v-if="files && files.length">
    <div class="body-post">
      <div v-for="file in files" :key="file.id">
        <div class="type-file">
          <div class="row">
            <div class="col-md-2 space col-resp-20">
              <div
                v-if="image_extension.includes(file.type)"
                class="img-png"
                @click="download(file)"
              ></div>
              <div @click="download(file)" :class="`img-${file.type}`" class="img-unknown" v-else></div>
            </div>
            <div class="col-md-9 space col-resp-65" style="padding-left: 0;">
              <h4 @click="download(file)">{{ file.name }}</h4>
            </div>
            <div class="col-md-1 space col-resp-15" style="justify-content: flex-end;">
              <a :href="`${baseUrl}/thumbnail/file/${file.filepath}`" download>
                <img :src="`${baseUrl}/images/Icons/IconFeedDownload.png`" class="img-download" />
              </a>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["files"],
  data() {
    return {
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