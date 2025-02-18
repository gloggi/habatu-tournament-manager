<template>
  <Container>
    <div
      class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4"
    >
      <Card
        v-for="item in menuList"
        :key="item.name"
        class="text-foreground aspect-square cursor-pointer"
        @click="handleRouteChange(item.routeName)"
      >
        <div class="object-cover transition-all hover:scale-105">
          <div class="w-full h-3/3 flex justify-center items-center">
            <component class="size-full" :is="item.icon" />
          </div>
          <div class="w-full h-1/4 flex justify-center">
            <p class="text-xl font-semibold">{{ item.name }}</p>
          </div>
        </div>
      </Card>
    </div>
  </Container>
</template>
<script setup lang="ts">
import Card from "@/components/Card.vue";
import {
  Table,
  Medal,
  Binoculars,
  UsersIcon,
  UserRoundPen,
  CircleGauge,
  MessageCircleIcon,
  Trophy,
} from "lucide-vue-next";
import { useRouter } from "vue-router";
import Container from "@/components/Container.vue";

const router = useRouter();

interface IMenuItem {
  name: string;
  icon: any;
  routeName: string;
}

const handleRouteChange = (routeName: string) => {
  if (!router.hasRoute(routeName)) {
    console.error(`Route ${routeName} does not exist`);
    return;
  }
  router.push({ name: routeName });
};

const menuList: IMenuItem[] = [
  {
    name: "Tournier erstellen",
    icon: Trophy,
    routeName: "Setup",
  },
  {
    name: "Spielplan",
    icon: Table,
    routeName: "GameTable",
  },
  {
    name: "Rangliste",
    icon: Medal,
    routeName: "Ranking",
  },
  {
    name: "Schiri",
    icon: Binoculars,
    routeName: "Referee",
  },
  {
    name: "Mis Team",
    icon: UsersIcon,
    routeName: "MyTeam",
  },
  {
    name: "Mis Profil",
    icon: UserRoundPen,
    routeName: "Profile",
  },
  {
    name: "Nachrichte",
    icon: MessageCircleIcon,
    routeName: "Messages",
  },
  {
    name: "Admin",
    icon: CircleGauge,
    routeName: "Admin",
  },
];
</script>
