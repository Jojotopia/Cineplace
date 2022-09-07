<template>
    <div class="chargement" v-if="!loaded">Chargement des données en cours...</div>
    <section class="long_admin_container">
        <h3 class="form_titre">Vos tickets</h3>
        <div id='error' v-if="error" > {{ error }} </div>

        <section id="comfirmation_suppression" v-if="reservationSupprimer">
            <div>
                <p>Voulez-vous vraiment supprimer votre billet pour {{reservationSupprimer.projection.film.titre}} ?</p>
                <p>
                    <a id="confirmation_suppression_oui" @click="deleteReservation()">Oui</a>
                    <a id="confirmation_suppression_non" @click="reservationSupprimer = null; error = ''">Non</a>
                </p>
            </div>
        </section>

        <div class="billet" v-for="(r,index) in reservations" :key="index">
            <!-- Si un film n'a pas d'affiche prendre l'image par défaut-->
            <img v-if="r.projection.film.affiche" class="billet_image" :src="'http://localhost:8000/uploads/affiches/' + r.projection.film.affiche" :alt="r.projection.film.titre">
            <img v-if="!r.projection.film.affiche" class="billet_image" src="http://localhost:8000/uploads/affiches/image-par-default.jpg" :alt="r.projection.film.titre">
            
            <div class="billet_description">
                <p class="billet_titre">{{r.projection.film.titre}}</p>
                <p class="billet_date">{{new Date(r.projection.date).toLocaleDateString("fr-FR", {weekday: "long", year: "numeric", month: "long", day: "numeric", hour: "numeric", minute: "numeric"}).toUpperCase()}}</p>
                <p class="billet_places">{{r.nbSieges}} places</p>
                <p class="billet_id">Numéro du billet : {{r.id}}</p>
                <p v-if="new Date(r.projection.date) > dateDuJour"><a class="reservationButton" @click="reservationSupprimer = r">Annuler</a></p>
            </div>
        </div>
        
        <p v-if="reservations.length == 0" class="description_film_age">Aucune réservations pour le moment !</p>
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
    const reservations = ref([])
    //Permet d'afficher la demande de confirmation de suppression
    const reservationSupprimer = ref(null)
    const dateDuJour = ref(new Date())
    //Permet à l'utilisateur de savoir si les données chargent ou si il n'a pas de billets
    const loaded = ref(false)
    const error = ref('')

    function actualiserReservations(){
        error.value = ""
        //Demande les reservations du client connécté à l'api
        Axios().get('reservations?client.email='+store.login)
            .then(response =>  {
                reservations.value = response.data["hydra:member"],
                loaded.value = true
            })
            .catch( e => {
                if (e.response) {
                    if (e.response.data.message === "Expired JWT Token") {
                        error.value = 'Votre session a expirée !'
                        sessionStorage.removeItem('token')
                        setTimeout(() => router.push('/login') , 2000);
                    }
                }
            })
    }

    onMounted(() => {
        //Au chargement de la page on cherche toutes les réservations du client connecté
        actualiserReservations();
    })
    function deleteReservation(){
        //Demande de suppression de la réservation à l'api
        Axios().post('annuler_reservation',{"reservation": reservationSupprimer.value.id, "client": store.login})
            .then(response =>  {
                actualiserReservations(),
                reservationSupprimer.value = null
            })
            .catch( e => {
                if (e.response) {
                    if (e.response.data.message === "Expired JWT Token") {
                        error.value = 'Votre session a expirée !'
                        sessionStorage.removeItem('token')
                        setTimeout(() => router.push('/login') , 2000);
                    }
                }else{
                    error.value = e.response.data['hydra:description']
                    reservationSupprimer.value = null
                }
            })
    }
</script>
