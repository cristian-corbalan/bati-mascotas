<template>
  <v-form
      ref="ownerForm"
      action="usuario/dueño"
      method="post"
      @submit.prevent="sendForm"
  >
    <InputText
        v-model="formData.first_name"
        label="Nombre"
        :loading="loading"
        :rules="[rules.obligatory]"
        :errors="errors.first_name"
    />

    <InputText
        v-model="formData.last_name"
        label="Apellido"
        :loading="loading"
        :rules="[rules.obligatory]"
        :errors="errors.last_name"
    />

    <InputText
        v-model="formData.email"
        type="email"
        label="Correo electrónico"
        :loading="loading"
        :rules="[rules.obligatory, rules.email]"
        :errors="errors.email"
    />

    <InputPassword
        v-if="!user"
        v-model="formData.password"
        label="Contraseña"
        :loading="loading"
        :errors="errors.password"
    />

    <button class="main-btn" type="submit" :disabled="loading">{{ buttonText }}</button>

    <DeleteAccountButton v-if="user"/>
  </v-form>
</template>

<script>
import authService from "@/services/auth";
import DeleteAccountButton from "@/components/general/button/BaseButtonDeleteAccount";
import InputText from "@/components/general/input/InputText";
import InputPassword from "@/components/general/input/InputPassword";
import store from "@/store";
import userService from "@/services/users";
import {handleAccessError} from "@/helpers";

export default {
  name: "OwnerSignUpForm",
  components: {
    InputText,
    InputPassword,
    DeleteAccountButton,
  },
  props: {
    user: {
      type: Object,
      default: null,
    },
  },
  data: () => ({
    loading: false,
    store,
    handleAccessError,
    formData: {
      first_name: null,
      last_name: null,
      email: null,
      password: null,
    },
    errors: {
      first_name: null,
      last_name: null,
      email: null,
      password: null,
    },
    rules: {
      obligatory: value => !!value || 'Este campo es obligatorio',
      email: value => {
        const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return pattern.test(value) || 'El correo electrónico no es válido';
      },
    },
  }),
  computed: {
    buttonText(){
      return this.user ? 'Guardar cambios' : 'Crear cuenta';
    },
  },
  mounted() {
    if (this.user) {
      this.formData = {
        ...this.user
      }
    }
  },
  methods: {
    sendForm() {
      if (this.$refs.ownerForm.validate()) {
        this.loading = true;

        this.errors = {
          first_name: null,
          last_name: null,
          email: null,
          password: null,
        }

        if (!this.user) {
          this.signUp();
        } else {
          this.updateUser();
        }
      }

    },
    /**
     * Attempt to create a new user with the role of owner, if something fails it will show error messages and why it failed.
     * If the process completes successfully the user will be authenticated and sent to home.
     */
    signUp() {
      userService.createOwner(this.formData)
          .then(res => {
            if (!res.success) {
              if (this.handleAccessError(res)) return;

              if (res.errors) {
                this.errors = {
                  first_name: null,
                  last_name: null,
                  email: null,
                  password: null,
                  ...res.errors
                }
              } else {
                this.store.setStatus({
                  msg: "¡Algo salió mal! Por favor, intentalo nuevamente más tarde",
                  type: 'error'
                });
              }
            } else {
              let credentials = {
                email: this.formData.email,
                password: this.formData.password,
              }

              authService.login(credentials)
                  .then(res => {
                    authService.saveAuthUser(res.data.user);

                    this.$router.push({name: 'Home'});
                  });
            }

            this.loading = false
          });

    },

    updateUser() {
      userService.update(this.formData, this.user.id)
          .then(res => {
            this.loading = false;

            if (!res.success) {
              if (this.handleAccessError(res)) return;

              if (res.errors) {
                this.errors = {
                  first_name: null,
                  last_name: null,
                  email: null,
                  password: null,
                  ...res.errors
                }
                this.store.setStatus({
                  msg: "Por favor corregí los datos del formulario.",
                  type: 'warning',
                });
              } else {
                this.store.setStatus({
                  msg: '¡Algo salió mal! Por favor, intentalo nuevamente más tarde.',
                  type: 'error',
                });
              }
            } else {
              authService.saveAuthUser(res.data.user);

              this.store.setStatus({
                msg: 'Los cambios se guardaron con éxito.',
                type: 'success',
              });

              this.$router.back();
            }
          })
    }
  },
}
</script>
