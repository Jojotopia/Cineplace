<template>
    <section v-if="supprimer" id="comfirmation_suppression">
        <div>
            <p>Voulez-vous vraiment supprimer votre compte ?</p>
            <p>
                <a id="confirmation_suppression_oui" @click="supprimerCompte()">Oui</a>
                <a id="confirmation_suppression_non" @click="supprimer = false">Non</a>
            </p>
        </div>
    </section>
    <section id="admin_button_section">
        <router-link class="admin_button" :to="{name: 'reserver'}"><span class="admin_button_icon">&#127916;</span>Acheter un billet</router-link>
        <router-link class="admin_button" :to="{name: 'reservationsClient'}" ><span class="admin_button_icon">&#127903;</span>Voir mes billets</router-link>
        <router-link class="admin_button" :to="{name: 'updateClient'}" ><span class="admin_button_icon">&#9998;</span>Modifier mes informations</router-link>
        <a class="admin_button" @click="deconnexion()"><span class="admin_button_icon">&#10162;</span>Se déconnecter</a>
        <a class="admin_button" @click="supprimer = true"><span class="admin_button_icon">X</span>Supprimer mon compte</a>
    </section>
</template>

<script setup>
    import Axios from '../../api/axios'
    import { ref } from 'vue'
    import { useCineplaceStore } from '../../stores/index'
    import { useRouter } from 'vue-router'
    
    const store = useCineplaceStore()
    const router = useRouter()

    //Variable permettant l'affichage de la confirmation de suppression
    const supprimer = ref(false);
    function deconnexion() {
        localStorage.removeItem('store')
        sessionStorage.removeItem('store')
        store.setConnecte(false, '', '')
        router.push('/login')
    }
    function supprimerCompte(){
        //Demande de suppression à l'api
        Axios().post('client_delete',{email: store.login})
            .then(response => {
                deconnexion()
            })
            .catch( e => {
                if (e.response) {
                    if (e.response.data.message === "Expired JWT Token") {
                        sessionStorage.removeItem('token')
                        router.push('/login');
                    }else{
                        console.log(e.response)
                    }
                }
            })
    }

</script>