<template>
<div class="bg-white w-2/3 p-4 my-4 rounded shadow-md">
  <div class="flex justify-between">
    <small class="font-sans uppercase">PUBLISHED ON {{ date }}</small>
    <span v-if="auth">
      <a class="uppercase mx-1" :href="`/post/${post.id}/edit`">Edit</a>
      <a @click="onDelete" class="uppercase mx-1 text-red-500 hover:text-red-700" href="#">Delete</a>
    </span>
  </div>
  <h2 class="my-2"><a :href="`/post/${post.id}`" class="hover:no-underline font-sans">{{ post.title }}</a></h2>
  <p class="font-serif">{{ post.description }}</p>
</div>
</template>

<script>
export default {
  props: ['post', 'auth'],

  computed: {
    date () {
      if (this.post) {
        return moment(this.post.updated_at).format('MMM DD, YYYY')
      }
    }
  },

  methods: {
    onDelete () {
      modal({
        text: 'Are you sure to want to delete post?',
      }, () => {
        axios.delete(`/post/${this.post.id}`).then(() => {
          success({ text: 'Post deleted.' })

          this.$emit('refetch')
        })
      })
    }
  }
}
</script>
