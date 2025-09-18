<script setup lang="ts">
import { onMounted, ref } from "vue";
import rotatingBall from "../assets/rotating_ball_a.png";
import InputField from "@/components/InputField.vue";
import { Button } from "@/components/ui/button";
import MiDataButton from "@/components/MiDataButton.vue";
import { Separator } from "@/components/ui/separator";
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
    userStore.fetchUser();
    router.push({ name: "Home", query: { login: "true" } });
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
    userStore.fetchUser();
    router.push({ name: "Home", query: { login: "true" } });
  } catch (e) {
    toast({
      title: "Fehler",
      description: "Fehler beim Registrieren",
      variant: "destructive",
    });
  }
};

const { data: loginData, fetchData: getLoginURL } = useApi<{ url: string }>(
  "auth/midata",
);

const loginWithMiData = async () => {
  try {
    await getLoginURL(undefined, true);
    if (loginData.value) {
      window.location.href = loginData.value.url;
    }
  } catch (e) {
    toast({
      title: "Fehler",
      description: "Fehler beim Login",
      variant: "destructive",
    });
  }
};

import { useRoute } from "vue-router";
const route = useRoute();

const { postData: registerWithCode } = useApi("auth/midata/callback");
const handleOAuthCode = async () => {
  const code = route.query.code;
  if (code) {
    console.log("Handling OAuth code:", code);
    try {
      const response = await registerWithCode({ code: code as string });
      localStorage.setItem("token", response.token);
      userStore.fetchUser();
      router.push({ name: "Home", query: { login: "true" } });
    } catch (e) {
      toast({
        title: "Fehler",
        description: "Fehler beim Registrieren",
        variant: "destructive",
      });
    }
  }
};
onMounted(() => {
  handleOAuthCode();
});
</script>

<template>
  <div class="w-full h-screen flex flex-col md:flex-row">
    <div
      class="bg-red-500 flex flex-col w-full md:w-2/3 items-center justify-center h-48 md:h-full"
    >
      <div
        class="flex flex-row space-x-5 justify-center items-center md:space-x-0 md:flex-col md:space-y-8 p-8 h-full"
      >
        <img
          :src="rotatingBall"
          alt="rotating ball"
          class="size-24 md:size-72"
        />
        <div class="flex flex-col space-y-2 h-full md:h-auto">
          <div class="text-2xl md:text-6xl font-bold text-white">
            HaBaTu Tournament Manager
          </div>
          <div class="text-sm md:text-3xl font-light text-white"></div>
        </div>
      </div>
    </div>
    <div
      class="w-full md:w-1/3 h-full flex justify-center pt-8 md:pt-0 md:items-center"
    >
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
            <div class="space-y-5">
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
              <Separator label="oder" />
              <MiDataButton @click="loginWithMiData" />
            </div>
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
