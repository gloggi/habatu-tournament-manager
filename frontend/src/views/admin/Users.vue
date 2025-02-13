<template>
    <Container>
       <EntityTable
         entity="users"
         name="Users"
         :columnKeys="['nickname']"
         :columnNames="['Pfadiname']"
        />
    </Container>
    </template>
    
    <script setup lang="ts">
    
    import Container from '@/components/Container.vue';
    import EntityTable from '@/components/EntityTable.vue';
    
    </script>