<template>
  <div class="flex h-screen w-full items-stretch justify-center">
    <div class="w-1/3 h-full flex flex-col justify-center"><p class="font-black px-5 mb-2 text-center text-4xl">
      {{login? "Login": "Register"}}
    </p>
    <LoginOrRegister @switch="handleSwitch" :login="login" />
    
      <form @submit.prevent="loginUser" v-if="login" class="px-5 text-left space-y-2">
        <TextInput v-model="loginForm.nickname" label="Username" type="text" />
        <TextInput v-model="loginForm.password" label="Password" type="password" />
        <BasicButton>Login</BasicButton>
      </form>
      <form @submit.prevent="registerUser" v-if="!login" class="px-5 text-left space-y-2">
        <TextInput v-model="registerForm.nickname" label="Username" type="text" />
        <TextInput v-model="registerForm.password" label="Password" type="password" />
        <TextInput v-model="registerForm.confirmPassword" label="Confirm Password" type="password" />
        <BasicButton>Register</BasicButton>
      </form>
      
   </div>
    <div class="w-2/3 h-full flex flex-col items-center justify-center bg-red-600"><h1
  class="font-black text-transparent text-center text-7xl px-10 bg-clip-text bg-white"
>
 HaBaTu Tournament Manager 
</h1>
<img class="h-72 w-72 mt-10" src="@/assets/rotating_ball_a.png" />
</div>

  </div>
</template>

<script>
import TextInput from '@/components/TextInput.vue';
import BasicButton from '@/components/BasicButton.vue';
import LoginOrRegister from '@/components/LoginOrRegister.vue';
export default {
    components: { TextInput, BasicButton, LoginOrRegister },
    data(){
      return {
        login: true,
        registerForm: {
          nickname: undefined,
          password: undefined,
          confirmPassword: undefined
        },
        loginForm: {
          nickname: undefined,
          password: undefined
        }
      }
    },
    methods:{
      handleSwitch(evt){
        this.login = evt
      },
      async loginUser(){
        await this.$store.dispatch("user/login", this.loginForm)
        if(localStorage.token){
          this.$router.push({name: "home"})
        }
      },
      async registerUser(){
        await this.$store.dispatch("user/register", this.registerForm)
        if(localStorage.token){
          this.$router.push({name: "home"})
        }

      }
    }
}
</script>

<style>

</style>