<template>
  <v-card width="500px" height="fit-content">
    <v-card-title>{{ ownerName }}</v-card-title>

    <v-divider/>

    <v-container>
      <div>
        <p>Mascota: {{ request.pet.name }}</p>
        <p>Fecha de expiración: {{ expirationDate }}</p>
      </div>

      <div v-if="request.description">
        <p><span class="sr-only">Descripción de la petición: </span>{{ request.description }}</p>
      </div>
    </v-container>

    <v-divider/>

    <v-card-actions class="justify-center pt-4 pb-4 accept-card-actions">
      <v-btn outlined @click="decline">
        Rechazar
      </v-btn>

      <v-btn @click="accept">
        Aceptar
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import contactService from "@/services/contact";
import {formatDate} from "@/helpers";
import {handleAccessError} from "@/helpers";

export default {
  name: "SharePetAcceptCard",
  props: {
    request: {
      required: true,
      type: Object,
    }
  },
  data() {
    return {
      expirationDate: formatDate(this.request.expiration_date),
      handleAccessError,
    }
  },
  computed: {
    ownerName() {
      return `${this.request.owner.first_name} ${this.request.owner.last_name}`
    }
  },
  methods: {
    accept() {
      contactService.accept(this.request.id)
          .then((res) => {
            if (res.access === false) {
              this.$emit('accessDenied');
            } else {
              this.$emit('accepted')
            }
          })
    },

    decline() {
      contactService.delete(this.request.id)
          .then((res) => {
            if (res.access === false) {
              this.$emit('accessDenied');
            } else {
              this.$emit('rejected')
            }
          })
    },
  }
}
</script>