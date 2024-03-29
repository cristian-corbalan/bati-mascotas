<template>
  <v-app>
    <div id="skip-to-content">
      <a href="#main-content">Saltar al contenido principal</a>
    </div>

    <v-app-bar app>
      <div id="brand-image">
        <router-link :to="logoRoute">
          <img :src="createStaticImgPath('brand/logotype.png')" alt="Logo de Basti"/>
        </router-link>
      </div>

      <v-spacer/>

      <ul id="nav-links">
        <template v-if="store.user.id">
          <li v-for="link in routerLinks" :key="link.text">
            <router-link :to="{name: link.name}" exact>
              <span class="material-icons">{{ link.icon }}</span>

              <span>{{ link.text }}</span>
            </router-link>
          </li>

          <li>
            <a href="#" @click.prevent="showSettingsDialog = true">
              <span class="material-icons">account_circle</span>

              <span class="sr-only">Abrir opciones (abre ventana modal)</span>
            </a>
          </li>
        </template>
      </ul>
    </v-app-bar>

    <SettingsDialog
        :showDialog="showSettingsDialog"
        :user="store.user"
        @cancel="showSettingsDialog = false"
        @logout="logout"
    />

    <!--Config modal-->
    <v-dialog v-model="dialog" max-width="500" class="container--fluid">
      <v-card id="config-list">
        <ul>
          <li><a href="#">Configuración</a></li>

          <li><a class="danger-text" @click.prevent="logout">Cerrar sesión</a></li>

          <li>
            <v-btn @click="dialog=false">Cancelar</v-btn>
          </li>
        </ul>
      </v-card>
    </v-dialog>

    <v-main id="main-content" class="pt-0">
      <router-view @create-notification="createNotification"/>
    </v-main>
  </v-app>
</template>

<script>

import authService from "./services/auth";
import SettingsDialog from "@/components/general/layout/setting/SettingsDialog";
import store from "./store";
import {createStaticImgPath} from "@/helpers";


export default {
  name: 'App',
  components: {
    SettingsDialog,
  },
  data: () => ({
    dialog: false,
    showSettingsDialog: false,
    showWarnDialog: false,
    createStaticImgPath,
    store,
    routerLinksOwner: [
      {
        name: 'Schedule',
        text: 'Agenda',
        icon: 'event',
      },
      {
        name: 'Explore',
        text: 'Explorar',
        icon: 'search',
      },
      {
        name: 'Pets',
        text: 'Mascotas',
        icon: 'format_list_bulleted',
      },
      {
        name: 'SharedPets',
        text: 'Compartir',
        icon: 'share',
      },
    ],
    routerLinksProfessional: [
      {
        name: 'HomeProfessional',
        text: 'Home',
        icon: 'home',
      },
      {
        name: 'ScheduleProfessional',
        text: 'Agenda',
        icon: 'event',
      },
      {
        name: 'ExploreProfessional',
        text: 'Explorar',
        icon: 'search',
      },
    ]
  }),
  computed: {
    logoRoute() {
      if (store.user.id) {
        return store.user.is_professional ? {name: 'HomeProfessional'} : {name: 'Pets'};
      }
      return '#';
    },

    routerLinks() {
      if (store.user.is_professional) {
        return this.routerLinksProfessional;
      } else {
        return this.routerLinksOwner;
      }
    },
  },
  methods: {
    /**
     * Logs out the user, and redirects to the login view
     */
    logout() {
      this.showSettingsDialog = false;
      this.store.setActivePet(null);
      this.store.setStatus({
        msg: 'Gracias y hasta la próxima',
        type: 'success',
      });
      authService.logout()
          .then(() => {
            this.dialog = false;
            this.$router.push({name: 'Login'});
          });
    },

    createNotification(type, content) {
      this.store.setStatus({
        msg: content,
        type: type,
      });
    },
  },
};
</script>
