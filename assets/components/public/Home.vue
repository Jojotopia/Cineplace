<template>
    <h3 id="container_titre">Films récents : </h3>
    <div class="chargement" v-if="!loaded">Chargement des données en cours...</div>
    <div v-if="loaded" id="container_affiches">
      <Affiche v-for="(film,index) in preview" :key="index" :film="film" />
    </div>
</template>

<script setup>
  import { onMounted } from 'vue';
  import Affiche from './/Affiche.vue'
  import { useCineplaceStore } from '../../stores/index'
  import { ref } from 'vue'
  import Axios from '../../api/axios'

  const store = useCineplaceStore()
  const limitPreview = 5;
  
  const preview = ref(null)
  const loaded = ref(false)

   onMounted(() => {
    Axios().get('films?order(id)=desc')
      .then(response =>  {
          preview.value = response.data["hydra:member"].slice(0, limitPreview),
          loaded.value = true
      })
      .catch( e => {
          //En cas de retour négatif le chargement est effectué et la page est vide (utilisé en cas de panne de l'api)
          loaded.value = true
      })
  })
</script>