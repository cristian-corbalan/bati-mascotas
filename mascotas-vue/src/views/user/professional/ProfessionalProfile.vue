<template>
  <TheLoader v-if="loading"/>

  <div v-else class="professional-profile">
    <TitleBar :title="nameToDisplay(professional)"/>

    <BaseNotification
        v-if="store.status.msg != null"
        :type="store.status.type"
        :text="store.status.msg"
        :title="store.status.title"
    />

    <div class="profile-container">
      <PetShareDialog
          :showDialog="showPetShareDialog"
          :professionalName="professional.first_name"
          :professionalId="professional.id"
          @closeDialog="showPetShareDialog = false"
      />

      <div class="profile-main-content">
        <button
            v-if="!isProfessional"
            class="float-circle-btn"
            @click="showPetShareDialog = true"
        >

          <img :src="createStaticImgPath('contact/share-pet.svg')" alt="Compartir mascota">

          <span class="sr-only">Compartir mascota con este profesional</span>
        </button>

        <button
            v-if="canEditProfile"
            class="float-circle-btn"
            @click="$router.push({ name: 'ProfessionalEdit' })"
        >

          <v-icon>mdi-pencil</v-icon>

          <span class="sr-only">Editar perfil</span>

        </button>

        <div class="intro">
          <div>
            <v-img 
              class="profile-img rounded-circle"
              aspect-ratio="1" 
              max-height="200"
              max-width="200"
              :src="createImgPath(professional.profile_image.src)" 
              :alt="professional.profile_image.alt"
            />
          </div>

          <h2>{{ professionalName }}</h2>

          <div class="professional-types">
            <div v-for="type in professional.user_types" :key="type.id">
              {{ type.name }}
            </div>
          </div>
        </div>

        <div v-if="professional.description" class="description">
          <h3>Descripción</h3>

          <p>{{ professional.description }}</p>
        </div>
      </div>

      <aside>
        <div v-if="hasContact" class="contact">
          <h3>Contactar a {{ professional.first_name }}</h3>

          <ul class="contact-data-container">
            <ProfessionalProfileContactItem
                v-for="method in contactMethods"
                :key="method.name"
                :contactData="method.contactData"
                :link="method.link"
                :imgPath="method.imgPath"
                :alt="method.alt"
            />
          </ul>
        </div>

        <div class="location">
          <h3>Dirección</h3>

          <ProfessionalProfileAddress :professional="professional"/>

          <a :href="googleMapsLink" target="_blank">
            <img :src="createStaticImgPath('contact/google-maps.png')" alt="Link a Google Maps">

            <span>Google Maps</span>
          </a>
        </div>
      </aside>
    </div>
  </div>
</template>

<script>
import BaseNotification from "@/components/general/notification/BaseNotification";
import PetShareDialog from "@/components/contact/request/SharePetDialog";
import ProfessionalProfileContactItem from "@/components/user/professional/ProfessionalProfileContactItem";
import ProfessionalProfileAddress from "@/components/user/professional/ProfessionalProfileAddress";
import store from "@/store";
import TheLoader from "@/components/general/layout/TheLoader";
import TitleBar from "@/components/general/layout/TitleBar";
import userServices from "@/services/users";
import {createImgPath, createStaticImgPath, nameToDisplay, handleAuthenticationError} from "@/helpers";

export default {
  name: "ProfessionalProfile",
  components: {
    TitleBar,
    TheLoader,
    ProfessionalProfileContactItem,
    PetShareDialog,
    ProfessionalProfileAddress,
    BaseNotification,
  },
  data() {
    return {
      loading: true,
      professional: null,
      store,
      createImgPath,
      createStaticImgPath,
      contactMethods: [],
      showPetShareDialog: false,
      nameToDisplay,
      handleAuthenticationError,
    }
  },
  computed: {
    googleMapsLink() {
      const directions = this.whiteSpacesToPlus(
          this.professional.street + '+'
          + this.professional.house_number + '+'
          + this.professional.postal_code + '+'
          + this.professional.city + '+'
          + this.professional.state
      )

      return 'https://www.google.com.ar/maps/dir//' + directions;
    },

    professionalName() {
      return `${this.professional.first_name} ${this.professional.last_name}`
    },

    /** Returns true if any of the contact methods is set */
    hasContact() {
      return this.professional.whatsapp
          || (this.professional.email && this.professional.email_visible)
          || this.professional.instagram
          || this.professional.facebook
          || this.professional.web;
    },

    /** Returns true if the logged in user has the same id as the profile's user */
    canEditProfile() {
      return this.professional.id === this.store.user.id;
    },

    isProfessional() {
      return store.user.is_professional;
    }
  },
  watch: {
    /**
     * Updates data if the url param changes
     */
    '$route.params.professional_id': function () {
      this.loadUserProfile();
    },
  },
  mounted() {
    this.loadUserProfile();
  },
  methods: {
    whiteSpacesToPlus(str) {
      return str.replace(/\s/g, '+');
    },

    loadUserProfile() {
      userServices.getUserById(this.$route.params.professional_id)
          .then(res => {
            if (this.handleAuthenticationError(res)) return;
            if (res.data.user) {
              this.professional = res.data.user;

              this.contactMethods = [
                {
                  'name': 'whatsapp',
                  'contactData': this.professional.whatsapp,
                  'link': 'https://api.whatsapp.com/send?phone=' + this.professional.whatsapp,
                  'imgPath': 'contact/whatsapp.png',
                  'alt': 'link a Whatsapp'
                },
                {
                  'name': 'email',
                  'contactData': this.professional.email,
                  'link': 'mailto:' + this.professional.email,
                  'imgPath': 'contact/gmail.png',
                  'alt': 'link a Gmail'
                },
                {
                  'name': 'instagram',
                  'contactData': this.professional.instagram,
                  'link': 'https://www.instagram.com/' + this.professional.instagram,
                  'imgPath': 'contact/instagram.png',
                  'alt': 'link a Instagram'
                },
                {
                  'name': 'facebook',
                  'contactData': this.professional.facebook,
                  'link': 'https://www.facebook.com/' + this.professional.facebook,
                  'imgPath': 'contact/facebook.png',
                  'alt': 'link a Facebook'
                },
                {
                  'name': 'web',
                  'contactData': this.professional.web,
                  'link': this.professional.web,
                  'imgPath': 'contact/www.png',
                  'alt': 'link a la página web'
                }
              ];

              this.loading = false;
            } else {
              this.store.setStatus({
                msg: 'El perfil que estás buscando no está disponible.',
                type: 'error',
              });

              this.$router.go(-1);
            }
          });
    }
  },
}
</script>
