<template>
<div class="flex justify-center flex-wrap">
  <loading v-if="load" />
  <div class="flex flex-wrap justify-center" v-else>
      <empty-state v-if="!posts.length"/>
      <post-card v-else
        v-for="(post, index) in posts"
        :post="post"
        :auth="auth"
        :key="index"
        @refetch="fetchPosts"
      />
  </div>
</div>
</template>

<script>
export default {
  props: ['auth'],

  data () {
    return {
      posts: [],
      load: false,
    }
  },

  mounted () {
    this.fetchPosts()
  },

  methods: {
    fetchPosts () {
      this.load = true

      setTimeout(() => {
        axios.get('/post').then(({ data: { data } }) => {
          this.posts = data
          this.load = false
        })
      }, 1000)
    }
  }
}
</script>
