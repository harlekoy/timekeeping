<template>
<div class="w-4/5">
  <div>
    <input v-model="title" class="bg-transparent font-serif text-4xl my-4 w-full outline-none" type="text" placeholder="Title">
    <editor
      class="bg-white"
      :init-data="content"
      autofocus
      ref="editor"
      @save="onSave"
    />
  </div>
  <div class="flex">
    <button @click="save" class="mt-12 py-3 px-4 mr-2 font-bold border-2 tracking-widest border-blue-500 text-blue-500 rounded w-full text-lg hover:bg-blue-500 hover:text-white">SAVE</button>
    <a href="/" class="hover:no-underline text-center hover:text-white mt-12 py-3 px-4 ml-2 font-bold border-2 hover:bg-red-500 tracking-widest border-red-500 text-red-500 rounded w-full text-lg">CANCEL</a>
  </div>
</div>
</template>

<script>
export default {
  props: ['post'],

  data () {
    return {
      title: '',
      content: ''
    }
  },

  watch: {
    post: {
      immediate: true,
      handler (current) {
        if (current) {
          this.title = current.title
          this.content = current.content
        }
      }
    }
  },

  methods: {
    save (data) {
      this.$refs.editor.save()
    },

    request (response) {
      if (this.post) {
        return axios.patch(`/post/${this.post.id}`, {
          title: this.title,
          content: response,
        })
      } else {
        return axios.post('/post', {
          title: this.title,
          content: response,
        })
      }
    },

    onSave (response) {
      this.request(response).then(() => {
        window.location.href = '/'
      }).catch(({ response: { data: { errors } } }) => {
        let key = _.head(_.keys(errors))

        if (key) {
            let message = _.get(errors, `${key}.0`)

            if (message) {
                fail({text: message})
            }
        }
      })
    }
  }
}
</script>
