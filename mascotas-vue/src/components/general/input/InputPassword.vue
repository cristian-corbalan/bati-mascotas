<template>
  <div>
    <v-text-field
        outlined
        class="form-control"
        v-bind="$attrs"
        :type="showPassword ? 'text' : 'password'"
        :rules="[rules.obligatory, rules.password]"
        :error="errors !== null"
        :messages="errors ? errors[0] : ''"
        :disabled="loading"
        id="password"
        name="password"
        color="#3fb094"
        hint="La contraseña debe tener mínimo 6 caracteres y contener un número"
        persistent-hint
        counter
        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
        v-on="$listeners"
        @click:append="showPassword = !showPassword"
    />
  </div>
</template>

<script>
export default {
  name: "InputPassword",
  props: {
    errors: {
      default: null
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      showPassword: false,
      rules: {
        obligatory: value => !!value || 'Este campo es obligatorio.',
        password: value => {
          const pattern = /^(?=.*[a-zA-Z])(?=.*[0-9])/;
          return pattern.test(value) || 'La contraseña debe tener mínimo 6 caracteres y contener un número'
        },
      },
    }
  },
}
</script>