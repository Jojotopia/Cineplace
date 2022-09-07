<template>
  <section id="admin_form_connection_container" class="admin_container">
    <h3 class="form_titre">Authentification Client</h3>
    <div class="div_form">
        <div v-if="error" id="error">{{ error }}</div>
        <p>
            <label for="inputEmail">Adresse email : </label>
            <input type="email" name="email" v-model="email" id="inputEmail" autocomplete="email" @keyup.enter="connexion()" autofocus>
        </p>
        <p>
            <label for="inputPassword">Mot de passe : </label>
            <input type="password" name="password" id="inputPassword" v-model="password" @keyup.enter="connexion()" autocomplete="current-password">
        </p>

        <p id="pCheckbox">
            <label for="saveConnection">Rester connecté</label>
            <input type="checkbox" name="saveConnection" id="saveConnection" v-model="saveConnection">
        </p>

        <p><button @click="connexion()">Se connecter</button></p>
    </div>
</section>
</template>

<script setup>
    import Axios from '../../api/axios'
    import { ref } from 'vue'
    import { useCineplaceStore } from '../../stores/index'
    import { useRouter } from 'vue-router'

    const store = useCineplaceStore()
    const router = useRouter()

    const email = ref('')
    const password = ref('')
    const token = ref('')
    const error = ref('')
    const saveConnection = ref(false)

    function connexion() {
        //Demande de connexion à l'api
        Axios().post('login_check',{username: email.value, password: password.value})
            .then(response => {
                token.value=response.data
                store.setConnecte(true, email.value, token.value.token)
                sessionStorage.setItem('store', JSON.stringify(store.$state));
                if (saveConnection.value) {
                    localStorage.setItem('store', JSON.stringify(store.$state));
                }
                router.push({ name: 'homeClient' })
            })
            .catch( e => {
                if (e.response) {
                    console.log(e.response)
                    error.value = 'login et/ou mot de passe incorrect'
                    password.value = ''
                }
            })
    }

</script>
