<script setup lang="ts">
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarHeader,
  SidebarInset,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarProvider,
  SidebarRail,
  SidebarTrigger,
} from "@/components/ui/sidebar";
import {
  HouseIcon,
  Layers3Icon,
  MapIcon,
  UsersIcon,
  TrophyIcon,
  CircleUserIcon,
} from "lucide-vue-next";

import { useRoute } from "vue-router";
import Container from "@/components/Container.vue";

const $route = useRoute();

const data = {
  projects: [
    {
      name: "Tournier",
      url: "/admin/settings",
      icon: TrophyIcon,
      active: "/admin/tournier" === $route.path,
    },
    {
      name: "Hallen",
      url: "/admin/halls",
      icon: HouseIcon,
      active: "/admin/halls" === $route.path,
    },
    {
      name: "Kategorien",
      url: "/admin/categories",
      icon: Layers3Icon,
      active: "/admin/categories" === $route.path,
    },
    {
      name: "Abteilungen",
      url: "/admin/sections",
      icon: MapIcon,
      active: "/admin/sections" === $route.path,
    },
    {
      name: "Teams",
      url: "/admin/teams",
      icon: UsersIcon,
      active: "/admin/teams" === $route.path,
    },
    {
      name: "Users",
      url: "/admin/users",
      icon: CircleUserIcon,
      active: "/admin/users" === $route.path,
    },
  ],
};

import rotatingBall from "@/assets/rotating_ball_a.png";
</script>

<template>
  <SidebarProvider>
    <Sidebar collapsible="icon" class="bg-gray-100">
      <SidebarHeader class="h-24 bg-gray-100">
        <div class="flex justify-center items-center size-full">
          <router-link to="/">
            <img
              :src="rotatingBall"
              alt="rotating ball"
              class="max-w-14 w-full aspect-square"
            />
          </router-link>
        </div>
      </SidebarHeader>
      <SidebarContent class="bg-gray-100">
        <SidebarGroup>
          <SidebarMenu>
            <Collapsible
              v-for="item in data.projects"
              :key="item.name"
              as-child
              :default-open="item.active"
              class="group/collapsible"
            >
              <router-link :to="item.url">
                <SidebarMenuItem>
                  <CollapsibleTrigger as-child>
                    <SidebarMenuButton :tooltip="item.name">
                      <component :is="item.icon" />
                      <span>{{ item.name }}</span>
                    </SidebarMenuButton>
                  </CollapsibleTrigger>
                </SidebarMenuItem>
              </router-link>
            </Collapsible>
          </SidebarMenu>
        </SidebarGroup>
      </SidebarContent>
      <SidebarFooter class="bg-gray-100"> </SidebarFooter>
      <SidebarRail />
    </Sidebar>
    <SidebarInset>
      <Container class="pb-0">
        <SidebarTrigger />
      </Container>

      <slot></slot>
    </SidebarInset>
  </SidebarProvider>
</template>
