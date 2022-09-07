<template>
  <h3 id="container_titre">Films disponibles : </h3>
  <div class="chargement" v-if="!loaded">Chargement des données en cours...</div>
  <div>
    <select name="categorie" id="categorie" v-model="categorie">
      <option value="all">Tous</option>
      <option v-for="(cat,index) in categories" :key="index" :value="cat.id">{{cat.nom}}</option>
    </select>
  </div>
  <div v-if="films != null && films.length > 0">
    <div id="container_affiches">
      <Affiche v-for="(film,index) in films" :key="index" :film="film" />
    </div>
  </div>
  <p v-else>Aucun film pour le moment</p>
</template>

<script setup>
  import { onMounted } from 'vue';
  import Affiche from './Affiche.vue'
  import { useCineplaceStore } from '../../stores/index'
  import { ref, computed } from 'vue'
  import Axios from '../../api/axios'

  const store = useCineplaceStore()

  const categorie = ref("all");
  const allFilms = ref([])
  let films = computed(function (){
    //Si une catégorie est défini seul les films possédant cette catégorie seront affichés
    if(categorie.value == "all"){
      return allFilms.value
    }else{
      return categories.value.filter(cat => cat.id == categorie.value)[0].films
    }
  })
  const categories = ref([])
  const loaded = ref(false)

  onMounted(() => {
    //Récupération de toutes les catégories
    Axios().get('categories')
      .then(response =>  {
          categories.value = response.data["hydra:member"]
      })
      .catch( e => {
          //En cas de retour négatif le chargement est effectué et la page est vide (utilisé en cas de panne de l'api)
          loaded.value = true
      })
    
    //Récupération de tous les films
    Axios().get('films')
          .then(response =>  {
            allFilms.value = response.data["hydra:member"],
            loaded.value = true
          })
          .catch( e => {
              //En cas de retour négatif le chargement est effectué et la page est vide (utilisé en cas de panne de l'api)
              loaded.value = true
          })
  })
</script>