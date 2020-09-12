<template>
  <div class="margin-container post-block">
    <div class="form-group">
      <!-- <vue-editor class="text-editor" rows="3" id="detail" v-model="form.detail"></vue-editor> -->
      <vue-editor
        class="text-editor"
        rows="3"
        id="detail"
        v-model="form.detail"
        v-on:ready="quill => editorQuill = quill"
      ></vue-editor>
      <h5 style="float: right;">{{form.detail.replace(/<\/?[^>]+(>|$)/g, '').length}}/255</h5>
      <p class="text-danger" v-for="error in errors.post" :key="error.id">
        <span v-if="errors.post">{{ error }}</span>
      </p>
    </div>
    <div class="row footer">
      <div class="col-md-6 col-resp-center col-display">
        <div class="display">
          <h3>แสดงกับ</h3>
          <div class="select-display">
            <v-select
              v-if="!this.disshow.length"
              :options="options"
              placeholder="เลือก..."
              v-model="form.display"
            ></v-select>
            <v-select v-else :options="optionsall" placeholder="เลือก..." v-model="form.display"></v-select>
          </div>
          <p class="text-danger" v-for="error in errors.post" :key="error.id">
            <span v-if="errors.post">{{ error }}</span>
          </p>
        </div>
      </div>
      <div class="col-md-6 right col-resp-center col-btn">
        <button
          type="button"
          class="btn btn-default submit cancel"
          @click="cancle()"
          data-dismiss="modal"
          v-if="this.windowWidth > 1300"
        >
          <h3>ยกเลิก</h3>
        </button>
        <button
          :disabled="(form.display === '' || form.detail === '') && form.hello == true"
          type="submit"
          class="btn btn-default submit"
          @click="post()"
          data-dismiss="modal"
        >
          <h3>สร้าง</h3>
        </button>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
</template>

<script>
import { VueEditor } from "vue2-editor";
import vSelect from "vue-select";
export default {
  props: ["detail", "errors"],
  data() {
    return {
      form: {
        detail: "",
        display: "",
        hello: true
      },
      options: [{ label: "ทุกคน", value: 0 }],
      optionsall: [
        { label: "ทุกคน", value: 0 },
        { label: "เฉพาะในกลุ่ม", value: 1 }
      ],
      windowWidth: window.innerWidth,
      disshow: "",
      editorQuill: "",
      max: 255
    };
  },
  components: {
    VueEditor,
    "v-select": vSelect
  },
  created() {
    var classroom_id = this.$route.params.id;
    this.token = this.$auth.getToken();

    axios
      .get("api/group/" + classroom_id, {
        headers: {
          Authorization: "Bearer " + this.token
        }
      })
      .then(response => {
        this.disshow = response.data;
      });
  },
  mounted() {
    window.addEventListener("resize", () => {
      this.windowWidth = window.innerWidth;
    });
  },
  watch: {
    "form.detail": function() {
      if (!!this.max && this.editorQuill.getLength() >= this.max) {
        this.editorQuill.deleteText(this.max, this.editorQuill.getLength());
      }
    }
  },

  methods: {
    post() {
      this.$emit("post", this.form);
    },
    cancle() {
      this.form.display = "";
      this.form.detail = "";
    }
  }
};
</script>