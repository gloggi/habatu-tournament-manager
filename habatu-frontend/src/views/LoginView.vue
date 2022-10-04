<template>
  <div class="flex h-screen w-full items-stretch justify-center">
    <div class="w-1/3 h-full flex flex-col justify-center"><p class="font-black text-4xl">
      {{login? "Login": "Register"}}
    </p>
    <LoginOrRegister @switch="handleSwitch" :login="login" />
      <div v-if="login" class="px-5 text-left space-y-2">
        <TextInput label="Username" type="text" />
        <TextInput label="Password" type="password" />
        <BasicButton>Login</BasicButton>
      </div>
      <div v-if="!login" class="px-5 text-left space-y-2">
        <TextInput label="Username" type="text" />
        <TextInput label="Password" type="password" />
        <TextInput label="Confirm Password" type="password" />
        <BasicButton>Register</BasicButton>
      </div>
      
   </div>
    <div class="w-2/3 h-full flex flex-col items-center justify-center bg-red-600"><h1
  class="font-black text-transparent text-7xl bg-clip-text bg-white"
>
 HaBaTu
</h1>
<img class="h-64 w-64 mt-10" src="@/assets/rotating_ball.gif" />
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
        login: true
      }
    },
    methods:{
      handleSwitch(evt){
        this.login = evt
      }
    }
}
</script>

<style>

</style>