<template>
  <TheLoader v-if="loading"/>

  <BaseFormContainer
      v-else
      :is_short_form="true"
      headline="Cambiar Contraseña"
  >
    <form
        ref="form"
        action="#"
        method="post"
        @submit.prevent="changePassword"
    >
      <p>Para la seguridad de tu cuenta es importante que cambies tu contraseña regularmente.</p>

      <InputText
          v-model="formData.currentPassword"
          :type="showPassword ? 'text' : 'password'"
          :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
          :loading="loading"
          :errors="errors.currentPassword"
          label="Contraseña actual"
          @click:append="showPassword = !showPassword"
      />

      <InputPassword
          v-model="formData.newPassword"
          :loading="loading"
          :errors="errors.newPassword"
          label="Contraseña nueva"
      />

      <button class="main-btn" type="submit" :disabled="loading">Guardar contraseña</button>
    </form>
  </BaseFormContainer>
</template>

<script>
import authService from "@/services/auth";
import BaseFormContainer from "@/components/general/form/BaseFormContainer";
import InputText from "@/components/general/input/InputText";
import InputPassword from "@/components/general/input/InputPassword";
import store from "@/store";
import TheLoader from "@/components/general/layout/TheLoader";
import {handleAccessError, handleAuthenticationError} from "@/helpers";

export default {
  name: "ChangePasswordAuthenticated",
  components: {
    InputText,
    InputPassword,
    BaseFormContainer,
    TheLoader,
  },
  data() {
    return {
      showPassword: false,
      loading: false,
      store,
      showFinalMessage: false,
      handleAccessError,
      handleAuthenticationError,
      formData: {
        currentPassword: null,
        newPassword: null,
      },
      errors: {
        currentPassword: null,
        newPassword: null,
      },
      rules: {
        obligatory: value => !!value || 'Este campo es obligatorio.',
      },
    }
  },
  methods: {
    changePassword() {
      this.loading = true;

      this.errors = {
        currentPassword: null,
        newPassword: null,
      }

      authService.changePasswordAuthenticated(this.formData)
          .then(res => {
            this.loading = false;
            if (!res.success) {
              if (this.handleAuthenticationError(res)) return;
              if (this.handleAccessError(res)) return;
              if (res.errors) {
                this.errors = {
                  ...res.errors
                }
              } else {
                this.store.setStatus({
                  msg: 'Lamentablemente no se pudo cambiar la contraseña. ' + res.msg ? res.msg : '',
                  type: 'error',
                });
              }

              this.formData = {
                currentPassword: null,
                newPassword: null,
              }
            } else {
              this.logout();
            }
          })
    },

    logout() {
      this.showSettingsDialog = false;

      this.store.setActivePet(null);

      this.store.setStatus({
        msg: 'Ya te podés loguear con tu nueva contraseña',
        type: 'success',
      });

      authService.logout()
          .then(() => {
            this.dialog = false;
            this.$router.push({name: 'Login'});
          });
    },
  }
}
</script>