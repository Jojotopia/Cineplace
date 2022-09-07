import { defineStore } from 'pinia'

export const useCineplaceStore = defineStore({
  id: 'cineplace',
  state: () => ({
    connecte : false,
    login : '',
    count : 1,
    token : ''
  }),
  getters: {
  },
  actions: {
    initialiseStore() {
      //Récupération des données via les sessionStorage et LocalStorage
      if(sessionStorage.getItem('store')) {
        console.log('store initialisé par session Storage')
        this.$state =  JSON.parse(sessionStorage.getItem('store'))
      }
      if(localStorage.getItem('store')) {
        console.log('store initialisé par session Storage')
        this.$state =  JSON.parse(localStorage.getItem('store'))
      }
    },
    //connexion d'un client
    setConnecte(connect, login, token) {
      this.connecte = connect
      this.login = login
      this.token = token
    }
  }
})
