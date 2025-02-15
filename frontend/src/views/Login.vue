<script setup lang="ts">
import { ref } from "vue";
import rotatingBall from "../assets/rotating_ball_a.png";
import InputField from "@/components/InputField.vue";
import { Button } from "@/components/ui/button";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { useToast } from "@/components/ui/toast/use-toast";
const { toast } = useToast();
import { useRouter } from "vue-router";
const router = useRouter();
import { useUserStore } from "@/stores/user";
const userStore = useUserStore();

import { useApi } from "@/api";
const { postData: doLogin } = useApi("login");
const { postData: doRegister } = useApi("register");

const loginForm = ref({
  nickname: "",
  password: "",
});

const registerForm = ref({
  nickname: "",
  password: "",
  passwordRepeat: "",
});

const login = async () => {
  try {
    const response = await doLogin(loginForm.value);
    localStorage.setItem("token", response.token);
    localStorage.setItem("userId", response.user.id);
    userStore.fetchUser();
    router.push({ name: "Home" });
  } catch (e) {
    toast({
      title: "Fehler",
      description: "Fehler beim Login",
      variant: "destructive",
    });
  }
};

const register = async () => {
  try {
    if (registerForm.value.password !== registerForm.value.passwordRepeat) {
      toast({
        title: "Fehler",
        description: "Passwörter stimmen nicht überein",
        variant: "destructive",
      });
      return;
    }
    const response = await doRegister(registerForm.value);
    localStorage.setItem("token", response.token);
    localStorage.setItem("userId", response.user.id);
    userStore.fetchUser();
    router.push({ name: "Home" });
  } catch (e) {
    toast({
      title: "Fehler",
      description: "Fehler beim Registrieren",
      variant: "destructive",
    });
  }
};
</script>

<template>
  <div class="w-full h-screen flex">
    <div
      class="bg-red-500 w-0 hidden md:w-2/3 h-full md:flex items-center justify-center"
    >
      <div class="flex flex-col space-y-8 p-8">
        <img :src="rotatingBall" alt="rotating ball" class="w-72" />
        <div class="flex flex-col space-y-2">
          <div class="text-6xl font-bold text-white">
            HaBaTu Tournament Manager
          </div>
          <div class="text-3xl font-light text-white">
            Will jedes Turnier die perfekti Planig verdient
          </div>
        </div>
      </div>
    </div>
    <div class="w-full md:w-1/3 h-full flex justify-center items-center">
      <div class="w-3/4">
        <Tabs default-value="login" class="w-full">
          <TabsList class="w-full mb-4">
            <TabsTrigger value="login" class="w-full"> Login </TabsTrigger>
            <TabsTrigger value="register" class="w-full">
              Registrieren
            </TabsTrigger>
          </TabsList>
          <TabsContent value="login">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Login</h2>
            <form class="flex flex-col space-y-3" @submit.prevent="login">
              <InputField
                label="Benutzername"
                type="text"
                v-model="loginForm.nickname"
              />
              <InputField
                label="Password"
                type="password"
                v-model="loginForm.password"
              />
              <Button>Login</Button>
            </form>
          </TabsContent>
          <TabsContent value="register">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Registrieren</h2>
            <form class="flex flex-col space-y-3" @submit.prevent="register">
              <InputField
                label="Benutzername"
                type="text"
                v-model="registerForm.nickname"
              />
              <InputField
                label="Password"
                type="password"
                v-model="registerForm.password"
              />
              <InputField
                label="Password wiederholen"
                type="password"
                v-model="registerForm.passwordRepeat"
              />
              <Button>Registrieren</Button>
            </form>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  </div>
</template>
