<template>
    <section class='long_admin_container'>
        <h3 v-if="!passwordEdit" class="form_titre">Modifier mon compte</h3>
        <h3 v-if="passwordEdit" class="form_titre">Modifier mon mot de passe</h3>
        <div class="div_form">
            <div id='message' v-if="message" > {{ message }} </div>
            <div id='error' v-if="error" > {{ error }} </div>
            <p v-if="!passwordEdit">
                <label for="civilite">Civilité : </label>
                <select name="civilite" id="civilite" v-model="client.civilite">
                    <option value="M.">M.</option>
                    <option value="MME.">MME.</option>
                </select>
            </p>
            <p v-if="!passwordEdit">
                <label for="nom">Nom : </label>
                <input type="text" name="nom" id="nom" v-model="client.nom" @keyup.enter="updateCompte()" autofocus>
            </p>
            <p v-if="!passwordEdit">
                <label for="prenom">Prénom : </label>
                <input type="text" name="prenom" id="prenom" v-model="client.prenom" @keyup.enter="updateCompte()">
            </p>
            <p v-if="!passwordEdit">
                <label for="mail">Email : </label>
                <input type="email" name="mail" id="mail" v-model="client.email" @keyup.enter="updateCompte()">
            </p>
            <p v-if="passwordEdit">
                <label for="passwordActuel">Mot de passe : </label>
                <input type="password" name="passwordActuel" id="passwordActuel" v-model="clientPassword.passwordActuel" @keyup.enter="updatePassword()">
            </p>
            <p v-if="passwordEdit">
                <label for="password">Mot de passe : </label>
                <input type="password" name="password" id="password" v-model="clientPassword.password" @keyup.enter="updatePassword()">
            </p>
            <p v-if="passwordEdit">
                <label for="passwordConfirmation">Confirmation mot de passe : </label>
                <input type="password" name="passwordConfirmation" id="passwordConfirmation" v-model="passwordConfirm" @keyup.enter="updatePassword()">
            </p>
            <p>
                <button v-if="!passwordEdit" @click="updateCompte()">Modifier mon compte</button>
                <button v-if="!passwordEdit" @click="passwordEdit = true">Modifier mon mot de passe</button>
                <button v-if="passwordEdit" @click="updatePassword()">Modifier mon mot de passe</button>
                <router-link class="button_cancel" :to="{ name: 'homeClient' }">Annuler</router-link>
            </p>
        </div>
    </section>
</template>

<script setup>
    import { onMounted } from 'vue';
    import Axios from '../../api/axios'
    import { ref } from 'vue'
    import { useCineplaceStore } from '../../stores/index'
    import { useRouter } from 'vue-router'
    
    const store = useCineplaceStore()
    const router = useRouter()

    //Permet de modifier le mot de passe
    const passwordEdit = ref(false);

    const client = ref({
        id: null,
        email: '',
        nom: '',
        prenom: '',
        civilite: 'M.'
    })
    const clientPassword = ref({
        email: store.login,
        password: '',
        passwordActuel: ''
    })
    const passwordConfirm = ref('')

    onMounted(() => {
        //Au chargement les données du client sont demandées à l'api
        Axios().get('clients?email='+store.login)
            .then(response =>  {
                let donnees = response.data["hydra:member"][0]
                client.value = {
                    email: donnees.email, 
                    id: donnees.id, 
                    nom: donnees.nom, 
                    prenom: donnees.prenom, 
                    civilite: donnees.civilite
                }
            })
            .catch( e => {
                if (e.response) {
                    if (e.response.data.message === "Expired JWT Token") {
                        error.value = 'Votre session a expirée !'
                        sessionStorage.removeItem('token')
                        setTimeout(() => router.push('/login') , 2000);
                    } else {
                        error.value = e.response.data['hydra:description']
                    }
                }
            })
    })

    const error = ref('')
    const message = ref('')

    function updateCompte(){
        error.value = ""
        //Vérification de la validité de l'adresse mail
        if(!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(client.value.email)) {
            error.value = "L'adresse mail n'est pas valide !";
        }else{
            //Si tous les champs sont remplis
            if(client.value.id != null && client.value.email != '' && client.value.prenom != '' && client.value.civilite != '' && client.value.nom != ''){
                //Demande de modification du client avec les nouvelles données via l'api
                 Axios().put('clients/'+client.value.id, client.value)
                    .then(response =>  {
                        message.value = "Votre compte à bien été modifié !"
                        if(store.login != response.data.email){
                            //Après la modification si le client à changé son email il doit se reconnecter
                            localStorage.removeItem('store')
                            sessionStorage.removeItem('store')
                            store.setConnecte(false, '', '')
                            router.push('/login')
                        }
                    })
                    .catch( e => {
                        if (e.response) {
                            if (e.response.data.message === "Expired JWT Token") {
                                error.value = 'Votre session a expirée !'
                                sessionStorage.removeItem('token')
                                setTimeout(() => router.push('/login') , 2000);
                            } else {
                                error.value = e.response.data['hydra:description']
                            }
                        }
                    })
            }else{
                error.value = "Vous devez remplir tous les champs !"
            }
        }
    }

    function updatePassword(){
        error.value = ""
        //Si les mots de passes sont bien remplis
        if(clientPassword.value.password != "" && clientPassword.value.email != "" && client.value.passwordActuel != ""){
            //Si le nouveau mot de passe fait bien au moins 6 caractères
            if(clientPassword.value.password.length < 6){
                error.value = "Le mot de passe doit contenir au moins 6 caractères"
            }else{
                if(clientPassword.value.password == passwordConfirm.value){
                    //Demande de modification de mot de passe à l'api
                    Axios().post('update_client_password',clientPassword.value)
                        .then(response =>  {
                            error.value = ""
                            message.value = "Votre mot de passe à bien été modifier !"
                            setTimeout(() => router.push('/homeClient') , 2000);
                        })
                        .catch( e => {
                            if (e.response) {
                                if (e.response.data.message === "Expired JWT Token") {
                                    error.value = 'Votre session a expirée !'
                                    sessionStorage.removeItem('token')
                                    setTimeout(() => router.push('/login') , 2000);
                                } else {
                                    error.value = e.response.data['hydra:description']
                                }
                            }
                        })
                        .then(
                            clientPassword.value = {email: store.login, password: '', passwordActuel: ''},
                            passwordConfirm.value = ""
                        )
                }else{
                    error.value = "Les mots de passe ne correspondent pas !"
                }
            }
        }else{
            error.value = "Vous devez remplir tous les champs !"
        }
    }
</script>
