<template>
    <section class='long_admin_container'>
        <div class="div_form">
            <div id='message' v-if="message" > {{ message }} </div>
            <div id='error' v-if="error" > {{ error }} </div>
            <p>
                <label for="civilite">Civilité : </label>
                <select name="civilite" id="civilite" v-model="client.civilite">
                    <option value="M.">M.</option>
                    <option value="MME.">MME.</option>
                </select>
            </p>
            <p>
                <label for="nom">Nom : </label>
                <input type="text" name="nom" id="nom" v-model="client.nom" @keyup.enter="creerCompte()" autofocus>
            </p>
            <p>
                <label for="prenom">Prénom : </label>
                <input type="text" name="prenom" id="prenom" v-model="client.prenom" @keyup.enter="creerCompte()">
            </p>
            <p>
                <label for="mail">Email : </label>
                <input type="email" name="mail" id="mail" v-model="client.email" @keyup.enter="creerCompte()">
            </p>
            <p>
                <label for="password">Mot de passe : </label>
                <input type="password" name="password" id="password" v-model="client.password" @keyup.enter="creerCompte()">
            </p>
            <p>
                <label for="passwordConfirmation">Confirmation mot de passe : </label>
                <input type="password" name="passwordConfirmation" id="passwordConfirmation" v-model="passwordConfirm" @keyup.enter="creerCompte()">
            </p>
            <p>
                <input type="submit" name="send" value="Créer mon compte" @click="creerCompte()" >
            </p>
        </div>
    </section>
</template>

<script setup>
    import Axios from '../../api/axios'
    import { ref } from 'vue'
    import { useRouter } from 'vue-router'

    const router = useRouter()

    const client = ref({
        email: '',
        password: '',
        nom: '',
        prenom: '',
        civilite: 'M.',
    })
    const passwordConfirm = ref('');
    const error = ref('')
    const message = ref('')

    function creerCompte(){
        error.value = ""
        //Vérification de la validité de l'email
        if(!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(client.value.email)) {
            error.value = "L'adresse mail n'est pas valide !";
        //Vérification que le mot de passe fasse au moins 6 caractères
        }else if(client.value.password.length < 6){
            error.value = "Le mot de passe est trop court il doit contenir au moins 6 caractère !";
        }else{
            //Si tous les champs sont remplis
            if(client.value.email != '' && client.value.password != '' && client.value.nom != '' && client.value.civilite != ''){
                if(client.value.password == passwordConfirm.value){
                    //Demande de création de compte client à l'api
                    Axios().post('register',client.value)
                    .then(response =>  {
                        error.value = ""
                        message.value = "Votre compte à été créer avec succes !"
                        //Renvoi à la page de connexion
                        setTimeout(() => router.push('/login') , 2000);
                    })
                    .catch( e => {
                        if (e.response) {
                            if (e.response.data.message === "Expired JWT Token") {
                                error.value = 'Votre session a expirée !'
                                sessionStorage.removeItem('token')
                                setTimeout(() => router.push('/login') , 2000);
                            } else {
                                error.value = "Erreur lors de l'ajout : " + e.response.data['hydra:description']
                            }
                        }
                    })
                    .then(
                        client.value = {email: '', password: '', nom: '', prenom: '', civilite: 'M.'},
                        passwordConfirm.value = ""
                    )
                }else{
                    client.value.password = ""
                    passwordConfirm.value = ""
                    error.value = "Les mots de passes ne correspondent pas !"
                }
            }
        }
    }
</script>
