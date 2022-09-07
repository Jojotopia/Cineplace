<script setup>
  import { onMounted } from 'vue';
  import { useCineplaceStore } from './stores/index'
  import { ref } from 'vue'
  import logo from './assets/img/mini-icone.svg'
  import Axios from './api/axios'
  import { useRouter, useRoute } from 'vue-router'
  
  const store = useCineplaceStore()
  const router = useRouter()
  const error = ref('')

  onMounted(() => {
    store.initialiseStore()
  })
  //Si l'on souhaite aller sur le BackOffice
  function adminPage(){
    //Demande d'autorisation d'accès à l'espace admin gérer par easyAdmin
    Axios().post('admin',store)
      .then(response => {
        //Si l'utilisateur est autorisé alors redirection vers /admin
        window.location.href = "/admin";
      })
      .catch( e => {
        error.value = e.response.data["hydra:description"]
        setTimeout(() => error.value = "" , 2000);
      })
  }
</script>

<template>
  <header>
    <h2>CinéPlace</h2>
    <router-link id="home_link" :to="{ name: 'home' }"><img :src="logo" alt="Accueil"/></router-link>
    <p id="open_menu_button">=</p>
    <p id="close_menu_button">X</p>
    <nav id="menu">
        <router-link :to="{ name: 'info' }">A propos</router-link>
        <router-link :to="{ name: 'voirTout' }">Voir tous les films</router-link>
        <router-link v-if="!store.connecte" :to="{ name: 'login' }">Se connecter</router-link>
        <router-link v-if="!store.connecte" :to="{ name: 'register' }">Créer un compte</router-link>
        <router-link v-if="store.connecte" :to="{ name: 'homeClient' }">Mon compte</router-link>
        <a @click="adminPage()">Admin</a>
    </nav>
  </header>
  <main>
    <div v-if="error" id="error">{{ error }}</div>
    <!-- Toutes les pages seront afficher dans ce routeur-view -->
    <router-view></router-view>
  </main>
  <footer>
      <p>Projet 2022 - Mineure Framework web - PIAUD Joackim</p>
  </footer>
</template>
