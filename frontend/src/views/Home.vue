<template>
    <Container>
    <div class=" grid grid-cols-1 gap-4 md:grid-cols-6">
        <Card v-for="item in menuList" :key="item.name" class="aspect-square text-gray-700 cursor-pointer" @click="handleRouteChange(item.routeName)">
            <div class="w-full h-3/3 flex justify-center items-center">
                <component class="size-full " :is="item.icon"/>
            </div>
            <div class="w-full h-1/4 flex justify-center">
            <p class="text-3xl font-semibold">{{ item.name }}</p>
        </div>
        </Card>
    </div>
</Container>
</template>
<script setup lang="ts">

import Card  from "@/components/Card.vue";
import { Table, Medal, Binoculars, UsersIcon, UserRoundPen, CircleGauge } from 'lucide-vue-next'
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
}

const menuList: IMenuItem[] = [
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
        name: "Admin",
        icon: CircleGauge,
        routeName: "Admin",
    }
]


</script>