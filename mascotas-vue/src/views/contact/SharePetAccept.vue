<template>
  <TheLoader v-if="loading"/>

  <v-container
      v-else-if="request && hasAccess"
      fluid class="justify-center d-flex align-center"
  >
    <h1 class="sr-only">Aceptar petición</h1>

    <SharePetAcceptCard
        v-if="incomplete"
        :request="request"
        @accepted="accepted"
        @rejected="rejected"
        @accessDenied="accessDenied"
    />

    <TitleWithLink
        v-else
        :title="result.title"
        :link-text="result.linkText"
        :route-name="result.routeName"
    />
  </v-container>

  <TitleWithLink
      v-else
      title="Este enlace no es válido, le podes pedir al dueño de la mascota que te la comparta nuevamente."
      link-text="Ir al inicio"
      route-name="Pets"
  />
</template>

<script>
import contactService from "@/services/contact";
import SharePetAcceptCard from "@/components/contact/request/SharePetAcceptCard";
import TheLoader from "@/components/general/layout/TheLoader";
import TitleWithLink from "@/components/general/layout/TitleWithLink";
import {handleAuthenticationError} from "@/helpers";

export default {
  name: "SharePetAccept",
  components: {TitleWithLink, SharePetAcceptCard, TheLoader},
  data: () => ({
    loading: true,
    request: null,
    incomplete: true,
    hasAccess: true,
    handleAuthenticationError,
    notification: {
      text: null,
      type: 'success',
    },
    result: {
      title: null,
      linkText: null,
      routeName: null,
    }
  }),
  mounted() {
    contactService.acceptGenerate(this.$route.params.request_id)
        .then(res => {
          if (this.handleAuthenticationError(res)) return;
          if (res.success) {
            this.request = res.data.request;
          }

          this.loading = false;
        })
  },
  methods: {
    accepted() {
      this.result = {
        title: 'Petición aceptada con éxito',
        linkText: 'Ir al inicio',
        routeName: 'HomeProfessional',
      }

      this.incomplete = false;
    },

    rejected() {
      this.result = {
        title: 'Petición rechazada con éxito',
        linkText: 'Ir al inicio',
        routeName: 'HomeProfessional',
      }

      this.incomplete = false;
    },

    accessDenied() {
      this.hasAccess = false;
    }
  },
}
</script>